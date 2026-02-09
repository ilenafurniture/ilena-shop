<?php
/**
 * ProjectInteriorTrait - Handles project interior management
 * 
 * Methods:
 * - projectInteriorList(): List projects
 * - projectInteriorAdd(): Add form
 * - actionProjectInteriorAdd(): Process add
 * - projectInteriorDetail(): View details
 * - projectInteriorSjDraft(): Create SJ draft
 * - projectInteriorPaymentInvoice(): Payment invoice
 * - actionProjectInteriorAddPayment(): Add payment
 * - projectInteriorCreateInvoice(): Create invoice
 * - projectInteriorSuratJalan(): SJ for project
 * - projectInteriorCreateSuratJalan(): Create SJ
 */

namespace App\Controllers\Admin\Traits;

trait ProjectInteriorTrait
{
    /**
     * List all interior projects
     */
    public function projectInteriorList()
    {
        $db = \Config\Database::connect();

        $projects = $db->table('project_interior')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        return view('admin/projectInteriorList', [
            'title'            => 'Project Interior',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'msg'              => session()->getFlashdata('msg'),
            'projects'         => $projects,
        ]);
    }

    /**
     * Show add project form
     */
    public function projectInteriorAdd()
    {
        $provinsi = [];
        try {
            $db = \Config\Database::connect();
            $candidateTables = ['provinsi', 'ro_provinsi', 'wilayah_provinsi', 'master_provinsi', 'tb_provinsi'];
            $foundTable = null;

            foreach ($candidateTables as $t) {
                if ($db->tableExists($t)) {
                    $foundTable = $t;
                    break;
                }
            }

            if ($foundTable) {
                $cols = $db->getFieldNames($foundTable);
                $idCol = null;
                foreach (['id', 'province_id', 'provinsi_id', 'kode'] as $c) {
                    if (in_array($c, $cols, true)) { $idCol = $c; break; }
                }
                $labelCol = null;
                foreach (['label', 'nama', 'name', 'province_name', 'provinsi_nama'] as $c) {
                    if (in_array($c, $cols, true)) { $labelCol = $c; break; }
                }
                if ($idCol && $labelCol) {
                    $provinsi = $db->table($foundTable)
                        ->select($idCol . ' AS id, ' . $labelCol . ' AS label')
                        ->orderBy($labelCol, 'ASC')
                        ->get()
                        ->getResultArray();
                }
            }
            if (!is_array($provinsi)) $provinsi = [];
        } catch (\Throwable $e) {
            $provinsi = [];
        }

        $data = [
            'title'            => 'Project Interior Baru',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'msg'              => session()->getFlashdata('msg'),
            'provinsi'         => $provinsi,
        ];

        return view('admin/projectInteriorAdd', $data);
    }

    public function actionProjectInteriorAdd()
    {
        $req = $this->request;
        $db  = \Config\Database::connect();

        // ======================
        // BASIC INPUT
        // ======================
        $namaProject  = trim((string)$req->getPost('nama_project'));
        $noPo         = trim((string)$req->getPost('no_po'));

        // SUPPORT 2 INPUT:
        // - view lama: jenis_faktur = sale|nf
        // - view baru: dokumen_utama = SJ|NF
        $jenisFaktur = strtolower(trim((string)$req->getPost('jenis_faktur')));
        $dokUtama    = strtoupper(trim((string)$req->getPost('dokumen_utama')));

        if ($dokUtama === 'NF') {
            $jenisFaktur = 'nf';
        } elseif ($dokUtama === 'SJ') {
            $jenisFaktur = 'sale';
        } else {
            // fallback
            $jenisFaktur = ($jenisFaktur === 'nf') ? 'nf' : 'sale';
        }

        // SUPPORT 2 INPUT:
        // - view lama: ppn_mode = non|ppn10|ppn11
        // - view baru: ppn_rate = 0|10|11
        $ppnMode = strtolower(trim((string)$req->getPost('ppn_mode')));
        $ppnRate = 0;

        $ppnRatePost = (string)$req->getPost('ppn_rate');
        if ($ppnRatePost !== '') {
            $ppnRate = (int)preg_replace('/[^\d]/', '', $ppnRatePost);
            if (!in_array($ppnRate, [0, 10, 11], true)) $ppnRate = 0;
            $ppnMode = ($ppnRate === 0) ? 'non' : ('ppn' . $ppnRate);
        } else {
            if (!in_array($ppnMode, ['non', 'ppn10', 'ppn11'], true)) $ppnMode = 'non';
            $ppnRate = $this->ppnRateFromMode($ppnMode);
        }

        // customer
        $namaCustomer     = trim((string)$req->getPost('nama_customer'));
        $nohp             = trim((string)$req->getPost('nohp'));
        $npwp             = trim((string)$req->getPost('npwp'));
        $namaNpwp         = trim((string)$req->getPost('nama_npwp'));
        $alamatPengiriman = trim((string)$req->getPost('alamat_pengiriman'));
        $alamatTagihan    = trim((string)$req->getPost('alamat_tagihan'));
        $catatan          = trim((string)$req->getPost('catatan'));

        if ($namaProject === '' || $namaCustomer === '' || $nohp === '') {
            return redirect()->back()->with('msg', 'Nama project, nama klien, dan no HP wajib diisi.');
        }

        // ======================
        // ITEMS INPUT (kompatibel)
        // ======================
        $items = null;

        // (1) items_json
        $itemsJson = (string)$req->getPost('items_json');
        if ($itemsJson !== '') {
            $decoded = json_decode($itemsJson, true);
            if (is_array($decoded)) $items = $decoded;
        }

        // (2) items array post
        if (!is_array($items) || count($items) === 0) {
            $itemsPost = $req->getPost('items');
            if (is_array($itemsPost)) {
                $isList = array_keys($itemsPost) === range(0, count($itemsPost) - 1);
                if ($isList) {
                    $items = $itemsPost;
                } else {
                    $kodeArr  = (array)($itemsPost['kode_barang'] ?? []);
                    $namaArr  = (array)($itemsPost['nama_barang'] ?? []);
                    $hargaArr = (array)($itemsPost['harga_satuan'] ?? []);
                    $qtyArr   = (array)($itemsPost['qty'] ?? []);

                    $max = max(count($kodeArr), count($namaArr), count($hargaArr), count($qtyArr));
                    $rows = [];
                    for ($i = 0; $i < $max; $i++) {
                        $rows[] = [
                            'kode_barang'  => $kodeArr[$i]  ?? '',
                            'nama_barang'  => $namaArr[$i]  ?? '',
                            'harga_satuan' => $hargaArr[$i] ?? '',
                            'qty'          => $qtyArr[$i]   ?? '',
                        ];
                    }
                    $items = $rows;
                }
            }
        }

        // (3) single item fallback (view lama)
        if (!is_array($items) || count($items) === 0) {
            $singleKode  = trim((string)$req->getPost('kode_barang'));
            $singleNama  = trim((string)$req->getPost('nama_barang'));
            $singleHarga = (string)$req->getPost('harga_satuan');
            $singleQty   = (string)$req->getPost('qty');

            if ($singleKode !== '' || $singleNama !== '' || $singleHarga !== '' || $singleQty !== '') {
                $items = [[
                    'kode_barang'  => $singleKode,
                    'nama_barang'  => $singleNama,
                    'harga_satuan' => $singleHarga,
                    'qty'          => $singleQty,
                ]];
            }
        }

        if (!is_array($items) || count($items) === 0) {
            return redirect()->back()->with('msg', 'Minimal 1 item harus diisi.');
        }

        // ======================
        // CLEAN + VALIDATE ITEMS
        // ======================
        $subtotalDpp = 0;
        $cleanItems  = [];

        foreach ($items as $it) {
            if (!is_array($it)) continue;

            // support alias dari view kamu: {nama,harga,qty}
            $kode = trim((string)($it['kode_barang'] ?? $it['kode'] ?? ''));
            $nama = trim((string)($it['nama_barang'] ?? $it['nama'] ?? ''));
            $hargaRaw = (string)($it['harga_satuan'] ?? $it['harga'] ?? 0);
            $qtyRaw   = $it['qty'] ?? $it['kuantiti'] ?? 0;

            // kalau kode kosong dari view baru, bikin kode otomatis dari nama (biar valid)
            if ($kode === '' && $nama !== '') {
                $kode = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $nama));
                if ($kode === '') $kode = 'ITEM';
                $kode = substr($kode, 0, 20);
            }

            $harga = (int)preg_replace('/[^\d]/', '', $hargaRaw);
            $qty   = (int)preg_replace('/[^\d]/', '', (string)$qtyRaw);

            if ($kode === '' || $nama === '' || $harga <= 0 || $qty <= 0) {
                return redirect()->back()->with('msg', 'Item tidak valid. Pastikan kode/nama/harga/qty terisi benar.');
            }

            $sub = $harga * $qty;
            $subtotalDpp += $sub;

            $cleanItems[] = [
                'kode_barang'  => $kode,
                'nama_barang'  => $nama,
                'harga_satuan' => $harga,
                'qty'          => $qty,
                'subtotal'     => $sub,
            ];
        }

        if (count($cleanItems) === 0) {
            return redirect()->back()->with('msg', 'Minimal 1 item valid harus diisi.');
        }

        $totalPpn   = (int) round($subtotalDpp * $ppnRate / 100);
        $grandTotal = $subtotalDpp + $totalPpn;

        // ======================
        // TRANSACTION
        // ======================
        $db->transBegin();

        try {
            // generate kode project
            $lastProject = $db->table('project_interior')->orderBy('id', 'DESC')->get(1)->getRowArray();
            $nextNo = $lastProject ? ((int)$lastProject['id'] + 1) : 1;
            $kodeProject = 'PI' . sprintf('%08d', $nextNo);

            // reserve nomor dokumen utama mengikuti offline (SJ / NF)
            $prefixInvoice = ($jenisFaktur === 'nf') ? 'NF' : 'SJ';
            $kodeInvoice   = $this->generateNextOfflineCode($prefixInvoice);

            $now = date('Y-m-d H:i:s', strtotime('+7 hours'));
            /*
            // NOTE: normalizeJenis and generateSjNumberGlobal are from OrderOfflineTrait/HelperTrait.
            // Since AdminController uses them, $this calls should work.
            */
            $normJenis = $this->normalizeJenis($jenisFaktur);
            $noSj = ($normJenis === 'nf') ? $this->generateSjNumberGlobalNF($now) : $this->generateSjNumberGlobal($now);

            // insert pemesanan_offline (reserved) - PENTING untuk cetak invoice/SJ
            // Jangan pakai model di sini (beberapa project punya model table berbeda),
            // langsung tulis ke tabel pemesanan_offline agar pasti ada record dokumen utama.
            $okOffline = $db->table('pemesanan_offline')->insert([
                'nama'              => $namaCustomer,
                'nohp'              => $nohp,
                'alamat_pengiriman' => $alamatPengiriman !== '' ? $alamatPengiriman : null,
                'alamat_tagihan'    => $alamatTagihan !== '' ? $alamatTagihan : null,
                'npwp'              => $npwp !== '' ? $npwp : null,
                'nama_npwp'         => $namaNpwp !== '' ? $namaNpwp : null,
                'tanggal'           => $now,
                'tanggal_inv'       => null,
                'status'            => 'reserved',
                'total_akhir'       => $grandTotal, // TOTAL incl PPN
                'keterangan'        => $namaProject . ' (' . $kodeProject . ')',
                'po'                => $noPo !== '' ? $noPo : null,
                'down_payment'      => null,
                'id_pesanan'        => $kodeInvoice,
                'jenis'             => $jenisFaktur,
                'is_draft'          => 0,
            ]);

            if (!$okOffline) {
                $err = $db->error();
                $msgErr = isset($err['message']) ? $err['message'] : 'Insert pemesanan_offline gagal';
                throw new \RuntimeException($msgErr);
            }

            // ======================
            // FIX UTAMA: nilai_kontrak HARUS diisi
            // karena view detail & payment pakai nilai_kontrak
            // ======================
            $db->table('project_interior')->insert([
                'kode_project' => $kodeProject,
                'nama_project' => $namaProject,
                'kode_sj'      => $kodeInvoice,
                'kode_sp'      => null,
                'total_dp'     => 0,
                'total_bayar'  => 0,
                'status'       => 'final',
                'no_po'        => $noPo !== '' ? $noPo : null,
                'catatan'      => $catatan !== '' ? $catatan : null,

                // FIX: ini yang bikin sisa tagihan benar
                'nilai_kontrak' => $grandTotal,

                // tetap simpan pajak
                'ppn_mode'     => $ppnMode,
                'ppn_rate'     => (int)$ppnRate,
                'subtotal_dpp' => (int)$subtotalDpp,
                'total_ppn'    => (int)$totalPpn,
                'grand_total'  => (int)$grandTotal,
            ]);

            $projectId = (int)$db->insertID();

            // insert items
            foreach ($cleanItems as $ci) {
                $this->projectInteriorItemModel->insert([
                    'project_id'   => $projectId,
                    'kode_barang'  => $ci['kode_barang'],
                    'nama_barang'  => $ci['nama_barang'],
                    'harga_satuan' => $ci['harga_satuan'],
                    'qty'          => $ci['qty'],
                    'subtotal'     => $ci['subtotal'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('DB transaction failed');
            }

            $db->transCommit();

            $labelDok = ($jenisFaktur === 'nf') ? 'NF' : 'SJ';
            return redirect()->to('/admin/project-interior/' . $kodeProject)
                ->with('msg', 'Project interior berhasil dibuat. ' . $labelDok . ': ' . $kodeInvoice);

        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('msg', 'Gagal membuat project interior: ' . $e->getMessage());
        }
    }


    /**
     * View project details
     */
    public function projectInteriorDetail(string $kodeProject)
    {
        $db = \Config\Database::connect();

        $project = $this->projectInteriorModel
            ->where('kode_project', $kodeProject)
            ->first();

        if (!$project) {
            return redirect()->to(site_url('admin/project-interior'))
                ->with('msg', 'Project tidak ditemukan.');
        }

        $projectId = (int)($project['id'] ?? 0);

        $items = $this->projectInteriorItemModel
            ->where('project_id', $projectId)
            ->orderBy('id', 'ASC')
            ->findAll();

        $payments = $this->projectInteriorPaymentModel->getByProjectId($projectId);

        $shippedMap = $this->interiorShippedQtyMap($project['kode_sj'] ?? '');

        $sjList = [];
        $idPesanan = (string)($project['kode_sj'] ?? '');
        if ($idPesanan !== '') {
            $sjList = $this->suratJalanModel
                ->where('id_pesanan', $idPesanan)
                ->orderBy('sj_ke', 'ASC')
                ->findAll();
        }

        $requireDpForSj = true;
        $hasDp = false;
        if (is_array($payments)) {
            foreach ($payments as $p) {
                if (strtolower((string)($p['jenis'] ?? '')) === 'dp') {
                    $hasDp = true;
                    break;
                }
            }
        }

        return view('admin/projectInteriorDetail', [
            'title'            => 'Detail Project Interior',
            'project'          => $project,
            'items'            => $items,
            'payments'         => $payments,
            'sj_list'          => $sjList,
            'shipped_map'      => $shippedMap,
            'require_dp_for_sj'=> $requireDpForSj,
            'has_dp'           => $hasDp,
            'msg'              => session()->getFlashdata('msg'),
        ]);
    }

    /**
     * Add payment to project
     */
    public function actionProjectInteriorAddPayment(string $kodeProject)
    {
        $db = \Config\Database::connect();

        $project = $db->table('project_interior')
            ->where('kode_project', $kodeProject)
            ->get()->getRowArray();

        if (!$project) {
            return redirect()->back()->with('msg', 'Project interior tidak ditemukan.');
        }

        $req     = $this->request;
        $jenis   = $req->getPost('jenis');
        $nominal = (int)preg_replace('/[^\d]/', '', (string)$req->getPost('nominal'));
        $catatan = $req->getPost('catatan');

        if (!in_array($jenis, ['dp', 'termin', 'pelunasan'], true)) {
            return redirect()->back()->with('msg', 'Jenis pembayaran tidak valid.');
        }

        $sumBefore = $db->table('project_interior_payment')
            ->selectSum('nominal', 'total')
            ->where('project_id', $project['id'])
            ->get()->getRowArray();
        $alreadyPaid = (int)($sumBefore['total'] ?? 0);

        $nilaiKontrak = (int)$project['nilai_kontrak'];
        $sisaTagihan  = max(0, $nilaiKontrak - $alreadyPaid);

        if ($nominal <= 0) {
            return redirect()->back()->with('msg', 'Nominal pembayaran harus lebih dari 0.');
        }
        if ($sisaTagihan <= 0) {
            return redirect()->back()->with('msg', 'Project sudah lunas, tidak bisa menambah pembayaran lagi.');
        }
        if ($nominal > $sisaTagihan) {
            return redirect()->back()->with('msg',
                'Nominal melebihi sisa tagihan. Sisa saat ini: Rp ' . number_format($sisaTagihan, 0, ',', '.'));
        }

        $now = date('Y-m-d H:i:s', strtotime('+7 hours'));

        $db->table('project_interior_payment')->insert([
            'project_id' => $project['id'],
            'tanggal'    => $now,
            'jenis'      => $jenis,
            'nominal'    => $nominal,
            'catatan'    => $catatan,
        ]);

        $totalBayar = $alreadyPaid + $nominal;

        $status = 'draft';
        if ($totalBayar > 0 && $totalBayar < $nilaiKontrak) {
            $status = ($jenis === 'dp') ? 'dp' : 'termin';
        } elseif ($totalBayar >= $nilaiKontrak) {
            $status = 'lunas';
        }

        $db->table('project_interior')
            ->where('id', $project['id'])
            ->update([
                'total_bayar' => $totalBayar,
                'status'      => $status,
            ]);

        return redirect()
            ->to('/admin/project-interior/' . $kodeProject)
            ->with('msg', 'Pembayaran berhasil ditambahkan. Status sekarang: ' . $status);
    }

    /**
     * Create SJ draft for project
     */
    public function projectInteriorSjDraft(string $kodeProject)
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s', strtotime('+7 hours'));

        $project = $db->table('project_interior')->where('kode_project', $kodeProject)->get()->getRowArray();
        if (!$project) {
            return redirect()->to(site_url('admin/project-interior'))
                ->with('msg', 'Project interior tidak ditemukan.');
        }

        $projectId = (int)($project['id'] ?? 0);
        if ($projectId <= 0) {
            return redirect()->back()->with('msg', 'Project ID tidak valid.');
        }

        $dpRow = $db->table('project_interior_payment')
            ->selectSum('nominal', 'total_dp')
            ->where('project_id', $projectId)
            ->where('jenis', 'dp')
            ->get()->getRowArray();

        $totalDp = (int)($dpRow['total_dp'] ?? 0);
        if ($totalDp <= 0) {
            return redirect()->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Untuk membuat Surat Jalan, project harus memiliki pembayaran DP terlebih dulu.');
        }

        $items = $db->table('project_interior_item')
            ->where('project_id', $projectId)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (!$items) {
            return redirect()->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Item project interior masih kosong. Tidak bisa membuat SJ.');
        }

        $idPesanan = (string)($project['kode_sj'] ?? '');
        if ($idPesanan === '') {
            return redirect()->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Kode dokumen utama (kode_sj) belum ada. Tidak bisa membuat SJ.');
        }

        $last = $db->table('surat_jalan')
            ->select('sj_ke')
            ->where('id_pesanan', $idPesanan)
            ->orderBy('sj_ke', 'DESC')
            ->get(1)->getRowArray();

        $nextKe = ((int)($last['sj_ke'] ?? 0)) + 1;

        $db->table('surat_jalan')->insert([
            'id_pesanan' => $idPesanan,
            'sj_ke'      => $nextKe,
            'status'     => 'draft',
            'no_sj'      => null,
            'tanggal'    => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $sjId = (int)$db->insertID();
        if ($sjId <= 0) {
            return redirect()->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Gagal membuat SJ draft.');
        }

        foreach ($items as $it) {
            $db->table('surat_jalan_item')->insert([
                'surat_jalan_id' => $sjId,
                'id_barang'      => null,
                'varian'         => (string)($it['nama_barang'] ?? ''),
                'qty'            => 0,
                'harga_satuan'   => (int)($it['harga_satuan'] ?? 0),
                'kode_barang'    => (string)($it['kode_barang'] ?? ''),
            ]);
        }

        return redirect()->to(site_url('admin/surat-jalan/offline/' . $sjId . '/edit'))
            ->with('msg', 'SJ draft berhasil dibuat. Silakan isi qty kirim per item lalu FINALIZE.');
    }

    /**
     * View SJ for interior project
     */
    public function projectInteriorSuratJalan(string $kodeProject)
    {
        $project = $this->projectInteriorModel
            ->where('kode_project', $kodeProject)
            ->first();

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Project interior dengan kode {$kodeProject} tidak ditemukan."
            );
        }

        $db = \Config\Database::connect();
        $offline = $db->table('pemesanan_offline')
            ->where('id_pesanan', $project['kode_sj'])
            ->whereIn('jenis', ['sale', 'nf'])
            ->get()->getRowArray();

        $qty = (int)($project['qty'] ?? 1);
        if ($qty <= 0) $qty = 1;

        $namaBarang = $project['nama_barang'] ?: ('PEKERJAAN INTERIOR - ' . $project['nama_project']);
        $kodeBarang = !empty($project['kode_barang']) ? $project['kode_barang'] : $project['kode_project'];

        $items = [
            [
                'id_baru'  => strtoupper($kodeBarang),
                'nama'     => $namaBarang,
                'varian'   => $project['nama_project'] ?? '',
                'dimensi'  => ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'],
                'jumlah'   => $qty,
            ],
        ];

        $tanggalSj = $offline['tanggal'] ?? date('Y-m-d');

        $pemesanan = [
            'id_pesanan'        => $project['kode_sj'],
            'jenis'             => $offline['jenis'] ?? 'sale',
            'tanggal'           => $tanggalSj,
            'nama'              => $offline['nama'] ?? ($project['nama_project'] ?? '-'),
            'nohp'              => $offline['nohp'] ?? '',
            'alamat_pengiriman' => $offline['alamat_pengiriman'] ?? '',
            'keterangan'        => $offline['keterangan'] ?? null,
        ];

        return view('admin/suratOffline', [
            'title'     => 'Surat Jalan Project Interior',
            'pemesanan' => $pemesanan,
            'items'     => $items,
        ]);
    }

    /**
     * Invoice Pengiriman - Accumulated invoice showing all SJ up to specified SJ
     * @param string $kodeProject - Project code
     * @param int $sjId - The SJ ID up to which to accumulate (0 = all)
     */
    public function projectInteriorInvoicePengiriman(string $kodeProject, int $sjId = 0)
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s', strtotime('+7 hours'));

        // Get project
        $project = $this->projectInteriorModel
            ->where('kode_project', $kodeProject)
            ->first();

        if (!$project) {
            return redirect()->to(site_url('admin/project-interior'))
                ->with('msg', 'Project tidak ditemukan.');
        }

        $projectId = (int)$project['id'];
        $kodeSj = $project['kode_sj'] ?? '';

        // Get all SJ for this project
        $sjQuery = $db->table('surat_jalan')
            ->where('id_pesanan', $kodeSj)
            ->where('status', 'final')
            ->orderBy('sj_ke', 'ASC');

        // If specific SJ ID provided, limit to that SJ and before
        if ($sjId > 0) {
            $targetSj = $db->table('surat_jalan')->where('id', $sjId)->get()->getRowArray();
            if ($targetSj) {
                $sjQuery->where('sj_ke <=', $targetSj['sj_ke']);
            }
        }

        $sjList = $sjQuery->get()->getResultArray();

        if (empty($sjList)) {
            return redirect()->to(site_url('admin/project-interior/' . $kodeProject))
                ->with('msg', 'Tidak ada Surat Jalan yang bisa di-invoice.');
        }

        // Collect all SJ IDs and numbers
        $sjIds = array_column($sjList, 'id');
        $sjNumbers = array_column($sjList, 'no_sj');

        // Get shipped items from these SJ
        $shippedItems = $db->table('surat_jalan_item sji')
            ->select('sji.kode_barang, sji.nama_barang, SUM(sji.qty) as qty_shipped')
            ->join('surat_jalan sj', 'sj.id = sji.surat_jalan_id')
            ->whereIn('sji.surat_jalan_id', $sjIds)
            ->groupBy('sji.kode_barang, sji.nama_barang')
            ->get()->getResultArray();

        // Get project items for pricing
        $projectItems = $db->table('project_interior_item')
            ->where('project_id', $projectId)
            ->get()->getResultArray();

        $itemPriceMap = [];
        foreach ($projectItems as $pi) {
            $itemPriceMap[$pi['kode_barang']] = (int)($pi['harga_satuan'] ?? 0);
        }

        // Calculate totals
        $invoiceItems = [];
        $totalNilai = 0;
        foreach ($shippedItems as $item) {
            $kode = $item['kode_barang'];
            $qty = (int)$item['qty_shipped'];
            $harga = $itemPriceMap[$kode] ?? 0;
            $subtotal = $qty * $harga;
            $totalNilai += $subtotal;

            $invoiceItems[] = [
                'kode_barang' => $kode,
                'nama_barang' => $item['nama_barang'],
                'qty' => $qty,
                'harga_satuan' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        // Check if invoice exists for this SJ combination
        $sjIdsStr = implode(',', $sjIds);
        $existingInvoice = $db->table('interior_invoice')
            ->where('project_id', $projectId)
            ->where('sj_ids', $sjIdsStr)
            ->get()->getRowArray();

        if (!$existingInvoice) {
            // Generate new invoice number
            $noInvoice = $this->generateInvoiceNumberGlobal($now);

            $db->table('interior_invoice')->insert([
                'project_id' => $projectId,
                'no_invoice' => $noInvoice,
                'tanggal' => $now,
                'sj_ids' => $sjIdsStr,
                'total_nilai' => $totalNilai,
                'created_at' => $now,
            ]);

            $invoiceId = $db->insertID();
            $existingInvoice = $db->table('interior_invoice')->where('id', $invoiceId)->get()->getRowArray();
        }

        // Get pemesanan_offline for customer info
        $offline = $db->table('pemesanan_offline')
            ->where('id_pesanan', $kodeSj)
            ->get()->getRowArray();

        return view('admin/projectInteriorInvoicePengiriman', [
            'title' => 'Invoice Pengiriman Interior',
            'project' => $project,
            'invoice' => $existingInvoice,
            'sj_list' => $sjList,
            'sj_numbers' => $sjNumbers,
            'items' => $invoiceItems,
            'total_nilai' => $totalNilai,
            'offline' => $offline,
        ]);
    }
}
