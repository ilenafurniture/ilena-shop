<?php
namespace App\Models;

use CodeIgniter\Model;

class VoucherUsageModel extends Model
{
    protected $table         = 'voucher_usage';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'kode_voucher',   // string KODE voucher
        'email',          // email pemakai
        'used_at',        // datetime pemakaian
        // 'order_id',     // optional: kalau mau catat order_id
    ];
}