<?php

namespace App\Models;

use CodeIgniter\Model;

class GambarHeaderModel extends Model
{
    protected $table = 'gambar_header';
    protected $allowedFields = [
        'id',
        'foto',
        'foto_hp',
        'url',
    ];

    public function getGambar($id)
    {
        return $this->where(['id' => $id])->first();
    }

    /** Return only slider metadata and content fingerprints; never move image BLOBs into page HTML. */
    public function getPublicHomeSlides(): array
    {
        $cacheKey = 'ilena_home_slider_metadata_v1';
        $cached = cache()->get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $slides = $this->db->table($this->table)
            ->select('id, url, MD5(foto) AS foto_version, MD5(foto_hp) AS foto_hp_version', false)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        cache()->save($cacheKey, $slides, 60);
        return $slides;
    }
}
