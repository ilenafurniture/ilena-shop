<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<?php
$rupiah = static fn($value) => 'Rp ' . number_format((int)$value, 0, ',', '.');
$statusTone = static function ($status) {
    return match ($status) {
        'Proses' => 'success',
        'Menunggu Pembayaran' => 'warning',
        'Dibatalkan', 'Gagal', 'Ditolak', 'Kadaluarsa' => 'danger',
        default => 'neutral',
    };
};
?>

<style>
.dash-wrap {
    display: grid;
    gap: 18px;
}
.dash-hero {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 18px;
    padding: 24px;
    border-radius: 26px;
    background:
        radial-gradient(circle at top right, rgba(225, 29, 72, .16), transparent 18rem),
        linear-gradient(135deg, #111827 0%, #1f2937 58%, #881337 100%);
    color: #fff;
    box-shadow: 0 24px 60px rgba(15, 23, 42, .16);
    overflow: hidden;
}
.dash-kicker {
    margin: 0 0 8px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgba(255,255,255,.68);
}
.dash-title {
    margin: 0;
    font-size: clamp(26px, 3vw, 38px);
    line-height: 1.05;
    font-weight: 900;
    letter-spacing: -.06em;
}
.dash-subtitle {
    margin: 10px 0 0;
    max-width: 650px;
    color: rgba(255,255,255,.72);
    font-size: 14px;
}
.dash-actions {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.dash-btn {
    min-height: 42px;
    padding: 10px 14px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
    border: 1px solid rgba(255,255,255,.18);
    color: #fff;
    background: rgba(255,255,255,.10);
    backdrop-filter: blur(12px);
}
.dash-btn.primary {
    background: #fff;
    color: #111827;
}
.dash-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}
.dash-card {
    background: rgba(255, 255, 255, .9);
    border: 1px solid #eef2f7;
    border-radius: 22px;
    padding: 18px;
    box-shadow: 0 18px 42px rgba(15, 23, 42, .055);
}
.dash-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    min-height: 138px;
}
.dash-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff1f2;
    color: #be123c;
}
.dash-icon .material-icons { font-size: 22px; }
.dash-label {
    margin: 0 0 8px;
    font-size: 12px;
    font-weight: 850;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #94a3b8;
}
.dash-value {
    margin: 0;
    font-size: 31px;
    line-height: 1;
    font-weight: 900;
    letter-spacing: -.055em;
    color: #0f172a;
}
.dash-note {
    margin: 8px 0 0;
    font-size: 12px;
    color: #64748b;
}
.dash-two-col {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(320px, .75fr);
    gap: 14px;
}
.dash-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}
.dash-section-head h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -.04em;
}
.dash-section-head a {
    color: #be123c;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
}
.dash-list {
    display: grid;
    gap: 8px;
}
.dash-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    background: #fff;
}
.dash-row-title {
    margin: 0;
    font-size: 13.5px;
    font-weight: 850;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.dash-row-meta {
    margin: 4px 0 0;
    font-size: 12px;
    color: #64748b;
}
.dash-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 28px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 850;
    white-space: nowrap;
}
.dash-pill.success { background: #ecfdf5; color: #047857; }
.dash-pill.warning { background: #fffbeb; color: #b45309; }
.dash-pill.danger { background: #fef2f2; color: #be123c; }
.dash-pill.neutral { background: #f1f5f9; color: #475569; }
.quick-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}
.quick-card {
    min-height: 92px;
    padding: 14px;
    border-radius: 18px;
    border: 1px solid #eef2f7;
    background: #fff;
    text-decoration: none;
    color: #111827;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform .18s ease, box-shadow .18s ease;
}
.quick-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
}
.quick-card i { color: #be123c; }
.quick-card span { font-size: 13px; font-weight: 850; }
.empty-state {
    padding: 22px;
    text-align: center;
    color: #64748b;
    background: #f8fafc;
    border-radius: 18px;
    border: 1px dashed #cbd5e1;
}
@media (max-width: 1100px) {
    .dash-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dash-two-col { grid-template-columns: 1fr; }
}
@media (max-width: 760px) {
    .dash-hero { flex-direction: column; padding: 20px; border-radius: 22px; }
    .dash-actions { justify-content: flex-start; }
    .dash-grid { grid-template-columns: 1fr; }
    .quick-grid { grid-template-columns: 1fr; }
}
</style>

<div class="dash-wrap">
    <section class="dash-hero">
        <div>
            <p class="dash-kicker">Website Operation</p>
            <h1 class="dash-title">Dashboard Admin Ilena</h1>
            <p class="dash-subtitle">Pantau order website, produk, promo, dan konten dari satu tempat yang lebih ringkas.</p>
        </div>
        <div class="dash-actions">
            <a class="dash-btn primary" href="/admin/order/online"><i class="material-icons">shopping_bag</i>Urus Pesanan</a>
            <a class="dash-btn" href="/admin/addproduct"><i class="material-icons">add</i>Tambah Produk</a>
        </div>
    </section>

    <section class="dash-grid">
        <div class="dash-card dash-stat">
            <div>
                <p class="dash-label">Perlu Diurus</p>
                <p class="dash-value"><?= number_format($stats['websiteOrdersTodo'], 0, ',', '.'); ?></p>
                <p class="dash-note"><?= number_format($stats['waitingPayment'], 0, ',', '.'); ?> menunggu pembayaran</p>
            </div>
            <span class="dash-icon"><i class="material-icons">notifications_active</i></span>
        </div>
        <div class="dash-card dash-stat">
            <div>
                <p class="dash-label">Omzet Website</p>
                <p class="dash-value" style="font-size:25px;"><?= $rupiah($stats['revenue']); ?></p>
                <p class="dash-note"><?= number_format($stats['paidOrders'], 0, ',', '.'); ?> order terbayar/diproses</p>
            </div>
            <span class="dash-icon"><i class="material-icons">payments</i></span>
        </div>
        <div class="dash-card dash-stat">
            <div>
                <p class="dash-label">Produk Aktif</p>
                <p class="dash-value"><?= number_format($stats['activeProducts'], 0, ',', '.'); ?></p>
                <p class="dash-note">dari <?= number_format($stats['totalProducts'], 0, ',', '.'); ?> total produk</p>
            </div>
            <span class="dash-icon"><i class="material-icons">inventory_2</i></span>
        </div>
        <div class="dash-card dash-stat">
            <div>
                <p class="dash-label">Konten & Promo</p>
                <p class="dash-value"><?= number_format($stats['articles'] + $stats['activeVouchers'], 0, ',', '.'); ?></p>
                <p class="dash-note"><?= number_format($stats['articles'], 0, ',', '.'); ?> artikel · <?= number_format($stats['activeVouchers'], 0, ',', '.'); ?> voucher aktif</p>
            </div>
            <span class="dash-icon"><i class="material-icons">campaign</i></span>
        </div>
    </section>

    <section class="dash-two-col">
        <div class="dash-card">
            <div class="dash-section-head">
                <h2>Pesanan Website Terbaru</h2>
                <a href="/admin/order/online">Lihat semua</a>
            </div>
            <div class="dash-list">
                <?php if (empty($recentOrders)): ?>
                    <div class="empty-state">Belum ada pesanan website terbaru.</div>
                <?php endif; ?>
                <?php foreach ($recentOrders as $order): ?>
                    <?php $mid = json_decode($order['data_mid'] ?? '{}', true) ?: []; ?>
                    <div class="dash-row">
                        <div>
                            <p class="dash-row-title"><?= esc($order['id_midtrans'] ?? '-'); ?> · <?= esc($order['nama'] ?? '-'); ?></p>
                            <p class="dash-row-meta"><?= esc($order['email'] ?? '-'); ?> · <?= $rupiah($mid['gross_amount'] ?? 0); ?></p>
                        </div>
                        <span class="dash-pill <?= $statusTone($order['status'] ?? ''); ?>"><?= esc($order['status'] ?? '-'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-section-head">
                <h2>Aksi Cepat</h2>
            </div>
            <div class="quick-grid">
                <a class="quick-card" href="/admin/product"><i class="material-icons">inventory_2</i><span>Kelola Produk</span></a>
                <a class="quick-card" href="/admin/voucher"><i class="material-icons">confirmation_number</i><span>Voucher</span></a>
                <a class="quick-card" href="/admin/free-shipping"><i class="material-icons">local_shipping</i><span>Gratis Ongkir</span></a>
                <a class="quick-card" href="/admin/homelayout"><i class="material-icons">dashboard_customize</i><span>Home Layout</span></a>
            </div>
        </div>
    </section>

    <section class="dash-card">
        <div class="dash-section-head">
            <h2>Stok Rendah</h2>
            <a href="/admin/product">Kelola produk</a>
        </div>
        <div class="dash-list">
            <?php if (empty($lowStockProducts)): ?>
                <div class="empty-state">Aman, belum ada produk dengan stok rendah.</div>
            <?php endif; ?>
            <?php foreach ($lowStockProducts as $product): ?>
                <div class="dash-row">
                    <div>
                        <p class="dash-row-title"><?= esc($product['nama']); ?></p>
                        <p class="dash-row-meta">ID #<?= esc($product['id']); ?></p>
                    </div>
                    <span class="dash-pill <?= $product['stok'] <= 0 ? 'danger' : 'warning'; ?>">Stok <?= number_format($product['stok'], 0, ',', '.'); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>
