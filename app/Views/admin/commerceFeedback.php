<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<section class="container py-4" style="max-width:1200px">
    <header class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4"><div><p class="text-uppercase small text-secondary mb-1">ILENA COMMERCE</p><h1 class="h3 mb-0">Ulasan & pertanyaan produk</h1></div><a class="btn btn-outline-secondary" href="/admin">Kembali ke admin</a></header>
    <?php if (!empty(session()->getFlashdata('msg'))): ?><div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('msg')); ?></div><?php endif; ?>
    <div class="alert alert-info">Kiriman baru berstatus menunggu. Tampilkan hanya ulasan/pertanyaan yang relevan dan tidak memuat data pribadi, spam, atau klaim yang tidak dapat diverifikasi.</div>
    <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Waktu</th><th>Produk</th><th>Jenis</th><th>Konten</th><th>Pelanggan</th><th>Status</th><th>Moderasi</th></tr></thead><tbody>
    <?php foreach ($rows as $row): ?><tr><td><?= esc($row['created_at']); ?></td><td><?= esc($row['product_id']); ?></td><td><?= esc($row['feedback_type']); ?><?= $row['rating'] ? ' · ' . (int)$row['rating'] . '/5' : ''; ?><?= (int)$row['verified_purchase'] ? ' · Pembeli terverifikasi' : ''; ?></td><td style="min-width:250px;white-space:pre-wrap"><?= esc($row['body']); ?></td><td><?= esc($row['author_name']); ?><br><small><?= esc($row['author_email']); ?></small></td><td><?= esc($row['status']); ?></td><td><?php if ($row['status'] === 'pending'): ?><form method="post" action="/admin/commerce-feedback/<?= (int)$row['id']; ?>" class="d-flex gap-1"><?= csrf_field(); ?><button class="btn btn-sm btn-success" name="status" value="approved">Setujui</button><button class="btn btn-sm btn-outline-danger" name="status" value="rejected">Tolak</button></form><?php else: ?><?= esc($row['moderated_by'] ?? '—'); ?><?php endif; ?></td></tr><?php endforeach; ?>
    <?php if ($rows === []): ?><tr><td colspan="7" class="text-center text-secondary py-5">Belum ada kiriman.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?= $this->endSection(); ?>
