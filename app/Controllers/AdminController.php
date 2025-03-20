<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\GambarBarang3000Model;
use App\Models\ArtikelModel;
use App\Models\GambarArtikelModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\PemesananGudangModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\AjukanPrintModel;
use App\Models\KartuStokModel;
use App\Models\GambarHeaderModel;

class AdminController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $gambarHeaderModel;
    protected $gambarBarang3000Model;
    protected $artikelModel;
    protected $gambarArtikelModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $pemesananGudangModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $ajukanPrintModel;
    protected $kartuStokModel;
    protected $session;
    protected $apikey_img_ilena = env('APIKEY_IMG_ILENA', 'DefaultValue'); 
    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->gambarHeaderModel = new GambarHeaderModel();
        $this->gambarBarang3000Model = new GambarBarang3000Model();
        $this->gambarArtikelModel = new GambarArtikelModel();
        $this->artikelModel = new ArtikelModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananGudangModel = new PemesananGudangModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->ajukanPrintModel = new AjukanPrintModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->session = \Config\Services::session();
        $this->apikey_img_ilena = env('APIKEY_IMG_ILENA', 'DefaultValue');
    }
    public function listProduct()
    {
        $product = $this->barangModel->getBarangAdmin();
        $koleksi = $this->koleksiModel->findAll();
        foreach ($product as $index_p => $p) {
            $product[$index_p]['varian'] = json_decode($p['varian'], true);
            $product[$index_p]['allstok'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
                // $product[$index_p]['allstok'] = $product[$index_p]['allstok'] . $v['stok'];
                if ($ind_v == 0) $product[$index_p]['allstok'] .= $v['nama'] . ' : ' . $v['stok'];
                else $product[$index_p]['allstok'] .= "<br>" . $v['nama'] . ' : ' . $v['stok'];
            }
        }
        $data = [
            'title' => 'Produk Kami',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produk' => $product,
            'koleksi' => $koleksi
        ];
        return view('admin/all', $data);
    }
    public function listProductTable()
    {
        $product = $this->barangModel->orderBy('nama', 'asc')->findAll();
        foreach ($product as $index_p => $p) {
            $deskripsiArr = json_decode($p['deskripsi'], true);
            $deskripsi = str_replace('</p>', '', str_replace('<br>', '', str_replace('<p>', '', $deskripsiArr['deskripsi'])));
            $dimensi = 'Dimensi : P(' . $deskripsiArr['dimensi']['asli']['panjang'] . 'mm) x L(' . $deskripsiArr['dimensi']['asli']['lebar'] . 'mm) x T(' . $deskripsiArr['dimensi']['asli']['tinggi'] . 'mm) dengan berat ' . $deskripsiArr['dimensi']['asli']['berat'] . 'kg';
            $product[$index_p]['deskripsi_nonhtml'] = $deskripsi . ' ' . $dimensi;
            $product[$index_p]['gambar'] = 'https://ilenafurniture.com/viewpichover/' . $p['id'];
            $product[$index_p]['varian'] = json_decode($p['varian'], true);
            $product[$index_p]['stok_total'] = 0;
            $product[$index_p]['warna'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
                $product[$index_p]['stok_total'] += $v['stok'];
                $product[$index_p]['warna'] .= ($ind_v == 0 ? '' : '/') . ucwords(strtolower($v['nama']));
            }
        }
        $data = [
            'title' => 'Produk Kami ',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produk' => $product,
        ];
        return view('admin/allTable', $data);
    }
    public function addProduct()
    {
        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = [
            'title' => 'Tambah Produk',
            'apikey_img_ilena' => $this->apikey_img_ilena,
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

        // $getFileGambarHover = $data_gambar_mentah['gambar_hover']->isValid() ? file_put_contents('img/barang/hover/'. $data['id'].'.webp' ,$data_gambar_mentah['gambar_hover']) : null;

        //gambar hover
        if ($data_gambar_mentah['gambar_hover']->isValid()) {
            $fp = 'imgdum/barang/hover';
            $data_gambar_mentah['gambar_hover']->move($fp, $data['id'] . '.webp');
            \Config\Services::image()
                ->withFile($fp)
                ->resize(300, 300, true, 'height')->save('img/barang/hover/' . $data['id'] . '.webp');
            unlink($fp . '/' . $data['id'] . '.webp');
        }

        unset($data_gambar_mentah['gambar_hover']);

        $data_gambar = [];
        foreach ($data_gambar_mentah as $key => $g) {
            if ($g->isValid()) $data_gambar[$key] = $g;
        }
        $jumlahVarian = explode(",", $this->request->getVar('hitung-varian'));

        // $insertGambarBarang = [
        //     'id' => $data['id']
        // ];
        // $insertGambarBarang3000 = [
        //     'id' => $data['id']
        // ];
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

        // $index_data_gambar = array_flip(array_keys($data_gambar));
        $iterasi = 0;
        foreach ($data_gambar as $key_dg => $dG) {
            $dG->move('imgdum');
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(3000, 3000, true, 'height')->save('img/barang/3000/' . $dG->getName() . '.webp');
            // $insertGambarBarang3000['gambar' . ((int)$index_data_gambar[$key_dg] + 1)] = file_get_contents('imgdum/1' . $dG->getName());
            // unlink('imgdum/1' . $dG->getName());

            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(1000, 1000, true, 'height')->save('img/barang/1000/' . $dG->getName() . '.webp');
            // $insertGambarBarang['gambar' . ((int)$index_data_gambar[$key_dg] + 1)] = file_get_contents('imgdum/1' . $dG->getName());
            // unlink('imgdum/1' . $dG->getName());

            if ($iterasi <= 0) {
                \Config\Services::image()
                    ->withFile('imgdum/' . $dG->getName())
                    ->resize(300, 300, true, 'height')->save('img/barang/300/' . $dG->getName() . '.webp');
                // $insertGambarBarang300 = file_get_contents('imgdum/1' . $dG->getName());
                // unlink('imgdum/1' . $dG->getName());
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
            'gambar' => null,
            'gambar_hover' => null,
            'ruang_tamu' => isset($data['ruang_tamu']) ? '1' : '0',
            'ruang_keluarga' => isset($data['ruang_keluarga']) ? '1' : '0',
            'ruang_tidur' => isset($data['ruang_tidur']) ? '1' : '0',
        ];
        $this->barangModel->insert($insertDataBarang);
        // $this->gambarBarangModel->insert($insertGambarBarang);
        // $this->gambarBarang3000Model->insert($insertGambarBarang3000);
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
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'koleksi' => $koleksi,
            'jenis' => $jenis,
            'produk' => $product,
            'hitungVarian' => $hitungVarian
        ];
        return view('admin/edit', $data);
    }
    public function actionEditProduct($pathname = false)
    {
        $idBarang = $this->request->getVar('id');
        $barangCur = $this->barangModel->getBarangAdmin($idBarang);
        $gambarBarangCur = $this->gambarBarangModel->getGambar($idBarang);
        if (!$barangCur) {
            session()->setFlashdata('val-id', 'ID barang tidak ditemukan');
            return redirect()->to('/admin/editproduct/' . $idBarang)->withInput();
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = $this->request->getVar();
        $data_gambar_mentah = $this->request->getFiles();

        $getFileGambarHover = $data_gambar_mentah['gambar_hover']->isValid() ? file_get_contents($data_gambar_mentah['gambar_hover']) : $barangCur['gambar_hover'];
        unset($data_gambar_mentah['gambar_hover']);

        // difilter data gambar mentah krn ada input yg hanya sebagai penambah saja
        reset($data_gambar_mentah);
        $cekInputTerakhir = explode("-", key($data_gambar_mentah))[0] . "-" . explode("-", key($data_gambar_mentah))[1];

        // dd($cekInputTerakhir);
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
        $jmlUrutanGambar = [];
        $sumJml = 0;
        foreach (json_decode($barangCur['varian'], true) as $v) {
            $sumJml += count(explode(',', $v['urutan_gambar']));
            array_push($jmlUrutanGambar, $sumJml);
        }
        $cekcekek = [];
        foreach ($data_gambar_mentah as $key => $g) {
            if ($g->isValid()) {
                $data_gambar[$key] = file_get_contents($g);
            } else {
                // if ($gambarBarangCur['gambar' . explode("-", $key)[2]] != null) {
                //     $data_gambar[$key] = $gambarBarangCur['gambar' . explode("-", $key)[2]];
                // }
                if ((int)explode("-", $key)[1] == 1) {
                    if ($gambarBarangCur['gambar' . explode("-", $key)[2]] != null) {
                        array_push($cekcekek, 'gambar' . explode("-", $key)[2]);
                        $data_gambar[$key] = $gambarBarangCur['gambar' . explode("-", $key)[2]];
                    }
                } else {
                    array_push($cekcekek, 'gambar' . ((int)explode("-", $key)[2] + $jmlUrutanGambar[(int)explode("-", $key)[1] - 2]));
                    if ($gambarBarangCur['gambar' . ((int)explode("-", $key)[2] + $jmlUrutanGambar[(int)explode("-", $key)[1] - 2])] != null) {
                        $data_gambar[$key] = $gambarBarangCur['gambar' . ((int)explode("-", $key)[2] + $jmlUrutanGambar[(int)explode("-", $key)[1] - 2])];
                    }
                }
            }
        }
        $jumlahVarian = explode(",", $this->request->getVar('hitung-varian')); //nilai indeks/urutan varian yg masuk ke backend
        // dd([
        //     'jmlUrutan' => $jmlUrutanGambar,
        //     'dataGambar' => $data_gambar,
        //     'getvar' => $data_gambar_mentah,
        //     'cekcekcek' => $cekcekek
        // ]);
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
            'gambar' => $insertGambarBarang['gambar1'],
            'gambar_hover' => $getFileGambarHover,
            'ruang_tamu' => isset($data['ruang_tamu']) ? '1' : '0',
            'ruang_keluarga' => isset($data['ruang_keluarga']) ? '1' : '0',
            'ruang_tidur' => isset($data['ruang_tidur']) ? '1' : '0',
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
        return redirect()->to($pathname ? str_replace('@', '/', $pathname) : 'admin/product');
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
            'apikey_img_ilena' => $this->apikey_img_ilena,
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

    public function gantiUkuran($koleksi, $jenis)
    {
        // $barangLama = $this->barangModel->findAll(10, 0);
        $barangLama = $this->barangModel->where(['kategori' => $koleksi, 'subkategori' => $jenis])->findAll();
        if (count($barangLama) <= 0) {
            return $this->response->setJSON(['message' => 'barang nggk nemu'], false);
        }
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
        $gambar3000 = $this->gambarBarang3000Model->where('id', $id_product)->delete();
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
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan)
        ];
        return view('admin/order', $data);
    }
    public function orderAdd()
    {
        $produk = $this->barangModel->findAll();
        foreach ($produk as $ind_p => $p) {
            $produk[$ind_p]['gambar'] = '/viewpic/' . $p['id'];
            $produk[$ind_p]['gambar_hover'] = '';
            $produk[$ind_p]['dimensi'] =  json_decode($p['deskripsi'], true)['dimensi']['asli'];
            $produk[$ind_p]['deskripsi'] =  '';
            $produk[$ind_p]['varian'] =  json_decode($p['varian'], true);
            $produk[$ind_p]['shopee'] = '';
            $produk[$ind_p]['tokped'] = '';
            $produk[$ind_p]['tiktok'] = '';
            $produk[$ind_p]['nama'] = ucwords($p['nama']);
        }

        //Dapatkan data provinsi
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "key: 6bc9315fb7a163e74a04f9f54ede3c2c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $provinsi = json_decode($response, true);
        // dd($provinsi);

        $data = [
            'title' => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produkJson' => json_encode($produk),
            'provinsi' => $provinsi['rajaongkir']['results']
        ];
        return view('admin/orderAdd', $data);
    }
    public function actionOrderAdd()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        $keranjang = $body['keranjang'];

        $items = [];
        foreach ($body['keranjang'] as $k) {
            array_push($items, [
                'id' => $k['id'],
                'name' => $k['name'],
                'quantity' => $k['quantity'],
                'price' => $k['price'],
            ]);
        }
        $hargaTotal = $body['hargaTotal'];
        $waktu = $body['waktu'] ? str_replace("T", " ", $body['waktu']) : date("Y-m-d H:i:s", strtotime(('+7 Hours')));
        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "AD" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1)) . '';
        $randomId = "AD" . rand();
        foreach ($keranjang as $k) {
            //kartu stok ditambahkan
            $varian = $k['detail']['varian'];
            $saldo = (int)$varian['stok'];
            $tanggalNoStrip = date("YmdHis", strtotime("+7 hours"));
            $this->kartuStokModel->insert([
                'id_barang' => $k['id'],
                'tanggal' => $waktu,
                'keterangan' => $tanggalNoStrip . "-" . $k['id'] . "-" . strtoupper($varian['nama']) . "-" . $idFix,
                'debit' => 0,
                'kredit' => $k['quantity'],
                'saldo' => $saldo,
                'pending' => true,
                'id_pesanan' => $idFix,
                'varian' => strtoupper($varian['nama'])
            ]);
        }
        $data = [
            'data_mid'          => json_encode([
                'transaction_time' => $waktu,
                'order_id'         => $idFix,
                'gross_amount'     => $hargaTotal,
                'payment_type'     => 'admin'
            ]),
            'id_midtrans'       => $idFix,
            'email'             => $body['email'],
            'nohp'              => $body['nohp'],
            'alamat'            => $body['alamatLengkap'],
            'nama'              => $body['nama'],
            'kurir'             => json_encode([
                'nama' => 'Menyesuaikan',
                'deskripsi' => 'Menyesuaikan',
                'harga' => 'Menyesuaikan',
                'estimasi' => 'Menyesuaikan'
            ]),
            'resi'              => 'Menunggu pengiriman',
            'status'            => 'Proses',
            'id_marketplace'    => '',
            'items'             => json_encode($items),
            'status_print'      => 'siap',
            'keterangan_suratjalan' => $body['keteranganSJ']
        ];
        $this->pemesananModel->insert($data);
        return $this->response->setStatusCode(200)->setJSON(['success' => true], false);
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
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
            'apikey_img_ilena' => $this->apikey_img_ilena,
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
            if ($i['name'] != "Voucher" && $i['name'] != "Biaya Admin") {
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
                'name' => $produk['nama'] . " " . json_decode($produk['deskripsi'], true)['dimensi']['asli']['panjang'] . ' (' . $k['varian'] . ')',
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

    public function labelBarang($id)
    {
        $pemesanan = $this->pemesananModel->where(['id_midtrans' => $id])->first();
        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $data = [
            'title' => 'Label Barang',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan
        ];
        return view('admin/labelBarang', $data);
    }

    // ARTIKEL
    public function article()
    {
        $artikel = $this->artikelModel->orderBy('waktu', 'desc')->findAll();
        $data = [
            'title' => 'Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => $artikel,
            'msg' => session()->getFlashdata('msg')
        ];
        return view('admin/artikel', $data);
    }
    public function articleCategory($kategori)
    {
        $artikel = $this->artikelModel->getArtikelKategori(str_replace("-", " ", $kategori));
        $bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        if (!$artikel) return redirect()->to('article');
        foreach ($artikel as $ind_a => $a) {
            $artikel[$ind_a]['header'] = '/imgart/' . $a['id'];
            $artikel[$ind_a]['isi'] = json_decode($a['isi'], true);
            $artikel[$ind_a]['kategori'] = explode(",", $a['kategori']);
            $artikel[$ind_a]['waktu'] = date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu']));
        }
        $data = [
            'title' => 'Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => $artikel
        ];
        return view('pages/artikelAll', $data);
    }
    // END ARTIKEL

    // LANJUTAN ARTIKEL
    public function addArticle()
    {
        $galeri = $this->gambarArtikelModel->findAll();
        $tinymce_key = env('TINYMCE_KEY', 'DefaultValue');
        $data = [
            'title' => 'Tambah Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'galeri' => $galeri,
            'tinyMCE' => $tinymce_key
        ];
        return view('admin/addArtikel', $data);
    }
    public function actionAddGaleriArticle()
    {
        $gambar = $this->request->getFile('file');
        $id = date(strtotime("+7 hours"));
        if ($gambar->isValid()) {
            $fp = 'img/artikel';
            $gambar->move($fp, $id . '.webp');
        }
        $this->gambarArtikelModel->insert([
            'id' => $id,
            'url' => 'img/artikel/' . $id . '.webp'
        ]);
        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'url' => 'img/artikel/' . $id . '.webp'
        ], false);
    }
    public function actionAddArticle()
    {
        $judul = $this->request->getVar('judul');
        $path = str_replace(",", "", $judul);
        $path = str_replace(".", "", $path);
        $path = str_replace("& ", "", $path);
        $path = str_replace("?", "", $path);
        $path = str_replace(" ", "-", $path);
        $path = strtolower($path);
        $this->artikelModel->insert([
            'id' => "A" . date("YmdHis", strtotime("+7 Hours")),
            'judul' => $judul,
            'path' => $path,
            'penulis' => $this->request->getVar('penulis'),
            'keywords' => $this->request->getVar('keywords'),
            'kategori' => strtolower($this->request->getVar('kategori')),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'waktu' => $this->request->getVar('waktu'),
            'isi' => $this->request->getVar('isi'),
            'header' => $this->request->getVar('header'),
            'suka' => 0,
            'bagikan' => 0,
            'komen' => json_encode([]),
        ]);
        session()->setFlashdata('msg', 'Artikel berhasil ditambahkan');
        return redirect()->to('/admin/article');
    }

    public function deleteArticle($id)
    {
        $this->artikelModel->where(['id' => $id])->delete();
        session()->setFlashdata('msg', 'Artikel berhasil dihapus');
        return redirect()->to('/admin/article');
    }
    public function editArticle($id)
    {
        $artikel = $this->artikelModel->where(['id' => $id])->first();
        $galeri = $this->gambarArtikelModel->findAll();
        $tinymce_key = env('TINYMCE_KEY', 'DefaultValue');
        $data = [
            'title' => 'Edit Artikel',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'artikel' => $artikel,
            'galeri' => $galeri,
            'tinyMCE' => $tinymce_key
        ];
        return view('admin/editArtikel', $data);
    }
    public function actionEditArticle($id)
    {
        $judul = $this->request->getVar('judul');
        $path = str_replace(",", "", $judul);
        $path = str_replace(".", "", $path);
        $path = str_replace("& ", "", $path);
        $path = str_replace("?", "", $path);
        $path = str_replace(" ", "-", $path);
        $path = strtolower($path);
        $this->artikelModel->where(['id' => $id])->set([
            'judul' => $judul,
            'path' => $path,
            'penulis' => $this->request->getVar('penulis'),
            'keywords' => $this->request->getVar('keywords'),
            'kategori' => strtolower($this->request->getVar('kategori')),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'waktu' => $this->request->getVar('waktu'),
            'isi' => $this->request->getVar('isi'),
            'header' => $this->request->getVar('header'),
        ])->update();
        session()->setFlashdata('msg', 'Artikel berhasil diedit');
        return redirect()->to('/admin/article');
    }

    public function homeLayout()
    {
        $gambarHeader = [
            'url1' => $this->gambarHeaderModel->where(['id' => 1])->first()['url'],
            'url2' => $this->gambarHeaderModel->where(['id' => 2])->first()['url'],
            'url3' => $this->gambarHeaderModel->where(['id' => 3])->first()['url'],
            'url4' => $this->gambarHeaderModel->where(['id' => 4])->first()['url'],
        ];
        $data = [
            'title' => 'Home Layout',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'msg' => session()->getFlashdata('msg'),
            'gambarHeader' => $gambarHeader
        ];
        return view('admin/homeLayout', $data);
    }

    public function actionHomeLayout()
    {
        $image1 = $this->request->getFile('image1');
        $image1hp = $this->request->getFile('image1-hp');
        $url1 = $this->request->getVar('url1');

        $image2 = $this->request->getFile('image2');
        $image2hp = $this->request->getFile('image2-hp');
        $url2 = $this->request->getVar('url2');

        $image3 = $this->request->getFile('image3');
        $image3hp = $this->request->getFile('image3-hp');
        $url3 = $this->request->getVar('url3');

        $image4 = $this->request->getFile('image4');
        $image4hp = $this->request->getFile('image4-hp');
        $url4 = $this->request->getVar('url4');

        $dataUpdate = [];
        if ($image1->isValid()) $dataUpdate['foto'] = file_get_contents($image1);
        if ($image1hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image1hp);
        $dataUpdate['url'] = $url1 ? $url1 : null;
        $this->gambarHeaderModel->where(['id' => 1])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($image2->isValid()) $dataUpdate['foto'] = file_get_contents($image2);
        if ($image2hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image2hp);
        $dataUpdate['url'] = $url2 ? $url2 : null;
        $this->gambarHeaderModel->where(['id' => 2])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($image3->isValid()) $dataUpdate['foto'] = file_get_contents($image3);
        if ($image3hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image3hp);
        $dataUpdate['url'] = $url3 ? $url3 : null;
        $this->gambarHeaderModel->where(['id' => 3])->set($dataUpdate)->update();

        $dataUpdate = [];
        if ($image4->isValid()) $dataUpdate['foto'] = file_get_contents($image4);
        if ($image4hp->isValid()) $dataUpdate['foto_hp'] = file_get_contents($image4hp);
        $dataUpdate['url'] = $url4 ? $url4 : null;
        $this->gambarHeaderModel->where(['id' => 4])->set($dataUpdate)->update();

        session()->setFlashdata('msg', 'Home layout telah diperbarui');
        return redirect()->to('/admin/homelayout');
    }

    public function suratJalan($id_midtrans)
    {
        $pemesanan = $this->pemesananModel->getPemesanan($id_midtrans);
        $d = strtotime("+7 hours");
        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'sudah print'])->update();
        $items = [];
        if ($pemesanan['status_print'] == 'siap') {
            foreach (json_decode($pemesanan['items'], true) as $p) {
                if ($p['name'] != 'Biaya Ongkir' && $p['name'] != 'Biaya Admin' && $p['name'] != 'Voucher' && strtolower($p['name']) != 'flash sale' && !str_contains(strtolower($p['name']), 'potongan')) {
                    $produknya = $this->barangModel->getBarang($p['id']);
                    $varian = json_decode($produknya['varian'], true);
                    $saldo = 0;
                    foreach ($varian as $ind_v => $v) {
                        if (strtolower($v['nama']) == strtolower(rtrim(explode("(", $p['name'])[1], ")"))) {
                            $saldo = (int)$v['stok'];
                        }
                    }
                    // $this->kartuStokModel->insert([
                    //     'id_barang' => $p['id'],
                    //     'tanggal' => $tanggal,
                    //     'keterangan' => $tanggalNoStrip . "-" . $p['id'] . "-" . strtoupper(rtrim(explode("(", $p['name'])[1], ")")) . "-" . $pemesanan['id_midtrans'],
                    //     'debit' => 0,
                    //     'kredit' => $p['quantity'],
                    //     'saldo' => (int)$saldo - (int)$p['quantity'],
                    // ]);
                    $this->kartuStokModel->where(['id_barang' => $p['id'], 'tanggal' => json_decode($pemesanan['data_mid'], true)['transaction_time']])->set([
                        'pending' => false,
                        'saldo' => (int)$saldo - (int)$p['quantity'],
                    ])->update();
                    array_push($items, $p);
                }
            }
        }

        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $pemesanan['kurir'] = json_decode($pemesanan['kurir'], true);
        $pemesanan['data_mid'] = json_decode($pemesanan['data_mid'], true);
        foreach ($pemesanan['items'] as $ind_i => $item) {
            if ($item['name'] != 'Voucher' && $item['name'] != 'Biaya Admin' && $item['name'] != 'Biaya Ongkir') {
                $varianItem = rtrim(explode("(", $item['name'])[1], ")");
                $produkCurr = $this->barangModel->getBarang($item['id']);
                $dimensi = json_decode($produkCurr['deskripsi'], true)['dimensi']['asli'];
                $pemesanan['items'][$ind_i]['name'] = $produkCurr['nama'] . ", " . $dimensi['panjang'] . "x" . $dimensi['lebar'] . "x" . $dimensi['tinggi'] . "<br>" . $varianItem;
                array_push($items, $item);
            }
        }
        $bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $data = [
            'title' => 'Surat Jalan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan,
            'tanggal' => date("d", $d) . " " . $bulan[(int)date("m", $d) - 1] . " " . date("Y", $d),
            'items' => $items
        ];
        return view('gudang/suratJalan', $data);
    }

    public function changePic()
    {
        $barangLama = $this->barangModel
            ->select('id')
            ->select('nama')
            ->select('deskripsi')
            ->findAll();
        foreach ($barangLama as $ind_b => $b) {
            $barangLama[$ind_b]['dimensi'] = json_decode($b['deskripsi'], true)['dimensi']['asli'];
            $barangLama[$ind_b]['deskripsi'] = '';
        }
        $data = [
            'title' => 'Ganti Gambar',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'barang' => $barangLama,
            'barangJson' => json_encode($barangLama)
        ];
        return view('admin/gantiGambar', $data);
    }
}