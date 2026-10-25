<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table      = 'barang';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id',
        'nama',
        'pencarian',
        'harga',
        'deskripsi',
        'kategori',
        'subkategori',
        'varian',
        'diskon',
        'shopee',
        'tokped',
        'tiktok',
        'active',
        'pengunjung',
        'ruang_tamu',
        'ruang_tidur',
        'ruang_keluarga',
        'tgl_update',

        // kolom jadwal diskon
        'pakai_jadwal_diskon',
        'diskon_mulai',
        'diskon_selesai',
    ];

    public function getBarang($id = false)
    {
        if ($id == false) {
            return array_map([\App\Services\CatalogPricing::class, 'forSale'], $this->orderBy('nama', 'asc')
                        ->where(['active' => '1'])
                        ->findAll());
        }
        $row = $this->where(['id' => $id, 'active' => '1'])->first();
        return $row ? \App\Services\CatalogPricing::forSale($row) : null;
    }

    public function getBarangNama($nama = false)
    {
        if ($nama == false) {
            $seluruhBrang = $this->orderBy('nama', 'asc')
                                 ->where(['active' => '1'])
                                 ->findAll();
            $seluruhBarangFilter = [];
            $namaTerpilih = [];
            foreach ($seluruhBrang as $s) {
                $namaKey = 'nama:' . (string) $s['nama'];
                if (!isset($namaTerpilih[$namaKey])) {
                    $seluruhBarangFilter[] = $s;
                    $namaTerpilih[$namaKey] = true;
                }
            }
            return array_map([\App\Services\CatalogPricing::class, 'forSale'], $seluruhBarangFilter);
        }
        $row = $this->where(['id' => $nama, 'active' => '1'])->first();
        return $row ? \App\Services\CatalogPricing::forSale($row) : null;
    }

    /** Caller must hold a transaction; used by stock-changing operations. */
    public function getBarangForUpdate($id): ?array
    {
        $sql=$this->db->table($this->table)->where('id',$id)->where('active','1')->getCompiledSelect();
        $row=$this->db->query($sql.($this->db->DBDriver==='MySQLi'?' FOR UPDATE':''))->getRowArray();
        return $row ? \App\Services\CatalogPricing::forSale($row) : null;
    }

    public function getBarangAdmin($id = false)
    {
        if ($id == false) {
            return $this->orderBy('nama', 'asc')->findAll();
        }
        return $this->where(['id' => $id])->first();
    }

    public function getBarangLimit()
    {
        return $this->orderBy('nama', 'asc')->findAll(10, 0);
    }

    public function getBarangBaru()
    {
        return $this->orderBy('id', 'desc')->findAll(10, 0);
    }

    public function getBarangPage($page)
    {
        $offset = 20 * max(0, ($page - 1));
        return $this->orderBy('nama', 'asc')->findAll(20, $offset);
    }

    // Opsional helper
    public function isDiskonAktif(array $row): bool
    {
        $diskon = (float)($row['diskon'] ?? 0);
        if ($diskon <= 0) return false;

        $pakaiJadwal = !empty($row['pakai_jadwal_diskon']);
        if (!$pakaiJadwal) return true;

        if (empty($row['diskon_mulai']) || empty($row['diskon_selesai'])) return false;

        try {
            $now     = new \DateTime('now');
            $mulai   = new \DateTime($row['diskon_mulai']);
            $selesai = new \DateTime($row['diskon_selesai']);
            return $now >= $mulai && $now <= $selesai;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function applyScheduleToPrice(array $row): array
    {
        $harga = (float)($row['harga'] ?? 0);
        $row['harga_akhir'] = $harga;

        if ($this->isDiskonAktif($row)) {
            $diskon = (float)$row['diskon'];
            $row['harga_akhir'] = round($harga * (100 - $diskon) / 100);
        }
        return $row;
    }
}
