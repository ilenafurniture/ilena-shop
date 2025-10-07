<?php
namespace App\Models;
use CodeIgniter\Model;

class VoucherUsageModel extends Model {
  protected $table = 'voucher_usage';
  protected $primaryKey = 'id';
  protected $allowedFields = ['kode_voucher','email','used_at'];
}