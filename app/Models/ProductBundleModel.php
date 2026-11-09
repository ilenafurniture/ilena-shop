<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductBundleModel extends Model
{
    protected $table = 'product_bundles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'slug', 'name', 'name_en', 'description', 'description_en',
        'discount_percent', 'active', 'starts_at', 'ends_at', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = false;
}
