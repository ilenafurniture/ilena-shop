<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_Tracking;

class Track extends BaseController
{
    public function hit()
    {
        // terima JSON
        $raw = $this->request->getBody();
        $json = json_decode($raw, true) ?: [];

        // Basic bot/admin guard (tambahkan sesuai kebutuhan)
        $ua = (string)($json['ua'] ?? $this->request->getUserAgent());
        if (stripos($ua, 'bot') !== false || stripos($ua, 'crawl') !== false) {
            return $this->response->setStatusCode(204); // abaikan bot
        }

        $path   = trim((string)($json['path'] ?? '/')) ?: '/';
        $durasi = max(0, (int)($json['durasi'] ?? 0));
        $ip     = $this->getClientIP();
        $now    = date('Y-m-d H:i:s');

        // Simpan (kamu sudah punya M_Tracking)
        $m = new M_Tracking();
        try {
            $m->insert([
                'waktu'  => $now,
                'ip'     => $ip,
                'path'   => $path,
                'durasi' => $durasi,
            ]);
        } catch (\Throwable $e) {
            // diamkan saja agar endpoint selalu cepat
        }

        return $this->response->setStatusCode(204);
    }

    /** Ambil IP dari proxy/CF dengan aman */
    private function getClientIP(): string
    {
        $req = $this->request;
        $ip = $req->getHeaderLine('CF-Connecting-IP')
           ?: $req->getHeaderLine('X-Forwarded-For')
           ?: $req->getIPAddress();

        // jika X-Forwarded-For berisi list
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        return $ip ?: '0.0.0.0';
    }
}