<?php
/**
 * OrderOfflineTrait - Handles offline order-related methods
 * 
 * Methods:
 * - orderOffline($jenis): Display offline orders
 * - orderOfflineUpdate(): Update offline order
 * - getItemsOffline($id): Get items for order
 * - orderOfflineAdd(): Show add form
 * - generateAlamat($alamat): Generate address string
 * - actionAddOrderOffline(): Process add order
 * - actionBuatDP(): Create DP payment
 * - actionKoreksiSP(): Correct SP order
 * - actionBuatInvoice(): Create invoice
 * - benerinSurat(): Fix letter dates
 * - actionAccOrderOffline($id): Accept order
 * - orderOfflineFinalize(): Finalize draft order
 * - normalizeJenis($j): Normalize order type
 * - prefixByJenis($j): Get prefix by type
 * - defaultStatusByJenis($j, $dp): Get default status
 * - shouldDeductStockNow($j, $dp): Check stock deduction
 */

namespace App\Controllers\Admin\Traits;

trait OrderOfflineTrait
{
    /**
     * Normalize order type
     */
    protected function normalizeJenis($j)
    {
        $j = strtolower(trim((string)$j));
        if (in_array($j, ['sale', 'sj', 'sjl'], true)) return 'sale';
        if (in_array($j, ['nf', 'nota-faktur'], true)) return 'nf';
        if (in_array($j, ['sp', 'sample'], true)) return 'sp';
        if ($j === 'display') return 'display';
        return 'sale';
    }

    /**
     * Get prefix by order type
     */
    protected function prefixByJenis($j)
    {
        switch ($this->normalizeJenis($j)) {
            case 'nf': return 'NF';
            case 'sp': return 'SP';
            case 'display': return 'DP';
            default: return 'SJ';
        }
    }

    /**
     * Get default status by order type
     */
    protected function defaultStatusByJenis($j, $dp = 0)
    {
        $n = $this->normalizeJenis($j);
        if ($n === 'sp') return 'sampled';
        if (($n === 'sale' || $n === 'nf') && (int)$dp > 0) return 'DP';
        return 'pending';
    }

    /**
     * Check if should deduct stock now
     */
    protected function shouldDeductStockNow($j, $dp)
    {
        $n = $this->normalizeJenis($j);
        if ($n === 'sp') return false;
        if (($n === 'sale' || $n === 'nf') && (int)$dp > 0) return false;
        return true;
    }

    /**
     * Display offline orders
     */
    public function orderOffline($jenis = 'sale')
    {
        $norm = $this->normalizeJenis($jenis);
        $builder = $this->pemesananOfflineModel->orderBy('id', 'DESC');
        if (in_array($norm, ['sale', 'nf'], true)) {
            $builder->where('jenis', $norm);
        } elseif ($norm === 'sp') {
            $builder->where('jenis', 'sp');
        } elseif ($norm === 'display') {
            $builder->where('jenis', 'display');
        }
        $pesanan = $builder->findAll();
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Query SJ data untuk semua order sekaligus
        $db = \Config\Database::connect();
        
        foreach ($pesanan as $ind_p => $p) {
            $d = strtotime($p['tanggal']);
            $pesanan[$ind_p]['tanggal_format'] = date('d', $d) . ' ' . $bulan[date('m', $d) - 1] . ' ' . date('Y', $d);
            $pesanan[$ind_p]['items'] = $this->pemesananOfflineItemModel
                ->select('pemesanan_offline_item.*, barang.nama')
                ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
                ->where('id_pesanan', $p['id_pesanan'])
                ->findAll();
            
            // Query SJ data untuk order ini
            $idPesananEsc = $db->escape($p['id_pesanan']);
            $sjLast = $db->query("SELECT * FROM surat_jalan WHERE id_pesanan = {$idPesananEsc} ORDER BY id DESC LIMIT 1")->getRowArray();
            
            if ($sjLast) {
                $pesanan[$ind_p]['sj_last_id'] = $sjLast['id'];
                $pesanan[$ind_p]['sj_last_no'] = $sjLast['no_sj'];
                $pesanan[$ind_p]['sj_last_status'] = $sjLast['status'];
                $pesanan[$ind_p]['sj_last_tanggal'] = $sjLast['tanggal'];
                
                // Jika ada SJ final, ambil tanggal terakhir yang final
                $sjFinal = $db->query("SELECT tanggal FROM surat_jalan WHERE id_pesanan = {$idPesananEsc} AND status = 'final' ORDER BY id DESC LIMIT 1")->getRowArray();
                $pesanan[$ind_p]['sj_final_tanggal'] = $sjFinal['tanggal'] ?? null;
            } else {
                $pesanan[$ind_p]['sj_last_id'] = null;
                $pesanan[$ind_p]['sj_last_no'] = null;
                $pesanan[$ind_p]['sj_last_status'] = null;
                $pesanan[$ind_p]['sj_last_tanggal'] = null;
                $pesanan[$ind_p]['sj_final_tanggal'] = null;
            }
        }
        $provinsi = $this->provinsiModel->findAll();
        $data = [
            'title'            => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pesanan'          => $pesanan,
            'pesananJson'      => json_encode($pesanan),
            'jenis'            => $this->normalizeJenis($jenis),
            'msg'              => session()->getFlashdata('msg'),
            'provinsi'         => $provinsi
        ];
        return view('admin/orderOffline', $data);
    }

    /**
     * Update offline order
     */
    public function orderOfflineUpdate()
    {
        $req = $this->request;
        $id = trim((string)$req->getPost('id_pesanan'));
        $jenis = trim((string)$req->getPost('jenis'));

        if ($id === '') {
            return redirect()->back()->with('msg', 'ID pesanan tidak valid.');
        }

        $dpRaw = $req->getPost('down_payment');
        $dp = null;
        if ($dpRaw !== null && $dpRaw !== '') {
            $dp = (int)preg_replace('/[^\d\-]/', '', (string)$dpRaw);
        }

        $tgl = $req->getPost('tanggal');
        if ($tgl) {
            $tgl = str_replace('T', ' ', $tgl);
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/', $tgl)) {
                $tgl .= ':00';
            }
        }

        $payload = array_filter([
            'tanggal'            => $tgl ?: null,
            'nama'               => $req->getPost('nama') ?: null,
            'nohp'               => $req->getPost('nohp') ?: null,
            'alamat_pengiriman'  => $req->getPost('alamat_pengiriman') ?: null,
            'keterangan'         => $req->getPost('keterangan') ?: null,
            'npwp'               => $req->getPost('npwp') ?: null,
            'down_payment'       => $dp,
        ], function($v){ return $v !== null; });

        $order = $this->pemesananOfflineModel->where('id_pesanan', $id)->first();
        if (!$order) {
            return redirect()->back()->with('msg', 'Order tidak ditemukan.');
        }

        $this->pemesananOfflineModel->where('id_pesanan', $id)->set($payload)->update();
        return redirect()->to('/admin/order/offline/'.($jenis ?: 'sale'))->with('msg', 'Data berhasil diperbarui.');
    }

    /**
     * Get items for order
     */
    public function getItemsOffline($id_pesanan)
    {
        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan])
            ->findAll();

        foreach ($items as $ind_i => $i) {
            $j = json_decode($i['deskripsi'], true);
            $items[$ind_i]['dimensi'] = $j['dimensi']['asli'] ?? ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'];
            $items[$ind_i]['deskripsi'] = '';
        }

        return $this->response->setStatusCode(200)->setJSON([
            'success' => true,
            'items'   => $items,
        ], false);
    }

    /**
     * Show add form
     */
    public function orderOfflineAdd()
    {
        $produk = $this->barangModel->findAll();
        foreach ($produk as $ind_p => $p) {
            $produk[$ind_p]['gambar'] = base_url('img/barang/300/' . $p['id'] . '.webp?v=' . strtotime($p['tgl_update']));
            $produk[$ind_p]['gambar_hover'] = '';
            $d = json_decode($p['deskripsi'], true);
            $produk[$ind_p]['dimensi'] = $d['dimensi']['asli'] ?? ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'];
            $produk[$ind_p]['deskripsi'] = '';
            $produk[$ind_p]['varian'] = json_decode($p['varian'], true);
            $produk[$ind_p]['shopee'] = '';
            $produk[$ind_p]['tokped'] = '';
            $produk[$ind_p]['tiktok'] = '';
            $produk[$ind_p]['nama'] = ucwords($p['nama']);
        }
        $provinsi = $this->provinsiModel->findAll();
        $data = [
            'title'            => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'produkJson'       => json_encode($produk),
            'provinsi'         => $provinsi
        ];
        return view('admin/orderOfflineAdd', $data);
    }

    /**
     * Generate address string
     */
    public function generateAlamat($alamat)
    {
        $detail    = $alamat['detail'] ?? '';
        $provinsi  = $alamat['provinsi'] ?? '';
        $kabupaten = $alamat['kabupaten'] ?? '';
        $kecamatan = $alamat['kecamatan'] ?? '';
        $kelurahan = $alamat['kelurahan'] ?? '';
        $kodepos   = $alamat['kodepos'] ?? '';
        return $detail . ", " . $kelurahan . ", " . $kecamatan . ", " . $kabupaten . ", " . $provinsi . " " . $kodepos;
    }

    /**
     * Process add order
     */
    public function actionAddOrderOffline()
    {
        $body = $this->request->getBody();
        $body = json_decode($body, true);

        if (!is_array($body)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'pesan'   => 'Payload tidak valid',
            ]);
        }

        $isDraft = !empty($body['isDraft']);
        $body['downPayment']         = (int)($body['downPayment'] ?? 0);
        $body['potonganHargaSatuan'] = (int)($body['potonganHargaSatuan'] ?? 0);
        $body['jenis']               = $body['jenis'] ?? 'sp';
        $body['npwp']                = isset($body['npwp']) ? ($body['npwp'] == '-' ? null : $body['npwp']) : null;

        if (!$isDraft && $body['downPayment'] > $body['totalAkhir']) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'pesan' => 'Down Payment melebihi total akhir',
            ]);
        }
        if (!$isDraft && $body['potonganHargaSatuan'] > 25) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'pesan' => 'Potongan harga satuan melebihi 25%',
            ]);
        }

        $alamatPengiriman = [
            'provinsi'  => $body['provinsi']       ?? null,
            'kabupaten' => $body['kabupaten']      ?? null,
            'kecamatan' => $body['kecamatan']      ?? null,
            'kelurahan' => $body['kelurahan']      ?? null,
            'kodepos'   => $body['kodepos']        ?? null,
            'detail'    => $body['detail']         ?? null,
        ];
        $alamatTagihan = [
            'provinsi'  => $body['provinsiTagihan']   ?? null,
            'kabupaten' => $body['kabupatenTagihan']  ?? null,
            'kecamatan' => $body['kecamatanTagihan']  ?? null,
            'kelurahan' => $body['kelurahanTagihan']  ?? null,
            'kodepos'   => $body['kodeposTagihan']    ?? null,
            'detail'    => $body['detailTagihan']     ?? null,
        ];
        $totalAkhir = $body['totalAkhir'];

        $normJenis = $this->normalizeJenis($body['jenis']);
        $KODE_AWAL = $this->prefixByJenis($normJenis);

        $dataTerbaru = $this->pemesananOfflineModel
            ->like('id_pesanan', $KODE_AWAL, 'after')
            ->orderBy('id', 'desc')->first();
        $idFix = $KODE_AWAL . sprintf("%08d", $dataTerbaru ? ((int)substr($dataTerbaru['id_pesanan'], 2) + 1) : 1);

        $tanggalNoStrip = date('Ymd', strtotime($body['tanggal']));

        foreach ($body['items'] as $item) {
            $produkCur = $this->barangModel->getBarang($item['id']);
            if (!$produkCur) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Produk dengan ID ' . $item['id'] . ' tidak ditemukan',
                ]);
            }

            $varian      = json_decode($produkCur['varian'], true);
            $saldo       = 0;
            $varianBaru  = $varian;

            foreach ($varian as $ind => $v) {
                if ($v['nama'] == $item['varian']) {
                    $saldo = (int)$v['stok'];
                    if (!$isDraft && $this->shouldDeductStockNow($normJenis, $body['downPayment'])) {
                        $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - $item['jumlah']);
                    }
                }
            }

            $saldoAkhir = $saldo - $item['jumlah'];

            if (
                !$isDraft &&
                $this->shouldDeductStockNow($normJenis, $body['downPayment']) &&
                $saldoAkhir < 0
            ) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi untuk produk ' . $item['varian'] . ' pada barang ' . $produkCur['nama'],
                ], false);
            }

            if (!$isDraft && $this->shouldDeductStockNow($normJenis, $body['downPayment'])) {
                $this->barangModel->where('id', $item['id'])->set([
                    'varian' => json_encode($varianBaru)
                ])->update();

                $this->kartuStokModel->insert([
                    'id_barang'  => $item['id'],
                    'tanggal'    => $body['tanggal'],
                    'keterangan' => $tanggalNoStrip . "-" . $item['id'] . "-" . str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $idFix,
                    'debit'      => 0,
                    'kredit'     => $item['jumlah'],
                    'saldo'      => $saldoAkhir,
                    'pending'    => false,
                    'id_pesanan' => $idFix,
                    'varian'     => $item['varian'],
                ]);
            }

            for ($i = 0; $i < $item['jumlah']; $i++) {
                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan'    => $idFix,
                    'id_barang'     => $item['id'],
                    'harga'         => $item['harga'],
                    'varian'        => $item['varian'],
                    'special_price' => $body['potonganHargaSatuan'],
                    'id_return'     => ''
                ]);
            }
        }

        $tanggal_inv = null;
        if (!$isDraft && ($normJenis === 'sale' || $normJenis === 'nf')) {
            $tanggal_inv = $body['npwp'] ? ($body['downPayment'] > 0 ? null : $body['tanggal']) : null;
        }

        $status = $isDraft ? 'draft' : $this->defaultStatusByJenis($normJenis, $body['downPayment']);
        $downPaymentValue = (int)$body['downPayment'];

        $data = [
            'nama'               => $body['nama'],
            'nohp'               => $body['nohp'],
            'alamat_pengiriman'  => $this->generateAlamat($alamatPengiriman),
            'alamat_tagihan'     => $body['npwp'] ? $this->generateAlamat($alamatTagihan) : null,
            'npwp'               => $body['npwp'] ? $body['npwp'] : null,
            'nama_npwp'          => $body['npwp'] ? $body['nama_npwp'] : null,
            'tanggal'            => $body['tanggal'],
            'tanggal_inv'        => $tanggal_inv,
            'id_pesanan'         => $idFix,
            'status'             => $status,
            'jenis'              => $normJenis,
            'total_akhir'        => $totalAkhir,
            'keterangan'         => $body['keterangan'],
            'po'                 => $body['po'] ? $body['po'] : null,
            'down_payment'       => (($normJenis === 'sale' || $normJenis === 'nf') && $downPaymentValue > 0) ? $downPaymentValue : null,
            'is_draft'           => $isDraft ? 1 : 0,
        ];

        $this->pemesananOfflineModel->insert($data);

        return $this->response->setStatusCode(200)->setJSON([
            'success'    => true,
            'id_pesanan' => $idFix,
            'is_draft'   => $isDraft,
        ], false);
    }

    /**
     * Create DP payment
     */
    public function actionBuatDP()
    {
        $tanggal   = $this->request->getVar('tanggal');
        $idPesanan = $this->request->getVar('id_pesanan');
        $npwp      = $this->request->getVar('npwp');
        $nama_npwp = $this->request->getVar('nama_npwp');

        $pesananDP = $this->pemesananOfflineModel->getPemesanan($idPesanan);
        if (!$pesananDP) {
            return redirect()->back()->with('msg', 'Pesanan sumber tidak ditemukan.');
        }

        $jenisAsal = $pesananDP['jenis'] ?: 'sale';
        $prefix    = $this->prefixByJenis($jenisAsal);

        $itemsDP = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->select('barang.deskripsi')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $idPesanan])
            ->findAll();

        $dataTerbaru = $this->pemesananOfflineModel
            ->like('id_pesanan', $prefix, 'after')
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = $dataTerbaru ? ((int)substr($dataTerbaru['id_pesanan'], 2) + 1) : 1;
        $idFix   = $prefix . sprintf('%08d', $nextNum);
        $tanggalNoStrip = date('Ymd', strtotime($tanggal));

        $data = [
            'nama'              => $pesananDP['nama'],
            'nohp'              => $pesananDP['nohp'],
            'alamat_pengiriman' => $pesananDP['alamat_pengiriman'],
            'alamat_tagihan'    => $pesananDP['alamat_pengiriman'],
            'nama_npwp'         => $npwp ? $nama_npwp : null,
            'npwp'              => $npwp ?: null,
            'tanggal'           => $tanggal,
            'tanggal_inv'       => $npwp ? $tanggal : null,
            'id_pesanan'        => $idFix,
            'status'            => 'pending',
            'jenis'             => $jenisAsal,
            'total_akhir'       => $pesananDP['total_akhir'],
            'keterangan'        => $pesananDP['keterangan'],
            'po'                => $pesananDP['po'],
            'down_payment'      => (int)$pesananDP['down_payment'] * -1,
        ];
        $this->pemesananOfflineModel->insert($data);

        $this->pemesananOfflineModel
            ->where(['id_pesanan' => $idPesanan])
            ->set(['status' => 'DP paid'])
            ->update();

        $filter         = [];
        $itemsFiltered  = [];
        $counterJumlah  = [];

        foreach ($itemsDP as $i) {
            if (!in_array($i['id_barang'].'-'.$i['varian'], $filter)) {
                $filter[] = $i['id_barang'].'-'.$i['varian'];
                $counterJumlah[] = 1;
            } else {
                $counterJumlah[count($counterJumlah) - 1] += 1;
            }
        }

        $filter = [];
        foreach ($itemsDP as $i) {
            if (!in_array($i['id_barang'].'-'.$i['varian'], $filter)) {
                $itemsFiltered[] = array_merge($i, [
                    'dimensi' => json_decode($i['deskripsi'], true)['dimensi']['asli'],
                    'jumlah'  => $counterJumlah[count($itemsFiltered)]
                ]);
                $filter[] = $i['id_barang'].'-'.$i['varian'];
            }
        }

        foreach ($itemsFiltered as $i) {
            $produkCur = $this->barangModel->getBarang($i['id_barang']);
            $varian    = json_decode($produkCur['varian'], true);
            $saldo     = 0; $varianBaru = $varian;

            foreach ($varian as $ind => $v) {
                if ($v['nama'] == $i['varian']) {
                    $saldo = (int)$v['stok'];
                    $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - $i['jumlah']);
                }
            }
            $saldoAkhir = $saldo - $i['jumlah'];

            $this->barangModel->where('id', $i['id_barang'])->set([
                'varian' => json_encode($varianBaru)
            ])->update();

            $this->kartuStokModel->insert([
                'id_barang' => $i['id_barang'],
                'tanggal'   => $tanggal,
                'keterangan'=> $tanggalNoStrip . "-" . $i['id_barang'] . "-" . str_replace(' ', '-', strtoupper($i['varian'])) . "-" . $idFix,
                'debit'     => 0,
                'kredit'    => $i['jumlah'],
                'saldo'     => $saldoAkhir,
                'pending'   => false,
                'id_pesanan'=> $idFix,
                'varian'    => $i['varian'],
            ]);

            for ($in = 0; $in < (int)$i['jumlah']; $in++) {
                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan'    => $idFix,
                    'id_barang'     => $i['id_barang'],
                    'harga'         => $i['harga'],
                    'varian'        => $i['varian'],
                    'special_price' => $i['special_price'],
                    'id_return'     => $idPesanan,
                ]);
            }
        }
        $segmentJenis = $jenisAsal === 'nf' ? 'nf' : 'sale';
        return redirect()->to('/admin/order/offline/' . $segmentJenis);
    }

    /**
     * Correct SP order
     */
    public function actionKoreksiSP()
    {
        $body = $this->request->getVar();

        $convertTo = $body['convert_to'] ?? 'sale';
        $normJenisTarget = $this->normalizeJenis($convertTo);
        if (!in_array($normJenisTarget, ['sale', 'nf'], true)) {
            $normJenisTarget = 'sale';
        }

        $prefixTarget = $this->prefixByJenis($normJenisTarget);

        $id_pesanan_SP = $body['id_pesanan'];
        $sp_current = $this->pemesananOfflineModel->getPemesanan($id_pesanan_SP);

        $items = $this->pemesananOfflineItemModel
            ->select('pemesanan_offline_item.*')
            ->select('barang.nama')
            ->join('barang', 'barang.id = pemesanan_offline_item.id_barang')
            ->where(['id_pesanan' => $id_pesanan_SP])
            ->findAll();

        $alamatTagihan = $body['provinsi'] ? [
            'provinsi'  => explode('-', $body['provinsi'])[1],
            'kabupaten' => explode('-', $body['kota'])[1],
            'kecamatan' => explode('-', $body['kecamatan'])[1],
            'kelurahan' => explode('-', $body['kodepos'])[0],
            'kodepos'   => explode('-', $body['kodepos'])[1],
            'detail'    => $body['detail'],
        ] : [];

        $dataTerbaru   = $this->pemesananOfflineModel
            ->like('id_pesanan', $prefixTarget, 'after')
            ->orderBy('id', 'desc')
            ->first();
        $dataTerbaruSK = $this->pemesananOfflineModel
            ->like('id_pesanan', 'SK', 'after')
            ->orderBy('id', 'desc')
            ->first();

        $idSJ = $prefixTarget . sprintf(
            "%08d",
            $dataTerbaru ? ((int)substr($dataTerbaru['id_pesanan'], 2) + 1) : 1
        );
        $idSK = 'SK' . sprintf(
            "%08d",
            $dataTerbaruSK ? ((int)substr($dataTerbaruSK['id_pesanan'], 2) + 1) : 1
        );

        $tanggalNoStrip = date('Ymd', strtotime($body['tanggal']));

        $indexItems = explode(',', $body['index_items_selected']);
        $totalAkhir = 0;

        foreach ($items as $ind_i => $item) {
            if ($indexItems[$ind_i] == '1') {
                $produkCur = $this->barangModel->getBarang($item['id_barang']);
                $varian    = json_decode($produkCur['varian'], true);
                $saldo     = 0;
                $varianBaru = $varian;

                foreach ($varian as $ind => $v) {
                    if ($v['nama'] == $item['varian']) {
                        $saldo = (int)$v['stok'];
                        $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - 1);
                    }
                }

                $this->pemesananOfflineItemModel
                    ->where(['id' => $item['id']])
                    ->set(['id_return' => $idSK])
                    ->update();

                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan' => $idSJ,
                    'id_barang'  => $item['id_barang'],
                    'harga'      => $item['harga'],
                    'varian'     => $item['varian'],
                    'id_return'  => $idSK
                ]);

                $this->pemesananOfflineItemModel->insert([
                    'id_pesanan' => $idSK,
                    'id_barang'  => $item['id_barang'],
                    'harga'      => $item['harga'],
                    'varian'     => $item['varian'],
                    'id_return'  => ''
                ]);

                $this->kartuStokModel->insert([
                    'id_barang' => $item['id_barang'],
                    'tanggal'   => $body['tanggal'],
                    'keterangan'=> $tanggalNoStrip . "-" . $item['id_barang'] . "-" . str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $idSK,
                    'debit'     => 1,
                    'kredit'    => 0,
                    'saldo'     => $saldo + 1,
                    'pending'   => false,
                    'id_pesanan'=> $idSK,
                    'varian'    => $item['varian'],
                ]);
                $this->kartuStokModel->insert([
                    'id_barang' => $item['id_barang'],
                    'tanggal'   => $body['tanggal'],
                    'keterangan'=> $tanggalNoStrip . "-" . $item['id_barang'] . "-" . str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $idSJ,
                    'debit'     => 0,
                    'kredit'    => 1,
                    'saldo'     => $saldo,
                    'pending'   => false,
                    'id_pesanan'=> $idSJ,
                    'varian'    => $item['varian'],
                ]);

                $totalAkhir += (int)$item['harga'];
            }
        }

        $diskonVal       = isset($body['diskon']) && $body['diskon'] !== '' ? (float)$body['diskon'] : 0;
        $diskon_koreksi  = ($diskonVal / 100) * $totalAkhir;
        $totalSetelahDiskon = $totalAkhir - $diskon_koreksi;

        $tanggal_inv = $body['npwp'] ? $body['tanggal'] : null;

        $dataSJ = [
            'nama'              => $sp_current['nama'],
            'nohp'              => $sp_current['nohp'],
            'alamat_pengiriman' => $sp_current['alamat_pengiriman'],
            'alamat_tagihan'    => $body['npwp']
                ? (isset($body['checkAlamat'])
                    ? $body['alamatTagihan']
                    : $this->generateAlamat($alamatTagihan))
                : null,
            'npwp'              => $body['npwp'] ? $body['npwp'] : null,
            'nama_npwp'         => $body['npwp'] ? $body['nama_npwp'] : null,
            'tanggal'           => $body['tanggal'],
            'tanggal_inv'       => $tanggal_inv,
            'id_pesanan'        => $idSJ,
            'status'            => 'pending',
            'jenis'             => $normJenisTarget,
            'total_akhir'       => $totalSetelahDiskon,
            'keterangan'        => $body['keterangan'],
            'po'                => $sp_current['po'],
        ];

        $dataSK = [
            'nama'              => $sp_current['nama'],
            'nohp'              => $sp_current['nohp'],
            'alamat_pengiriman' => $sp_current['alamat_pengiriman'],
            'tanggal'           => $body['tanggal'],
            'id_pesanan'        => $idSK,
            'status'            => 'success',
            'jenis'             => 'sale',
            'total_akhir'       => $totalAkhir,
            'keterangan'        => $body['keterangan'],
            'po'                => $sp_current['po'],
        ];

        $this->pemesananOfflineModel->insert($dataSJ);
        $this->pemesananOfflineModel->insert($dataSK);

        $seg = ($normJenisTarget === 'nf') ? 'nf' : 'sale';
        return redirect()->to('/admin/order/offline/' . $seg);
    }

    /**
     * Create invoice
     */
    public function actionBuatInvoice()
    {
        $tanggal = $this->request->getVar('tanggal');
        $npwp = $this->request->getVar('npwp');
        $nama_npwp = $this->request->getVar('nama_npwp');
        $alamat = $this->request->getVar('alamat');
        $idPesanan = $this->request->getVar('id_pesanan');

        $this->pemesananOfflineModel->where(['id_pesanan' => $idPesanan])->set([
            'npwp' => $npwp,
            'nama_npwp' => $nama_npwp,
            'tanggal_inv' => $tanggal,
            'alamat_tagihan' => $alamat
        ])->update();

        session()->setFlashdata('msg', 'Invoice ' . $idPesanan . ' berhasil dibuat');
        $row = $this->pemesananOfflineModel->where('id_pesanan', $idPesanan)->select('jenis')->first();
        $seg = ($row && $row['jenis'] === 'nf') ? 'nf' : 'sale';
        return redirect()->to('/admin/order/offline/' . $seg);
    }

    /**
     * Fix letter dates
     */
    public function benerinSurat()
    {
        $pemesanan = $this->pemesananOfflineModel->findAll();
        foreach ($pemesanan as $p) {
            $this->pemesananOfflineModel->where(['id' => $p['id']])->set(['tanggal_inv' => $p['tanggal']])->update();
        }
        dd('done');
    }

    /**
     * Accept order
     */
    public function actionAccOrderOffline($id_pesanan)
    {
        $this->pemesananOfflineModel->where(['id_pesanan' => $id_pesanan])->set(['status' => 'success'])->update();
        session()->setFlashdata('msg', 'Invoice ' . $id_pesanan . ' sudah lunas');
        $jenis = $this->pemesananOfflineModel->where('id_pesanan', $id_pesanan)->select('jenis')->first()['jenis'] ?? 'sale';
        return redirect()->to('/admin/order/offline/' . ($jenis === 'nf' ? 'nf' : 'sale'));
    }

    /**
     * Finalize draft order
     */
    public function orderOfflineFinalize()
    {
        $req = $this->request;
        $id  = trim((string)$req->getPost('id_pesanan'));

        if ($id === '') {
            return redirect()->back()->with('msg', 'ID pesanan tidak valid.');
        }

        $order = $this->pemesananOfflineModel->where('id_pesanan', $id)->first();
        if (!$order) {
            return redirect()->back()->with('msg', 'Order tidak ditemukan.');
        }

        if ((int)($order['is_draft'] ?? 0) !== 1) {
            return redirect()->back()->with('msg', 'Order ini bukan draft.');
        }

        $items = $this->pemesananOfflineItemModel
            ->where('id_pesanan', $id)
            ->findAll();

        $normJenis   = $order['jenis'] ?: 'sale';
        $downPayment = (int)($order['down_payment'] ?? 0);
        $tanggal     = $order['tanggal'];
        $tanggalNoStrip = date('Ymd', strtotime($tanggal));

        if ($this->shouldDeductStockNow($normJenis, $downPayment)) {
            foreach ($items as $item) {
                $produkCur = $this->barangModel->getBarang($item['id_barang']);
                $varian    = json_decode($produkCur['varian'], true);
                $saldo     = 0;
                $varianBaru = $varian;

                foreach ($varian as $ind => $v) {
                    if ($v['nama'] == $item['varian']) {
                        $saldo = (int)$v['stok'];
                        $varianBaru[$ind]['stok'] = (string)((int)$v['stok'] - 1);
                    }
                }

                $saldoAkhir = $saldo - 1;
                if ($saldoAkhir < 0) {
                    return redirect()->back()->with('msg',
                        'Stok tidak mencukupi untuk varian ' . $item['varian'] . ' (' . $produkCur['nama'] . ')');
                }

                $this->barangModel->where('id', $item['id_barang'])->set([
                    'varian' => json_encode($varianBaru)
                ])->update();

                $this->kartuStokModel->insert([
                    'id_barang' => $item['id_barang'],
                    'tanggal'   => $tanggal,
                    'keterangan'=> $tanggalNoStrip . "-" . $item['id_barang'] . "-" .
                                str_replace(' ', '-', strtoupper($item['varian'])) . "-" . $id,
                    'debit'     => 0,
                    'kredit'    => 1,
                    'saldo'     => $saldoAkhir,
                    'pending'   => false,
                    'id_pesanan'=> $id,
                    'varian'    => $item['varian'],
                ]);
            }
        }

        $statusFinal = $this->defaultStatusByJenis($normJenis, $downPayment);

        $tanggal_inv = $order['tanggal_inv'];
        if (($normJenis === 'sale' || $normJenis === 'nf')
            && ($order['npwp'] ?? null)
            && $downPayment <= 0
            && !$tanggal_inv
        ) {
            $tanggal_inv = $tanggal;
        }

        $this->pemesananOfflineModel
            ->where('id_pesanan', $id)
            ->set([
                'is_draft'    => 0,
                'status'      => $statusFinal,
                'tanggal_inv' => $tanggal_inv,
            ])->update();

        $seg = ($normJenis === 'nf') ? 'nf' : ($normJenis === 'display' ? 'display' : 'sale');

        return redirect()->to('/admin/order/offline/' . $seg)
            ->with('msg', 'Draft ' . $id . ' berhasil difinalisasi.');
    }
}
