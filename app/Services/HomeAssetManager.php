<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

/** Fixed, admin-managed image slots used by the public home page. */
class HomeAssetManager
{
    private const MAX_UPLOAD_BYTES = 8_388_608;
    private const MAX_DIMENSION = 6000;
    private const MAX_PIXELS = 25_000_000;
    private const MAX_OUTPUT_BYTES = 900_000;

    private const ASSETS = [
        '1742444827619.webp' => 'Inspirasi interior utama',
        '1742445131798.webp' => 'Inspirasi interior kedua',
        '1742445399641.webp' => 'Cerita ruang 1',
        '1742445431983.webp' => 'Cerita ruang 2',
        '1742445475511.webp' => 'Cerita ruang 3',
        '1742445542692.webp' => 'Cerita ruang 4',
        '1742445603554.webp' => 'Cerita ruang 5',
        '1742445633826.webp' => 'Cerita ruang 6',
        '1742445684882.webp' => 'Slide inspirasi 1',
        '1742445731467.png' => 'Slide inspirasi 2',
        '1742445771003.png' => 'Slide inspirasi 3',
        '1742446101353.webp' => 'Latar koleksi',
        '1742448909605.png' => 'Galeri ruang 1',
        '1742448940409.png' => 'Galeri ruang 2',
        '1742449015853.png' => 'Galeri ruang 3',
        '1742449044439.png' => 'Galeri ruang 4',
        '1742449070724.png' => 'Galeri ruang 5',
        '1742449142002.png' => 'Galeri ruang 6',
        '1742449164348.png' => 'Galeri ruang 7',
        '1742449189905.png' => 'Galeri ruang 8',
        '1742449272365.png' => 'Galeri ruang 9',
        '1742449295384.png' => 'Galeri ruang 10',
        '1742449317998.png' => 'Galeri ruang 11',
        '1742449351540.png' => 'Galeri ruang 12',
        '1742449375221.png' => 'Galeri ruang 13',
        '1742449396827.png' => 'Galeri ruang 14',
        '1742449442663.png' => 'Galeri ruang 15',
        '1742449462506.png' => 'Galeri ruang 16',
        '1742449486165.png' => 'Galeri ruang 17',
        '1742449659675.webp' => 'Koleksi Cabana',
        '1742972542166.png' => 'Banner ruang utama',
        '1742973475864.png' => 'Koleksi pilihan 1',
        '1748239864605.jpg' => 'Kampanye ruang 1',
        '1748241007946.jpg' => 'Kampanye ruang 2',
        '1748243821780.jpg' => 'Kampanye ruang 3',
        '1748244032628.jpg' => 'Kampanye ruang 4',
        '1748244053696.jpg' => 'Kampanye ruang 5',
        '1748244474986.jpg' => 'Kampanye ruang 6',
        '1748244523907.jpg' => 'Kampanye ruang 7',
        '1748244560652.jpg' => 'Kampanye ruang 8',
        '1748318610868.jpg' => 'Koleksi pilihan 2',
        '1748320128043.jpg' => 'Koleksi pilihan 3',
    ];

    private R2ProductImageService $r2;

    public function __construct(?R2ProductImageService $r2 = null)
    {
        $this->r2 = $r2 ?? new R2ProductImageService();
    }

    public function catalog(): array
    {
        return self::ASSETS;
    }

    public function records(): array
    {
        $path = $this->manifestPath();
        if (!is_file($path)) {
            return [];
        }
        $decoded = json_decode((string) @file_get_contents($path), true);
        return is_array($decoded) ? $decoded : [];
    }

    public function publicUrls(): array
    {
        $urls = [];
        foreach ($this->records() as $filename => $record) {
            if (!isset(self::ASSETS[$filename]) || !is_array($record)) {
                continue;
            }
            $version = preg_replace('/[^a-f0-9]/i', '', (string) ($record['version'] ?? ''));
            if (($record['storage'] ?? '') === 'r2') {
                $url = $this->r2->publicUrl((string) ($record['key'] ?? ''));
            } else {
                $path = (string) ($record['path'] ?? '');
                $url = preg_match('#^uploads/home/[A-Za-z0-9._-]+\.webp$#', $path) ? base_url($path) : null;
            }
            if ($url !== null) {
                $urls[$filename] = $url . '?v=' . rawurlencode($version ?: '1');
            }
        }
        return $urls;
    }

    public function upload(string $filename, UploadedFile $file): void
    {
        if (!isset(self::ASSETS[$filename])) {
            throw new RuntimeException('Slot gambar Home tidak dikenal.');
        }
        if (!$file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('File upload tidak valid.');
        }
        if ($file->getSize() < 1 || $file->getSize() > self::MAX_UPLOAD_BYTES) {
            throw new RuntimeException('Ukuran file sumber maksimal 8 MB.');
        }

        $source = @file_get_contents($file->getTempName());
        if (!is_string($source) || $source === '') {
            throw new RuntimeException('File gambar tidak dapat dibaca.');
        }
        $this->storeSource($filename, $source);
    }

    public function importLegacy(string $filename): void
    {
        if (!isset(self::ASSETS[$filename])) {
            throw new RuntimeException('Slot gambar Home tidak dikenal.');
        }
        $apiKey = trim((string) env('APIKEY_IMG_ILENA', ''));
        $baseUrl = rtrim((string) env('ILENA_HOME_IMAGE_BASE_URL', 'https://img.ilenafurniture.com'), '/');
        if ($apiKey === '' || !function_exists('curl_init')) {
            throw new RuntimeException('Konfigurasi sumber gambar lama tidak tersedia.');
        }
        $url = $baseUrl . '/image/' . rawurlencode($filename) . '/?apikey=' . rawurlencode($apiKey);
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_MAXFILESIZE => 15_728_640,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $source = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        if (!is_string($source) || $source === '' || strlen($source) > 15_728_640 || $status !== 200) {
            throw new RuntimeException('Gagal mengambil gambar lama dari sumber.');
        }
        $this->storeSource($filename, $source);
    }

    private function storeSource(string $filename, string $source): void
    {
        $imageInfo = @getimagesizefromstring($source);
        $mime = (string) ($imageInfo['mime'] ?? '');
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
        if (!is_array($imageInfo) || !in_array($mime, $allowed, true)) {
            throw new RuntimeException('Gunakan file gambar JPEG, PNG, WebP, atau AVIF yang valid.');
        }
        $width = (int) $imageInfo[0];
        $height = (int) $imageInfo[1];
        if ($width < 1 || $height < 1 || $width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION
            || ($width * $height) > self::MAX_PIXELS) {
            throw new RuntimeException('Dimensi maksimal 6000 × 6000 piksel dan 25 megapiksel.');
        }

        $optimizer = new ResponsiveImageOptimizer();
        $output = null;
        foreach ([82, 76, 70, 64] as $quality) {
            $candidate = $optimizer->toWebp($source, 1600, $quality);
            if (is_string($candidate) && $candidate !== '' && strlen($candidate) <= self::MAX_OUTPUT_BYTES) {
                $output = $candidate;
                break;
            }
        }
        if (!is_string($output)) {
            throw new RuntimeException('Gambar gagal dikonversi ke WebP di bawah batas 900 KB.');
        }

        $version = substr(hash('sha256', $output), 0, 16);
        $key = 'home/assets/' . pathinfo($filename, PATHINFO_FILENAME) . '-' . $version . '.webp';
        if (!$this->r2->configured() || $this->r2->publicUrl($key) === null) {
            throw new RuntimeException('Storage R2/CDN belum terkonfigurasi; gambar lama tetap aktif.');
        }

        $temporary = tempnam(sys_get_temp_dir(), 'ilena-home-');
        if ($temporary === false || file_put_contents($temporary, $output, LOCK_EX) === false) {
            throw new RuntimeException('Gagal menyiapkan gambar untuk storage.');
        }
        try {
            if (!$this->r2->uploadFile($temporary, $key, 'image/webp')) {
                throw new RuntimeException('Upload ke storage CDN gagal; gambar lama tetap aktif.');
            }
        } finally {
            @unlink($temporary);
        }
        $record = ['version' => $version, 'storage' => 'r2', 'key' => $key];

        $this->saveRecord($filename, $record);
    }

    private function manifestPath(): string
    {
        return rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'home-assets.json';
    }

    private function saveRecord(string $filename, array $record): void
    {
        $path = $this->manifestPath();
        $lock = @fopen($path . '.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) {
            if (is_resource($lock)) fclose($lock);
            throw new RuntimeException('Manifest gambar Home sedang tidak dapat diperbarui.');
        }
        try {
            $records = $this->records();
            $records[$filename] = $record;
            $temporary = $path . '.' . bin2hex(random_bytes(5)) . '.tmp';
            $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if (!is_string($json) || @file_put_contents($temporary, $json, LOCK_EX) === false || !@rename($temporary, $path)) {
                @unlink($temporary);
                throw new RuntimeException('Gagal memperbarui daftar gambar Home.');
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
