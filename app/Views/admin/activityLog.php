<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<style>
.audit-page { max-width: 1200px; margin: 0 auto; padding: 10px 0 42px; }
.audit-card {
    background: transparent;
    border: 0;
    border-top: 1px solid var(--ia-line, #e8e4e1);
    border-radius: 0;
    box-shadow: none;
}
.audit-title { font-weight: 800; letter-spacing: -.8px; }
.audit-muted { color: #6b7280; }
.audit-item {
    border-bottom: 1px solid var(--ia-line, #e8e4e1);
    padding: 18px 0;
}
.audit-item:last-child { border-bottom: 0; }
.audit-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 3px;
    background: #f8eceb;
    color: #8d2e2b;
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
.audit-details { margin-top:12px; border-top:1px solid var(--ia-line,#e8e4e1); }
.audit-details summary { display:inline-flex; align-items:center; gap:6px; min-height:38px; color:#8d2e2b; cursor:pointer; font-size:12px; font-weight:700; list-style:none; }
.audit-details summary::-webkit-details-marker { display:none; }
.audit-details summary::after { content:'+'; font-size:17px; font-weight:400; }
.audit-details[open] summary::after { content:'−'; }
.audit-detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px 24px; padding:7px 0 3px; }
.audit-detail { min-width:0; }
.audit-detail dt { margin-bottom:3px; color:#77716d; font-size:10px; font-weight:700; }
.audit-detail dd { margin:0; color:#302d2b; font-size:12px; overflow-wrap:anywhere; }
.audit-changes { display:grid; gap:5px; margin:0; padding:0; list-style:none; }
.audit-changes li { display:flex; flex-wrap:wrap; gap:5px; }
.audit-changes strong { color:#6e6865; font-weight:600; }
@media(max-width:640px){ .audit-detail-grid{grid-template-columns:1fr;} }
</style>

<div class="audit-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4 audit-heading">
        <div>
            <h1 class="audit-title mb-1">Log Aktivitas Admin</h1>
            <p class="audit-muted mb-0">Catatan lengkap tentang perubahan yang dilakukan admin, siapa yang melakukannya, dan kapan.</p>
        </div>
        <a href="/admin/product" class="btn btn-light border">Kembali</a>
    </div>

    <div class="audit-card" aria-label="Daftar aktivitas admin">
        <?php if (empty($logs)): ?>
            <div class="audit-empty">
                <i class="material-icons d-block mb-2" style="font-size:42px;color:#9ca3af;">history</i>
                Belum ada aktivitas perubahan yang tercatat.
            </div>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <?php $logDate = (new DateTimeImmutable((string) ($log['created_at'] ?? 'now'), new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta')); ?>
                <div class="audit-item">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <span class="audit-badge">
                                <i class="material-icons" style="font-size:16px;">edit_note</i>
                                <?= esc($log['activity'] ?? 'Aktivitas admin'); ?>
                            </span>
                        </div>
                        <div class="audit-muted small">
                            <?= esc($logDate->format('d M Y, H:i:s')); ?> WIB
                        </div>
                    </div>

                    <p class="audit-desc mb-2"><?= esc($log['description'] ?? 'Rincian tindakan belum tersedia.'); ?></p>

                    <div class="audit-meta">
                        <span><b>Admin:</b> <?= esc($log['actor_name'] ?? '-'); ?></span>
                        <span><b>Email:</b> <?= esc($log['actor_email'] ?? '-'); ?></span>
                        <span><b>Role:</b> <?= esc($log['role_label'] ?? 'Admin'); ?></span>
                        <span><b>Alamat jaringan:</b> <?= esc($log['ip_address'] ?? '-'); ?></span>
                    </div>

                    <?php
                    $recordedChanges = json_decode((string) ($log['request_data'] ?? ''), true);
                    $recordedChanges = is_array($recordedChanges) ? $recordedChanges : [];
                    $safeUrl = (string) ($log['url'] ?? '');
                    $pagePath = parse_url($safeUrl, PHP_URL_PATH) ?: '-';
                    $actionSource = match (strtoupper((string) ($log['method'] ?? ''))) {
                        'POST' => 'Formulir disimpan',
                        'PUT', 'PATCH' => 'Data diperbarui',
                        'DELETE' => 'Data dihapus',
                        default => 'Tindakan dicatat',
                    };
                    $userAgent = (string) ($log['user_agent'] ?? '');
                    $browser = str_contains($userAgent, 'Edg/') ? 'Microsoft Edge'
                        : (str_contains($userAgent, 'Firefox/') ? 'Mozilla Firefox'
                        : (str_contains($userAgent, 'Chrome/') ? 'Google Chrome'
                        : (str_contains($userAgent, 'Safari/') ? 'Safari' : 'Tidak diketahui')));
                    $device = str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')
                        ? 'Ponsel' : 'Komputer';
                    $changeLabels = [
                        'id' => 'ID data', 'product_id' => 'ID produk', 'order_id' => 'ID pesanan',
                        'status' => 'Status', 'aktif' => 'Status aktif', 'stok' => 'Stok',
                        'quantity' => 'Jumlah', 'qty' => 'Jumlah', 'kategori' => 'Kategori',
                        'type' => 'Jenis', 'jenis' => 'Jenis', 'role' => 'Peran', 'role_id' => 'ID peran',
                        'permission' => 'Hak akses', 'permissions' => 'Hak akses', 'action' => 'Tindakan',
                    ];
                    ?>
                    <details class="audit-details">
                        <summary>Lihat rincian lengkap</summary>
                        <dl class="audit-detail-grid">
                            <div class="audit-detail"><dt>Tindakan tersimpan</dt><dd><?= esc($actionSource); ?></dd></div>
                            <div class="audit-detail"><dt>Halaman terkait</dt><dd><?= esc($pagePath); ?></dd></div>
                            <div class="audit-detail"><dt>Perangkat dan browser</dt><dd><?= esc($device . ' · ' . $browser); ?></dd></div>
                            <div class="audit-detail"><dt>Alamat jaringan</dt><dd><?= esc($log['ip_address'] ?? '-'); ?></dd></div>
                            <div class="audit-detail"><dt>Rincian perubahan yang terekam</dt><dd>
                                <?php if ($recordedChanges === []): ?>Belum ada rincian kolom perubahan yang tersimpan.<?php else: ?>
                                    <ul class="audit-changes">
                                        <?php foreach ($recordedChanges as $key => $value): ?>
                                            <?php $displayValue = is_array($value) ? implode(', ', array_map('strval', $value)) : (string) $value; ?>
                                            <li><strong><?= esc($changeLabels[strtolower((string) $key)] ?? ucwords(str_replace(['_', '-'], ' ', (string) $key))); ?>:</strong><span><?= esc($displayValue); ?></span></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </dd></div>
                        </dl>
                    </details>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
