<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<section class="admin-records-page">
    <header class="admin-records-heading"><div><h1>Ulasan & pertanyaan</h1><p>Periksa kiriman pelanggan, lalu setujui yang layak ditampilkan.</p></div><a class="btn btn-outline-secondary" href="/admin">Kembali ke admin</a></header>
    <?php if (!empty(session()->getFlashdata('msg'))): ?><div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('msg')); ?></div><?php endif; ?>
    <p class="admin-records-note">Publikasikan kiriman yang relevan dan tidak memuat data pribadi, spam, atau klaim yang tidak terverifikasi.</p>
    <div class="admin-data-table-wrap"><table class="table admin-data-table align-middle"><thead><tr><th>Waktu</th><th>Produk</th><th>Jenis</th><th>Kiriman</th><th>Pelanggan</th><th>Status</th><th>Tindakan</th></tr></thead><tbody>
    <?php foreach ($rows as $row): ?>
        <?php $feedbackStatus = (string) ($row['status'] ?? 'pending'); ?>
        <tr>
            <td data-label="Waktu"><?= esc($row['created_at']); ?></td>
            <td data-label="Produk"><span class="admin-records-code"><?= esc($row['product_id']); ?></span></td>
            <td data-label="Jenis"><?= esc(ucfirst((string) $row['feedback_type'])); ?><?php if (!empty($row['rating'])): ?><br><span class="admin-records-muted">Nilai <?= (int) $row['rating']; ?>/5</span><?php endif; ?><?php if ((int) $row['verified_purchase'] === 1): ?><br><span class="admin-records-muted">Pembeli terverifikasi</span><?php endif; ?></td>
            <td data-label="Kiriman" class="admin-records-message"><?= esc($row['body']); ?></td>
            <td data-label="Pelanggan"><?= esc($row['author_name']); ?><br><small><?= esc($row['author_email']); ?></small></td>
            <td data-label="Status"><span class="admin-status-pill admin-status-pill--<?= esc($feedbackStatus, 'attr'); ?>"><?= esc(ucfirst($feedbackStatus)); ?></span></td>
            <td data-label="Tindakan">
                <?php if ($feedbackStatus === 'pending'): ?>
                    <form method="post" action="/admin/commerce-feedback/<?= (int) $row['id']; ?>" class="admin-feedback-actions">
                        <?= csrf_field(); ?>
                        <button class="btn btn-sm btn-primary" type="submit" name="status" value="approved">Setujui</button>
                        <button class="btn btn-sm btn-outline-danger" type="submit" name="status" value="rejected">Tolak</button>
                    </form>
                <?php else: ?><span class="admin-records-muted"><?= esc($row['moderated_by'] ?? 'Sudah ditinjau'); ?></span><?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($rows === []): ?><tr><td colspan="7" class="admin-records-empty">Belum ada ulasan atau pertanyaan.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?= $this->endSection(); ?>
