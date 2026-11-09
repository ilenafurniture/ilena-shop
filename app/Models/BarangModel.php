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

    /** Fetch active catalog products in one query and index them by product ID. */
    public function getActiveProductsByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map(
            static fn ($id): string => is_scalar($id) ? trim((string) $id) : '',
            $ids
        ), static fn (string $id): bool => $id !== '')));

        if ($ids === []) {
            return [];
        }

        $products = [];
        foreach (array_chunk($ids, 500) as $idBatch) {
            $rows = $this->whereIn('id', $idBatch)
                ->where('active', '1')
                ->findAll();

            foreach ($rows as $row) {
                $product = \App\Services\CatalogPricing::forSale($row);
                $products[(string) $product['id']] = $product;
            }
        }

        return $products;
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
        return \App\Services\CatalogPricing::discount($row) > 0;
    }

    public function applyScheduleToPrice(array $row): array
    {
        return \App\Services\CatalogPricing::forSale($row);
    }
}
