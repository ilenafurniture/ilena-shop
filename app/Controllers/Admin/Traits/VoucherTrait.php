<?php
/**
 * VoucherTrait - Handles all voucher-related methods
 * 
 * Methods:
 * - voucher(): Display voucher list
 * - actionAddVoucher(): Add new voucher
 * - deleteVoucher($id): Delete voucher
 * - editVoucher($id): Edit voucher
 * - toggleVoucher($id): Toggle voucher active status
 * - voucherUsage(): Display voucher usage log
 * - deleteVoucherUsage($id): Delete voucher usage log
 */

namespace App\Controllers\Admin\Traits;

trait VoucherTrait
{
    /**
     * Display voucher list
     */
    public function voucher()
    {
        $q = $this->request->getGet('q');
        $builder = $this->voucherModel->orderBy('id','DESC');
        if ($q) {
            $builder->groupStart()
                ->like('kode', $q)
                ->orLike('nama', $q)
                ->groupEnd();
        }
        $data = [
            'title'   => 'Kelola Voucher',
            'voucher' => $builder->findAll(),
        ];
        return view('admin/voucher', $data);
    }

    /**
     * Add new voucher
     */
    public function actionAddVoucher()
    {
        $tipe  = $this->request->getPost('tipe') ?: 'persen';
        $nilai = (int)($this->request->getPost('nilai') ?? 0);

        $data = [
            'kode'   => strtoupper((string)$this->request->getPost('kode')),
            'nama'   => (string)$this->request->getPost('nama'),
            'deskripsi' => (string)$this->request->getPost('deskripsi'),
            'tipe'   => $tipe,
            'nilai'  => $nilai,
            'satuan'  => ($tipe === 'persen') ? 'persen' : 'rupiah',
            'nominal' => $nilai,
            'minimal_belanja' => (int)($this->request->getPost('minimal_belanja') ?: 0),
            'mulai'           => $this->request->getPost('mulai') ?: null,
            'berakhir'        => $this->request->getPost('berakhir') ?: null,
            'auto_apply'      => $this->request->getPost('auto_apply') ? 1 : 0,
            'aktif'           => $this->request->getPost('aktif') ? 1 : 0,
            'target'          => $this->request->getPost('target') ?: 'semua',
            'max_pakai'       => (int)($this->request->getPost('max_pakai') ?: 0),
            'sekali_pakai_per_user' => $this->request->getPost('sekali_pakai_per_user') ? 1 : 0,
        ];

        $this->voucherModel->insert($data);
        return redirect()->to('/admin/voucher')->with('msg', 'Voucher berhasil ditambahkan!');
    }

    /**
     * Delete voucher
     */
    public function deleteVoucher($id)
    {
        $this->voucherModel->delete((int)$id);
        return redirect()->to('/admin/voucher')->with('msg', 'Voucher berhasil dihapus!');
    }

    /**
     * Edit voucher
     */
    public function editVoucher($id)
    {
        $row = $this->voucherModel->find((int)$id);
        if (!$row) {
            return redirect()->to('/admin/voucher')->with('msg', 'Voucher tidak ditemukan.');
        }

        $tipe  = $this->request->getPost('tipe') ?: ($row['tipe'] ?? 'persen');
        $nilai = (int)($this->request->getPost('nilai') ?? ($row['nilai'] ?? 0));

        $payload = [
            'kode'   => strtoupper((string)$this->request->getPost('kode')),
            'nama'   => (string)$this->request->getPost('nama'),
            'deskripsi' => (string)$this->request->getPost('deskripsi'),
            'tipe'   => $tipe,
            'nilai'  => $nilai,
            'satuan'  => ($tipe === 'persen') ? 'persen' : 'rupiah',
            'nominal' => $nilai,
            'minimal_belanja' => (int)($this->request->getPost('minimal_belanja') ?? 0),
            'mulai'           => $this->request->getPost('mulai') ?: null,
            'berakhir'        => $this->request->getPost('berakhir') ?: null,
            'target'          => $this->request->getPost('target') ?: 'semua',
            'auto_apply'      => $this->request->getPost('auto_apply') ? 1 : 0,
            'sekali_pakai_per_user' => $this->request->getPost('sekali_pakai_per_user') ? 1 : 0,
        ];

        $payload = array_filter($payload, static function($v){ return $v !== null; });

        if ($payload === []) {
            return redirect()->to('/admin/voucher')->with('msg', 'Tidak ada perubahan.');
        }

        $this->voucherModel->update((int)$id, $payload);
        return redirect()->to('/admin/voucher')->with('msg', 'Voucher berhasil diperbarui!');
    }

    /**
     * Toggle voucher active status via AJAX
     */
    public function toggleVoucher($id)
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success'=>false,'message'=>'Method not allowed']);
        }

        $row = $this->voucherModel->find((int)$id);
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'Voucher tidak ditemukan']);
        }

        $aktifReq = $this->request->getPost('aktif');
        $new = ($aktifReq === null) ? (int)!((int)($row['aktif'] ?? 0))
                                    : ((int)$aktifReq ? 1 : 0);

        if ((int)$row['aktif'] === $new) {
            return $this->response->setJSON(['success'=>true,'message'=>'Tidak ada perubahan','data'=>['aktif'=>$new]]);
        }

        $ok = $this->voucherModel->update((int)$id, ['aktif' => $new]);
        return $this->response->setJSON([
            'success' => (bool)$ok,
            'message' => $ok ? 'Berhasil mengubah status.' : 'Gagal mengubah status.',
            'data'    => ['aktif' => $new]
        ]);
    }

    /**
     * Display voucher usage log
     */
    public function voucherUsage()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('voucher_usage vu')
            ->select('vu.id, vu.kode_voucher, vu.email, vu.used_at, v.nama as nama_voucher')
            ->join('voucher v', 'v.kode = vu.kode_voucher', 'left')
            ->orderBy('vu.id', 'DESC');

        $q = $this->request->getGet('q');
        if ($q) {
            $builder->groupStart()
                ->like('vu.kode_voucher', $q)
                ->orLike('vu.email', $q)
                ->orLike('v.nama', $q)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();

        return view('admin/voucher_usage', [
            'title' => 'Pemakaian Voucher',
            'rows'  => $rows,
            'q'     => $q
        ]);
    }

    /**
     * Delete voucher usage log
     */
    public function deleteVoucherUsage($id)
    {
        $this->voucherUsageModel->delete((int)$id);
        return redirect()->to('/admin/voucher/usage')->with('msg','Log penggunaan dihapus.');
    }
}
