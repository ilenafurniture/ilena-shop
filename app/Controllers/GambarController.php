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

    public function tampilGambarHeader($id)
    {
        $gambar = $this->gambarHeaderModel->getGambar($id)['foto'];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambar;
    }
    public function tampilGambarHeaderHp($id)
    {
        $gambar = $this->gambarHeaderModel->getGambar($id)['foto_hp'];
        $this->response->setHeader('Content-Type', 'image/webp');
        echo $gambar;
    }

    public function gantiUkuran($id) //koleksinya = water_case
    {
        $barangLama = $this->barangModel->where(['id' => $id])->first();
        if (!$barangLama) {
            return $this->response->setJSON(['message' => 'barang nggk nemu'], false);
        }

        $dataChecker = [];
        $insertGambarBarang = [];
        $insertGambar300 = false;
        $jumlahGambar = '';
        foreach (json_decode($barangLama['varian'], true) as $ind_v => $v) {
            if($ind_v == 0){
                $jumlahGambar = $jumlahGambar . $v['urutan_gambar'];
            } else {
                $jumlahGambar = $jumlahGambar . ',' . $v['urutan_gambar'];
            }
        }
        $jumlahGambar = count(explode(',', $jumlahGambar));
        $dataGambar = $this->gambarBarang3000Model->where(['id' => $barangLama['id']])->first();
        for ($i = 1; $i <= $jumlahGambar; $i++) {
            $gambarSelected = $dataGambar['gambar' . $i];
            $fp = 'imgdum/' . $barangLama['id'] . '-' . $i . '.webp';
            file_put_contents($fp, $gambarSelected);
            \Config\Services::image()
                ->withFile($fp)
                ->resize(1000, 1000, true, 'height')->save('imgdum/' . $barangLama['id'] . '-' . $i . '(1).webp');
            $insertGambarBarang['gambar' . $i] = file_get_contents('imgdum/' . $barangLama['id'] . '-' . $i . '(1).webp');
            unlink('imgdum/' . $barangLama['id'] . '-' . $i . '(1).webp');
            $dataChecker['resize_300'] = 'success';

            if ($i == 1) {
                \Config\Services::image()
                    ->withFile($fp)
                    ->resize(300, 300, true, 'height')->save('imgdum/' . $barangLama['id'] . '-' . $i . '300(1).webp');
                $insertGambar300 = file_get_contents('imgdum/' . $barangLama['id'] . '-' . $i . '300(1).webp');
                unlink('imgdum/' . $barangLama['id'] . '-' . $i . '300(1).webp');
            }
            unlink($fp);
            $dataChecker['resize_1000'] = 'success';
        }

        //resize gambar hover
        $fp = 'imgdum/hover-' . $barangLama['id'] .'.webp';
        file_put_contents($fp, $barangLama['gambar_hover']);
        \Config\Services::image()
            ->withFile($fp)
            ->resize(300, 300, true, 'height')->save('imgdum/hover-' . $barangLama['id'] . '(1).webp');
        
        $this->barangModel->where(['id' => $barangLama['id']])->set([
            'gambar' => $insertGambar300,
            'gambar_hover' => file_get_contents('imgdum/hover-' . $barangLama['id'] . '(1).webp')
            ])->update();
        unlink('imgdum/hover-' . $barangLama['id'] . '(1).webp');
        unlink($fp);
        $dataChecker['resize_hover'] = 'success';
        $this->gambarBarangModel->where(['id' => $barangLama['id']])->set($insertGambarBarang)->update();
        $dataChecker['nama_barang'] = $barangLama['nama'];
        
        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'barang' => $dataChecker
        ], false);
    }

    public function gantiLokasi($id) //koleksinya = water_case
    {
        $dataChecker = [];
        $barangLama = $this->barangModel->where(['id' => $id])->first();
        
            // Ukuran 300
        $fp = 'imgdum/barang/300/' . $barangLama['id'] .'.webp';
        file_put_contents($fp, $barangLama['gambar']);
        \Config\Services::image()
                ->withFile($fp)
                ->resize(300, 300, true, 'height')->save('img/barang/300/' . $barangLama['id'].'.webp');
        unlink($fp);
        $dataChecker['resize_300'] = 'success';


        // Ukuran Hover
        $fp = 'imgdum/barang/hover/' . $barangLama['id'] .'.webp';
        file_put_contents($fp, $barangLama['gambar_hover']);
        \Config\Services::image()
                ->withFile($fp)
                ->resize(300, 300, true, 'height')->save('img/barang/hover/' . $barangLama['id'].'.webp');
        unlink($fp);
        $dataChecker['resize_hover'] = 'success';


        $gambarBarang = $this->gambarBarangModel->where(['id' => $barangLama['id']])->first();
        $gambarBarang3000 = $this->gambarBarang3000Model->where(['id' => $barangLama['id']])->first();
        $jumlahGambar = '';
        foreach (json_decode($barangLama['varian'], true) as $ind_v => $v) {
            if($ind_v == 0){
                $jumlahGambar = $jumlahGambar . $v['urutan_gambar'];
            } else {
                $jumlahGambar = $jumlahGambar . ',' . $v['urutan_gambar'];
            }
        }
        $jumlahGambar = count(explode(',', $jumlahGambar));
        for ($i = 1; $i <= $jumlahGambar; $i++) {
            // Ukuran 1000
            $fp = 'imgdum/barang/1000/' . $barangLama['id'] . '-' . $i.'.webp';
            file_put_contents($fp, $gambarBarang['gambar'.$i]);
            \Config\Services::image()
                ->withFile($fp)
                ->resize(1000, 1000, true, 'height')->save('img/barang/1000/' . $barangLama['id'].'-'.$i.'.webp');
            unlink($fp);
            $dataChecker['resize_1000'] = 'success';


            // Ukuran 3000
            $fp = 'imgdum/barang/3000/' . $barangLama['id'] . '-' . $i.'.webp';
            file_put_contents($fp, $gambarBarang3000['gambar'.$i]);
            \Config\Services::image()
                ->withFile($fp)
                ->resize(3000, 3000, true, 'height')->save('img/barang/3000/' . $barangLama['id'].'-'.$i.'.webp');
            unlink($fp);
            $dataChecker['resize_3000'] = 'success';

        }

        $dataChecker['nama_barang'] = $barangLama['nama'];
        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'barang' => $dataChecker
        ], false);
    }
}