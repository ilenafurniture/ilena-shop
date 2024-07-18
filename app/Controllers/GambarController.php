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
        $gambar = $this->barangModel->getBarangAdmin($idBarang)['gambar'];
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
