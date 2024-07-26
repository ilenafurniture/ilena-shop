<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\PemesananGudangModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\AjukanPrintModel;
use App\Models\KartuStokModel;

class AdminController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $gambarBarang3000Model;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $ajukanPrintModel;
    protected $kartuStokModel;
    protected $session;
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->gambarBarang3000Model = new GambarBarang3000Model();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananGudangModel = new PemesananGudangModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->ajukanPrintModel = new AjukanPrintModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->session = \Config\Services::session();
    }
    public function listProduct()
    {
        $product = $this->barangModel->getBarangAdmin();
        $data = [
            'title' => 'Produk Kami',
            'produk' => $product
        ];
        return view('admin/all', $data);
    }
    public function addProduct()
    {
        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = [
            'title' => 'Tambah Produk',
            'koleksi' => $koleksi,
            'koleksiJson' => json_encode($koleksi),
            'jenis' => $jenis,
            'jenisJson' => json_encode($jenis),
            'val' => [
                'msg' => session()->getFlashdata('val-id'),
            ]
        ];
        return view('admin/add', $data);
    }
    public function actionAddProduct()
    {
        if (!$this->validate([
            'id' => [
                'rules' => 'required|is_unique[barang.id]',
                'errors' => [
                    'required' => 'Id harus diisi',
                    'is_unique' => 'Id sudah terdaftar',
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-id', $validation->getError('id'));
            return redirect()->to('/admin/addproduct')->withInput();
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();

        $data = $this->request->getVar();
        $data_gambar_mentah = $this->request->getFiles();
        $data_gambar = [];
        foreach ($data_gambar_mentah as $key => $g) {
            if ($g->isValid()) $data_gambar[$key] = $g;
        }
        $jumlahVarian = explode(",", $this->request->getVar('hitung-varian'));

        $insertGambarBarang = [
            'id' => $data['id']
        ];
        $insertGambarBarang3000 = [
            'id' => $data['id']
        ];
        $varianData = [];
        $counterGambar = 0;
        foreach ($jumlahVarian as $j) {
            $urutanGambar = [];
            foreach ($data_gambar as $ind_g => $g) {
                if (explode("-", $ind_g)[1] == $j) {
                    $counterGambar++;
                    array_push($urutanGambar, $counterGambar);
                }
            }
            $itemVarian = [
                'nama' => $data['nama-var' . $j],
                'kode' => $data['kode-var' . $j],
                'stok' => $data['stok-var' . $j],
                'urutan_gambar' => implode(",", $urutanGambar),
            ];
            array_push($varianData, $itemVarian);

            $tanggalNoStrip = date("YmdHis", strtotime("+7 Hours"));
            $this->kartuStokModel->insert([
                'id_barang' => $data['id'],
                'tanggal' => date("Y-m-d H:i:s", strtotime("+7 Hours")),
                'keterangan' => $tanggalNoStrip . "-" . $data['id'] . "-" . strtoupper($data['nama-var' . $j]) . "-ADDPRODUCT",
                'debit' => $data['stok-var' . $j],
                'kredit' => 0,
                'saldo' => $data['stok-var' . $j],
                'pending' => false,
                'id_pesanan' => 'ADDPRODUCT',
                'varian' => strtoupper($data['nama-var' . $j])
            ]);
        }

        $dataKategori = $data['kategori'];
        $koleksiSelected = array_values(array_filter($koleksi, function ($var) use ($dataKategori) {
            return ($var['id'] == $dataKategori);
        }))[0]['nama'];
        $dataSubkategori = $data['subkategori'];
        $jenisSelected = array_values(array_filter($jenis, function ($var) use ($dataSubkategori) {
            return ($var['id'] == $dataSubkategori);
        }))[0]['nama'];

        $index_data_gambar = array_flip(array_keys($data_gambar));
        $iterasi = 0;
        foreach ($data_gambar as $key_dg => $dG) {
            $dG->move('imgdum');
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(3000, 3000, true, 'height')->save('imgdum/1' . $dG->getName());
            $insertGambarBarang3000['gambar' . ((int)$index_data_gambar[$key_dg] + 1)] = file_get_contents('imgdum/1' . $dG->getName());
            unlink('imgdum/1' . $dG->getName());

            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(1000, 1000, true, 'height')->save('imgdum/1' . $dG->getName());
            $insertGambarBarang['gambar' . ((int)$index_data_gambar[$key_dg] + 1)] = file_get_contents('imgdum/1' . $dG->getName());
            unlink('imgdum/1' . $dG->getName());

            if ($iterasi <= 0) {
                \Config\Services::image()
                    ->withFile('imgdum/' . $dG->getName())
                    ->resize(300, 300, true, 'height')->save('imgdum/1' . $dG->getName());
                $insertGambarBarang300 = file_get_contents('imgdum/1' . $dG->getName());
                unlink('imgdum/1' . $dG->getName());
            }
            unlink('imgdum/' . $dG->getName());
            $iterasi++;
        }

        $insertDataBarang = [
            'id' => $data['id'],
            'nama' => $data['nama'],
            'harga' => $data['harga'],
            'pencarian' => '',
            'rate' => '0',
            'deskripsi' => json_encode([
                'deskripsi' => $data['deskripsi'],
                'dimensi' => [
                    'asli' => [
                        'panjang' => $data['panjang-asli'],
                        'lebar' => $data['lebar-asli'],
                        'tinggi' => $data['tinggi-asli'],
                        'berat' => $data['berat-asli'],
                    ],
                    'paket' => [
                        'panjang' => $data['panjang-paket'],
                        'lebar' => $data['lebar-paket'],
                        'tinggi' => $data['tinggi-paket'],
                        'berat' => $data['berat-paket'],
                    ]
                ],
                'perawatan' => $data['perawatan']
            ]),
            'kategori' => $koleksiSelected,
            'subkategori' => $jenisSelected,
            'diskon' => $data['diskon'],
            'varian' => json_encode($varianData),
            'shopee' => $data['shopee'],
            'tokped' => $data['tokped'],
            'tiktok' => $data['tiktok'],
            'active' => '1',
            'gambar' => $insertGambarBarang300
        ];
        $this->barangModel->insert($insertDataBarang);
        $this->gambarBarangModel->insert($insertGambarBarang);
        $this->gambarBarang3000Model->insert($insertGambarBarang3000);

        return redirect()->to('admin/product');
    }
    public function editProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        // $product['pencarian'] = json_decode($product['pencarian'],true);  
        $product['deskripsi'] = json_decode($product['deskripsi'], true);
        $product['varian'] = json_decode($product['varian'], true);
        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $hitungVarian = '';
        foreach ($product['varian'] as $ind => $v) {
            if ($ind == 0) {
                $hitungVarian = $hitungVarian . ($ind + 1);
            } else {
                $hitungVarian = $hitungVarian . "," . ($ind + 1);
            }
        }
        $data = [
            'title' => 'Tambah Produk',
            'koleksi' => $koleksi,
            'jenis' => $jenis,
            'produk' => $product,
            'hitungVarian' => $hitungVarian
        ];
        return view('admin/edit', $data);
    }
    public function actionEditProduct()
    {
        $idBarang = $this->request->getVar('id');
        $barangCur = $this->barangModel->getBarangAdmin($idBarang);
        $gambarBarangCur = $this->gambarBarangModel->getGambar($idBarang);
        if (!$barangCur) {
            session()->setFlashdata('val-id', 'ID barang tidak ditemukan');
            return redirect()->to('/admin/editproduct')->withInput();
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = $this->request->getVar();
        $data_gambar_mentah = $this->request->getFiles();

        // difilter data gambar mentah krn ada input yg hanya sebagai penambah saja
        reset($data_gambar_mentah);
        $cekInputTerakhir = explode("-", key($data_gambar_mentah))[0] . "-" . explode("-", key($data_gambar_mentah))[1];
        $simpanInd = '';
        $arrIndLast = [];
        foreach ($data_gambar_mentah as $ind_dgm => $dgm) {
            if (explode("-", $ind_dgm)[0] . "-" . explode("-", $ind_dgm)[1] != $cekInputTerakhir) {
                array_push($arrIndLast, $simpanInd);
                $cekInputTerakhir = explode("-", $ind_dgm)[0] . "-" . explode("-", $ind_dgm)[1];
            }
            $simpanInd = $ind_dgm;
        }

        end($data_gambar_mentah);
        array_push($arrIndLast, key($data_gambar_mentah));
        foreach ($arrIndLast as $ai) {
            unset($data_gambar_mentah[$ai]);
        }
        // dd($data_gambar_mentah);

        $data_gambar = [];
        foreach ($data_gambar_mentah as $key => $g) {
            if ($g->isValid()) {
                $data_gambar[$key] = file_get_contents($g);
            } else {
                if ($gambarBarangCur['gambar' . explode("-", $key)[2]] != null) {
                    $data_gambar[$key] = $gambarBarangCur['gambar' . explode("-", $key)[2]];
                }
            }
        }
        $jumlahVarian = explode(",", $this->request->getVar('hitung-varian')); //nilai indeks/urutan varian yg masuk ke backend

        $insertGambarBarang = [];
        $varianData = json_decode($barangCur['varian'], true);

        // dd($data_gambar);

        $counterGambar = 0;
        $varianDataBaru = [];
        foreach ($jumlahVarian as $j) {
            $urutanGambar = [];
            foreach ($data_gambar as $ind_g => $g) {
                if (explode("-", $ind_g)[1] == $j) {
                    $counterGambar++;
                    array_push($urutanGambar, $counterGambar);
                    $insertGambarBarang['gambar' . $counterGambar] = $g;
                }
            }
            $itemVarianBaru = [
                'nama' => $data['nama-var' . $j],
                'kode' => $data['kode-var' . $j],
                'stok' => $data['stok-var' . $j],
                'urutan_gambar' => implode(",", $urutanGambar),
            ];
            array_push($varianDataBaru, $itemVarianBaru);
        }

        $dataKategori = $data['kategori'];
        $koleksiSelected = array_values(array_filter($koleksi, function ($var) use ($dataKategori) {
            return ($var['id'] == $dataKategori);
        }))[0]['nama'];
        $dataSubkategori = $data['subkategori'];
        $jenisSelected = array_values(array_filter($jenis, function ($var) use ($dataSubkategori) {
            return ($var['id'] == $dataSubkategori);
        }))[0]['nama'];

        // $index_data_gambar = array_flip(array_keys($data_gambar));
        // foreach ($data_gambar as $key_dg => $dG) {
        //     $insertGambarBarang['gambar' . ((int)$index_data_gambar[$key_dg] + 1)] = file_get_contents($dG);
        // }
        $insertDataBarang = [
            'nama' => $data['nama'],
            'harga' => $data['harga'],
            'deskripsi' => json_encode([
                'deskripsi' => $data['deskripsi'],
                'dimensi' => [
                    'asli' => [
                        'panjang' => $data['panjang-asli'],
                        'lebar' => $data['lebar-asli'],
                        'tinggi' => $data['tinggi-asli'],
                        'berat' => $data['berat-asli'],
                    ],
                    'paket' => [
                        'panjang' => $data['panjang-paket'],
                        'lebar' => $data['lebar-paket'],
                        'tinggi' => $data['tinggi-paket'],
                        'berat' => $data['berat-paket'],
                    ]
                ],
                'perawatan' => $data['perawatan']
            ]),
            'kategori' => $koleksiSelected,
            'subkategori' => $jenisSelected,
            'diskon' => $data['diskon'],
            'varian' => json_encode($varianDataBaru),
            'shopee' => $data['shopee'],
            'tokped' => $data['tokped'],
            'tiktok' => $data['tiktok'],
            'gambar' => $insertGambarBarang['gambar1']
        ];

        $this->barangModel->where(['id' => $idBarang])->set($insertDataBarang)->update();

        //kosongin semua gambar di db gambar barang
        foreach ($gambarBarangCur as $ind_g => $g) {
            if ($g != null && $ind_g != 'id') {
                $this->gambarBarangModel->where(['id' => $idBarang])->set([$ind_g => null])->update();
                $this->gambarBarang3000Model->where(['id' => $idBarang])->set([$ind_g => null])->update();
            }
        }
        $this->gambarBarangModel->where(['id' => $idBarang])->set($insertGambarBarang)->update();
        $this->gambarBarang3000Model->where(['id' => $idBarang])->set($insertGambarBarang)->update();
        return redirect()->to('admin/product');
    }

    public function mutasiConfirm()
    {
        $mutasi = $this->kartuStokModel->where('alasan !=', '')->findAll();
        foreach ($mutasi as $ind_m => $m) {
            $produk  = $this->barangModel->where(['id' => $m['id_barang']])->first();
            $mutasi[$ind_m]['detail'] = $produk;
            if ($produk) {
                foreach (json_decode($produk['varian'], true) as $v) {
                    if (strtoupper($v['nama']) == strtoupper($m['varian'])) {
                        $mutasi[$ind_m]['stok'] = $v['stok'];
                    }
                }
            } else {
                $mutasi[$ind_m]['stok'] = 0;
            }
        }
        $data = [
            'title' => 'Konfirm Mutasi',
            'mutasi' => $mutasi,
            'msg' => session()->getFlashdata('msg')
        ];
        return view('admin/mutasiConfirm', $data);
    }
    public function accMutasi($id)
    {
        $getMutasi  = $this->kartuStokModel->where(['id' => $id])->first();
        $produk = $this->barangModel->getBarang($getMutasi['id_barang']);
        $varianCurr = json_decode($produk['varian'], true);
        $saldoFix = 0;
        foreach ($varianCurr as $ind_v => $v) {
            if (strtoupper($v['nama']) == strtoupper($getMutasi['varian'])) {
                $saldoSkrg = $v['stok'];
                $saldoFix = $saldoSkrg + ($getMutasi['debit'] ? $getMutasi['debit'] : -$getMutasi['kredit']);
                $varianCurr[$ind_v]['stok'] = $saldoFix;
            }
        }
        if ($saldoFix < 0) {
            session()->setFlashdata('msg', 'Saldo melebihi batas minimal');
            return redirect()->to('/admin/mutasiconfirm');
        }
        $this->kartuStokModel->where(['id' => $id])->set([
            'saldo' => $saldoFix,
            'alasan' => '',
            'pending' => false
        ])->update();
        $this->barangModel->where(['id' => $getMutasi['id_barang']])->set(['varian' => json_encode($varianCurr)])->update();
        return redirect()->to('admin/mutasiconfirm');
    }
    public function denyMutasi($id)
    {
        $this->kartuStokModel->where(['id' => $id])->delete();
        return redirect()->to('admin/mutasiconfirm');
    }

    public function gantiUkuran()
    {
        $barangLama = $this->barangModel->findAll(10, 0);
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

        foreach ($barangLama as $b) {
            $varian = json_decode($b['varian'], true);
            $insertGambarBarang = [];
            $insertGambarBarang3000 = [];
            $insertGambarBarang300 = '';
            foreach ($varian as $v) {
                $urutanGambar = explode(",", $v['urutan_gambar']);
                foreach ($urutanGambar as $u) {
                    $dataGambar = file_get_contents_curl(
                        'https://ilenafurniture.com/viewvar3000/' . $b['id'] . '/' . $u
                    );
                    $fp = 'imgdum/' . $b['id'] . '-' . $u . '.webp';
                    file_put_contents($fp, $dataGambar);

                    \Config\Services::image()
                        ->withFile($fp)
                        ->resize(3000, 3000, true, 'height')->save('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    $insertGambarBarang3000['gambar' . $u] = file_get_contents('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    unlink('imgdum/1' . $b['id'] . '-' . $u . '.webp');

                    \Config\Services::image()
                        ->withFile($fp)
                        ->resize(1000, 1000, true, 'height')->save('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    $insertGambarBarang['gambar' . $u] = file_get_contents('imgdum/1' . $b['id'] . '-' . $u . '.webp');
                    unlink('imgdum/1' . $b['id'] . '-' . $u . '.webp');

                    unlink($fp);
                }
            }

            $dataGambar = file_get_contents_curl(
                'https://ilenafurniture.com/viewpic/' . $b['id']
            );
            $fp = 'imgdum/' . $b['id'] . '.webp';
            file_put_contents($fp, $dataGambar);

            \Config\Services::image()
                ->withFile($fp)
                ->resize(300, 300, true, 'height')->save('imgdum/1' . $b['id']  . '.webp');
            $insertGambarBarang300 = file_get_contents('imgdum/1' . $b['id']  . '.webp');

            unlink($fp);
            unlink('imgdum/1' . $b['id']  . '.webp');

            $this->barangModel->where(['id' => $b['id']])->set(['gambar' => $insertGambarBarang300])->update();
            $this->gambarBarangModel->where(['id' => $b['id']])->set($insertGambarBarang)->update();
            $this->gambarBarang3000Model->where(['id' => $b['id']])->set($insertGambarBarang3000)->update();
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
    }
    public function activeProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        $this->barangModel->where(['id' => $id_product])->set(['active' => $product['active'] == '0' ? '1' : '0'])->update();
        $arr = [
            'pesan' => 'Ok',
        ];
        return $this->response->setJSON($arr, false);
    }
    public function deleteProduct($id_product)
    {
        $produk = $this->barangModel->where('id', $id_product)->delete();
        $gambar = $this->gambarBarangModel->where('id', $id_product)->delete();
        return redirect()->to('admin/product');
    }
    public function order()
    {
        $pesanan = $this->pemesananModel->getPemesanan();
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field1']))
                $pesanan[$ind_p]['data_mid']['custom_field1'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field1']);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field2']))
                $pesanan[$ind_p]['data_mid']['custom_field2'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field2']);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field3']))
                $pesanan[$ind_p]['data_mid']['custom_field3'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field3']);
            $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
        }
        $data = [
            'title' => 'Pesanan',
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan)
        ];
        return view('admin/order', $data);
    }
    public function actionEditResi()
    {
        $nama = $this->request->getVar('nama');
        $deskripsi = $this->request->getVar('deskripsi');
        $resi = $this->request->getVar('resi');
        $idMid = $this->request->getVar('idMid');
        $this->pemesananModel->where(['id_midtrans' => $idMid])->set([
            'kurir' => json_encode([
                'nama' => $nama,
                'deskripsi' => $deskripsi,
            ]),
            'resi' => $resi,
            'status' => 'Dikirim'
        ])->update();
        return redirect()->to('/admin/order');
    }

    public function reprint()
    {
        $ajukanPrint = $this->ajukanPrintModel->findAll();
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($ajukanPrint as $ind_a => $a) {
            $d = strtotime($a['tanggal']);
            $ajukanPrint[$ind_a]['tanggal'] = date("d", $d) . " " . $bulan[(int)date("m", $d) - 1] . " " . date("Y", $d);
        }
        $data = [
            'title' => 'Pengajuan Print Ulang',
            'ajukan' => $ajukanPrint
        ];
        return view('admin/reprint', $data);
    }
    public function accReprint($id_midtrans)
    {
        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'siap'])->update();
        $this->ajukanPrintModel->where(['id_midtrans' => $id_midtrans])->delete();
        $this->kartuStokModel->where(['id_pesanan' => $id_midtrans])->set(['pending' => true])->update();
        return redirect()->to('/admin/reprint');
    }
    public function marketplace()
    {
        $pemesanan = $this->pemesananModel->getPemesananMarket();
        $pemesananBelumKonfirmasi = [];
        foreach ($pemesanan as $p) {
            $pemesananGudang = $this->pemesananGudangModel->where([
                'id_pesanan' => $p['id_midtrans'],
            ])->first();
            if (!$pemesananGudang) {
                $items = json_decode($p['items'], true);
                $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $data_mid = json_decode($p['data_mid'], true);
                $data_mid['transaction_time'] = date('d', strtotime($data_mid['transaction_time'])) . ' ' . $bulan[date('m', strtotime($data_mid['transaction_time'])) - 1] . ' ' . date('Y', strtotime($data_mid['transaction_time']));
                $kurir = json_decode($p['kurir'], true);

                $pemesanan_curr = $p;
                $pemesanan_curr['items'] = $items;
                $pemesanan_curr['data_mid'] = $data_mid;
                $pemesanan_curr['kurir'] = $kurir;

                array_push($pemesananBelumKonfirmasi, $pemesanan_curr);
            }
        }

        $data = [
            'title' => 'Konfirmasi Marketplace',
            'val' => [
                'msg' => session()->getFlashdata('msg')
            ],
            'pemesanan' => $pemesananBelumKonfirmasi,
            'pemesananJson' => json_encode($pemesananBelumKonfirmasi),
        ];
        return view('admin/marketplace', $data);
    }

    public function confirmMarketplace($id)
    {
        $pemesanan = $this->pemesananModel->where([
            'id' => $id,
        ])->first();
        $items_curr = json_decode($pemesanan['items'], true);
        foreach ($items_curr as $i) {
            if ($i['name'] != "Biaya Ongkir" && $i['name'] != "Biaya Admin") {
                for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                    $this->pemesananGudangModel->insert([
                        'id_pesanan' => $pemesanan['id_midtrans'],
                        'tanggal' => json_decode($pemesanan['data_mid'], true)['transaction_time'],
                        'nama' => $i['name'],
                        'id_barang' => $i['id'],
                        'packed' => false
                    ]);
                }
            }
        }
        return redirect()->to('/admin/marketplace');
    }

    public function orderToko($ind_add)
    {
        $email = session()->get('email');
        $nama = session()->get('nama');
        $nohp = session()->get('nohp');
        $keranjang = session()->get('keranjang');
        $alamat = session()->get('alamat')[$ind_add];

        $hargaTotal = 0;
        $items = [];
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                }
            }
            $keranjang[$index]['detail'] = $produk;
            $hargaTotal += $produk['harga'] * $k['jumlah'] * (100 - $produk['diskon']) / 100;

            $item = [
                'id' => $k['id_barang'],
                'price' => $produk['harga'],
                'quantity' => $k['jumlah'],
                'name' => $produk['nama'] . ' (' . $k['varian'] . ')',
            ];
            array_push($items, $item);
        }

        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "TOKO" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "TOKO" . rand();
        $tanggal = date('Y-m-d H:i:s', strtotime('+7 hours'));
        $data = [
            'data_mid'          => json_encode([
                'transaction_time'  => $tanggal,
                'order_id'          => $idFix,
                'gross_amount'      => $hargaTotal,
                'payment_type'      => 'toko'
            ]),
            'id_midtrans'       => $idFix,
            'email'             => $email,
            'kurir'             => json_encode([]),
            'resi'              => 'Menunggu pengiriman',
            'harga'             => $hargaTotal,
            'nama'              => $nama,
            'nohp'              => $nohp,
            'alamat'            => $alamat['alamat_lengkap'],
            'status'            => 'Proses',
            'id_marketplace'    => '',
            'items'             => json_encode($items),
        ];
        $this->pemesananModel->insert($data);

        //insert pesanan gudang
        foreach ($items as $i) {
            if ($i['name'] != "Biaya Ongkir" && $i['name'] != "Biaya Admin") {
                for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                    $this->pemesananGudangModel->insert([
                        'id_pesanan' => $idFix,
                        'tanggal' => $tanggal,
                        'nama' => $i['name'],
                        'id_barang' => $i['id'],
                        'packed' => false
                    ]);
                }
            }
        }

        //pengurangan stok
        $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $idFix)->first();
        $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
        foreach ($dataTransaksiFulDariDatabase_items as $item) {
            $barangCurr = $this->barangModel->where('id', $item['id'])->first();
            $varianBarangCurr = json_decode($barangCurr['varian'], true);
            foreach ($varianBarangCurr as $ind_v => $v) {
                if ($v['nama'] == rtrim(explode("(", $item['name'])[1], ")")) {
                    $varianBarangCurr[$ind_v]['stok'] = (int)$v['stok'] - $item['quantity'];
                }
            }
            $this->barangModel->where('id', $item['id'])->set([
                'varian' => json_encode($varianBarangCurr)
            ])->update();
        }

        return redirect()->to('/order');
    }
}
