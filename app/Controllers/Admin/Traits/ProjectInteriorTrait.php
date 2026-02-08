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
}
