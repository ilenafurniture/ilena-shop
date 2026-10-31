<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="container py-4" style="max-width:1200px">
    <header class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4"><div><p class="text-uppercase small text-secondary mb-1">ILENA COMMERCE</p><h1 class="h3 mb-0">Permintaan pembatalan, retur & refund</h1></div><a class="btn btn-outline-secondary" href="/admin">Kembali ke admin</a></header>
    <?php if (!empty(session()->getFlashdata('msg'))): ?><div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('msg')); ?></div><?php endif; ?>
    <div class="alert alert-warning">Halaman ini mencatat dan melacak permintaan; perubahan status/refund pembayaran tetap harus diproses melalui alur pembayaran dan kebijakan yang berlaku.</div>
    <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Waktu</th><th>Pesanan</th><th>Pelanggan</th><th>Permintaan</th><th>Status</th><th>Perbarui</th></tr></thead><tbody>
    <?php foreach ($rows as $row): ?><tr><td><?= esc($row['created_at']); ?></td><td><?= esc($row['order_id']); ?></td><td><?= esc($row['customer_email']); ?></td><td><strong><?= esc(ucfirst($row['case_type'])); ?> · <?= esc($row['reason']); ?></strong><br><?= nl2br(esc($row['message'] ?? '')); ?></td><td><?= esc($row['status']); ?><?php if (!empty($row['staff_note'])): ?><br><small><?= esc($row['staff_note']); ?></small><?php endif; ?></td><td><form method="post" action="/admin/commerce-service-cases/<?= (int)$row['id']; ?>" class="d-grid gap-2"><?= csrf_field(); ?><select class="form-select form-select-sm" name="status"><option value="reviewing">Sedang ditinjau</option><option value="resolved">Ditangani</option><option value="rejected">Ditolak</option></select><textarea class="form-control form-control-sm" name="staff_note" maxlength="4000" placeholder="Catatan untuk tim"></textarea><button class="btn btn-sm btn-dark">Simpan</button></form></td></tr><?php endforeach; ?>
    <?php if ($rows === []): ?><tr><td colspan="6" class="text-center text-secondary py-5">Belum ada permintaan layanan.</td></tr><?php endif; ?>
    </tbody></table></div>
</main>
<?= $this->endSection(); ?>
