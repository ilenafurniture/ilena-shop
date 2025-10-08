<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid mt-2">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-1">📒 Pemakaian Voucher</h3>
            <div class="text-muted">Log redeem voucher oleh pelanggan. Hapus hanya jika betul-betul perlu (mis. data
                uji).</div>
        </div>
        <div>
            <a href="/admin/voucher" class="btn btn-outline-secondary">← Kembali ke Voucher</a>
        </div>
    </div>

    <?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('msg'); ?></div>
    <?php endif; ?>

    <form method="get" action="/admin/voucher/usage" class="d-flex gap-2 mb-3" style="max-width:480px;">
        <input type="text" class="form-control" name="q" value="<?= esc($q ?? '') ?>"
            placeholder="Cari kode/nama/email...">
        <button class="btn btn-primary" type="submit">Cari</button>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Kode Voucher</th>
                        <th>Nama Voucher</th>
                        <th>Email</th>
                        <th>Waktu</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): foreach($rows as $r): ?>
                    <tr>
                        <td class="text-muted"><?= (int)$r['id']; ?></td>
                        <td><span class="badge bg-dark-subtle text-dark"><?= esc($r['kode_voucher']); ?></span></td>
                        <td><?= esc($r['nama_voucher'] ?? ''); ?></td>
                        <td><?= esc($r['email']); ?></td>
                        <td><?= esc($r['used_at']); ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus log ini?')"
                                href="/admin/voucher/usage/delete/<?= (int)$r['id']; ?>">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada penggunaan</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>