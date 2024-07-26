<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PemesananModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\KartuStokModel;
use App\Models\PemesananGudangModel;
use App\Models\AjukanPrintModel;

class GudangController extends BaseController
{
    protected $barangModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $kartuStokModel;
    protected $ajukanPrint;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananGudangModel = new PemesananGudangModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->ajukanPrint = new AjukanPrintModel();
        $this->session = \Config\Services::session();
    }

    public function listOrder()
    {
        $pesananGudang = $this->pemesananGudangModel->getPemesananGudang();
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($pesananGudang as $ind_p => $p) {
            $barang = $this->barangModel->getBarang($p['id_barang']);
            $pesananGudang[$ind_p]['detail_barang'] = $barang;
            foreach (json_decode($barang['varian'], true) as $v) {
                if (strtolower($v['nama']) == strtolower(rtrim(explode("(", $p['nama'])[1], ")"))) {
                    $pesananGudang[$ind_p]['stok'] = $v['stok'];
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
            $pesananGudang[$ind_p]['tanggal'] = date('d', strtotime($p['tanggal'])) . ' ' . $bulan[(int)date('m', strtotime($p['tanggal'])) - 1] . ' ' . date('Y', strtotime($p['tanggal']));
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
    public function listOrderTable()
    {
        $pesananGudang = $this->pemesananGudangModel->getPemesananGudang();
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($pesananGudang as $ind_p => $p) {
            $barang = $this->barangModel->getBarang($p['id_barang']);
            $pesananGudang[$ind_p]['detail_barang'] = $barang;
            foreach (json_decode($barang['varian'], true) as $v) {
                if (strtolower($v['nama']) == strtolower(rtrim(explode("(", $p['nama'])[1], ")"))) {
                    $pesananGudang[$ind_p]['stok'] = $v['stok'];
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
            $pesananGudang[$ind_p]['tanggal'] = date('d', strtotime($p['tanggal'])) . ' ' . $bulan[(int)date('m', strtotime($p['tanggal'])) - 1] . ' ' . date('Y', strtotime($p['tanggal']));
        }
        $data = [
            'pesanan' => $pesananGudang
        ];
        return view('gudang/listOrderTable', $data);
    }

    public function listOrderAfter()
    {
        $pesanan = $this->pemesananModel->where(['status_print' => 'siap'])->orWhere(['status_print' => 'sudah print'])->orWhere(['status_print' => 'ajukan'])->findAll();
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            $itemsnya = json_decode($p['items'], true);
            foreach ($itemsnya as $ind_i => $i) {
                $barangnya = $this->barangModel->getBarang($i['id']);
                $itemsnya[$ind_i]['name'] = strtoupper($barangnya['kategori']) . ' ' . $i['name'];
            }
            $pesanan[$ind_p]['items'] = $itemsnya;
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
            $pesanan[$ind_p]['data_mid']['custom_field1'] = '';
            $pesanan[$ind_p]['data_mid']['custom_field2'] = '';
            $pesanan[$ind_p]['data_mid']['custom_field3'] = '';
        }
        $data = [
            'title' => 'Pesanan Selesai',
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan),
        ];
        return view('gudang/listOrderAfter', $data);
    }

    public function mutasi($data = false)
    {
        $product = $this->barangModel->getBarang();
        if (!$data) {
            $id_barang = $product[0]['id'];
            $varian = json_decode($product[0]['varian'], true)[0]['nama'];
        } else {
            $id_barang = explode('-', $data)[0];
            $varian = explode('-', $data)[1];
        }
        foreach ($product as $ind_p => $p) {
            $product[$ind_p]['varian'] = json_decode($p['varian'], true);
            $product[$ind_p]['deskripsi'] = json_decode($p['deskripsi'], true);
        }
        $mutasi = $this->kartuStokModel->where(['id_barang' => $id_barang, 'varian' => $varian])->findAll();
        // $pemesananBelumPrint = $this->pemesananModel->where('status_print !=', 'sudah print')->findAll();

        // $mutasiDanPemesanan = $mutasi;
        // foreach ($pemesananBelumPrint as $p) {
        //     foreach (json_decode($pemesananBelumPrint['items'], true) as $i) {
        //         if($i['id'] == $id_barang) {
        //             $itemMP = [
        //                 'id_barang' => $id_barang,
        //                 'tanggal' => json_decode($pemesananBelumPrint['data_mid'], true)['transaction_time'],
        //                 'keterangan' => 'Masih menunggu surat jalan di print',
        //                 'debit' => 0,
        //                 'kredit' => $i['quantity'],
        //                 'saldo' => 'Menyesuaikan'
        //             ];
        //             array_push($mutasiDanPemesanan, )
        //         }
        //     }
        // }
        $msg = session()->getFlashdata('msg');
        $data = [
            'title' => 'Mutasi',
            'mutasi' => $mutasi,
            'product' => $product,
            'idBarang' => $id_barang,
            'data' => $data,
            'msg' => $msg ? $msg : false
        ];
        return view('gudang/mutasi', $data);
    }
    public function fixMutasi()
    {
        $mutasi = $this->kartuStokModel->findAll();
        foreach ($mutasi as $m) {
            $idPesanan = explode('-', $m['keterangan'])[3];
            $varian = explode('-', $m['keterangan'])[2];
            $this->kartuStokModel->where(['id' => $m['id']])->set([
                'id_pesanan' => $idPesanan,
                'varian' => $varian
            ])->update();
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
    }
    public function actionAddMutasi()
    {
        //cek apakah ada pesanan yg masuh menunggu pembayaran
        $pesanan = $this->pemesananModel->where(['status' => 'Menunggu Pembayaran'])->findAll();
        if (count($pesanan) > 0) {
            session()->setFlashdata('msg', 'Ada pesanan dengan status Menunggu Pembayaran sehingga belum bisa melakukan perubahan data stok');
            return redirect()->to('/gudang/mutasi');
        }

        $tanggal = $this->request->getVar('tanggal');
        $barang = explode("-", $this->request->getVar('barang'));
        $jenis = $this->request->getVar('jenis');
        $nominal = $this->request->getVar('nominal');

        $d = strtotime($tanggal);
        $keterangan = date("Ymd", $d) . date("His") . "-" . $barang[0] . "-" . strtoupper($barang[1]) . "-MANUALLY";

        $produk = $this->barangModel->getBarang($barang[0]);
        $saldoSkrg = json_decode($produk['varian'], true)[(int)$barang[2]]['stok'];
        $debit = 0;
        $kredit = 0;
        if ($jenis == 'debit') {
            $debit = $nominal;
        } else if ($jenis == 'kredit') {
            $kredit = $nominal;
        }
        // dd([
        //     'id_barang' => $barang[0],
        //     'tanggal' => date("Y-m-d", $d) . ' ' . date("H:i:s"),
        //     'keterangan' => $keterangan,
        //     'debit' => $debit,
        //     'kredit' => $kredit,
        //     'saldo' => $saldo,
        // ]);
        $this->kartuStokModel->insert([
            'id_barang' => $barang[0],
            'tanggal' => date("Y-m-d", $d) . ' ' . date("H:i:s"),
            'keterangan' => $keterangan,
            'debit' => $debit,
            'kredit' => $kredit,
            'id_pesanan' => 'MANUALLY',
            'varian' => strtoupper($barang[1]),
            'alasan' => $this->request->getVar('alasan'),
            'pending' => true
        ]);
        // $varianCurr = json_decode($produk['varian'], true);
        // $varianCurr[(int)$barang[2]]['stok'] = $saldo;
        // $this->barangModel->where(['id' => $barang[0]])->set(['varian' => json_encode($varianCurr)])->update();
        return redirect()->to('/gudang/mutasi');
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
                $this->pemesananGudangModel->where([
                    'id_pesanan' => $pesanan['id_pesanan']
                ])->delete();
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
            return redirect()->to('/gudang/listorderafter');
        }

        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'sudah print'])->update();
        $d = strtotime("+7 hours");
        $tanggal = date("Y-m-d H:i:s", $d);
        $tanggalNoStrip = date("YmdHis", $d);
        $items = [];
        foreach (json_decode($pemesanan['items'], true) as $p) {
            if ($p['name'] != 'Biaya Ongkir' && $p['name'] != 'Biaya Admin' && $p['name'] != 'Voucher') {
                // $produknya = $this->barangModel->getBarang($p['id']);
                // $varian = json_decode($produknya['varian'], true);
                // $saldo = 0;
                // foreach ($varian as $ind_v => $v) {
                //     if (strtolower($v['nama']) == strtolower(rtrim(explode("(", $p['name'])[1], ")"))) {
                //         $saldo = (int)$v['stok'];
                //     }
                // }
                // $this->kartuStokModel->insert([
                //     'id_barang' => $p['id'],
                //     'tanggal' => $tanggal,
                //     'keterangan' => $tanggalNoStrip . "-" . $p['id'] . "-" . strtoupper(rtrim(explode("(", $p['name'])[1], ")")) . "-" . $pemesanan['id_midtrans'],
                //     'debit' => 0,
                //     'kredit' => $p['quantity'],
                //     'saldo' => (int)$saldo - (int)$p['quantity'],
                // ]);
                $this->kartuStokModel->where(['id_barang' => $p['id'], 'tanggal' => json_decode($pemesanan['data_mid'], true)['transaction_time']])->set([
                    'pending' => false
                ])->update();
                array_push($items, $p);
            }
        }

        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $pemesanan['kurir'] = json_decode($pemesanan['kurir'], true);
        $pemesanan['data_mid'] = json_decode($pemesanan['data_mid'], true);
        foreach ($pemesanan['items'] as $ind_i => $item) {
            if ($item['name'] != 'Biaya Ongkir' && $item['name'] != 'Biaya Admin') {
                $varianItem = rtrim(explode("(", $item['name'])[1], ")");
                $produkCurr = $this->barangModel->getBarang($item['id']);
                $dimensi = json_decode($produkCurr['deskripsi'], true)['dimensi']['asli'];
                $pemesanan['items'][$ind_i]['name'] = $produkCurr['nama'] . ", " . $dimensi['panjang'] . "x" . $dimensi['lebar'] . "x" . $dimensi['tinggi'] . "<br>" . $varianItem;
            }
        }
        $bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $data = [
            'title' => 'Surat Jalan',
            'pemesanan' => $pemesanan,
            'tanggal' => date("d", $d) . " " . $bulan[(int)date("m", $d)] . " " . date("Y", $d),
            'items' => $items
        ];
        return view('gudang/suratJalan', $data);
    }

    public function ajukanPrint()
    {
        $id_midtrans = $this->request->getVar('id_midtrans');
        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'ajukan'])->update();
        $d = strtotime('+7 hours');
        $this->ajukanPrint->insert([
            'tanggal' => date("Y-m-d", $d),
            'atas_nama' => session()->get('nama'),
            'kendala' => $this->request->getVar('kendala'),
            'id_midtrans' => $id_midtrans
        ]);
        session()->setFlashdata('msg', 'Pengajuan print ulang pemesanan ' . $id_midtrans . ' telah dibuat');
        return redirect()->to('/gudang/listorderafter');
    }
}
