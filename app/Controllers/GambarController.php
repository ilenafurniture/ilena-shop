<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\GambarArtikelModel;
use App\Models\ArtikelModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use CodeIgniter\Images\Handlers\GDHandler;
use App\Models\GambarHeaderModel;

class GambarController extends BaseController
{
    protected $barangModel;
    protected $artikelModel;
    protected $gambarArtikelModel;
    protected $gambarBarangModel;
    protected $gambarBarang3000Model;
    protected $gambarHeaderModel;
    public function __construct()
    {
        $this->artikelModel = new ArtikelModel();
        $this->gambarArtikelModel = new GambarArtikelModel();
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->gambarBarang3000Model = new GambarBarang3000Model();
        $this->gambarHeaderModel = new GambarHeaderModel();
    }

    public function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function tampilGambarBarangCoba($idBarang)
    {
        // Decode URL dari Base64
        // $imageUrl = base64_decode($encodedUrl);
        // $imageUrl = 'https://ilenafurniture.com/viewpic/' . $idBarang;
        // dd($imageUrl);

        // Mendownload gambar dari URL
        // $imageContent = file_get_contents($imageUrl);
        $imageContent = $this->barangModel->getBarangAdmin($idBarang)['gambar'];
        if ($imageContent === false) {
            return $this->response->setStatusCode(404, 'Image not found.');
        }

        // Buat gambar dari string
        $image = imagecreatefromstring($imageContent);
        if ($image === false) {
            return $this->response->setStatusCode(500, 'Invalid image format.');
        }

        // Mendapatkan watermark PNG
        $watermarkPath = base_url('img/wm.png'); // Pastikan path watermark yang benar
        // dd($watermarkPath);
        // if (!file_exists($watermarkPath)) {
        //     return $this->response->setStatusCode(404, 'Watermark image not found.');
        // }
        $watermarkContent = file_get_contents($watermarkPath);
        if ($watermarkContent === false) {
            return $this->response->setStatusCode(404, 'Watermark image not found.');
        }

        // $watermark = imagecreatefrompng($watermarkPath);
        $watermark = imagecreatefromstring($watermarkContent);
        if ($watermark === false) {
            return $this->response->setStatusCode(500, 'Invalid watermark format.');
        }

        // Dapatkan ukuran gambar dan watermark
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $watermarkWidth = imagesx($watermark);
        $watermarkHeight = imagesy($watermark);

        // Tentukan posisi watermark di pojok kanan bawah
        $xPos = $imageWidth - $watermarkWidth - 10; // Margin 10px dari kanan
        $yPos = $imageHeight - $watermarkHeight - 10; // Margin 10px dari bawah

        // Tambahkan watermark ke gambar
        imagecopy($image, $watermark, $xPos, $yPos, 0, 0, $watermarkWidth, $watermarkHeight);

        // Set header untuk menampilkan gambar dengan tipe konten yang sesuai
        header('Content-Type: image/jpeg');

        // Output gambar
        imagejpeg($image);

        // Bersihkan memori
        imagedestroy($image);
        imagedestroy($watermark);
    }
    // public function tampilGambarBarang($idBarang)
    // {
    //     $data = $this->file_get_contents_curl(
    //         base_url('img/barang/300/' . $idBarang . '.webp')
    //     );
    //     // $gambar = $this->barangModel->getBarangAdmin($idBarang)['gambar'];
    //     $this->response->setHeader('Content-Type', 'image/webp');
    //     echo  $data;
    // }
    // public function tampilGambarBarangHover($idBarang)
    // {
    //     $data = $this->file_get_contents_curl(
    //         base_url('img/barang/hover/' . $idBarang . '.webp')
    //     );
    //     // $gambar = $this->barangModel->getBarangAdmin($idBarang)['gambar_hover'];
    //     $this->response->setHeader('Content-Type', 'image/webp');
    //     echo  $data;
    // }

    // public function tampilGambarVarian($idBarang, $urutan)
    // {
    //     $data = $this->file_get_contents_curl(
    //         base_url('img/barang/1000/' . $idBarang . '/' . $urutan . '.webp')
    //     );
    //     // $gambar = $this->gambarBarangModel->getGambar($idBarang);
        
    //     $this->response->setHeader('Content-Type', 'image/webp');
    //     echo $data;
    // }
    // public function tampilGambarVarian3000($idBarang, $urutan)
    // {
    //     $data = $this->file_get_contents_curl(
    //         base_url('img/barang/3000/' . $idBarang . '/' . $urutan . '.webp')
    //     );
    //     // $gambar = $this->gambarBarang3000Model->getGambar($idBarang);
    //     // $gambarSelected = $gambar ? $gambar['gambar' . $urutan] : $data;
    //     $this->response->setHeader('Content-Type', 'image/webp');
    //     echo $data;
    // }

    private function publicImagePath(string $relativePath): string
    {
        return rtrim(FCPATH, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
    }

    private function serveImageContent(string $content, string $defaultMime = 'image/webp')
    {
        $info = @getimagesizefromstring($content);
        $mime = $info['mime'] ?? $defaultMime;
        $etag = '"' . md5($content) . '"';
        $cacheControl = $this->request->getGet('v')
            ? 'public, max-age=31536000, immutable'
            : 'public, max-age=86400';

        if ($this->request->getHeaderLine('If-None-Match') === $etag) {
            return $this->response
                ->setStatusCode(304)
                ->removeHeader('Cache-Control')
                ->setHeader('ETag', $etag)
                ->setHeader('Cache-Control', $cacheControl);
        }

        return $this->response
            ->removeHeader('Cache-Control')
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) strlen($content))
            ->setHeader('Cache-Control', $cacheControl)
            ->setHeader('ETag', $etag)
            ->setBody($content);
    }

    private function serveImageFile(string $path)
    {
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/webp';
        $etag = '"' . md5($path . '|' . filemtime($path) . '|' . filesize($path)) . '"';
        $cacheControl = $this->request->getGet('v')
            ? 'public, max-age=31536000, immutable'
            : 'public, max-age=86400';

        if ($this->request->getHeaderLine('If-None-Match') === $etag) {
            return $this->response
                ->setStatusCode(304)
                ->removeHeader('Cache-Control')
                ->setHeader('ETag', $etag)
                ->setHeader('Cache-Control', $cacheControl);
        }

        return $this->response
            ->removeHeader('Cache-Control')
            ->setHeader('Content-Type', $mime ?: 'image/webp')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('Cache-Control', $cacheControl)
            ->setHeader('ETag', $etag)
            ->setBody(file_get_contents($path));
    }

    private function legacyProductImageFallback(string $relativePath): ?array
    {
        if (preg_match('#^img/barang/300/([^/]+)\.webp$#', $relativePath, $match)) {
            $barang = $this->barangModel->getBarangAdmin($match[1]);
            return ['value' => $barang['gambar'] ?? null, 'dir' => 'img/barang/300'];
        }

        if (preg_match('#^img/barang/hover/([^/]+)\.webp$#', $relativePath, $match)) {
            $barang = $this->barangModel->getBarangAdmin($match[1]);
            return ['value' => $barang['gambar_hover'] ?? null, 'dir' => 'img/barang/hover'];
        }

        if (preg_match('#^img/barang/(1000|3000)/([^/]+)-([0-9]+)\.webp$#', $relativePath, $match)) {
            $model = $match[1] === '3000' ? $this->gambarBarang3000Model : $this->gambarBarangModel;
            $row = $model->getGambar($match[2]);
            return ['value' => $row['gambar' . $match[3]] ?? null, 'dir' => 'img/barang/' . $match[1]];
        }

        return null;
    }

    private function tampilFileGambar(string $relativePath)
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $path = $this->publicImagePath($relativePath);

        if (is_file($path)) {
            return $this->serveImageFile($path);
        }

        $fallback = $this->legacyProductImageFallback($relativePath);
        $legacyValue = $fallback['value'] ?? null;
        if (!empty($legacyValue) && is_string($legacyValue)) {
            // Beberapa data lama menyimpan nama file, sementara data yang lebih lama lagi menyimpan blob gambar.
            if (preg_match('#^[A-Za-z0-9._-]+\.(?:jpe?g|png|webp|avif)$#i', $legacyValue)) {
                $legacyPath = $this->publicImagePath(($fallback['dir'] ?? '') . '/' . $legacyValue);
                if (is_file($legacyPath)) {
                    return $this->serveImageFile($legacyPath);
                }
            }

            if (@getimagesizefromstring($legacyValue) !== false) {
                return $this->serveImageContent($legacyValue);
            }
        }

        log_message('error', 'Gambar produk tidak ditemukan: {path}', ['path' => $relativePath]);
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Gambar tidak ditemukan');
    }

    public function tampilGambarBarang($idBarang)
    {
        return $this->tampilFileGambar('img/barang/300/' . $idBarang . '.webp');
    }

    public function tampilGambarBarangHover($idBarang)
    {
        return $this->tampilFileGambar('img/barang/hover/' . $idBarang . '.webp');
    }

    public function tampilGambarVarian($idBarang, $urutan)
    {
        return $this->tampilFileGambar('img/barang/1000/' . $idBarang . '-' . $urutan . '.webp');
    }

    public function tampilGambarVarian3000($idBarang, $urutan)
    {
        return $this->tampilFileGambar('img/barang/3000/' . $idBarang . '-' . $urutan . '.webp');
    }

    public function tampilGambarVarWM($idBarang, $urutan)
    {
        $data = $this->file_get_contents_curl(
            base_url('viewvar/' . $idBarang . '/' . $urutan)
        );
        $fp = 'imgdum/logo-1.webp';
        file_put_contents($fp, $data);

        // $gambarnya = imagecreatefromstring();

        // \Config\Services::image()
        //     ->withFile($fp)
        //     ->resize(300, 300, true, 'height')->save('imgdum/1logo-1.webp');
        // $this->response->setHeader('Content-Type', 'image/webp');
        // echo file_get_contents('imgdum/1logo-1.webp');

        // unlink($fp);
        // unlink('imgdum/1logo-1.webp');
    }

    public function formCobaInput()
    {
        return view('cobainput');
    }
    public function actionCobaInput()
    {
        // $gambarnya = $this->request->getFile('gambar');
        // $gambarnya->move('imgdum');

        // \Config\Services::image()
        //     ->withFile('imgdum/' . $gambarnya->getName())
        //     ->resize(300, 300, true, 'height')->save('imgdum/1' . $gambarnya->getName());
        // $this->response->setHeader('Content-Type', 'image/webp');
        // echo file_get_contents('imgdum/1' . $gambarnya->getName());

        // unlink('imgdum/' . $gambarnya->getName());
        // unlink('imgdum/1' . $gambarnya->getName());
        $data = $this->file_get_contents_curl(
            'https://ilenafurniture.com/viewpic/1000801'
        );
        $fp = 'imgdum/logo-1.webp';
        file_put_contents($fp, $data);

        \Config\Services::image()
            ->withFile($fp)
            ->resize(300, 300, true, 'height')->save('imgdum/1logo-1.webp');
        $this->response->setHeader('Content-Type', 'image/webp');
        echo file_get_contents('imgdum/1logo-1.webp');

        unlink($fp);
        unlink('imgdum/1logo-1.webp');
    }

    public function tampilGambarArtikel($idArtikel, $urutan = false)
    {
        if ($urutan) {
            $gambar = $this->gambarArtikelModel->getGambar($idArtikel);
            $gambarSelected = $gambar['gambar' . $urutan];
        } else {
            $artikel = $this->artikelModel->getArtikel($idArtikel);
            $gambarSelected = $artikel['header'];
        }
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambarSelected;
    }

    private function tampilGambarHeaderField($id, string $field)
    {
        $safeId = preg_replace('/[^0-9]/', '', (string) $id);
        if ($safeId === '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Gambar tidak ditemukan');
        }

        $row = $this->gambarHeaderModel->getGambar($safeId);
        $gambar = $row[$field] ?? null;
        if (empty($gambar)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Gambar tidak ditemukan');
        }

        if (is_string($gambar) && preg_match('#^uploads/slider/[A-Za-z0-9._-]+\.(?:jpe?g|png|webp|avif)$#', $gambar)) {
            $path = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $gambar);
            if (!is_file($path)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Gambar tidak ditemukan');
            }

            $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/jpeg';
            $etag = '"' . md5_file($path) . '"';
            $cacheControl = $this->request->getGet('v')
                ? 'public, max-age=31536000, immutable'
                : 'public, max-age=86400';

            if ($this->request->getHeaderLine('If-None-Match') === $etag) {
                return $this->response
                    ->setStatusCode(304)
                    ->removeHeader('Cache-Control')
                    ->setHeader('ETag', $etag)
                    ->setHeader('Cache-Control', $cacheControl);
            }

            return $this->response
                ->removeHeader('Cache-Control')
                ->setHeader('Content-Type', $mime ?: 'image/jpeg')
                ->setHeader('Content-Length', (string) filesize($path))
                ->setHeader('Cache-Control', $cacheControl)
                ->setHeader('ETag', $etag)
                ->setBody(file_get_contents($path));
        }

        $info = @getimagesizefromstring($gambar);
        $mime = $info['mime'] ?? 'image/jpeg';
        $etag = '"' . md5($gambar) . '"';
        $cacheControl = $this->request->getGet('v')
            ? 'public, max-age=31536000, immutable'
            : 'public, max-age=86400';

        if ($this->request->getHeaderLine('If-None-Match') === $etag) {
            return $this->response
                ->setStatusCode(304)
                ->removeHeader('Cache-Control')
                ->setHeader('ETag', $etag)
                ->setHeader('Cache-Control', $cacheControl);
        }

        return $this->response
            ->removeHeader('Cache-Control')
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) strlen($gambar))
            ->setHeader('Cache-Control', $cacheControl)
            ->setHeader('ETag', $etag)
            ->setBody($gambar);
    }

    public function tampilGambarHeader($id)
    {
        return $this->tampilGambarHeaderField($id, 'foto');
    }
    public function tampilGambarHeaderHp($id)
    {
        return $this->tampilGambarHeaderField($id, 'foto_hp');
    }

    private function productImageSlots(array $barang): array
    {
        $slots = [];
        $variants = json_decode($barang['varian'] ?? '[]', true) ?: [];
        foreach ($variants as $variant) {
            foreach (explode(',', (string)($variant['urutan_gambar'] ?? '')) as $slot) {
                $slot = preg_replace('/[^0-9]/', '', trim($slot));
                if ($slot !== '' && !in_array($slot, $slots, true)) {
                    $slots[] = $slot;
                }
            }
        }
        return $slots ?: ['1'];
    }

    private function usableImageFile(string $path): bool
    {
        return is_file($path) && filesize($path) > 0 && @getimagesize($path) !== false;
    }

    private function imageSourceFromLegacy($value, string $dir, string $tmpPrefix): ?array
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        if (preg_match('#^[A-Za-z0-9._-]+\.(?:jpe?g|png|webp|avif)$#i', $value)) {
            $path = $this->publicImagePath($dir . '/' . $value);
            return $this->usableImageFile($path) ? ['path' => $path, 'temporary' => false] : null;
        }

        if (@getimagesizefromstring($value) === false) {
            return null;
        }

        $tmpDir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
        $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $tmpPrefix . '-' . uniqid('', true) . '.webp';
        file_put_contents($tmpFile, $value);
        return $this->usableImageFile($tmpFile) ? ['path' => $tmpFile, 'temporary' => true] : null;
    }

    private function firstAvailableSource(array $sources): ?array
    {
        foreach ($sources as $source) {
            if (!is_array($source)) {
                continue;
            }
            if (!empty($source['path']) && $this->usableImageFile($source['path'])) {
                return $source;
            }
        }
        return null;
    }

    private function saveResizedProductImage(string $source, string $relativeDestination, int $size): bool
    {
        $destination = $this->publicImagePath($relativeDestination);
        $dir = dirname($destination);
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        if (!is_writable($dir)) { @chmod($dir, 0775); }
        if (!is_writable($dir)) {
            throw new \RuntimeException('Folder gambar belum writable: ' . $relativeDestination);
        }

        $tmpDir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
        $tmpOut = $tmpDir . DIRECTORY_SEPARATOR . 'safe-resize-' . uniqid('', true) . '.webp';

        \Config\Services::image()
            ->withFile($source)
            ->resize($size, $size, true, 'height')
            ->save($tmpOut);

        if (!$this->usableImageFile($tmpOut)) {
            @unlink($tmpOut);
            throw new \RuntimeException('Hasil resize kosong: ' . $relativeDestination);
        }

        @unlink($destination);
        $ok = @copy($tmpOut, $destination);
        @unlink($tmpOut);
        if ($ok) { @touch($destination); }
        return $ok;
    }

    public function gantiUkuran($id)
    {
        return $this->gantiLokasi($id);
    }

    public function gantiLokasi($id)
    {
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$id);
        $barang = $this->barangModel->where(['id' => $safeId])->first();
        if (!$barang) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ]);
        }

        $force = $this->request->getGet('force') === '1';
        $report = [
            'nama_barang' => $barang['nama'] ?? $safeId,
            'id' => $safeId,
            'created' => [],
            'skipped' => [],
            'missing_source' => [],
        ];

        $gambar1000 = $this->gambarBarangModel->where(['id' => $safeId])->first() ?: [];
        $gambar3000 = $this->gambarBarang3000Model->where(['id' => $safeId])->first() ?: [];
        $slots = $this->productImageSlots($barang);

        foreach ($slots as $slot) {
            $path1000 = $this->publicImagePath("img/barang/1000/{$safeId}-{$slot}.webp");
            $path3000 = $this->publicImagePath("img/barang/3000/{$safeId}-{$slot}.webp");
            $source = $this->firstAvailableSource([
                ['path' => $path3000, 'temporary' => false],
                ['path' => $path1000, 'temporary' => false],
                $this->imageSourceFromLegacy($gambar3000['gambar' . $slot] ?? null, 'img/barang/3000', $safeId . '-' . $slot . '-3000'),
                $this->imageSourceFromLegacy($gambar1000['gambar' . $slot] ?? null, 'img/barang/1000', $safeId . '-' . $slot . '-1000'),
            ]);

            if (!$source) {
                $report['missing_source'][] = "slot {$slot}";
                continue;
            }

            foreach ([3000, 1000] as $size) {
                $relative = "img/barang/{$size}/{$safeId}-{$slot}.webp";
                $dest = $this->publicImagePath($relative);
                if (!$force && $this->usableImageFile($dest)) {
                    $report['skipped'][] = $relative;
                    continue;
                }
                $this->saveResizedProductImage($source['path'], $relative, $size);
                $report['created'][] = $relative;
            }

            if (!empty($source['temporary'])) { @unlink($source['path']); }
        }

        $firstSlot = $slots[0] ?? '1';
        $coverDest = $this->publicImagePath("img/barang/300/{$safeId}.webp");
        $coverSource = $this->firstAvailableSource([
            ['path' => $this->publicImagePath("img/barang/1000/{$safeId}-{$firstSlot}.webp"), 'temporary' => false],
            ['path' => $this->publicImagePath("img/barang/3000/{$safeId}-{$firstSlot}.webp"), 'temporary' => false],
            $this->imageSourceFromLegacy($barang['gambar'] ?? null, 'img/barang/300', $safeId . '-cover'),
        ]);
        if ($coverSource) {
            if ($force || !$this->usableImageFile($coverDest)) {
                $this->saveResizedProductImage($coverSource['path'], "img/barang/300/{$safeId}.webp", 300);
                $report['created'][] = "img/barang/300/{$safeId}.webp";
            } else {
                $report['skipped'][] = "img/barang/300/{$safeId}.webp";
            }
            if (!empty($coverSource['temporary'])) { @unlink($coverSource['path']); }
        } else {
            $report['missing_source'][] = 'cover 300';
        }

        $hoverDest = $this->publicImagePath("img/barang/hover/{$safeId}.webp");
        $hoverSource = $this->firstAvailableSource([
            ['path' => $hoverDest, 'temporary' => false],
            $this->imageSourceFromLegacy($barang['gambar_hover'] ?? null, 'img/barang/hover', $safeId . '-hover'),
        ]);
        if ($hoverSource) {
            if ($force || !$this->usableImageFile($hoverDest)) {
                $this->saveResizedProductImage($hoverSource['path'], "img/barang/hover/{$safeId}.webp", 300);
                $report['created'][] = "img/barang/hover/{$safeId}.webp";
            } else {
                $report['skipped'][] = "img/barang/hover/{$safeId}.webp";
            }
            if (!empty($hoverSource['temporary'])) { @unlink($hoverSource['path']); }
        } else {
            $report['missing_source'][] = 'hover';
        }

        $this->barangModel->update($safeId, ['tgl_update' => date('Y-m-d H:i:s', strtotime('+7 hours'))]);

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'message' => 'Perbaikan gambar selesai tanpa menimpa data gambar DB lama.',
            'barang' => $report,
        ], false);
    }
}
