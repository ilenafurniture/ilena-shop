<?php

namespace App\Models;

use CodeIgniter\Model;

class PartnerModel extends Model
{
    protected $table = 'partners';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'name',
        'city',
        'address',
        'maps_url',
        'image_url',
        'lat',
        'lng',
        'active',
        'sort_order',
        'created_at',
        'updated_at',
    ];
}
