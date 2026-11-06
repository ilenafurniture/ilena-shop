<?php

namespace App\Services;

/** Local APM tariff lookup with a writable admin-import override. */
final class ApmShippingRateService
{
    private string $defaultPath;
    private string $overridePath;
    private string $lastError = '';

    public function __construct(?string $defaultPath = null, ?string $overridePath = null)
    {
        $this->defaultPath = $defaultPath ?? APPPATH . 'Data/apm_shipping_rates.json';
        $this->overridePath = $overridePath ?? WRITEPATH . 'apm_shipping_rates.json';
    }

    public function lastError(): string
    {
        return $this->lastError;
    }

    public function rates(array $address, array $cart): array
    {
        $this->lastError = '';
        $all = $this->loadRates();
        if ($all === []) {
            $this->lastError = 'dataset_unavailable';
            return [];
        }

        $destination = self::normalizePlace((string) ($address['kab'] ?? ''));
        $province = self::normalizePlace((string) ($address['prov'] ?? ''));
        if ($destination === '' || $province === '') {
            $this->lastError = 'address_incomplete';
            return [];
        }

        $candidates = array_values(array_filter($all, static fn (array $row): bool => self::normalizePlace((string) $row['destination']) === $destination));
        if ($candidates === []) {
            $this->lastError = 'not_covered';
            return [];
        }

        if (count($candidates) > 1) {
            $provinceMatches = array_values(array_filter($candidates, static fn (array $row): bool => self::rowMatchesProvince($row, $province)));
            if (count($provinceMatches) === 1) {
                $candidates = $provinceMatches;
            } else {
                $this->lastError = 'ambiguous_destination';
                return [];
            }
        }

        $rate = $candidates[0];
        if (preg_match('/anomali|tidak lengkap|perlu konfirmasi/i', (string) ($rate['source_status'] ?? '')) === 1) {
            $this->lastError = 'tariff_needs_confirmation';
            return [];
        }

        $actualWeight = $this->cartActualWeight($cart);
        if ($actualWeight <= 0) {
            $this->lastError = 'product_weight_missing';
            return [];
        }

        // The APM workbook defines a minimum charge, but no automatic volumetric
        // divisor. Do not invent one: bill on actual product weight and the listed minimum.
        $billableKg = max((float) $rate['minimum_kg'], $actualWeight);
        $price = (int) ceil($billableKg * (int) $rate['rate_per_kg']);
        if ($price <= 0) {
            $this->lastError = 'tariff_invalid';
            return [];
        }

        $leadMin = (int) $rate['lead_min_days'];
        $leadMax = (int) $rate['lead_max_days'];

        return [[
            'nama' => 'APM',
            'deskripsi' => 'Pengiriman ke ' . (string) $rate['destination'],
            'harga' => $price,
            'estimasi' => $leadMin > 0 && $leadMax > 0 ? $leadMin . ' - ' . $leadMax : '',
            'provider' => 'apm',
            'service_code' => (string) $rate['id'],
            'billable_weight_kg' => $billableKg,
            'rate_per_kg' => (int) $rate['rate_per_kg'],
            'minimum_weight_kg' => (int) $rate['minimum_kg'],
            'schedule' => (string) ($rate['schedule'] ?? ''),
            'area' => (string) ($rate['area'] ?? ''),
            'regional' => (string) ($rate['regional'] ?? ''),
            'destination' => (string) $rate['destination'],
            'tariff_id' => (string) $rate['id'],
        ]];
    }

    /** @return list<array{destination:string,province:string,label:string}> */
    public function destinationSuggestions(string $query, int $limit = 8): array
    {
        $query = self::normalizePlace($query);
        if (mb_strlen($query, 'UTF-8') < 2) {
            return [];
        }

        $limit = max(1, min(12, $limit));
        $matches = [];
        $destinationCounts = [];
        foreach ($this->loadRates() as $row) {
            $destination = trim((string) ($row['destination'] ?? ''));
            if ($destination === '' || !str_contains(self::normalizePlace($destination), $query)) {
                continue;
            }
            $matches[] = $row;
            $destinationKey = self::normalizePlace($destination);
            $destinationCounts[$destinationKey] = ($destinationCounts[$destinationKey] ?? 0) + 1;
        }

        $suggestions = [];
        foreach ($matches as $row) {
            $destination = trim((string) $row['destination']);
            $area = trim((string) ($row['area'] ?? ''));
            $regional = trim((string) ($row['regional'] ?? ''));
            $destinationKey = self::normalizePlace($destination);
            $province = $area !== '' ? $area : $regional;
            $key = self::normalizePlace($destination) . '|' . self::normalizePlace($province);
            if (isset($suggestions[$key])) {
                continue;
            }
            $label = $destination;
            if (($destinationCounts[$destinationKey] ?? 0) > 1) {
                $disambiguator = $area !== '' && self::normalizePlace($area) !== $destinationKey ? $area : $regional;
                if ($disambiguator !== '') {
                    $label .= ' — ' . $disambiguator;
                }
            }
            $suggestions[$key] = [
                'destination' => $destination,
                'province' => $province,
                'label' => $label,
            ];
            if (count($suggestions) >= $limit) {
                break;
            }
        }

        return array_values($suggestions);
    }

    /** @return array{rows:int,updated_at:?string,source:string} */
    public function summary(): array
    {
        $data = $this->loadData();
        return [
            'rows' => count($data['rates'] ?? []),
            'updated_at' => $data['updated_at'] ?? null,
            'source' => (string) ($data['source'] ?? 'APM'),
            'customized' => is_file($this->overridePath),
            'has_previous' => is_file($this->overridePath . '.previous'),
        ];
    }

    public function restorePrevious(): bool
    {
        $previousPath = $this->overridePath . '.previous';
        $previous = json_decode((string) @file_get_contents($previousPath), true);
        if (!is_array($previous) || !is_array($previous['rates'] ?? null) || $previous['rates'] === []) {
            return false;
        }
        $ids = [];
        foreach ($previous['rates'] as $row) {
            if (!is_array($row) || !preg_match('/^APM-[A-Z0-9-]+$/', (string) ($row['id'] ?? ''))
                || isset($ids[(string) $row['id']]) || (int) ($row['rate_per_kg'] ?? 0) < 1
                || (int) ($row['minimum_kg'] ?? 0) < 1 || trim((string) ($row['destination'] ?? '')) === ''
            ) {
                return false;
            }
            $ids[(string) $row['id']] = true;
        }
        $previous['source'] = 'APM / versi sebelumnya dipulihkan';
        $previous['updated_at'] = date('Y-m-d H:i:s');
        return $this->writeOverride($previous);
    }

    /** Import one complete CSV snapshot; duplicate IDs reject the entire upload. */
    public function importCsv(string $path): array
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            return ['ok' => false, 'message' => 'File tarif tidak dapat dibaca.'];
        }

        try {
            $headers = fgetcsv($handle);
            if (!is_array($headers)) {
                return ['ok' => false, 'message' => 'CSV kosong atau formatnya tidak valid.'];
            }
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
            $headers = array_map(static fn ($v): string => strtolower(trim((string) $v)), $headers);
            $required = ['id', 'regional', 'area', 'destination', 'rate_per_kg', 'minimum_kg', 'lead_min_days', 'lead_max_days', 'schedule', 'source_status'];
            if ($headers !== $required) {
                return ['ok' => false, 'message' => 'Header CSV tidak cocok. Unduh template dari halaman Tarif Pengiriman APM.'];
            }

            $rows = [];
            $seenIds = [];
            while (($values = fgetcsv($handle)) !== false) {
                if ($values === [null] || (count($values) === 1 && trim((string) $values[0]) === '')) {
                    continue;
                }
                if (count($values) !== count($required)) {
                    return ['ok' => false, 'message' => 'Ada baris CSV dengan jumlah kolom yang tidak sesuai. Tidak ada perubahan yang disimpan.'];
                }
                $row = array_combine($required, array_map(static fn ($v): string => trim((string) $v), $values));
                if ($row === false || $row['id'] === '' || $row['destination'] === '' || $row['regional'] === '' || $row['area'] === '') {
                    return ['ok' => false, 'message' => 'ID, regional, area, dan tujuan wajib diisi pada setiap baris.'];
                }
                $row['id'] = strtoupper($row['id']);
                if (isset($seenIds[$row['id']])) {
                    return ['ok' => false, 'message' => 'ID tarif ganda ditemukan. Impor dibatalkan agar tarif tidak terduplikasi.'];
                }
                $seenIds[$row['id']] = true;
                foreach (['rate_per_kg', 'minimum_kg', 'lead_min_days', 'lead_max_days'] as $field) {
                    if ($row[$field] === '' || !ctype_digit($row[$field]) || strlen($row[$field]) > 9) {
                        return ['ok' => false, 'message' => 'Kolom ' . $field . ' harus berupa bilangan bulat positif atau nol.'];
                    }
                    $row[$field] = (int) $row[$field];
                }
                if ($row['rate_per_kg'] < 1 || $row['rate_per_kg'] > 100_000_000 || $row['minimum_kg'] < 1 || $row['minimum_kg'] > 100_000
                    || $row['lead_min_days'] > 365 || $row['lead_max_days'] > 365 || $row['lead_max_days'] < $row['lead_min_days']) {
                    return ['ok' => false, 'message' => 'Tarif, minimum berat, atau estimasi hari di luar rentang yang valid.'];
                }
                $maxLengths = ['id' => 40, 'regional' => 160, 'area' => 160, 'destination' => 120, 'schedule' => 120, 'source_status' => 80];
                foreach ($maxLengths as $field => $limit) {
                    if (mb_strlen($row[$field], 'UTF-8') > $limit || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $row[$field]) === 1) {
                        return ['ok' => false, 'message' => 'Ada teks yang tidak valid atau melebihi batas panjang kolom.'];
                    }
                }
                foreach (['regional', 'area', 'destination', 'schedule', 'source_status'] as $field) {
                    if (preg_match('/^[=+@-]/u', $row[$field]) === 1) {
                        return ['ok' => false, 'message' => 'Teks tarif tidak boleh diawali karakter formula spreadsheet.'];
                    }
                }
                if (preg_match('/^APM-[A-Z0-9-]+$/', $row['id']) !== 1) {
                    return ['ok' => false, 'message' => 'Ada teks yang melebihi batas panjang kolom.'];
                }
                $rows[] = $row;
                if (count($rows) > 2000) {
                    return ['ok' => false, 'message' => 'CSV melebihi batas 2.000 baris.'];
                }
            }

            if ($rows === []) {
                return ['ok' => false, 'message' => 'CSV tidak berisi tarif. Tarif aktif tetap dipertahankan.'];
            }

            $payload = ['version' => 1, 'source' => 'APM / impor admin', 'updated_at' => date('Y-m-d H:i:s'), 'rates' => $rows];
            return $this->writeOverride($payload)
                ? ['ok' => true, 'rows' => count($rows)]
                : ['ok' => false, 'message' => 'Tarif baru gagal disimpan; data tarif sebelumnya tetap dipakai.'];
        } finally {
            fclose($handle);
        }
    }

    public function csvTemplate(): string
    {
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['id', 'regional', 'area', 'destination', 'rate_per_kg', 'minimum_kg', 'lead_min_days', 'lead_max_days', 'schedule', 'source_status']);
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return (string) $csv;
    }

    public function csvExport(): string
    {
        $stream = fopen('php://temp', 'r+');
        $headers = ['id', 'regional', 'area', 'destination', 'rate_per_kg', 'minimum_kg', 'lead_min_days', 'lead_max_days', 'schedule', 'source_status'];
        fputcsv($stream, $headers);
        foreach ($this->loadRates() as $row) {
            fputcsv($stream, array_map(static fn (string $key) => (string) ($row[$key] ?? ''), $headers));
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return (string) $csv;
    }

    private function loadRates(): array
    {
        $data = $this->loadData();
        return is_array($data['rates'] ?? null) ? $data['rates'] : [];
    }

    private function loadData(): array
    {
        $path = is_file($this->overridePath) ? $this->overridePath : $this->defaultPath;
        $data = json_decode((string) @file_get_contents($path), true);
        return is_array($data) && is_array($data['rates'] ?? null) ? $data : [];
    }

    private function writeOverride(array $payload): bool
    {
        $dir = dirname($this->overridePath);
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return false;
        }
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $lock = @fopen($this->overridePath . '.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) {
            if (is_resource($lock)) {
                fclose($lock);
            }
            return false;
        }
        $tmp = @tempnam($dir, '.apm-rates-');
        if ($tmp === false || file_put_contents($tmp, $json, LOCK_EX) === false) {
            flock($lock, LOCK_UN);
            fclose($lock);
            if (is_string($tmp) && is_file($tmp)) {
                @unlink($tmp);
            }
            return false;
        }
        try {
            $previous = $this->overridePath . '.previous';
            $activePath = is_file($this->overridePath) ? $this->overridePath : $this->defaultPath;
            if (is_file($activePath) && !@copy($activePath, $previous)) {
                return false;
            }
            if (is_file($this->overridePath) && DIRECTORY_SEPARATOR === '\\' && !@unlink($this->overridePath)) {
                return false;
            }
            if (!@rename($tmp, $this->overridePath)) {
                if (is_file($previous) && !is_file($this->overridePath)) {
                    @copy($previous, $this->overridePath);
                }
                return false;
            }
            return true;
        } finally {
            if (is_file($tmp)) {
                @unlink($tmp);
            }
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function cartActualWeight(array $cart): float
    {
        $total = 0.0;
        foreach ($cart as $item) {
            $product = $item['detail'] ?? [];
            if (!is_array($product) || $product === []) {
                return 0;
            }
            $description = json_decode((string) ($product['deskripsi'] ?? '{}'), true);
            $package = is_array($description) ? ($description['dimensi']['paket'] ?? []) : [];
            $actual = is_array($description) ? ($description['dimensi']['asli'] ?? []) : [];
            $dimension = is_array($package) && $package !== [] ? $package : (is_array($actual) ? $actual : []);
            $weight = (float) ($dimension['berat'] ?? $actual['berat'] ?? 0);
            if ($weight <= 0) {
                return 0;
            }
            $quantity = (int) ($item['jumlah'] ?? 0);
            if ($quantity <= 0 || $quantity > 100) {
                return 0;
            }
            $total += $weight * $quantity;
        }
        return $total;
    }

    private static function rowMatchesProvince(array $row, string $province): bool
    {
        $province = self::normalizePlace($province);
        $aliases = [
            'diyogyakarta' => ['yogyakarta', 'diyogyakarta'],
            'daerahistimewayogyakarta' => ['yogyakarta', 'diyogyakarta'],
            'daerahkhususibukotajakarta' => ['jakarta', 'dki'],
            'dkijakarta' => ['jakarta', 'dki'],
        ][$province] ?? [$province];
        foreach (['regional', 'area'] as $field) {
            $segments = preg_split('~[/,;|]+~u', (string) ($row[$field] ?? '')) ?: [];
            foreach ($segments as $segment) {
                $normalized = self::normalizePlace($segment);
                foreach ($aliases as $alias) {
                    if ($normalized !== '' && ($normalized === $alias || str_contains($normalized, $alias) || str_contains($alias, $normalized))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function normalizePlace(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = preg_replace('/^(?:kabupaten|kab\.?|kota administrasi|kota)\s+/u', '', $value) ?? $value;
        $value = str_replace(['daerah istimewa ', 'provinsi ', 'propinsi '], '', $value);
        $normalized = preg_replace('/[^\pL\pN]+/u', '', $value) ?? '';
        if (in_array($normalized, ['jakartapusat', 'jakartautara', 'jakartabarat', 'jakartatimur', 'jakartaselatan'], true)) {
            return 'jakarta';
        }
        return $normalized;
    }
}
