<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<?php
$config = $config ?? [];
$isEnabled = !empty($config['enabled']);
?>

<style>
.meta-capi-page { max-width: 980px; margin: 0 auto; }
.meta-capi-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
}
.meta-capi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
}
.meta-capi-title { letter-spacing: -1px; font-weight: 800; }
.meta-capi-muted { color: #6b7280; }
.meta-capi-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    padding: 8px 13px;
    font-weight: 800;
    font-size: 13px;
}
.meta-capi-status.active { color: #166534; background: #dcfce7; }
.meta-capi-status.inactive { color: #991b1b; background: #fee2e2; }
.meta-capi-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #f8fafc;
    min-height: 48px;
}
.meta-capi-switch input { width: 18px; height: 18px; }
.meta-capi-help {
    border-left: 4px solid #dc2626;
    background: #fff7f7;
    border-radius: 12px;
    padding: 14px 16px;
    color: #374151;
}
.meta-capi-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
@media (max-width: 768px) {
    .meta-capi-header { flex-direction: column; }
    .meta-capi-grid { grid-template-columns: 1fr; }
}
</style>

<div class="meta-capi-page">
    <div class="meta-capi-header mb-4">
        <div>
            <h1 class="meta-capi-title mb-1">Meta CAPI</h1>
            <p class="meta-capi-muted mb-0">
                Atur Pixel ID dan Access Token Meta langsung dari admin tanpa edit file <code>.env</code>.
            </p>
        </div>
        <span class="meta-capi-status <?= $isEnabled ? 'active' : 'inactive'; ?>">
            <i class="material-icons" style="font-size:18px;"><?= $isEnabled ? 'check_circle' : 'pause_circle' ?></i>
            <?= $isEnabled ? 'Aktif' : 'Nonaktif'; ?>
        </span>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= esc($msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($err)): ?>
        <div class="alert alert-danger"><?= esc($err); ?></div>
    <?php endif; ?>

    <form action="/admin/meta-capi" method="post" class="meta-capi-card p-4">
        <div class="mb-4">
            <label class="meta-capi-switch mb-0">
                <input type="checkbox" name="enabled" value="1" <?= $isEnabled ? 'checked' : ''; ?>>
                <span>
                    <strong>Aktifkan Meta Conversions API</strong><br>
                    <small class="text-muted">Event Purchase dikirim saat webhook Midtrans membuat order menjadi Proses.</small>
                </span>
            </label>
        </div>

        <div class="meta-capi-grid mb-3">
            <div>
                <label for="pixel_id" class="form-label fw-bold">Meta Pixel ID <span class="text-danger">*</span></label>
                <input
                    type="text"
                    id="pixel_id"
                    name="pixel_id"
                    class="form-control"
                    value="<?= esc($config['pixel_id'] ?? ''); ?>"
                    placeholder="Contoh: 1030536602870149"
                    autocomplete="off">
                <div class="form-text">Ambil dari Meta Events Manager &gt; Data Sources.</div>
            </div>

            <div>
                <label for="graph_version" class="form-label fw-bold">Graph API Version</label>
                <input
                    type="text"
                    id="graph_version"
                    name="graph_version"
                    class="form-control"
                    value="<?= esc($config['graph_version'] ?? 'v20.0'); ?>"
                    placeholder="v20.0"
                    autocomplete="off">
                <div class="form-text">Default: v20.0.</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="access_token" class="form-label fw-bold">Meta Access Token</label>
            <input
                type="password"
                id="access_token"
                name="access_token"
                class="form-control"
                value=""
                placeholder="<?= !empty($maskedToken) ? esc($maskedToken) : 'Paste access token Meta di sini'; ?>"
                autocomplete="new-password">
            <div class="form-text">
                Kosongkan kalau token lama tetap dipakai. Isi ulang hanya jika ingin mengganti token.
            </div>
        </div>

        <div class="mb-4">
            <label for="test_event_code" class="form-label fw-bold">Test Event Code</label>
            <input
                type="text"
                id="test_event_code"
                name="test_event_code"
                class="form-control"
                value="<?= esc($config['test_event_code'] ?? ''); ?>"
                placeholder="Kosongkan untuk production"
                autocomplete="off">
            <div class="form-text">Isi saat testing di Meta Events Manager. Kosongkan lagi untuk produksi.</div>
        </div>

        <div class="meta-capi-help mb-4">
            <strong>Event yang dikirim:</strong> Purchase, currency IDR, order_id, content_ids, contents, hashed email,
            hashed phone, IP, user agent, fbp, dan fbc jika tersedia.
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <a href="/admin/order/online" class="btn btn-light border">Kembali</a>
            <button type="submit" class="btn-default-merah px-4">Simpan Meta CAPI</button>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>
