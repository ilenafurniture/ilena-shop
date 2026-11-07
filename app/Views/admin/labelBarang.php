<?php
$orderItems = is_array($pemesanan['items'] ?? null) ? $pemesanan['items'] : [];
$labelItems = array_values(array_filter($orderItems, static function ($item): bool {
    if (! is_array($item)) return false;
    $excluded = ['biaya admin', 'biaya ongkir', 'voucher', 'flash sale'];
    foreach ([$item['name'] ?? '', $item['id'] ?? ''] as $value) {
        if (in_array(mb_strtolower(trim((string) $value)), $excluded, true)) return false;
    }
    return true;
}));
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Label Pengiriman'); ?> | Ilena Furniture</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: A4 portrait; margin: 14mm; }
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; color: #211e1b; }
        body { margin: 0; background: #f1efeb; }
        .sheet { width: min(100%, 182mm); min-height: 269mm; margin: 0 auto; display: grid; place-items: center; }
        .shipping-label { width: 100%; min-height: 104mm; padding: 10mm 12mm; border: 1.2px solid #27231f; background: #fff; }
        .label-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10mm; padding-bottom: 6mm; border-bottom: 1px solid #c9c2bb; }
        .brand { margin: 0; color: #8f2632; font-size: 20pt; font-weight: 800; letter-spacing: .06em; }
        .brand-sub { margin: 1.5mm 0 0; color: #68615a; font-size: 8.5pt; }
        .order-ref { text-align: right; color: #514b45; font-size: 8pt; line-height: 1.5; overflow-wrap: anywhere; }
        .order-ref strong { display: block; color: #211e1b; font-size: 11pt; }
        .label-body { display: grid; grid-template-columns: 1fr 1.35fr; gap: 9mm; padding-top: 7mm; }
        .section-label { margin: 0 0 3mm; color: #746b62; font-size: 7.5pt; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .sender-name { margin: 0; font-size: 10pt; font-weight: 700; }
        .sender-detail { margin: 1.5mm 0 0; color: #5d564f; font-size: 8.5pt; line-height: 1.45; }
        .recipient { padding-left: 7mm; border-left: 3px solid #8f2632; }
        .recipient-name { margin: 0; font-size: 17pt; font-weight: 700; line-height: 1.15; overflow-wrap: anywhere; }
        .recipient-phone { margin: 2mm 0; color: #8f2632; font-size: 12pt; font-weight: 700; }
        .recipient-address { margin: 0; font-size: 10pt; line-height: 1.45; overflow-wrap: anywhere; }
        .contents { margin-top: 6mm; padding-top: 4mm; border-top: 1px dashed #c9c2bb; }
        .contents-title { margin: 0 0 2mm; color: #746b62; font-size: 7.5pt; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .contents-list { margin: 0; padding-left: 5mm; font-size: 8.5pt; line-height: 1.5; }
        .contents-list li { padding-left: 1mm; }
        .label-foot { display: flex; justify-content: space-between; gap: 8mm; margin-top: 6mm; padding-top: 3mm; border-top: 1px solid #c9c2bb; color: #746b62; font-size: 7.5pt; }
        @media screen { .sheet { padding: 12mm 0; } .shipping-label { box-shadow: 0 8px 28px rgba(30, 25, 20, .12); } }
        @media print { html, body { width: auto; min-height: 0; background: #fff; } .sheet { width: 100%; min-height: 269mm; margin: 0; padding: 0; } .shipping-label { break-inside: avoid; } }
    </style>
</head>
<body>
<main class="sheet">
    <article class="shipping-label" aria-label="Label pengiriman">
        <header class="label-top">
            <div>
                <p class="brand">ILENA</p>
                <p class="brand-sub">Furniture untuk ruang yang terasa seperti rumah</p>
            </div>
            <div class="order-ref">
                <span>Nomor pesanan</span>
                <strong><?= esc(\App\Services\OrderInvoiceNumber::fromPrimaryKey($pemesanan['id'] ?? null)); ?></strong>
            </div>
        </header>
        <div class="label-body">
            <section>
                <p class="section-label">Pengirim</p>
                <p class="sender-name">Ilena Furniture</p>
                <p class="sender-detail">0811-2938-158<br>Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa Tengah</p>
            </section>
            <section class="recipient">
                <p class="section-label">Penerima</p>
                <p class="recipient-name"><?= esc($pemesanan['nama'] ?? ''); ?></p>
                <p class="recipient-phone"><?= esc($pemesanan['nohp'] ?? ''); ?></p>
                <p class="recipient-address"><?= nl2br(esc($pemesanan['alamat'] ?? '')); ?></p>
            </section>
        </div>
        <?php if ($labelItems !== []): ?>
        <section class="contents">
            <p class="contents-title">Isi paket</p>
            <ul class="contents-list">
                <?php foreach ($labelItems as $item): ?>
                <li><?= esc(trim((string) preg_replace('/\s+/', ' ', strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], ' ', (string) ($item['name'] ?? $item['product_name'] ?? 'Produk')))))); ?><?= (int) ($item['quantity'] ?? $item['jumlah'] ?? 1) > 1 ? ' × ' . (int) ($item['quantity'] ?? $item['jumlah']) : ''; ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>
        <footer class="label-foot"><span>Harap tangani dengan baik</span><span>Ilena Furniture · Semarang</span></footer>
    </article>
</main>
<script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
