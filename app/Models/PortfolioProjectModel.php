<?php

namespace App\Models;

use CodeIgniter\Model;

class PortfolioProjectModel extends Model
{
    protected $table = 'portfolio_projects';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'slug', 'title_id', 'title_en', 'category_id', 'category_en',
        'summary_id', 'summary_en', 'description_id', 'description_en',
        'location', 'completed_year', 'cover_image', 'gallery_json', 'video_url',
        'featured', 'active', 'sort_order', 'created_at', 'updated_at',
    ];
}
