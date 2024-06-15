<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PemesananModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\KartuStokModel;
use App\Models\PemesananGudangModel;

class GudangController extends BaseController
{
    protected $barangModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $kartuStokModel;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananGudangModel = new PemesananGudangModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->session = \Config\Services::session();
    }

    public function listOrder()
    {
        $pesananGudang = $this->pemesananGudangModel->getPemesananGudang();
        foreach ($pesananGudang as $ind_p => $p) {
            $barang = $this->barangModel->getBarang($p['id_barang']);
            foreach (json_decode($barang['varian'], true) as $v) {
                if ($v['nama'] == strtolower(rtrim(explode("(", $p['nama'])[1], ")"))) {
                    $pesananGudang[$ind_p]['stok'] = $v['stok'];
                    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    $tanggal = strtotime($p['tanggal']);
                    if ((int)date("H", $tanggal) > 12) {
                        $tanggal = strtotime($p['tanggal'] . " +1 day");
                        $targetSelesai = date("d", $tanggal) . " " . $bulan[(int)date("m", $tanggal) - 1] . " " . date("Y", $tanggal) . " 08:00:00";
                    } else {
                        $tanggal = strtotime($p['tanggal'] . " +3 hours");
                        $targetSelesai = date("d", $tanggal) . " " . $bulan[(int)date("m", $tanggal) - 1] . " " . date("Y H:i:s", $tanggal);
                    }
                    $pesananGudang[$ind_p]['target_selesai'] = $targetSelesai;
                }
            }
        }
        // dd($pesananGudang);
        $data = [
            'title' => 'Pesanan',
            'pesanan' => $pesananGudang,
            'val' => [
                'msg' => session()->getFlashdata('msg')
            ]
        ];
        return view('gudang/listOrder', $data);
    }

    public function listOrderAfter()
    {
        $pesanan = $this->pemesananModel->where(['status_print' => 'siap'])->findAll();
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
        }
        $data = [
            'title' => 'Pesanan Selesai',
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan),
        ];
        return view('gudang/listOrderAfter', $data);
    }

    public function mutasi($id_barang = false)
    {
        $product = $this->barangModel->getBarang();
        if (!$id_barang) {
            $id_barang = $product[0]['id'];
        }
        $mutasi = $this->kartuStokModel->getKartu($id_barang);
        $data = [
            'title' => 'Mutasi',
            'mutasi' => $mutasi,
            'product' => $product,
            'idBarang' => $id_barang
        ];
        return view('gudang/mutasi', $data);
    }
    public function actionAddMutasi()
    {
        $tanggal = $this->request->getVar('tanggal');
        $keterangan = $this->request->getVar('keterangan');
        $jenis = $this->request->getVar('jenis');
        $nominal = $this->request->getVar('nominal');
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

    public function actionScan($id_barang, $varian)
    {
        $produk = $this->barangModel->getBarang($id_barang);
        $namaSelected = $produk['nama'] . " (" . $varian . ")";
        $pesanan = $this->pemesananGudangModel->getPemesananGudang(false, $namaSelected);
        if ($pesanan) {
            $this->pemesananGudangModel->where([
                'nama' => $namaSelected,
                'id' => $pesanan['id']
            ])->set(['packed' => true])->update();

            $pemesananGudangCurr = $this->pemesananGudangModel->where([
                'id_pesanan' => $pesanan['id_pesanan']
            ])->findAll();
            $pemesananGudangCurr_packed = $this->pemesananGudangModel->where([
                'id_pesanan' => $pesanan['id_pesanan'],
                'packed' => true
            ])->findAll();
            if (count($pemesananGudangCurr) == count($pemesananGudangCurr_packed)) {
                $this->pemesananModel->where(['id_midtrans' => $pesanan['id_pesanan']])->set(['status_print' => 'siap'])->update();
            }

            session()->setFlashdata('msg', 'Barang sudah diupdate');
            return redirect()->to('/gudang/listorder');
        } else {
            session()->setFlashdata('msg', 'Barang tidak dalam antrian');
            return redirect()->to('/gudang/listorder');
        }
    }

    public function suratJalan($id_midtrans)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        if ($pemesanan['status_print'] != 'siap') {
            session()->setFlashdata('msg_status_print', 'Surat belum bisa atau sudah di cetak');
            return redirect()->to('/gudang/listorder');
        }
        $data = [
            'title' => 'Surat Jalan',
            'pemesanan' => $pemesanan
        ];
        return view('gudang/suratJalan', $data);
    }
}
