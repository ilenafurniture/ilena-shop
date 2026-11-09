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
    public function getPublicHomeSlides(bool $forceRefresh = false): array
    {
        $cacheKey = 'ilena_home_slider_metadata_v1';
        return (new \App\Services\CoordinatedPublicCache())->remember(
            $cacheKey,
            'home_slider',
            60,
            900,
            fn (): array => $this->loadPublicHomeSlides(),
            $forceRefresh
        );
    }

    private function loadPublicHomeSlides(): array
    {
        return $this->db->table($this->table)
            ->select('id, url, MD5(foto) AS foto_version, MD5(foto_hp) AS foto_hp_version', false)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }
}
