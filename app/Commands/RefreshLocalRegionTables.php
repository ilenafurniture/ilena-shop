<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/** Validates or refreshes legacy region tables without changing their schema. */
class RefreshLocalRegionTables extends BaseCommand
{
    protected $group = 'Data maintenance';
    protected $name = 'regions:refresh-local';
    protected $description = 'Validasi dry-run atau perbarui tabel wilayah tanpa mengubah struktur tabel.';
    protected $usage = 'regions:refresh-local [--source=PATH] [--apply] [--confirm-production=ilena] [--backup=PATH] [--confirm-regions-hash=SHA256] [--confirm-postal-hash=SHA256]';
    protected $options = [
        'source' => 'Direktori yang berisi wilayah.sql dan wilayah_kodepos.sql.',
        'apply' => 'Terapkan setelah seluruh validasi lulus; production wajib konfirmasi database, backup, dan hash sumber.',
        'confirm-production' => 'Wajib bernilai ilena untuk menerapkan perubahan pada production.',
        'backup' => 'Path dump gzip sebelum perubahan; wajib memuat enam tabel terkait.',
        'confirm-regions-hash' => 'SHA-256 wilayah.sql yang telah ditinjau.',
        'confirm-postal-hash' => 'SHA-256 wilayah_kodepos.sql yang telah ditinjau.',
    ];

    private const SOURCE_DIR = ROOTPATH . 'ops/region-data/2025.2.5' . DIRECTORY_SEPARATOR;
    private const TABLE_COLUMNS = [
        'provinsi' => ['id', 'label'],
        'kabupaten' => ['id', 'provinsi_id', 'label'],
        'kecamatan' => ['id', 'provinsi_id', 'kabupaten_id', 'label'],
        'kelurahan' => ['id', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'label', 'kodepos'],
    ];

    public function run(array $params)
    {
        ini_set('memory_limit', '512M');
        $apply = CLI::getOption('apply') !== null;
        if (!in_array(ENVIRONMENT, ['development', 'production'], true)) {
            CLI::error('Perintah hanya mendukung environment development atau production.');
            return EXIT_ERROR;
        }

        try {
            $db = Database::connect();
            $database = (string) $db->query('SELECT DATABASE() AS name')->getRow()->name;
            if ($database !== 'ilena') {
                throw new \RuntimeException('Database aktif bukan database lokal ilena; tidak ada data yang diubah.');
            }
            $this->assertLegacySchema($db);
            $this->assertTransactionalTables($db);

            $sourceDir = rtrim((string) (CLI::getOption('source') ?: self::SOURCE_DIR), "\\/") . DIRECTORY_SEPARATOR;
            $regionsSql = file_get_contents($sourceDir . 'wilayah.sql');
            $postalSql = file_get_contents($sourceDir . 'wilayah_kodepos.sql');
            if (!is_string($regionsSql) || !is_string($postalSql)) {
                throw new \RuntimeException('Snapshot wilayah/kodepos lokal tidak ditemukan.');
            }
            $catalog = $this->buildCatalog($regionsSql, $postalSql);
            $addressUpdates = $this->mapSavedAddresses($db, $catalog);
            $freeShippingUpdate = $this->mapFreeShippingConfig($db, $catalog);

            CLI::write('Database: ' . $database . ' (' . ENVIRONMENT . ')');
            CLI::write('Struktur tabel: cocok; tidak ada kolom yang akan ditambahkan/diubah.');
            CLI::write(sprintf(
                'Katalog tervalidasi: provinsi %d, kabupaten/kota %d, kecamatan %d, desa/kelurahan %d.',
                count($catalog['provinceRows']), count($catalog['cityRows']), count($catalog['districtRows']), count($catalog['villageRows'])
            ));
            CLI::write('Alamat tersimpan yang berhasil dipetakan ulang: ' . count($addressUpdates) . '.');
            CLI::write('Konfigurasi gratis ongkir yang berhasil dipetakan ulang: ' . ($freeShippingUpdate === null ? 'tidak ada' : 'ya') . '.');
            CLI::write('Hash wilayah: ' . $this->canonicalSourceHash($regionsSql));
            CLI::write('Hash kodepos: ' . $this->canonicalSourceHash($postalSql));

            if (ENVIRONMENT === 'production' && $apply) {
                $this->assertProductionApplyConfirmation(
                    (string) $database,
                    $regionsSql,
                    $postalSql,
                    (string) (CLI::getOption('backup') ?? '')
                );
            }

            if (!$apply) {
                CLI::write('DRY-RUN: database belum diubah. Untuk apply production wajib sertakan konfirmasi, hash, dan backup terverifikasi.', 'yellow');
                return EXIT_SUCCESS;
            }

            $this->applyCatalog($db, $catalog, $addressUpdates, $freeShippingUpdate);
            CLI::write('Selesai: isi empat tabel wilayah diperbarui; struktur tabel tetap sama.', 'green');
            return EXIT_SUCCESS;
        } catch (\Throwable $error) {
            log_message('error', 'Local region refresh failed: {type}: {message}', [
                'type' => get_class($error), 'message' => $error->getMessage(),
            ]);
            CLI::error('Tidak ada perubahan yang diterapkan, atau transaksi telah dibatalkan: ' . $error->getMessage());
            return EXIT_ERROR;
        }
    }

    private function assertLegacySchema($db): void
    {
        foreach (self::TABLE_COLUMNS as $table => $expected) {
            if (!$db->tableExists($table)) {
                throw new \RuntimeException('Tabel yang diharapkan tidak ditemukan: ' . $table);
            }
            $actual = $db->getFieldNames($table);
            if ($actual !== $expected) {
                throw new \RuntimeException('Struktur tabel ' . $table . ' berbeda dari skema yang diuji; dihentikan tanpa perubahan.');
            }
        }
    }

    private function assertTransactionalTables($db): void
    {
        $tables = ['provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'pembeli'];
        if ($db->tableExists('app_settings')) {
            $tables[] = 'app_settings';
        }
        $rows = $db->query(
            'SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('
            . implode(',', array_fill(0, count($tables), '?')) . ')',
            $tables
        )->getResultArray();
        $engines = [];
        foreach ($rows as $row) {
            $engines[(string) $row['TABLE_NAME']] = strtoupper((string) $row['ENGINE']);
        }
        foreach ($tables as $table) {
            if (($engines[$table] ?? '') !== 'INNODB') {
                throw new \RuntimeException('Tabel ' . $table . ' bukan InnoDB; transaksi rollback tidak dapat dijamin, dihentikan tanpa perubahan.');
            }
        }
    }

    private function buildCatalog(string $regionsSql, string $postalSql): array
    {
        preg_match_all("/\\('([0-9.]+)'\\s*,\\s*'((?:[^']|'')*)'\\)/u", $regionsSql, $regionMatches, PREG_SET_ORDER);
        $names = [];
        foreach ($regionMatches as $match) {
            $code = $match[1];
            if (isset($names[$code])) {
                throw new \RuntimeException('Kode wilayah ganda: ' . $code);
            }
            $length = strlen($code);
            if (!in_array($length, [2, 5, 8, 13], true)) {
                throw new \RuntimeException('Panjang kode tidak valid: ' . $code);
            }
            $names[$code] = str_replace("''", "'", $match[2]);
        }

        preg_match_all("/\\('([0-9.]+)'\\s*,\\s*'([0-9]{5})'\\)/", $postalSql, $postalMatches, PREG_SET_ORDER);
        $postcodes = [];
        foreach ($postalMatches as $match) {
            if (isset($postcodes[$match[1]])) {
                throw new \RuntimeException('Kodepos ganda untuk kode wilayah: ' . $match[1]);
            }
            $postcodes[$match[1]] = $match[2];
        }

        $levels = [2 => [], 5 => [], 8 => [], 13 => []];
        foreach ($names as $code => $name) {
            $levels[strlen($code)][$code] = $name;
        }
        $expected = [2 => 38, 5 => 514, 8 => 7285, 13 => 83762];
        foreach ($expected as $length => $count) {
            if (count($levels[$length]) !== $count) {
                throw new \RuntimeException('Jumlah data level ' . $length . ' berubah; snapshot perlu ditinjau.');
            }
        }
        if (count($postcodes) !== $expected[13] || array_diff_key($levels[13], $postcodes) !== []) {
            throw new \RuntimeException('Pemetaan kodepos tidak lengkap terhadap daftar desa/kelurahan.');
        }

        $ids = [];
        foreach ($levels as $length => $rows) {
            ksort($rows, SORT_STRING);
            $id = 1;
            foreach ($rows as $code => $_name) {
                $ids[$code] = $id++;
            }
        }

        foreach ([5 => 2, 8 => 5, 13 => 8] as $length => $parentLength) {
            foreach ($levels[$length] as $code => $name) {
                if (!isset($names[substr($code, 0, $parentLength)])) {
                    throw new \RuntimeException('Kode induk tidak ditemukan untuk ' . $code);
                }
                if (trim($name) === '') {
                    throw new \RuntimeException('Nama wilayah kosong untuk ' . $code);
                }
            }
        }

        $provinceRows = $cityRows = $districtRows = $villageRows = [];
        $provinceByCode = $cityByCode = $districtByCode = [];
        foreach ($levels[2] as $code => $name) {
            $provinceRows[] = ['id' => $ids[$code], 'label' => $name];
            $provinceByCode[$code] = ['id' => $ids[$code], 'label' => $name];
        }
        foreach ($levels[5] as $code => $name) {
            $parent = substr($code, 0, 2);
            $cityRows[] = ['id' => $ids[$code], 'provinsi_id' => $ids[$parent], 'label' => $name];
            $cityByCode[$code] = ['id' => $ids[$code], 'provinsi_id' => $ids[$parent], 'label' => $name];
        }
        foreach ($levels[8] as $code => $name) {
            $province = substr($code, 0, 2);
            $city = substr($code, 0, 5);
            $districtRows[] = ['id' => $ids[$code], 'provinsi_id' => $ids[$province], 'kabupaten_id' => $ids[$city], 'label' => $name];
            $districtByCode[$code] = ['id' => $ids[$code], 'provinsi_id' => $ids[$province], 'kabupaten_id' => $ids[$city], 'label' => $name];
        }
        foreach ($levels[13] as $code => $name) {
            $province = substr($code, 0, 2);
            $city = substr($code, 0, 5);
            $district = substr($code, 0, 8);
            $villageRows[] = [
                'id' => $ids[$code], 'provinsi_id' => $ids[$province], 'kabupaten_id' => $ids[$city],
                'kecamatan_id' => $ids[$district], 'label' => $name, 'kodepos' => $postcodes[$code],
            ];
        }

        return compact('provinceRows', 'cityRows', 'districtRows', 'villageRows', 'provinceByCode', 'cityByCode', 'districtByCode', 'levels', 'postcodes');
    }

    private function mapSavedAddresses($db, array $catalog): array
    {
        $provinceIndex = $cityIndex = $districtIndex = [];
        $villageNameByCity = $villageNameByCityCompact = [];
        $villageNameGlobal = $villageNameGlobalCompact = $villageNameAndPostcode = [];
        foreach ($catalog['levels'][2] as $code => $name) {
            $provinceIndex[$this->normaliseName($name)] = $code;
        }
        foreach ([
            'Bangka Belitung' => 'Kepulauan Bangka Belitung',
            'DI Yogyakarta' => 'Daerah Istimewa Yogyakarta',
            'DKI Jakarta' => 'Daerah Khusus Ibukota Jakarta',
            'Nanggroe Aceh Darussalam' => 'Aceh',
            'Nusa Tenggara Barat NTB' => 'Nusa Tenggara Barat',
            'Nusa Tenggara Timur NTT' => 'Nusa Tenggara Timur',
        ] as $legacy => $canonical) {
            $canonicalKey = $this->normaliseName($canonical);
            if (isset($provinceIndex[$canonicalKey])) {
                $provinceIndex[$this->normaliseName($legacy)] = $provinceIndex[$canonicalKey];
            }
        }
        foreach ($catalog['levels'][5] as $code => $name) {
            $province = substr($code, 0, 2);
            $cityIndex[$province][$this->normaliseName($name)][] = $code;
        }
        foreach ($catalog['levels'][8] as $code => $name) {
            $city = substr($code, 0, 5);
            $districtIndex[$city][$this->normaliseName($name)][] = $code;
        }
        foreach ($catalog['levels'][13] as $code => $name) {
            $city = substr($code, 0, 5);
            $villageName = $this->normaliseName($name);
            $compactName = str_replace(' ', '', $villageName);
            $villageNameByCity[$city][$villageName][] = $code;
            $villageNameByCityCompact[$city][$compactName][] = $code;
            $villageNameGlobal[$villageName][] = $code;
            $villageNameGlobalCompact[$compactName][] = $code;
            $villageNameAndPostcode[$villageName][$catalog['postcodes'][$code]][] = $code;
        }

        $query = $db->table('pembeli')->select('email, alamat')->where('alamat !=', '')->get();
        $updates = [];
        $mappingIssues = [];
        $addressNumber = 0;
        foreach ($query->getResultArray() as $buyer) {
            $addresses = json_decode((string) $buyer['alamat'], true);
            if (!is_array($addresses)) {
                throw new \RuntimeException('JSON alamat pembeli tidak valid; pembaruan dihentikan tanpa perubahan.');
            }
            $changed = false;
            foreach ($addresses as &$address) {
                if (!is_array($address)) {
                    continue;
                }
                $hasRegionFields = array_intersect(
                    ['prov', 'kab', 'kec', 'desa', 'kodepos', 'prov_id', 'kab_id', 'kec_id'],
                    array_keys($address)
                ) !== [];
                if (!$hasRegionFields) {
                    continue;
                }
                $addressNumber++;
                if (!array_key_exists('prov_id', $address)) {
                    $mappingIssues[] = '#' . $addressNumber . ': ID provinsi alamat tidak tersedia';
                    continue;
                }
                $provinceName = $this->normaliseName((string) ($address['prov'] ?? ''));
                $provinceCode = $provinceIndex[$provinceName] ?? null;
                $cityName = $this->normaliseName((string) ($address['kab'] ?? ''));
                $districtName = $this->normaliseName((string) ($address['kec'] ?? ''));
                $villageName = $this->normaliseName((string) ($address['desa'] ?? ''));
                $postcode = preg_replace('/\D+/', '', (string) ($address['kodepos'] ?? '')) ?? '';
                $compactVillageName = str_replace(' ', '', $villageName);

                // First trust an exact village+postcode pair only when it uniquely
                // identifies one canonical region nationwide. This repairs legacy
                // addresses whose stored parent hierarchy is demonstrably corrupt.
                $villageCode = null;
                $postalCandidates = $postcode === '' ? [] : ($villageNameAndPostcode[$villageName][$postcode] ?? []);
                if (count($postalCandidates) === 1) {
                    $villageCode = $postalCandidates[0];
                }

                // Otherwise resolve against a valid province/city. An exact unique
                // village in that city outranks a stale postal code; spacing-only
                // spelling variants are accepted only when unique in the same city.
                $cityCode = null;
                if ($provinceCode !== null) {
                    $cityCandidates = $cityIndex[$provinceCode][$cityName] ?? [];
                    if (count($cityCandidates) > 1) {
                        $cityCandidates = array_values(array_filter(
                            $cityCandidates,
                            fn(string $city): bool => isset($districtIndex[$city][$districtName])
                        ));
                    }
                    if (count($cityCandidates) === 1) {
                        $cityCode = $cityCandidates[0];
                        if ($villageCode === null) {
                            $cityVillageCandidates = $villageNameByCity[$cityCode][$villageName] ?? [];
                            if (count($cityVillageCandidates) === 1) {
                                $villageCode = $cityVillageCandidates[0];
                            } elseif (count($cityVillageCandidates) > 1 && $postcode !== '') {
                                $postalCityCandidates = array_values(array_filter(
                                    $cityVillageCandidates,
                                    static fn(string $code): bool => $catalog['postcodes'][$code] === $postcode
                                ));
                                if (count($postalCityCandidates) === 1) {
                                    $villageCode = $postalCityCandidates[0];
                                }
                            }
                            if ($villageCode === null) {
                                $compactCandidates = $villageNameByCityCompact[$cityCode][$compactVillageName] ?? [];
                                if (count($compactCandidates) === 1) {
                                    $villageCode = $compactCandidates[0];
                                } elseif (count($compactCandidates) > 1 && $postcode !== '') {
                                    $postalCompactCandidates = array_values(array_filter(
                                        $compactCandidates,
                                        static fn(string $code): bool => $catalog['postcodes'][$code] === $postcode
                                    ));
                                    if (count($postalCompactCandidates) === 1) {
                                        $villageCode = $postalCompactCandidates[0];
                                    }
                                }
                            }
                        }
                    }
                }

                if ($villageCode === null) {
                    $globalCandidates = $villageNameGlobal[$villageName] ?? [];
                    if (count($globalCandidates) === 1) {
                        $villageCode = $globalCandidates[0];
                    } else {
                        $compactGlobalCandidates = $villageNameGlobalCompact[$compactVillageName] ?? [];
                        if (count($compactGlobalCandidates) === 1) {
                            $villageCode = $compactGlobalCandidates[0];
                        }
                    }
                }
                if ($villageCode === null) {
                    $mappingIssues[] = '#' . $addressNumber . ': wilayah alamat tidak cocok secara unik';
                    continue;
                }

                // The canonical village code determines the complete hierarchy.
                $provinceCode = substr($villageCode, 0, 2);
                $cityCode = substr($villageCode, 0, 5);
                $districtCode = substr($villageCode, 0, 8);
                if (!isset($catalog['provinceByCode'][$provinceCode], $catalog['cityByCode'][$cityCode], $catalog['districtByCode'][$districtCode])) {
                    $mappingIssues[] = '#' . $addressNumber . ': induk wilayah kanonis tidak lengkap';
                    continue;
                }
                $address['prov_id'] = (string) $catalog['provinceByCode'][$provinceCode]['id'];
                $address['prov'] = $catalog['provinceByCode'][$provinceCode]['label'];
                $address['kab_id'] = (string) $catalog['cityByCode'][$cityCode]['id'];
                $address['kab'] = $catalog['cityByCode'][$cityCode]['label'];
                $address['kec_id'] = (string) $catalog['districtByCode'][$districtCode]['id'];
                $address['kec'] = $catalog['districtByCode'][$districtCode]['label'];
                $address['desa'] = $catalog['levels'][13][$villageCode];
                $address['kodepos'] = $catalog['postcodes'][$villageCode];
                $detail = trim((string) ($address['alamat_tambahan'] ?? ''));
                $address['alamat_lengkap'] = trim($detail . ' ' . $address['desa'] . ', ' . $address['kec'] . ', ' . $address['kab'] . ', ' . $address['prov'] . ' ' . $address['kodepos']);
                $changed = true;
            }
            unset($address);
            if ($changed) {
                $updates[] = ['email' => (string) $buyer['email'], 'alamat' => json_encode($addresses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)];
            }
        }
        if ($mappingIssues !== []) {
            throw new \RuntimeException('Alamat tersimpan belum aman dipetakan (' . implode(', ', $mappingIssues) . '); transaksi tidak dijalankan.');
        }
        return $updates;
    }

    /** Resolve free-shipping ID selections against old labels before replacing region IDs. */
    private function mapFreeShippingConfig($db, array $catalog): ?array
    {
        $this->assertNoUnmappedFreeShippingFile();
        if (!$db->tableExists('app_settings')) {
            return null;
        }
        $row = $db->table('app_settings')->select('setting_value')->where('setting_key', 'free_shipping_regions')->get()->getRowArray();
        if (!$row || !is_string($row['setting_value'] ?? null)) {
            return null;
        }
        $config = json_decode($row['setting_value'], true);
        if (!is_array($config)) {
            throw new \RuntimeException('JSON konfigurasi gratis ongkir tidak valid; pembaruan dihentikan.');
        }

        $provinceRows = $db->table('provinsi')->select('id,label')->get()->getResultArray();
        $cityRows = $db->table('kabupaten')->select('id,provinsi_id,label')->get()->getResultArray();
        $oldProvinceById = [];
        $newProvinceByName = [];
        $newProvinceLabelById = [];
        foreach ($provinceRows as $province) {
            $oldProvinceById[(string) $province['id']] = $this->normaliseName((string) $province['label']);
        }
        foreach ($catalog['provinceRows'] as $province) {
            $newProvinceByName[$this->normaliseName($province['label'])][] = (string) $province['id'];
            $newProvinceLabelById[(string) $province['id']] = (string) $province['label'];
        }
        $provinceMap = [];
        $selectedProvinceIds = array_map('strval', (array) ($config['province_ids'] ?? []));
        foreach ((array) ($config['province_ids'] ?? []) as $oldId) {
            $name = $oldProvinceById[(string) $oldId] ?? null;
            $matches = $name === null ? [] : ($newProvinceByName[$name] ?? []);
            if (count($matches) !== 1) {
                throw new \RuntimeException('Provinsi gratis ongkir tidak dapat dipetakan persis; tidak ada perubahan yang diterapkan.');
            }
            $provinceMap[(string) $oldId] = $matches[0];
        }
        if (array_diff($selectedProvinceIds, array_keys($oldProvinceById)) !== []) {
            throw new \RuntimeException('ID provinsi pada konfigurasi gratis ongkir tidak ditemukan; tidak ada perubahan yang diterapkan.');
        }

        $newProvinceNameById = [];
        foreach ($catalog['provinceRows'] as $province) {
            $newProvinceNameById[(string) $province['id']] = $this->normaliseName($province['label']);
        }
        $newCityByParentAndName = [];
        foreach ($catalog['cityRows'] as $city) {
            $parentName = $newProvinceNameById[(string) $city['provinsi_id']] ?? '';
            $newCityByParentAndName[$parentName][$this->normaliseName($city['label'])][] = (string) $city['id'];
        }
        $newCityIds = [];
        $selectedCityIds = array_map('strval', (array) ($config['kabupaten_ids'] ?? []));
        $seenSelectedCityIds = [];
        foreach ($cityRows as $city) {
            $oldProvinceName = $oldProvinceById[(string) $city['provinsi_id']] ?? null;
            $cityName = $this->normaliseName((string) $city['label']);
            $matches = $oldProvinceName === null ? [] : ($newCityByParentAndName[$oldProvinceName][$cityName] ?? []);
            $isSelected = in_array((string) $city['id'], $selectedCityIds, true);
            if (count($matches) !== 1) {
                if ($isSelected) {
                    throw new \RuntimeException('Kabupaten/kota gratis ongkir tidak dapat dipetakan persis; tidak ada perubahan yang diterapkan.');
                }
                continue;
            }
            if ($isSelected) {
                $seenSelectedCityIds[] = (string) $city['id'];
                $newCityIds[] = $matches[0];
            }
        }
        if (array_diff($selectedCityIds, $seenSelectedCityIds) !== []) {
            throw new \RuntimeException('ID kabupaten/kota pada konfigurasi gratis ongkir tidak ditemukan; tidak ada perubahan yang diterapkan.');
        }

        $config['province_ids'] = array_values(array_unique(array_values($provinceMap)));
        $config['kabupaten_ids'] = array_values(array_unique($newCityIds));
        if (array_key_exists('province_names', $config)) {
            $mappedNames = [];
            foreach ((array) $config['province_names'] as $oldName) {
                $matches = $newProvinceByName[$this->normaliseName((string) $oldName)] ?? [];
                if (count($matches) !== 1) {
                    throw new \RuntimeException('Nama provinsi pada konfigurasi gratis ongkir tidak dapat dipetakan persis; refresh dihentikan.');
                }
                $mappedNames[] = $newProvinceLabelById[$matches[0]];
            }
            $config['province_names'] = array_values(array_unique($mappedNames));
        }
        return ['setting_value' => json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)];
    }

    private function assertNoUnmappedFreeShippingFile(): void
    {
        $path = WRITEPATH . 'free_shipping_regions.json';
        if (!is_file($path)) {
            return;
        }
        $config = json_decode((string) file_get_contents($path), true);
        if (!is_array($config)) {
            throw new \RuntimeException('File cadangan gratis ongkir tidak valid; refresh dihentikan.');
        }
        if ((array) ($config['province_ids'] ?? []) !== [] || (array) ($config['kabupaten_ids'] ?? []) !== []) {
            throw new \RuntimeException('Konfigurasi gratis ongkir masih memakai file cadangan dan belum dipetakan; refresh dihentikan.');
        }
    }

    private function normaliseName(string $value): string
    {
        $value = preg_replace('/\s*\([^)]*\)/u', '', trim($value)) ?? trim($value);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = strtoupper($ascii === false ? $value : $ascii);
        $value = preg_replace('/^(KABUPATEN|KAB\.?|KOTA ADMINISTRASI|KOTA|KOTAMADYA|PROVINSI|PROPINSI|DESA|KELURAHAN|KEL\.?|KAMPUNG|GAMPONG)\s+/u', '', $value) ?? $value;
        return trim(preg_replace('/[^A-Z0-9]+/', ' ', $value) ?? $value);
    }

    private function canonicalSourceHash(string $contents): string
    {
        return hash('sha256', str_replace(["\r\n", "\r"], "\n", $contents));
    }

    private function assertProductionApplyConfirmation(string $database, string $regionsSql, string $postalSql, string $backupPath): void
    {
        if ($database !== 'ilena' || CLI::getOption('confirm-production') !== 'ilena') {
            throw new \RuntimeException('Apply production ditolak: database dan konfirmasi harus bernilai ilena.');
        }

        if (!hash_equals($this->canonicalSourceHash($regionsSql), (string) (CLI::getOption('confirm-regions-hash') ?? ''))
            || !hash_equals($this->canonicalSourceHash($postalSql), (string) (CLI::getOption('confirm-postal-hash') ?? ''))
        ) {
            throw new \RuntimeException('Apply production ditolak: hash sumber tidak sama dengan hash yang ditinjau saat dry-run.');
        }

        if ($backupPath === '' || !is_file($backupPath) || !is_readable($backupPath) || filesize($backupPath) < 1) {
            throw new \RuntimeException('Apply production ditolak: file backup gzip tidak ditemukan, kosong, atau tidak terbaca.');
        }

        $handle = @gzopen($backupPath, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Apply production ditolak: backup tidak dapat dibaca sebagai gzip.');
        }

        $required = array_fill_keys(['provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'pembeli', 'app_settings'], false);
        try {
            while (!gzeof($handle)) {
                $line = gzgets($handle);
                if ($line === false) {
                    break;
                }
                if (preg_match('/^CREATE TABLE `?(provinsi|kabupaten|kecamatan|kelurahan|pembeli|app_settings)`?/i', $line, $matches) === 1) {
                    $required[strtolower($matches[1])] = true;
                }
            }
        } finally {
            gzclose($handle);
        }

        $missing = array_keys($required, false, true);
        if ($missing !== []) {
            throw new \RuntimeException('Apply production ditolak: backup tidak memuat tabel ' . implode(', ', $missing) . '.');
        }
    }

    private function applyCatalog($db, array $catalog, array $addressUpdates, ?array $freeShippingUpdate): void
    {
        ini_set('memory_limit', '512M');
        $db->transBegin();
        try {
            foreach (['kelurahan', 'kecamatan', 'kabupaten', 'provinsi'] as $table) {
                $db->query('DELETE FROM `' . $table . '`');
            }
            foreach ([
                'provinsi' => $catalog['provinceRows'],
                'kabupaten' => $catalog['cityRows'],
                'kecamatan' => $catalog['districtRows'],
                'kelurahan' => $catalog['villageRows'],
            ] as $table => $rows) {
                foreach (array_chunk($rows, 500) as $batch) {
                    if (!$db->table($table)->insertBatch($batch, 500)) {
                        throw new \RuntimeException('Batch insert gagal pada tabel ' . $table . '.');
                    }
                }
            }
            foreach ($addressUpdates as $update) {
                $ok = $db->table('pembeli')->where('email', $update['email'])->update(['alamat' => $update['alamat']]);
                if (!$ok) {
                    throw new \RuntimeException('Gagal memetakan ulang alamat lokal tersimpan.');
                }
            }
            if ($freeShippingUpdate !== null) {
                $db->table('app_settings')->where('setting_key', 'free_shipping_regions')->update([
                    'setting_value' => $freeShippingUpdate['setting_value'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $this->verifyPersistedCatalog($db, $catalog);
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database melaporkan kegagalan.');
            }
            $db->transCommit();
        } catch (\Throwable $error) {
            $db->transRollback();
            throw $error;
        }
    }

    private function verifyPersistedCatalog($db, array $catalog): void
    {
        $expected = [
            'provinsi' => count($catalog['provinceRows']),
            'kabupaten' => count($catalog['cityRows']),
            'kecamatan' => count($catalog['districtRows']),
            'kelurahan' => count($catalog['villageRows']),
        ];
        foreach ($expected as $table => $count) {
            if ((int) $db->table($table)->countAllResults() !== $count) {
                throw new \RuntimeException('Pemeriksaan jumlah gagal pada tabel ' . $table . '; transaksi dibatalkan.');
            }
        }
        $checks = [
            'kabupaten tanpa provinsi' => 'SELECT COUNT(*) AS n FROM kabupaten c LEFT JOIN provinsi p ON p.id=c.provinsi_id WHERE p.id IS NULL',
            'kecamatan tanpa induk konsisten' => 'SELECT COUNT(*) AS n FROM kecamatan d LEFT JOIN kabupaten c ON c.id=d.kabupaten_id AND c.provinsi_id=d.provinsi_id LEFT JOIN provinsi p ON p.id=d.provinsi_id WHERE c.id IS NULL OR p.id IS NULL',
            'kelurahan tanpa induk konsisten' => 'SELECT COUNT(*) AS n FROM kelurahan v LEFT JOIN kecamatan d ON d.id=v.kecamatan_id AND d.kabupaten_id=v.kabupaten_id AND d.provinsi_id=v.provinsi_id LEFT JOIN kabupaten c ON c.id=v.kabupaten_id AND c.provinsi_id=v.provinsi_id LEFT JOIN provinsi p ON p.id=v.provinsi_id WHERE d.id IS NULL OR c.id IS NULL OR p.id IS NULL',
        ];
        foreach ($checks as $label => $sql) {
            if ((int) $db->query($sql)->getRow()->n !== 0) {
                throw new \RuntimeException('Pemeriksaan relasi gagal (' . $label . '); transaksi dibatalkan.');
            }
        }
        $this->assertLegacySchema($db);
    }
}
