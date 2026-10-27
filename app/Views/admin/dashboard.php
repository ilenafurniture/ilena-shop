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
/* Ilena editorial dashboard: compact metrics and separators instead of oversized cards. */
.dash-wrap { max-width:1480px; margin:0 auto; padding:10px clamp(14px,2vw,28px) 34px; gap:24px; color:#302d25; font-family:Inter,"Segoe UI",Arial,sans-serif; }
.dash-hero { padding:8px 0 20px; border:0; border-bottom:1px solid #ded9cf; border-radius:0; background:transparent; color:#302d25; box-shadow:none; }
.dash-kicker { color:#89785e; font-size:10px; letter-spacing:.14em; font-weight:700; }
.dash-title { color:#29271f; font-size:clamp(23px,2.4vw,31px); font-weight:620; line-height:1.18; letter-spacing:-.04em; }
.dash-subtitle { margin-top:7px; color:#77736c; font-size:13px; }
.dash-btn { min-height:36px; padding:8px 12px; border:1px solid #ded9cf; border-radius:6px; color:#514a3d; background:#fff; backdrop-filter:none; font-size:12px; font-weight:600; transition:background .18s,border-color .18s,color .18s; }
.dash-btn:hover { border-color:#a28e6b; color:#66583f; background:#f8f6f1; }
.dash-btn.primary { border-color:#66583f; color:#fff; background:#66583f; }
.dash-btn.primary:hover { background:#51452f; }
.dash-grid { grid-template-columns:repeat(4,minmax(0,1fr)); gap:0; border-top:1px solid #eae5db; border-bottom:1px solid #eae5db; }
.dash-card { padding:16px 18px; border:0; border-radius:0; background:transparent; box-shadow:none; }
.dash-grid .dash-card { min-height:108px; border-left:1px solid #eae5db; }
.dash-grid .dash-card:first-child { border-left:0; padding-left:0; }
.dash-grid .dash-card:last-child { padding-right:0; }
.dash-stat { min-height:0; align-items:center; }
.dash-icon { width:32px; height:32px; border-radius:50%; background:#f4f0e8; color:#806c4b; }
.dash-icon .material-icons { font-size:18px; }
.dash-label { margin-bottom:7px; color:#817b70; font-size:10px; letter-spacing:.09em; font-weight:650; }
.dash-value { color:#302d25; font-size:26px; line-height:1.1; font-weight:620; letter-spacing:-.04em; }
.dash-note { margin-top:5px; color:#817b70; font-size:11px; }
.dash-two-col { grid-template-columns:minmax(0,1.4fr) minmax(260px,.65fr); gap:32px; }
.dash-two-col > .dash-card,.dash-wrap > .dash-card { padding:0; border:0; background:transparent; box-shadow:none; }
.dash-section-head { margin-bottom:8px; padding-bottom:10px; border-bottom:1px solid #ded9cf; }
.dash-section-head h2 { color:#302d25; font-size:15px; font-weight:650; letter-spacing:-.02em; }
.dash-section-head a { color:#806c4b; font-size:12px; font-weight:600; }
.dash-section-head a:hover { color:#51452f; text-decoration:underline; }
.dash-list { gap:0; }
.dash-row { padding:11px 0; border:0; border-bottom:1px solid #eeeae3; border-radius:0; background:transparent; }
.dash-row-title { color:#373229; font-size:12.5px; font-weight:620; }
.dash-row-meta { margin-top:3px; color:#817b70; font-size:11px; }
.dash-pill { min-height:24px; padding:4px 8px; border:1px solid transparent; border-radius:4px; font-size:10px; font-weight:650; }
.dash-pill.success { color:#496b50; background:#f0f5ef; border-color:#e1e9df; }
.dash-pill.warning { color:#876a36; background:#f8f4eb; border-color:#eee5d2; }
.dash-pill.danger { color:#985b51; background:#f9f0ed; border-color:#efdfda; }
.dash-pill.neutral { color:#6e685d; background:#f5f3ef; border-color:#e9e5dc; }
.quick-grid { display:block; }
.quick-card { min-height:0; flex-direction:row; align-items:center; justify-content:flex-start; gap:10px; padding:11px 2px; border:0; border-bottom:1px solid #eeeae3; border-radius:0; color:#373229; background:transparent; transition:padding .18s,color .18s,background .18s; }
.quick-card:hover { padding-left:7px; color:#66583f; background:#faf9f6; box-shadow:none; transform:none; }
.quick-card i { color:#8b7959; font-size:19px; }
.quick-card span { font-size:12px; font-weight:600; }
.empty-state { padding:16px 0; text-align:left; color:#817b70; background:transparent; border:0; border-bottom:1px solid #eeeae3; border-radius:0; font-size:12px; }
.dash-wrap > .dash-card > .dash-section-head { margin-bottom:0; }
@media(max-width:1100px){ .dash-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dash-grid .dash-card:nth-child(3){border-left:0;padding-left:0}.dash-grid .dash-card:nth-child(n+3){border-top:1px solid #eae5db} }
@media(max-width:760px){ .dash-wrap{padding:8px 14px 28px;gap:19px}.dash-hero{padding:7px 0 16px}.dash-actions{gap:7px}.dash-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dash-grid .dash-card{padding:12px 10px;min-height:96px}.dash-grid .dash-card:nth-child(odd){border-left:0;padding-left:0}.dash-grid .dash-card:nth-child(even){border-left:1px solid #eae5db;padding-right:0}.dash-grid .dash-card:nth-child(n+3){border-top:1px solid #eae5db}.dash-stat{gap:6px}.dash-value{font-size:22px}.dash-note{font-size:10.5px}.dash-two-col{gap:22px}.dash-row{gap:8px}.quick-card{padding:10px 0} }
@media(prefers-reduced-motion:reduce){ .dash-btn,.quick-card{transition:none} }
</style>

<div class="dash-wrap">
    <section class="dash-hero">
        <div>
            <p class="dash-kicker">Ringkasan Operasional</p>
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
                <p class="dash-label">Omzet Kumulatif</p>
                <p class="dash-value" style="font-size:25px;"><?= $rupiah($stats['revenue']); ?></p>
                <p class="dash-note"><?= number_format($stats['paidOrders'], 0, ',', '.'); ?> order diproses, dikirim, atau selesai</p>
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
        <p class="dash-row-meta" style="margin:8px 0 0;">Jumlah stok dijumlahkan dari semua varian; produk dengan total 3 unit atau kurang ditampilkan.</p>
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
