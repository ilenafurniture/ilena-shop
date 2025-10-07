<?php
namespace App\Models;

use CodeIgniter\Model;

class VoucherModel extends Model
{
    protected $table      = 'voucher';
    protected $primaryKey = 'id';

    // aktifkan kalau tabelmu punya timestamp
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';

    // PENTING: sertakan list_email agar update tidak dibuang
    protected $allowedFields = [
        'kode','nama','deskripsi',
        'tipe','nilai',                 // skema baru
        'satuan','nominal',             // fallback skema lama (optional, kalau masih ada di DB)
        'minimal_belanja',
        'mulai','berakhir',
        'auto_apply','aktif','target',
        'max_pakai','sekali_pakai_per_user',
        'list_email',                   // untuk limit 1x per user
        // 'created_at','updated_at'
    ];
}