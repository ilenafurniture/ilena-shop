<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<style>
.audit-page { max-width: 1200px; margin: 0 auto; }
.audit-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}
.audit-title { font-weight: 800; letter-spacing: -.8px; }
.audit-muted { color: #6b7280; }
.audit-item {
    border-bottom: 1px solid #eef2f7;
    padding: 16px 18px;
}
.audit-item:last-child { border-bottom: 0; }
.audit-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    font-size: 12px;
    font-weight: 800;
}
.audit-desc {
    color: #374151;
    line-height: 1.55;
}
.audit-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 14px;
    color: #6b7280;
    font-size: 13px;
}
.audit-empty {
    padding: 48px 18px;
    text-align: center;
    color: #6b7280;
}
</style>

<div class="audit-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="audit-title mb-1">Log Aktivitas Admin</h1>
            <p class="audit-muted mb-0">
                Catatan perubahan admin dalam bahasa sederhana. Data lebih dari 6 bulan otomatis dihapus permanen.
            </p>
        </div>
        <a href="/admin/product" class="btn btn-light border">Kembali</a>
    </div>

    <div class="audit-card">
        <?php if (empty($logs)): ?>
            <div class="audit-empty">
                <i class="material-icons d-block mb-2" style="font-size:42px;color:#9ca3af;">history</i>
                Belum ada aktivitas perubahan yang tercatat.
            </div>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <div class="audit-item">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <span class="audit-badge">
                                <i class="material-icons" style="font-size:16px;">edit_note</i>
                                <?= esc($log['activity'] ?? 'Aktivitas admin'); ?>
                            </span>
                        </div>
                        <div class="audit-muted small">
                            <?= esc(date('d M Y H:i', strtotime($log['created_at'] ?? 'now'))); ?>
                        </div>
                    </div>

                    <p class="audit-desc mb-2"><?= esc($log['description'] ?? '-'); ?></p>

                    <div class="audit-meta">
                        <span><b>Admin:</b> <?= esc($log['actor_name'] ?? '-'); ?></span>
                        <span><b>Email:</b> <?= esc($log['actor_email'] ?? '-'); ?></span>
                        <span><b>Role:</b> <?= esc($log['role_label'] ?? 'Admin'); ?></span>
                        <span><b>IP:</b> <?= esc($log['ip_address'] ?? '-'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
