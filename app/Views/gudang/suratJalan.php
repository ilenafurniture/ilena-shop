<?php
$orderItems = is_array($pemesanan['items'] ?? null) ? $pemesanan['items'] : [];
$shippingItems = array_values(array_filter($orderItems, static function ($item): bool {
    if (! is_array($item)) return false;
    $excluded = ['voucher', 'biaya admin', 'biaya ongkir', 'flash sale'];
    foreach ([$item['name'] ?? '', $item['id'] ?? ''] as $value) {
        if (in_array(mb_strtolower(trim((string) $value)), $excluded, true)) return false;
    }
    return true;
}));
$courier = is_array($pemesanan['kurir'] ?? null) ? $pemesanan['kurir'] : [];
$shipmentReference = \App\Services\OrderInvoiceNumber::fromPrimaryKey($pemesanan['id'] ?? null);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Surat Jalan'); ?> | Ilena Furniture</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: A4 portrait; margin: 14mm; }
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; color: #24211e; }
        body { margin: 0; background: #f1efeb; font-size: 9pt; line-height: 1.45; }
        .sheet { width: min(100%, 182mm); min-height: 269mm; margin: 0 auto; padding: 12mm; display: flex; flex-direction: column; background: #fff; }
        .doc-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 10mm; padding-bottom: 7mm; border-bottom: 2px solid #8f2632; }
        .brand { display: flex; align-items: center; gap: 3mm; }
        .brand-mark { width: 12mm; height: 12mm; display: grid; place-items: center; border: 1.5px solid #8f2632; border-radius: 50%; color: #8f2632; font: 700 15pt Georgia, serif; }
        .brand-name { margin: 0; font-size: 15pt; font-weight: 800; letter-spacing: .08em; }
        .brand-entity { margin: 1mm 0 0; color: #6e665e; font-size: 7.5pt; }
        .doc-title { margin: 0; color: #8f2632; font: 19pt Georgia, 'Times New Roman', serif; text-align: right; }
        .doc-subtitle { margin: 1.5mm 0 0; color: #6e665e; font-size: 8pt; text-align: right; }
        .doc-meta { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5mm; padding: 5mm 0 7mm; border-bottom: 1px solid #d9d2ca; }
        .meta-label, .block-label { display: block; margin: 0 0 1.5mm; color: #796f65; font-size: 7pt; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }
        .meta-value { display: block; font-size: 9pt; font-weight: 700; overflow-wrap: anywhere; }
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 7mm; padding: 7mm 0; }
        .party { min-width: 0; padding: 4.5mm; border: 1px solid #e3ded8; }
        .party-recipient { border-top: 2px solid #8f2632; }
        .party p { margin: 0 0 1.5mm; overflow-wrap: anywhere; }
        .party .party-name { margin: 0 0 2mm; font-size: 11pt; font-weight: 700; }
        .party .muted { color: #6e665e; }
        .section-title { margin: 1mm 0 3mm; font-size: 10pt; font-weight: 700; }
        .items { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items th { padding: 3mm 2.5mm; border-top: 1px solid #8f2632; border-bottom: 1px solid #d9d2ca; background: #f6f3f0; color: #5e554d; font-size: 7pt; text-align: left; text-transform: uppercase; letter-spacing: .04em; }
        .items td { padding: 3.5mm 2.5mm; border-bottom: 1px solid #e9e5e0; vertical-align: top; overflow-wrap: anywhere; }
        .items tbody tr { break-inside: avoid; page-break-inside: avoid; }
        .items th:first-child, .items td:first-child { width: 10mm; text-align: center; }
        .items th:nth-child(2), .items td:nth-child(2) { width: 29mm; }
        .items th:last-child, .items td:last-child { width: 22mm; text-align: center; }
        .item-name { font-weight: 700; }
        .item-code { color: #6e665e; font-size: 7.5pt; }
        .shipping-note { margin-top: 7mm; padding: 4mm 5mm; border-left: 2px solid #8f2632; background: #f7f5f2; }
        .shipping-note strong { display: block; margin-bottom: 1mm; font-size: 8pt; }
        .shipping-note p { margin: 0; color: #5e554d; white-space: pre-line; overflow-wrap: anywhere; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 14mm; margin-top: 12mm; break-inside: avoid; page-break-inside: avoid; }
        .signature { min-height: 32mm; display: flex; flex-direction: column; justify-content: space-between; text-align: center; }
        .signature-label { margin: 0; font-size: 8pt; }
        .signature-line { width: 70%; margin: 0 auto; padding-top: 2mm; border-top: 1px solid #625a52; font-weight: 700; }
        .doc-foot { margin-top: auto; padding-top: 3mm; border-top: 1px solid #d9d2ca; color: #766e66; font-size: 7pt; text-align: center; }
        @media screen { .sheet { margin: 10mm auto; box-shadow: 0 8px 28px rgba(30, 25, 20, .12); } }
        @media print { html, body { width: auto; margin: 0; padding: 0; background: #fff; } .sheet { width: 100%; min-height: 269mm; margin: 0; padding: 0; box-shadow: none; } .no-print { display: none !important; } }
    </style>
</head>
<body>
<main class="sheet">
    <header class="doc-head">
        <div class="brand"><div class="brand-mark" aria-hidden="true">N</div><div><p class="brand-name">ILENA</p><p class="brand-entity">Furniture · CV Catur Bhakti Mandiri</p></div></div>
        <div><h1 class="doc-title">Surat Jalan</h1><p class="doc-subtitle">Dokumen pengiriman barang</p></div>
    </header>

    <section class="doc-meta" aria-label="Informasi surat jalan">
        <div><span class="meta-label">Nomor dokumen</span><span class="meta-value"><?= esc($shipmentReference); ?></span></div>
        <div><span class="meta-label">Tanggal</span><span class="meta-value"><?= esc($tanggal ?? '—'); ?></span></div>
        <div><span class="meta-label">Nomor pesanan</span><span class="meta-value"><?= esc($orderId ?? '—'); ?></span></div>
    </section>

    <section class="parties">
        <div class="party">
            <span class="block-label">Pengirim</span>
            <p class="party-name">Ilena Furniture</p>
            <p>Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa Tengah</p>
            <p class="muted">0811-2938-158</p>
        </div>
        <div class="party party-recipient">
            <span class="block-label">Penerima</span>
            <p class="party-name"><?= esc($pemesanan['nama'] ?? '—'); ?></p>
            <?php if (trim((string) ($pemesanan['nohp'] ?? '')) !== ''): ?><p><?= esc($pemesanan['nohp']); ?></p><?php endif; ?>
            <p><?= nl2br(esc($pemesanan['alamat'] ?? '—')); ?></p>
        </div>
    </section>

    <section>
        <h2 class="section-title">Rincian barang</h2>
        <table class="items">
            <thead><tr><th>No.</th><th>Kode</th><th>Nama barang dan varian</th><th>Jumlah</th></tr></thead>
            <tbody>
                <?php foreach ($shippingItems as $index => $item):
                    $rawName = (string) ($item['name'] ?? 'Produk');
                    $plainName = strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $rawName));
                ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td class="item-code"><?= esc($item['id'] ?? '—'); ?></td>
                    <td><div class="item-name"><?= nl2br(esc(trim($plainName))); ?></div></td>
                    <td><?= (int) ($item['quantity'] ?? $item['jumlah'] ?? 1); ?> unit</td>
                </tr>
                <?php endforeach; ?>
                <?php if ($shippingItems === []): ?><tr><td colspan="4">Tidak ada rincian barang.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="shipping-note">
        <strong>Informasi pengiriman</strong>
        <p>Ekspedisi: <?= esc(trim((string) ($courier['deskripsi'] ?? $courier['nama'] ?? 'Menyesuaikan')) ?: 'Menyesuaikan'); ?>
Nomor resi: <?= esc($pemesanan['resi'] ?? 'Menunggu pengiriman'); ?>
Catatan: <?= esc(trim((string) ($pemesanan['keterangan_suratjalan'] ?? '')) ?: '—'); ?></p>
    </section>

    <section class="signatures" aria-label="Tanda tangan">
        <div class="signature"><p class="signature-label">Diserahkan oleh</p><p class="signature-line"><?= esc(session()->get('nama') ?: 'Petugas Ilena'); ?></p></div>
        <div class="signature"><p class="signature-label">Diterima dalam kondisi baik oleh</p><p class="signature-line"><?= esc($pemesanan['nama'] ?? 'Penerima'); ?></p></div>
    </section>
    <footer class="doc-foot">Mohon periksa jumlah dan kondisi barang saat diterima.</footer>
</main>

<form id="mark-printed" method="post" action="<?= site_url('admin/suratjalan/' . rawurlencode((string) ($orderId ?? '')) . '/printed'); ?>" hidden>
    <?= csrf_field(); ?>
</form>
<script src="<?= base_url('js/ilena-dialog.js?v=20261027'); ?>"></script>
<script>
window.addEventListener('afterprint', function () {
    window.IlenaDialog.confirm('Surat jalan sudah berhasil dicetak?', { title: 'Konfirmasi cetak' }).then(function (confirmed) {
        if (confirmed) document.getElementById('mark-printed').submit();
    });
}, { once: true });
window.addEventListener('load', function () { window.print(); }, { once: true });
</script>
</body>
</html>
