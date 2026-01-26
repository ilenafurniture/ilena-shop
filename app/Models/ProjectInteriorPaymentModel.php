<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectInteriorPaymentModel extends Model
{
    protected $table            = 'project_interior_payment';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'project_id',
        'tanggal',
        'jenis',        // dp|termin|pelunasan
        'nominal',
        'catatan',
    ];

    protected $useTimestamps = false;

    public function getByProjectId(int $projectId): array
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('tanggal', 'ASC')
                    ->findAll();
    }

    public function getTotalPaidForProject(int $projectId): int
    {
        $row = $this->selectSum('nominal', 'total')
                    ->where('project_id', $projectId)
                    ->first();

        return (int) ($row['total'] ?? 0);
    }
}