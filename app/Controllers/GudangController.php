<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PemesananModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\PemesananGudangModel;

class GudangController extends BaseController
{
    protected $barangModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananGudangModel = new PemesananGudangModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->session = \Config\Services::session();
    }

    public function listOrder()
    {
        $pesananGudang = $this->pemesananGudangModel->getPemesananGudang();
        foreach ($pesananGudang as $ind_p => $p) {
            $barang = $this->barangModel->getBarang($p['id_barang']);
            foreach (json_decode($barang['varian'], true) as $v) {
                if ($v['nama'] == rtrim(explode("(", $p['nama'])[1], ")")) {
                    $pesananGudang[$ind_p]['stok'] = $v['stok'];
                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $tanggal = strtotime($p['tanggal']);
                    if ((int)date("H", $tanggal) > 12) {
                        $tanggal = strtotime($p['tanggal'] . " +1 day");
                        $targetSelesai = date("d", $tanggal) . " " . $bulan[(int)date("m", $tanggal) - 1] . " " . date("Y", $tanggal) . " 08:00:00";
                    } else {
                        $tanggal = strtotime($p['tanggal'] . " +3 hours");
                        $targetSelesai = date("d", $tanggal) . " " . $bulan[(int)date("m", $tanggal) - 1] . " " . date("Y H:i:s", $tanggal);;
                    }
                    $pesananGudang[$ind_p]['target_selesai'] = $targetSelesai;
                }
            }
        }
        $data = [
            'title' => 'Pesanan',
            'pesanan' => $pesananGudang,
        ];
        return view('gudang/listOrder', $data);
    }

    public function listOrderAfter()
    {
        $pesanan = $this->pemesananGudangModel->getPemesananGudang(true);
        $data = [
            'title' => 'Pesanan Selesai',
            'pesanan' => $pesanan,
        ];
        return view('gudang/listOrderAfter', $data);
    }

    public function mutasi()
    {
        $data = [
            'title' => 'Mutasi',
        ];
        return view('gudang/mutasi', $data);
    }

    public function product()
    {
        $product = $this->barangModel->getBarang();
        $productAll = [];
        foreach ($product as $p) {
            foreach (json_decode($p['varian'], true) as $v) {
                $item = [
                    'id' => $p['id'],
                    'nama' => $p['nama'],
                    'varian' => $v['nama'],
                    'stok' => $v['stok'],
                ];
                array_push($productAll, $item);
            }
        }
        $data = [
            'title' => 'Produk',
            'produk' => $productAll
        ];
        return view('gudang/product', $data);
    }

    public function scanOrder()
    {
        $data = [
            'title' => 'Scan',
            'val' => [
                'msg' => session()->getFlashdata('msg')
            ]
        ];
        return view('gudang/scanOrder', $data);
    }
    public function actionScan($id_barang)
    {
        // $id_barang = $this->request->getVar('id_produk');
        $produk = $this->barangModel->getBarang($id_barang);
        $produk['varian'] = json_decode($produk['varian'], true);
        $data = [
            'title' => 'Pilih Varian',
            'produk' => $produk
        ];
        if (count($produk['varian']) > 1) {
            return view('gudang/pilihVarian', $data);
        } else {
            //ganti packed menjadi true
            $namaSelected = $produk['nama'] . " (" . $produk['varian'][0]['nama'] . ")";
            $pesanan = $this->pemesananGudangModel->getPemesananGudang($namaSelected);
            if ($pesanan) {
                $this->pemesananGudangModel->where([
                    'nama' => $namaSelected,
                    'id_pesanan' => $pesanan['id_pesanan']
                ])->set(['packed' => true])->update();

                //kurangi stok di varian produk
                $produk['varian'][0]['stok'] = (int)$produk['varian'][0]['stok'] - 1;
                $this->barangModel->where(['id' => $id_barang])->set(['varian' => json_encode($produk['varian'])])->update();
                session()->setFlashdata('msg', 'Barang sudah diupdate');
                return redirect()->to('/gudang/scanorder');
            } else {
                session()->setFlashdata('msg', 'Barang tidak dalam antrian');
                return redirect()->to('/gudang/scanorder');
            }
        }
    }
    public function actionPilihVarian($id_produk, $ind_varian)
    {
        $produk = $this->barangModel->getBarang($id_produk);
        $produk['varian'] = json_decode($produk['varian'], true);
        //ganti packed menjadi true
        $namaSelected = $produk['nama'] . " (" . $produk['varian'][$ind_varian]['nama'] . ")";
        $pesanan = $this->pemesananGudangModel->getPemesananGudang($namaSelected);
        if ($pesanan) {
            $this->pemesananGudangModel->where([
                'nama' => $namaSelected,
                'id_pesanan' => $pesanan['id_pesanan']
            ])->set(['packed' => true])->update();

            //kurangi stok di varian produk
            $produk['varian'][$ind_varian]['stok'] = (int)$produk['varian'][$ind_varian]['stok'] - 1;
            $this->barangModel->where(['id' => $id_produk])->set(['varian' => json_encode($produk['varian'])])->update();
            session()->setFlashdata('msg', 'Barang sudah diupdate');
            return redirect()->to('/gudang/scanorder');
        } else {
            session()->setFlashdata('msg', 'Barang tidak dalam antrian');
            return redirect()->to('/gudang/scanorder');
        }
    }
}
