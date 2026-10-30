<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_Tracking;

class Track extends BaseController
{
    public function hit()
    {
        // Bound this public write endpoint by the visitor IP resolved through
        // App::$proxyIPs; untrusted forwarding headers are never read directly.
        $ip = $this->request->getIPAddress();
        $key = 'ilena-track-' . hash('sha256', $ip);
        if (!service('throttler')->check($key, 120, 60)) {
            return $this->response->setStatusCode(204);
        }

        $raw = $this->request->getBody();
        $decoded = json_decode($raw, true);
        $json = is_array($decoded) ? $decoded : [];

        $ua = (string) $this->request->getUserAgent();
        if (stripos($ua, 'bot') !== false || stripos($ua, 'crawl') !== false) {
            return $this->response->setStatusCode(204); // abaikan bot
        }

        $pathInput = $json['path'] ?? '/';
        $path = is_string($pathInput) ? (trim($pathInput) ?: '/') : '/';
        $path = mb_substr((string) preg_replace('/[\x00-\x1F\x7F]/u', '', $path), 0, 300);
        $durationInput = $json['durasi'] ?? 0;
        $durasi = is_scalar($durationInput) && is_numeric($durationInput)
            ? max(0, min(86400, (int) $durationInput))
            : 0;
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

}
