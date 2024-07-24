<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\KeranjangMarketModel;
use App\Models\KartuStokModel;

class MarketplaceController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $keranjangMarketModel;
    protected $kartuStokModel;
    protected $session;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->keranjangMarketModel = new KeranjangMarketModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->session = \Config\Services::session();
    }

    public function product()
    {
        $cart = $this->keranjangMarketModel->getKeranjang();
        $product = $this->barangModel->getBarang();
        $data = [
            'title' => 'Semua Produk',
            'produk' => $product,
            'keranjang' => $cart
        ];
        return view('market/product', $data);
    }
    public function actionFind()
    {
        $cari = str_replace(" ", "-", $this->request->getVar('cari'));
        return redirect()->to('/market/find/' . $cari);
    }
    public function find($teks)
    {
        $cari = str_replace("-", " ", $teks);
        $cart = $this->keranjangMarketModel->getKeranjang();
        $produk = $this->barangModel->like('nama', $cari, 'both')->where(['active' => '1'])->findAll();
        $data = [
            'title' => 'Cari Produk',
            'produk' => $produk,
            'keranjang' => $cart,
            'find' => $cari
        ];
        return view('market/product', $data);
    }

    public function cart()
    {
        $cart = $this->keranjangMarketModel->getKeranjang();
        foreach ($cart as $ind_c => $c) {
            $produknya = $this->barangModel->getBarang($c['id_barang']);
            $cart[$ind_c]['nama'] = $produknya['nama'];
        }
        $data = [
            'title' => 'Keranjang Produk',
            'keranjang' => $cart
        ];
        return view('market/cart', $data);
    }

    public function addCart($id_barang, $varian)
    {
        $cart = $this->keranjangMarketModel->getKeranjang();
        $ketemu = false;
        foreach ($cart as $ind_c => $c) {
            $jumlahsekarang = $c['jumlah'];
            if ($c['id_barang'] == $id_barang && $c['varian'] == $varian) {
                $this->keranjangMarketModel->where(['id_barang' => $id_barang, 'varian' => $varian])->set([
                    'jumlah' => $jumlahsekarang + 1,
                ])->update();
                $ketemu = true;
            }
        }
        if (!$ketemu) {
            $data = [
                'id_barang' => $id_barang,
                'varian' => $varian,
                'jumlah' => 1,
            ];
            $this->keranjangMarketModel->insert((object)$data);
        }
        return redirect()->to('/market/product');
    }

    public function submitOrder()
    {
        $keranjang = $this->keranjangMarketModel->getKeranjang();
        $items = [];
        $hargaTotal = 0;
        $waktu = date("H:i:s", strtotime(('+7 Hours')));
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "IL" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1)) . 'MP';
        $randomId = "IL" . rand();
        foreach ($keranjang as $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            $item = [
                'id' => $k['id_barang'],
                'price' => $produk['harga'],
                'quantity' => $k['jumlah'],
                'name' => $produk['nama'] . ' (' . $k['varian'] . ')',
                'packed' => false,
            ];
            array_push($items, $item);
            $hargaTotal += $produk['harga'] * $k['jumlah'] * (100 - $produk['diskon']) / 100;

            //kartu stok ditambahkan
            $varian = json_decode($produk['varian'], true);
            $saldo = 0;
            foreach ($varian as $ind_v => $v) {
                if (strtolower($v['nama']) == strtolower($k['varian'])) {
                    $saldo = (int)$v['stok'];
                }
            }
            $tanggalNoStrip = date("YmdHis", strtotime($this->request->getVar('tanggal') . " " . $waktu));
            $this->kartuStokModel->insert([
                'id_barang' => $k['id_barang'],
                'tanggal' => $this->request->getVar('tanggal') . " " . $waktu,
                'keterangan' => $tanggalNoStrip . "-" . $k['id_barang'] . "-" . strtoupper($k['varian']) . "-" . $idFix,
                'debit' => 0,
                'kredit' => $k['jumlah'],
                'saldo' => $saldo,
                'pending' => true,
                'id_pesanan' => $idFix,
                'varian' => strtoupper($k['varian'])
            ]);
        }
        $data = [
            'data_mid'          => json_encode([
                'transaction_time' => $this->request->getVar('tanggal') . " " . $waktu,
                'order_id'         => $idFix,
                'gross_amount'     => $hargaTotal,
                'payment_type'     => 'market'
            ]),
            'id_midtrans'       => $idFix,
            'email'             => $this->request->getVar('email'),
            'kurir'             => json_encode([
                'nama' => $this->request->getVar('nama_ekspedisi'),
                'deskripsi' => $this->request->getVar('jenis_kurir'),
                'harga' => $this->request->getVar('harga_pengiriman'),
                'estimasi' => $this->request->getVar('estimasi')
            ]),
            'resi'              => $this->request->getVar('no_resi'),
            'harga'             => $this->request->getVar('harga_pengiriman'),
            'estimasi'          => $this->request->getVar('estimasi'),
            'nama'              => $this->request->getVar('nama_lengkap'),
            'nohp'              => $this->request->getVar('no_hp'),
            'alamat'            => $this->request->getVar('alamat_lengkap'),
            'status'            => 'Proses',
            'id_marketplace'    => $this->request->getVar('id_marketplace'),
            'items'             => json_encode($items),
        ];
        $this->pemesananModel->insert((object)$data);
        $this->keranjangMarketModel->truncate();
        return redirect()->to('/market/product');
    }

    public function reduceCart($id)
    {
        $cart = $this->keranjangMarketModel->getKeranjang();
        foreach ($cart as $c) {
            $jumlahsekarang = $c['jumlah'];
            if ($c['id'] == $id) {
                if ($jumlahsekarang > 1) {
                    $this->keranjangMarketModel->where(['id_barang' => $c['id_barang'], 'varian' => $c['varian']])->set([
                        'jumlah' => $jumlahsekarang - 1,
                    ])->update();
                } else {
                    $this->keranjangMarketModel->where(['id' => $id])->delete();
                }
            }
        }
        return redirect()->to('/market/product');
    }
}
