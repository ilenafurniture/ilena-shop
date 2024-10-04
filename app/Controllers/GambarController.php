<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use CodeIgniter\Images\Handlers\GDHandler;

class GambarController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $gambarBarang3000Model;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->gambarBarang3000Model = new GambarBarang3000Model();
    }

    public function tampilGambarBarang($idBarang)
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
    public function tampilGambarBarangBenar($idBarang)
    {
        $gambar = $this->barangModel->getBarangAdmin($idBarang)['gambar'];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambar;
    }
    public function tampilGambarBarangHover($idBarang)
    {
        $gambar = $this->barangModel->getBarangAdmin($idBarang)['gambar_hover'];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambar;
    }

    public function tampilGambarVarian($idBarang, $urutan)
    {
        $gambar = $this->gambarBarangModel->getGambar($idBarang);
        $gambarSelected = $gambar['gambar' . $urutan];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambarSelected;
    }
    public function tampilGambarVarian3000($idBarang, $urutan)
    {
        $gambar = $this->gambarBarang3000Model->getGambar($idBarang);
        $gambarSelected = $gambar['gambar' . $urutan];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambarSelected;
    }

    public function tampilGambarVarWM($idBarang, $urutan)
    {
        $data = file_get_contents_curl(
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



        function file_get_contents_curl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        $data = file_get_contents_curl(
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
}
