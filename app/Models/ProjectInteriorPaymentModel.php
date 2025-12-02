<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectInteriorPaymentModel extends Model
{
    protected $table            = 'project_interior_payment'; // <== sesuai DB
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'project_id',   // relasi ke project_interior.id
        'tanggal',
        'jenis',        // enum: dp, termin, pelunasan
        'nominal',
        'catatan',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil semua pembayaran berdasarkan ID project (project_interior.id)
     */
    public function getByProjectId(int $projectId): array
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('tanggal', 'ASC')
                    ->findAll();
    }

    /**
     * Hitung total nominal pembayaran untuk 1 project.
     */
    public function getTotalPaidForProject(int $projectId): int
    {
        $row = $this->selectSum('nominal', 'total')
                    ->where('project_id', $projectId)
                    ->first();

        return (int) ($row['total'] ?? 0);
    }
}