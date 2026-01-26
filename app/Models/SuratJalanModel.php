<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $table            = 'surat_jalan';
    protected $primaryKey       = 'id';
    protected $allowedFields = [
        'id_pesanan',
        'no_sj',
        'sj_ke',
        'tanggal',
        'status',
        'finalized_at',
    ];

    protected $useTimestamps    = false;

    public function getLastSjKe(string $idPesanan): int
    {
        $row = $this->selectMax('sj_ke', 'max_ke')
            ->where('id_pesanan', $idPesanan)
            ->first();

        return (int)($row['max_ke'] ?? 0);
    }
}