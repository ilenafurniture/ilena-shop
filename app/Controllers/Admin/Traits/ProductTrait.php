<?php
/**
 * ProductTrait - Handles all product-related methods
 * 
 * Methods:
 * - listProduct(): Display product list
 * - listProductTable(): Display product table view
 * - addProduct(): Show add product form
 * - actionAddProduct(): Process add product
 * - editProduct($id): Show edit product form
 * - actionEditProduct($id): Process edit product
 * - actionEditProductOld($path): Legacy edit handler
 * - activeProduct($id): Toggle product active status
 * - deleteProduct($id): Delete product
 * - gantiUkuran($koleksi, $jenis): Resize images (internal)
 */

namespace App\Controllers\Admin\Traits;

trait ProductTrait
{
    /**
     * Display product list with variants
     */
    public function listProduct()
    {
        $product = $this->barangModel->getBarangAdmin();
        $koleksi = $this->koleksiModel->findAll();
        foreach ($product as $index_p => $p) {
            $product[$index_p]['varian'] = json_decode($p['varian'], true);
            $product[$index_p]['allstok'] = '';
            foreach ($product[$index_p]['varian'] as $ind_v => $v) {
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

    /**
     * Display product table view
     */
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

    /**
     * Show add product form
     */
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
            'produk' => false,
            'val' => [
                'msg' => session()->getFlashdata('val-id'),
            ]
        ];
        return view('admin/add', $data);
    }

    /**
     * Process add product
     */
    public function actionAddProduct()
    {
        if (!$this->validate([
            'id' => [
                'rules'  => 'required|is_unique[barang.id]',
                'errors' => [
                    'required'  => 'Id harus diisi',
                    'is_unique' => 'Id sudah terdaftar',
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            session()->setFlashdata('val-id', $validation->getError('id'));
            return redirect()->to('/admin/addproduct')->withInput();
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis   = $this->jenisModel->getJenis();

        $data               = $this->request->getVar();
        $data_gambar_mentah = $this->request->getFiles();

        if (isset($data_gambar_mentah['gambar_hover']) && $data_gambar_mentah['gambar_hover']->isValid()) {
            $fp = 'imgdum/barang/hover';
            $data_gambar_mentah['gambar_hover']->move($fp, $data['id'] . '.webp');
            if (file_exists('img/barang/hover/' . $data['id'] . '.webp')) {
                unlink('img/barang/hover/' . $data['id'] . '.webp');
            }
            \Config\Services::image()
                ->withFile($fp . '/' . $data['id'] . '.webp')
                ->resize(300, 300, true, 'height')
                ->save('img/barang/hover/' . $data['id'] . '.webp');
            @unlink($fp . '/' . $data['id'] . '.webp');
        }
        unset($data_gambar_mentah['gambar_hover']);

        foreach (json_decode($data['varian'], true) as $varian) {
            $tanggalNoStrip = date("YmdHis", strtotime("+7 Hours"));
            $this->kartuStokModel->insert([
                'id_barang'   => $data['id'],
                'tanggal'     => date("Y-m-d H:i:s", strtotime("+7 Hours")),
                'keterangan'  => $tanggalNoStrip . "-" . $data['id'] . "-" . strtoupper($varian['nama']) . "-ADDPRODUCT",
                'debit'       => $varian['stok'],
                'kredit'      => 0,
                'saldo'       => $varian['stok'],
                'pending'     => false,
                'id_pesanan'  => 'ADDPRODUCT',
                'varian'      => strtoupper($varian['nama'])
            ]);
        }

        $koleksiSelected = '';
        if (!empty($data['kategori'])) {
            $row = array_values(array_filter($koleksi, fn($v) => $v['id'] == $data['kategori']))[0] ?? null;
            $koleksiSelected = $row ? $row['nama'] : '';
        }

        $jenisSelected = '';
        if (!empty($data['subkategori'])) {
            $row = array_values(array_filter($jenis, fn($v) => $v['id'] == $data['subkategori']))[0] ?? null;
            $jenisSelected = $row ? $row['nama'] : '';
        }

        foreach ($data_gambar_mentah as $ind_g => $dG) {
            $urutan = (int)explode('_', $ind_g)[1];
            $dG->move('imgdum');

            if (file_exists('img/barang/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp')) {
                unlink('img/barang/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');
            }
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(3000, 3000, true, 'height')
                ->save('img/barang/3000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');

            if (file_exists('img/barang/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp')) {
                unlink('img/barang/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');
            }
            \Config\Services::image()
                ->withFile('imgdum/' . $dG->getName())
                ->resize(1000, 1000, true, 'height')
                ->save('img/barang/1000/' . $data['id'] . '-' . ($urutan + 1) . '.webp');

            if ($urutan <= 0) {
                if (file_exists('img/barang/300/' . $data['id'] . '.webp')) {
                    unlink('img/barang/300/' . $data['id'] . '.webp');
                }
                \Config\Services::image()
                    ->withFile('imgdum/' . $dG->getName())
                    ->resize(300, 300, true, 'height')
                    ->save('img/barang/300/' . $data['id'] . '.webp');
            }
            @unlink('imgdum/' . $dG->getName());
        }

        // Jadwal diskon
        $pakaiSchedule = !empty($this->request->getVar('pakai_jadwal_diskon')) ? 1 : 0;
        $mulaiRaw      = (string) $this->request->getVar('diskon_mulai');
        $selesaiRaw    = (string) $this->request->getVar('diskon_selesai');

        $insertDataBarang = [
            'id'            => $data['id'],
            'nama'          => $data['nama'],
            'harga'         => $data['harga'],
            'pencarian'     => '',
            'rate'          => '0',
            'deskripsi'     => $data['deskripsi'],
            'kategori'      => $koleksiSelected,
            'subkategori'   => $jenisSelected,
            'diskon'        => $data['diskon'],
            'varian'        => $data['varian'],
            'shopee'        => $data['shopee'],
            'tokped'        => $data['tokped'],
            'tiktok'        => $data['tiktok'],
            'active'        => '1',
            'ruang_tamu'    => $data['ruang_tamu'],
            'ruang_keluarga'=> $data['ruang_keluarga'],
            'ruang_tidur'   => $data['ruang_tidur'],
            'pakai_jadwal_diskon' => $pakaiSchedule,
            'diskon_mulai'        => $pakaiSchedule && $mulaiRaw
                                        ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $mulaiRaw))) : null,
            'diskon_selesai'      => $pakaiSchedule && $selesaiRaw
                                        ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $selesaiRaw))) : null,
        ];

        $this->barangModel->insert($insertDataBarang);

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'dataYgDiInsertKeBarang' => $insertDataBarang,
                'pesan' => 'Berhasil menambahkan produk ' . $data['nama']
            ]);
    }

    /**
     * Show edit product form
     */
    public function editProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan');
        }

        $product['deskripsi'] = json_decode($product['deskripsi'] ?? '[]', true);
        $product['varian']    = json_decode($product['varian'] ?? '[]', true);

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis   = $this->jenisModel->getJenis();

        unset($product['gambar'], $product['tgl_update'], $product['active']);

        $product['ruang_tamu']     = ($product['ruang_tamu'] ?? '0') == '1';
        $product['ruang_keluarga'] = ($product['ruang_keluarga'] ?? '0') == '1';
        $product['ruang_tidur']    = ($product['ruang_tidur'] ?? '0') == '1';

        $katRow = $this->koleksiModel->where('nama', $product['kategori'] ?? '')->first();
        $jenRow = $this->jenisModel->where('nama', $product['subkategori'] ?? '')->first();
        $product['kategori']    = $katRow['id'] ?? '';
        $product['subkategori'] = $jenRow['id'] ?? '';

        $product['pakai_jadwal_diskon'] = (int)($product['pakai_jadwal_diskon'] ?? 0);
        $product['diskon_mulai']        = $product['diskon_mulai']   ?? null;
        $product['diskon_selesai']      = $product['diskon_selesai'] ?? null;

        $data = [
            'title'            => 'Tambah Produk',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'jenisJson'        => json_encode($jenis),
            'koleksiJson'      => json_encode($koleksi),
            'koleksi'          => $koleksi,
            'jenis'            => $jenis,
            'produk'           => $product,
            'produkJson'       => json_encode($product),
            'idProduct'        => $id_product,
            'val'              => ['msg' => session()->getFlashdata('val-id')],
        ];

        return view('admin/add', $data);
    }

    /**
     * Process edit product
     */
    public function actionEditProduct($id_product)
    {
        try {
            $barang = $this->barangModel->getBarangAdmin($id_product);
            if (!$barang) {
                return $this->response->setStatusCode(404)->setJSON(['pesan' => 'Produk tidak ditemukan']);
            }

            $data  = $this->request->getVar();
            $files = $this->request->getFiles();

            $ensureDir = function (string $dir) {
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                if (!is_writable($dir)) { @chmod($dir, 0775); }
            };
            $ensureDir('imgdum');
            $ensureDir('imgdum/barang/hover');
            $ensureDir('img/barang/300');
            $ensureDir('img/barang/1000');
            $ensureDir('img/barang/3000');
            $ensureDir('img/barang/hover');

            if (isset($files['gambar_hover']) && $files['gambar_hover'] && $files['gambar_hover']->isValid()) {
                try {
                    $tmpPath = 'imgdum/barang/hover';
                    $files['gambar_hover']->move($tmpPath, $id_product . '.webp');

                    @unlink('img/barang/hover/' . $id_product . '.webp');
                    \Config\Services::image()
                        ->withFile($tmpPath . '/' . $id_product . '.webp')
                        ->resize(300, 300, true, 'height')
                        ->save('img/barang/hover/' . $id_product . '.webp');

                    @unlink($tmpPath . '/' . $id_product . '.webp');
                } catch (\Throwable $e) {
                    log_message('error', 'EDIT hover gagal: {msg}', ['msg' => $e->getMessage()]);
                }
            }

            if (!empty($files)) {
                foreach ($files as $field => $file) {
                    if ($field === 'gambar_hover') continue;
                    if (strpos($field, 'gambar_') !== 0) continue;
                    if (!$file || !$file->isValid()) continue;

                    try {
                        $parts  = explode('_', $field);
                        $urutan = isset($parts[1]) ? (int)$parts[1] : 0;

                        $file->move('imgdum');

                        @unlink("img/barang/3000/{$id_product}-" . ($urutan + 1) . ".webp");
                        \Config\Services::image()
                            ->withFile('imgdum/' . $file->getName())
                            ->resize(3000, 3000, true, 'height')
                            ->save("img/barang/3000/{$id_product}-" . ($urutan + 1) . ".webp");

                        @unlink("img/barang/1000/{$id_product}-" . ($urutan + 1) . ".webp");
                        \Config\Services::image()
                            ->withFile('imgdum/' . $file->getName())
                            ->resize(1000, 1000, true, 'height')
                            ->save("img/barang/1000/{$id_product}-" . ($urutan + 1) . ".webp");

                        if ($urutan <= 0) {
                            @unlink("img/barang/300/{$id_product}.webp");
                            \Config\Services::image()
                                ->withFile('imgdum/' . $file->getName())
                                ->resize(300, 300, true, 'height')
                                ->save("img/barang/300/{$id_product}.webp");
                        }

                        @unlink('imgdum/' . $file->getName());
                    } catch (\Throwable $e) {
                        log_message('error', 'EDIT varian gambar gagal ({field}): {msg}', [
                            'field' => $field,
                            'msg'   => $e->getMessage()
                        ]);
                    }
                }
            }

            $kategoriNama    = $barang['kategori'];
            $subkategoriNama = $barang['subkategori'];

            if (!empty($data['kategori'])) {
                $koleksiRow = $this->koleksiModel->where('id', $data['kategori'])->first();
                if ($koleksiRow) $kategoriNama = $koleksiRow['nama'];
            }
            if (!empty($data['subkategori'])) {
                $jenisRow = $this->jenisModel->where('id', $data['subkategori'])->first();
                if ($jenisRow) $subkategoriNama = $jenisRow['nama'];
            }

            $deskripsi = $data['deskripsi'] ?? $barang['deskripsi'];
            if (is_array($deskripsi)) $deskripsi = json_encode($deskripsi);
            $varian = $data['varian'] ?? $barang['varian'];
            if (is_array($varian)) $varian = json_encode($varian);

            $boolTo01 = fn($v) => (is_bool($v) ? ($v ? '1' : '0') : (string)$v);

            $payload = [
                'nama'           => $data['nama']        ?? $barang['nama'],
                'harga'          => $data['harga']       ?? $barang['harga'],
                'pencarian'      => $barang['pencarian'] ?? '',
                'deskripsi'      => $deskripsi,
                'kategori'       => $kategoriNama,
                'subkategori'    => $subkategoriNama,
                'diskon'         => $data['diskon']      ?? $barang['diskon'],
                'varian'         => $varian,
                'shopee'         => $data['shopee']      ?? ($barang['shopee'] ?? ''),
                'tokped'         => $data['tokped']      ?? ($barang['tokped'] ?? ''),
                'tiktok'         => $data['tiktok']      ?? ($barang['tiktok'] ?? ''),
                'ruang_tamu'     => $boolTo01($data['ruang_tamu']     ?? ($barang['ruang_tamu'] ?? '0')),
                'ruang_keluarga' => $boolTo01($data['ruang_keluarga'] ?? ($barang['ruang_keluarga'] ?? '0')),
                'ruang_tidur'    => $boolTo01($data['ruang_tidur']    ?? ($barang['ruang_tidur'] ?? '0')),
            ];

            // Jadwal diskon
            if ($this->request->getVar('pakai_jadwal_diskon') !== null) {
                $pakaiSchedule = !empty($this->request->getVar('pakai_jadwal_diskon')) ? 1 : 0;
                $mulaiRaw      = (string) $this->request->getVar('diskon_mulai');
                $selesaiRaw    = (string) $this->request->getVar('diskon_selesai');

                $payload['pakai_jadwal_diskon'] = $pakaiSchedule;
                $payload['diskon_mulai']   = $pakaiSchedule && $mulaiRaw
                    ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $mulaiRaw))) : null;
                $payload['diskon_selesai'] = $pakaiSchedule && $selesaiRaw
                    ? date('Y-m-d H:i:s', strtotime(str_replace('T',' ', $selesaiRaw))) : null;
            }

            $this->barangModel->update($id_product, $payload);

            return $this->response->setStatusCode(200)->setJSON([
                'pesan'   => 'Berhasil mengubah produk.',
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'EDIT PRODUCT ERROR: {msg} at {file}:{line}', [
                'msg'  => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'pesan' => 'Gagal mengubah produk.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Legacy edit product handler
     */
    public function actionEditProductOld($pathname = false)
    {
        $idBarang = $this->request->getVar('id');
        if (!$idBarang) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'pesan' => 'ID barang tidak dikirim']);
        }

        $barangCur = $this->barangModel->getBarangAdmin($idBarang);
        if (!$barangCur) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'pesan' => 'Barang tidak ditemukan']);
        }

        $koleksi = $this->koleksiModel->getKoleksi();
        $jenis = $this->jenisModel->getJenis();
        $data = $this->request->getVar();
        $files = $this->request->getFiles();

        $data['deskripsi'] = json_decode($data['deskripsi'] ?? '', true) ?? [];
        $data['varian'] = json_decode($data['varian'] ?? '', true) ?? [];

        if (!is_array($data['deskripsi']) || !is_array($data['varian'])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'pesan' => 'Format JSON tidak valid']);
        }

        if (isset($files['gambar_hover']) && $files['gambar_hover']->isValid()) {
            $tmpPath = 'imgdum/barang/hover';
            $files['gambar_hover']->move($tmpPath, $idBarang . '.webp');
            $finalPath = 'img/barang/hover/' . $idBarang . '.webp';
            if (file_exists($finalPath)) unlink($finalPath);
            \Config\Services::image()
                ->withFile($tmpPath . '/' . $idBarang . '.webp')
                ->resize(300, 300, true, 'height')
                ->save($finalPath);
            unlink($tmpPath . '/' . $idBarang . '.webp');
        }

        $gambarPaths = [];
        foreach ($files as $key => $file) {
            if (strpos($key, 'gambar_') === 0 && $file->isValid()) {
                $urutan = (int) explode('_', $key)[1];
                $file->move('imgdum');
                $filename = $file->getName();

                foreach ([3000, 1000, 300] as $size) {
                    $savePath = "img/barang/{$size}/{$idBarang}-" . ($urutan + 1) . '.webp';
                    if (file_exists($savePath)) unlink($savePath);
                    \Config\Services::image()
                        ->withFile('imgdum/' . $filename)
                        ->resize($size, $size, true, 'height')
                        ->save($savePath);
                }

                unlink('imgdum/' . $filename);
                $gambarPaths[$key] = "{$idBarang}-" . ($urutan + 1) . '.webp';
            }
        }

        foreach ($gambarPaths as $key => $filename) {
            $this->gambarBarangModel->where(['id' => $idBarang])->set([$key => $filename])->update();
            $this->gambarBarang3000Model->where(['id' => $idBarang])->set([$key => $filename])->update();
        }

        $koleksiSelected = array_values(array_filter($koleksi, fn($k) => $k['id'] == $data['kategori']))[0]['nama'] ?? '';
        $jenisSelected = array_values(array_filter($jenis, fn($j) => $j['id'] == $data['subkategori']))[0]['nama'] ?? '';

        $dataUpdate = [
            'nama' => $data['nama'] ?? null,
            'harga' => $data['harga'] ?? null,
            'deskripsi' => json_encode($data['deskripsi']),
            'kategori' => $koleksiSelected,
            'subkategori' => $jenisSelected,
            'diskon' => $data['diskon'] ?? '0',
            'varian' => json_encode($data['varian']),
            'shopee' => $data['shopee'] ?? '',
            'tokped' => $data['tokped'] ?? '',
            'tiktok' => $data['tiktok'] ?? '',
            'gambar' => $gambarPaths['gambar_0'] ?? $barangCur['gambar'],
            'ruang_tamu' => $data['ruang_tamu'] ?? '0',
            'ruang_keluarga' => $data['ruang_keluarga'] ?? '0',
            'ruang_tidur' => $data['ruang_tidur'] ?? '0',
        ];

        $dataUpdate = array_filter($dataUpdate, fn($val) => !is_null($val));

        if (empty($dataUpdate)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'pesan' => 'Tidak ada data untuk diperbarui']);
        }

        $this->barangModel->where(['id' => $idBarang])->set($dataUpdate)->update();

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json') {
            return $this->response->setJSON(['status' => 'ok', 'pesan' => 'Produk berhasil diupdate']);
        }

        return redirect()->to($pathname ? str_replace('@', '/', $pathname) : 'admin/product');
    }

    /**
     * Toggle product active status
     */
    public function activeProduct($id_product)
    {
        $product = $this->barangModel->getBarangAdmin($id_product);
        $this->barangModel->where(['id' => $id_product])->set(['active' => $product['active'] == '0' ? '1' : '0'])->update();
        $arr = [
            'pesan' => 'Ok',
        ];
        return $this->response->setJSON($arr, false);
    }

    /**
     * Delete product
     */
    public function deleteProduct($id_product)
    {
        $produk = $this->barangModel->where('id', $id_product)->delete();
        $gambar = $this->gambarBarangModel->where('id', $id_product)->delete();
        $gambar3000 = $this->gambarBarang3000Model->where('id', $id_product)->delete();
        return redirect()->to('admin/product');
    }
}
