<?php
/**
 * SuratJalanTrait - Handles surat jalan (delivery document) methods
 * 
 * Methods:
 * - createSuratJalanOffline(): Create SJ for offline order
 * - suratJalanOffline(): View SJ
 * - suratJalanOfflineEdit(): Edit SJ form
 * - suratJalanOfflineEditSave(): Save SJ edits
 * - suratJalanOfflineItemAdd(): Add item to SJ
 * - suratJalanOfflineItemDelete(): Delete item from SJ
 * - suratJalanOfflineFinalize(): Finalize SJ
 */

namespace App\Controllers\Admin\Traits;

trait SuratJalanTrait
{
    /**
     * Create SJ for offline order
     */
    public function createSuratJalanOffline(string $idPesanan)
    {
        $order = $this->pemesananOfflineModel->where('id_pesanan', $idPesanan)->first();
        if (!$order) {
            return redirect()->back()->with('msg', 'Pesanan tidak ditemukan.');
        }

        $lastKe = $this->suratJalanModel->getLastSjKe($idPesanan);
        $sjKe   = $lastKe + 1;

        $now = date('Y-m-d H:i:s', strtotime('+7 hours'));
        $normJenis = $this->normalizeJenis($order['jenis'] ?? 'sale');
        $noSj = ($normJenis === 'nf') ? $this->generateSjNumberGlobalNF($now) : $this->generateSjNumberGlobal($now);
        
        $this->suratJalanModel->insert([
            'id_pesanan'   => $idPesanan,
            'no_sj'        => $noSj,
            'sj_ke'        => $sjKe,
            'tanggal'      => $now,
            'status'       => 'final',
            'finalized_at' => $now,
        ]);
        $sjId = (int)$this->suratJalanModel->getInsertID();

        $ordered    = $this->groupOrderItems($idPesanan);
        $shippedMap = $this->shippedQtyMap($idPesanan);

        foreach ($ordered as $o) {
            $key = $o['id_barang'] . '||' . $o['varian'];
            $already = (int)($shippedMap[$key] ?? 0);
            $remain  = max(0, (int)$o['qty'] - $already);
            if ($remain <= 0) continue;

            $this->suratJalanItemModel->insert([
                'surat_jalan_id' => $sjId,
                'id_barang'      => (int)$o['id_barang'],
                'varian'         => (string)$o['varian'],
                'qty'            => $remain,
            ]);
        }

        return redirect()->to('/admin/surat-jalan/offline/' . $sjId . '/edit')
            ->with('msg', 'SJ ke-' . $sjKe . ' dibuat (DRAFT). Cek qty lalu finalize untuk nomor SJ.');
    }

    /**
     * View SJ
     */
    public function suratJalanOffline(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        $pemesanan = $this->pemesananOfflineModel
            ->where('id_pesanan', $sj['id_pesanan'])
            ->first();
        if (!$pemesanan) return redirect()->back()->with('msg', 'Pesanan sumber SJ tidak ditemukan.');

        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.*')
            ->select('barang.nama as barang_nama, barang.deskripsi as barang_deskripsi')
            ->join('barang', 'barang.id = surat_jalan_item.id_barang', 'left')
            ->where('surat_jalan_item.surat_jalan_id', $suratJalanId)
            ->orderBy('surat_jalan_item.id', 'ASC')
            ->findAll();

        $items = [];
        foreach ($rows as $r) {
            $dim = ['panjang'=>'-','lebar'=>'-','tinggi'=>'-'];

            if (!empty($r['dimensi_json'])) {
                $dj = json_decode($r['dimensi_json'], true);
                if (is_array($dj)) $dim = array_merge($dim, $dj);
            } else {
                $d = json_decode($r['barang_deskripsi'] ?? '', true);
                if (is_array($d) && isset($d['dimensi']['asli'])) {
                    $dim = $d['dimensi']['asli'];
                }
            }

            $kode = !empty($r['kode_barang']) ? $r['kode_barang'] : (string)($r['id_barang'] ?? '');
            $nama = !empty($r['nama_barang']) ? $r['nama_barang'] : (string)($r['barang_nama'] ?? '-');

            $items[] = [
                'id_baru' => (string)$kode,
                'nama'    => (string)$nama,
                'varian'  => (string)($r['varian'] ?? ''),
                'dimensi' => $dim,
                'jumlah'  => (int)($r['qty'] ?? 0),
            ];
        }

        return view('admin/suratOffline', [
            'title'     => 'Surat Jalan',
            'pemesanan' => $pemesanan,
            'items'     => $items,
            'sj'        => $sj,
        ]);
    }

    /**
     * Edit SJ form
     */
    public function suratJalanOfflineEdit(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        $pemesanan = $this->pemesananOfflineModel->where('id_pesanan', $sj['id_pesanan'])->first();
        if (!$pemesanan) return redirect()->back()->with('msg', 'Pesanan sumber tidak ditemukan.');

        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.*')
            ->select('barang.nama as barang_nama, barang.deskripsi as barang_deskripsi')
            ->join('barang', 'barang.id = surat_jalan_item.id_barang', 'left')
            ->where('surat_jalan_item.surat_jalan_id', $suratJalanId)
            ->orderBy('surat_jalan_item.id', 'ASC')
            ->findAll();

        $isInteriorSj = false;
        foreach ($rows as $r) {
            if (empty($r['id_barang'])) { $isInteriorSj = true; break; }
        }

        $ordered = [];
        if (!$isInteriorSj) {
            $ordered = $this->groupOrderItems($sj['id_pesanan']);
        }

        $shippedMap = $this->shippedQtyMapExceptSj($sj['id_pesanan'], $suratJalanId);

        return view('admin/suratJalanEdit', [
            'title'        => 'Edit Surat Jalan',
            'sj'           => $sj,
            'pemesanan'    => $pemesanan,
            'rows'         => $rows,
            'ordered'      => $ordered,
            'shippedMap'   => $shippedMap,
            'isInteriorSj' => $isInteriorSj,
            'msg'          => session()->getFlashdata('msg'),
        ]);
    }

    /**
     * Save SJ edits
     */
    public function suratJalanOfflineEditSave(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (in_array(($sj['status'] ?? ''), ['final','printed','void'], true)) {
            return redirect()->back()->with('msg', 'SJ sudah final/printed, tidak bisa diedit.');
        }

        $req = $this->request;

        $tanggal = $req->getPost('tanggal');
        if ($tanggal) {
            $tanggal = str_replace('T', ' ', $tanggal);
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/', $tanggal)) $tanggal .= ':00';
            $this->suratJalanModel->where('id', $suratJalanId)->set(['tanggal' => $tanggal])->update();
        }

        $itemIds = $req->getPost('item_id');
        $qtys    = $req->getPost('qty');

        if (!is_array($itemIds) || !is_array($qtys) || count($itemIds) !== count($qtys)) {
            return redirect()->back()->with('msg', 'Payload item tidak valid.');
        }

        $shippedMap = $this->shippedQtyMapExceptSj($sj['id_pesanan'], $suratJalanId);
        $rows = $this->suratJalanItemModel->where('surat_jalan_id', $suratJalanId)->findAll();
        $byId = [];
        foreach ($rows as $r) $byId[(int)$r['id']] = $r;

        $isInteriorSj = false;
        foreach ($rows as $r) { if (empty($r['id_barang'])) { $isInteriorSj = true; break; } }

        $orderedMap = [];
        if (!$isInteriorSj) {
            $ordered = $this->groupOrderItems($sj['id_pesanan']);
            foreach ($ordered as $o) {
                $key = (int)$o['id_barang'].'||'.(string)$o['varian'];
                $orderedMap[$key] = (int)$o['qty'];
            }
        } else {
            $db = \Config\Database::connect();
            $proj = $db->table('project_interior')
                ->where('kode_sj', $sj['id_pesanan'])
                ->get()->getRowArray();

            if (!$proj) {
                return redirect()->back()->with('msg', 'Project interior tidak ditemukan untuk SJ ini.');
            }

            $projectId = (int)($proj['id'] ?? 0);
            $items = $db->table('project_interior_item')
                ->where('project_id', $projectId)
                ->get()->getResultArray();

            foreach ($items as $it) {
                $kode = trim((string)($it['kode_barang'] ?? ''));
                if ($kode === '') continue;
                $key = 'I||' . $kode;
                $orderedMap[$key] = (int)($it['qty'] ?? 0);
            }
        }

        $newTotals = [];
        for ($i=0; $i<count($itemIds); $i++) {
            $id  = (int)$itemIds[$i];
            $qty = (int)$qtys[$i];
            if (!isset($byId[$id])) continue;
            if ($qty < 0) $qty = 0;

            $r = $byId[$id];

            if (!$isInteriorSj) {
                $key = (int)$r['id_barang'].'||'.(string)$r['varian'];
            } else {
                $kode = trim((string)($r['kode_barang'] ?? ''));
                $key  = 'I||' . $kode;
            }

            $newTotals[$key] = ($newTotals[$key] ?? 0) + $qty;
        }

        foreach ($newTotals as $key => $qtyNew) {
            $already = (int)($shippedMap[$key] ?? 0);
            $allowed = (int)($orderedMap[$key] ?? 0);

            if (($already + $qtyNew) > $allowed) {
                $remain = max(0, $allowed - $already);
                return redirect()->back()->with('msg', 'Qty melebihi sisa untuk item ini. Sisa yang boleh dikirim: ' . $remain);
            }
        }

        for ($i=0; $i<count($itemIds); $i++) {
            $id  = (int)$itemIds[$i];
            $qty = (int)$qtys[$i];
            if ($qty < 0) $qty = 0;
            $this->suratJalanItemModel->where('id', $id)->set(['qty' => $qty])->update();
        }

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
            ->with('msg', 'SJ berhasil disimpan.');
    }

    /**
     * Add item to SJ
     */
    public function suratJalanOfflineItemAdd(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (($sj['status'] ?? '') === 'final') {
            return redirect()->back()->with('msg', 'SJ sudah final, tidak bisa ditambah item.');
        }

        $req = $this->request;
        $mode = $req->getPost('mode');
        $qty  = (int)$req->getPost('qty');
        if ($qty <= 0) $qty = 1;

        if ($mode === 'interior') {
            $kodeBarang = trim((string)$req->getPost('kode_barang'));
            $namaBarang = trim((string)$req->getPost('nama_barang'));
            $varian     = trim((string)$req->getPost('varian'));

            if ($kodeBarang === '' || $namaBarang === '') {
                return redirect()->back()->with('msg', 'Kode barang & nama barang wajib diisi.');
            }

            $this->suratJalanItemModel->insert([
                'surat_jalan_id' => $suratJalanId,
                'id_barang'      => null,
                'kode_barang'    => $kodeBarang,
                'nama_barang'    => $namaBarang,
                'varian'         => $varian,
                'qty'            => $qty,
                'dimensi_json'   => json_encode(['panjang'=>'-','lebar'=>'-','tinggi'=>'-']),
            ]);

            return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
                ->with('msg', 'Item interior ditambahkan.');
        }

        $idBarang = (int)$req->getPost('id_barang');
        $varian   = trim((string)$req->getPost('varian'));

        if ($idBarang <= 0 || $varian === '') {
            return redirect()->back()->with('msg', 'Barang & varian wajib dipilih.');
        }

        $this->suratJalanItemModel->insert([
            'surat_jalan_id' => $suratJalanId,
            'id_barang'      => $idBarang,
            'kode_barang'    => null,
            'nama_barang'    => null,
            'varian'         => $varian,
            'qty'            => $qty,
            'dimensi_json'   => null,
        ]);

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
            ->with('msg', 'Item offline ditambahkan.');
    }

    /**
     * Delete item from SJ
     */
    public function suratJalanOfflineItemDelete(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (($sj['status'] ?? '') === 'final') {
            return redirect()->back()->with('msg', 'SJ sudah final, tidak bisa hapus item.');
        }

        $itemId = (int)$this->request->getPost('item_id');
        if ($itemId <= 0) return redirect()->back()->with('msg', 'Item tidak valid.');

        $this->suratJalanItemModel->where('id', $itemId)->where('surat_jalan_id', $suratJalanId)->delete();

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId.'/edit')
            ->with('msg', 'Item dihapus.');
    }

    /**
     * Finalize SJ
     */
    public function suratJalanOfflineFinalize(int $suratJalanId)
    {
        $sj = $this->suratJalanModel->where('id', $suratJalanId)->first();
        if (!$sj) return redirect()->back()->with('msg', 'Surat jalan tidak ditemukan.');

        if (($sj['status'] ?? '') === 'final') {
            return redirect()->back()->with('msg', 'SJ sudah final.');
        }

        $sum = (int)($this->suratJalanItemModel
            ->selectSum('qty','total')
            ->where('surat_jalan_id', $suratJalanId)
            ->get()->getRowArray()['total'] ?? 0);

        if ($sum <= 0) {
            return redirect()->back()->with('msg', 'Tidak ada qty untuk diprint. Isi qty dulu.');
        }

        $tanggalDb = $sj['tanggal'] ?? date('Y-m-d H:i:s', strtotime('+7 hours'));

        $noSj = $sj['no_sj'] ?? null;
        if (!$noSj) {
            $noSj = $this->generateSjNumberGlobal($tanggalDb);
        }

        $this->suratJalanModel->where('id', $suratJalanId)->set([
            'no_sj'        => $noSj,
            'status'       => 'final',
            'finalized_at' => date('Y-m-d H:i:s', strtotime('+7 hours')),
        ])->update();

        return redirect()->to('/admin/surat-jalan/offline/'.$suratJalanId)
            ->with('msg', 'SJ di-finalize. Nomor: '.$noSj.' (siap print)');
    }
}
