<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<section class="admin-records-page">
    <header class="admin-records-heading"><div><h1>Permintaan retur & refund</h1><p>Tinjau permintaan pelanggan dan catat tindak lanjut tim.</p></div><a class="btn btn-outline-secondary" href="/admin">Kembali ke admin</a></header>
    <?php if (!empty(session()->getFlashdata('msg'))): ?><div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('msg')); ?></div><?php endif; ?>
    <p class="admin-records-note">Perubahan status di sini mencatat penanganan. Proses refund pembayaran tetap mengikuti alur pembayaran yang berlaku.</p>
    <div class="admin-data-table-wrap"><table class="table admin-data-table align-middle"><thead><tr><th>Waktu</th><th>Pesanan</th><th>Pelanggan</th><th>Permintaan</th><th>Status & catatan</th><th>Tindak lanjut</th></tr></thead><tbody>
    <?php foreach ($rows as $row): ?>
        <?php $caseStatus = (string) ($row['status'] ?? 'reviewing'); ?>
        <tr>
            <td data-label="Waktu"><?= esc($row['created_at']); ?></td>
            <td data-label="Pesanan"><span class="admin-records-code"><?= esc($row['order_id']); ?></span></td>
            <td data-label="Pelanggan"><?= esc($row['customer_email']); ?></td>
            <td data-label="Permintaan"><strong><?= esc(ucfirst((string) $row['case_type'])); ?> · <?= esc($row['reason']); ?></strong><div class="admin-records-message"><?= nl2br(esc($row['message'] ?? '')); ?></div></td>
            <td data-label="Status & catatan"><span class="admin-status-pill admin-status-pill--<?= esc($caseStatus, 'attr'); ?>"><?= esc(ucfirst(str_replace('_', ' ', $caseStatus))); ?></span><?php if (!empty($row['staff_note'])): ?><div class="admin-records-muted mt-2"><?= esc($row['staff_note']); ?></div><?php endif; ?></td>
            <td data-label="Tindak lanjut">
                <form method="post" action="/admin/commerce-service-cases/<?= (int) $row['id']; ?>" class="admin-case-update-form">
                    <?= csrf_field(); ?>
                    <label class="visually-hidden" for="case-status-<?= (int) $row['id']; ?>">Status permintaan</label>
                    <select id="case-status-<?= (int) $row['id']; ?>" class="form-select form-select-sm" name="status">
                        <option value="reviewing" <?= $caseStatus === 'reviewing' ? 'selected' : ''; ?>>Sedang ditinjau</option>
                        <option value="resolved" <?= $caseStatus === 'resolved' ? 'selected' : ''; ?>>Ditangani</option>
                        <option value="rejected" <?= $caseStatus === 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                    <label class="visually-hidden" for="case-note-<?= (int) $row['id']; ?>">Catatan internal</label>
                    <textarea id="case-note-<?= (int) $row['id']; ?>" class="form-control form-control-sm" name="staff_note" maxlength="4000" placeholder="Catatan internal (opsional)"><?= esc($row['staff_note'] ?? ''); ?></textarea>
                    <button class="btn btn-sm btn-primary" type="submit">Simpan perubahan</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($rows === []): ?><tr><td colspan="6" class="admin-records-empty">Belum ada permintaan retur atau refund.</td></tr><?php endif; ?>
    </tbody></table></div>
</section>
<?= $this->endSection(); ?>
