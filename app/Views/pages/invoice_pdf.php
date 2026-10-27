<?php
$mid = is_array($transaksi['data_mid'] ?? null) ? $transaksi['data_mid'] : [];
$items = is_array($transaksi['items'] ?? null) ? $transaksi['items'] : [];
$courier = is_array($transaksi['kurir'] ?? null) ? $transaksi['kurir'] : [];
$timestamp = strtotime((string)($mid['transaction_time'] ?? '')) ?: 0;
$status = (string)($transaksi['status'] ?? '');
$courierName = trim((string)($courier['nama'] ?? ''));
$courierService = trim((string)($courier['deskripsi'] ?? ''));
$courierLabel = $courierName !== '' && strcasecmp($courierName, 'Menunggu pengiriman') !== 0
    ? (($courierService !== '' && stripos($courierService, $courierName) === 0) ? $courierService : trim($courierName . ' ' . $courierService))
    : 'Menunggu pengiriman';
$rupiah = static fn (int $value): string => 'Rp ' . number_format($value, 0, ',', '.');
$specialNames = ['voucher', 'biaya ongkir', 'biaya admin', 'flash sale'];
$productSubtotal = 0;
$productDiscount = 0;
$voucherDiscount = 0;
$flashDiscount = 0;
$shipping = 0;
$adminFee = 0;
$hasShipping = false;
$voucherCodes = [];
$products = [];
foreach ($items as $item) {
    if (!is_array($item)) continue;
    $name = strtolower(trim((string)($item['name'] ?? '')));
    $price = max(0, (int)($item['price'] ?? 0));
    if ($name === 'biaya ongkir') { $shipping += $price; $hasShipping = true; continue; }
    if ($name === 'biaya admin') { $adminFee += $price; continue; }
    if ($name === 'voucher') {
        $voucherDiscount += abs((int)($item['price'] ?? 0));
        $code = trim((string)($item['id'] ?? ''));
        if ($code !== '' && strcasecmp($code, 'voucher') !== 0) $voucherCodes[] = $code;
        continue;
    }
    if ($name === 'flash sale') { $flashDiscount += abs((int)($item['price'] ?? 0)); continue; }
    $qty = max(0, (int)($item['quantity'] ?? 0));
    $original = max($price, (int)($item['original_price'] ?? $price));
    $productSubtotal += $original * $qty;
    $productDiscount += ($original - $price) * $qty;
    $dimensions = is_array($item['dimensions'] ?? null) ? $item['dimensions'] : [];
    $dimensionParts = [];
    foreach (['panjang' => 'P', 'lebar' => 'L', 'tinggi' => 'T'] as $key => $label) {
        $value = trim((string)($dimensions[$key] ?? ''));
        if ($value !== '' && $value !== '-') $dimensionParts[] = $label . ' ' . $value . ' mm';
    }
    $products[] = [
        'name' => trim((string)($item['product_name'] ?? $item['name'] ?? $item['id'] ?? 'Produk')) ?: 'Produk',
        'sku' => trim((string)($item['id'] ?? '')),
        'collection' => trim((string)($item['collection'] ?? '')),
        'variant' => trim((string)($item['variant'] ?? $item['varian'] ?? '')),
        'dimensions' => implode(' × ', $dimensionParts),
        'weight' => trim((string)($item['weight_kg'] ?? '')),
        'qty' => $qty,
        'price' => $price,
        'original' => $original,
        'total' => $price * $qty,
    ];
}
$gross = isset($mid['gross_amount']) ? (int)$mid['gross_amount'] : ($productSubtotal - $productDiscount - $voucherDiscount - $flashDiscount + $shipping + $adminFee);
$paymentType = strtolower((string)($mid['payment_type'] ?? ''));
$paymentLabels = [
    'gopay' => 'GoPay', 'qris' => 'QRIS', 'shopeepay' => 'ShopeePay', 'permata' => 'Bank Permata VA',
    'credit_card' => 'Kartu kredit', 'admin' => 'Pembayaran manual', 'market' => 'Pembayaran marketplace', 'toko' => 'Pembayaran marketplace',
    'echannel' => 'Mandiri Bill',
];
$paymentLabel = $paymentLabels[$paymentType] ?? ($paymentType === 'bank_transfer'
    ? (isset($mid['va_numbers'][0]['bank']) ? strtoupper((string)$mid['va_numbers'][0]['bank']) . ' VA' : (isset($mid['permata_va_number']) ? 'Bank Permata VA' : 'Transfer bank'))
    : ($paymentType !== '' ? ucwords(str_replace('_', ' ', $paymentType)) : 'Belum tersedia'));
$paymentReference = trim((string)($mid['permata_va_number'] ?? $mid['bca_va_number'] ?? ''));
if ($paymentReference === '' && isset($mid['va_numbers'][0]['va_number'])) $paymentReference = (string)$mid['va_numbers'][0]['va_number'];
$paid = in_array($status, ['Proses', 'Dikirim', 'Selesai'], true);
$logoData = '';
if (is_file($logoPath ?? '')) {
    $logoData = 'data:image/png;base64,' . base64_encode((string)file_get_contents($logoPath));
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($transaksi['id_midtrans'] ?? '') ?> | Ilena Furniture</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #292520; font: 9pt/1.45 Arial, sans-serif; }
        .topline { height: 3px; background: #806448; margin-bottom: 22px; }
        .head { width: 100%; margin-bottom: 22px; }
        .head td { vertical-align: top; }
        .logo { width: 125px; height: auto; }
        .invoice-title { text-align: right; font: 26pt Georgia, serif; letter-spacing: 2px; }
        .muted { color: #746c63; }
        .number { margin-top: 4px; font-size: 9pt; overflow-wrap: anywhere; }
        .status { display: inline-block; margin-top: 8px; padding: 4px 9px; border: 1px solid #806448; color: #806448; font-size: 8pt; font-weight: bold; letter-spacing: 1px; }
        .parties { width: 100%; margin: 0 0 20px; border-collapse: collapse; }
        .parties td { width: 50%; padding: 0 14px 0 0; vertical-align: top; }
        .eyebrow { margin: 0 0 7px; color: #806448; font-size: 7.5pt; font-weight: bold; letter-spacing: 1.1px; text-transform: uppercase; }
        .parties p { margin: 0 0 3px; }
        .parties strong { color: #292520; }
        .section-title { margin: 17px 0 8px; color: #806448; font-size: 8pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        table.items { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items th { padding: 8px 6px; background: #f4f1ed; border-top: 1px solid #806448; border-bottom: 1px solid #d9d2ca; color: #62584e; text-align: left; font-size: 7.5pt; letter-spacing: .5px; }
        .items td { padding: 9px 6px; border-bottom: 1px solid #e9e5e0; vertical-align: top; }
        .items .product { width: 48%; }
        .items .qty { width: 9%; text-align: center; }
        .items .unit { width: 21%; text-align: right; }
        .items .total { width: 22%; text-align: right; }
        .product-name { margin-bottom: 3px; font-size: 9.5pt; font-weight: bold; }
        .meta { color: #746c63; font-size: 7.5pt; }
        .meta div { margin-top: 1px; }
        .strike { color: #8e877f; text-decoration: line-through; font-size: 7.5pt; }
        .totals { width: 54%; margin: 13px 0 0 auto; border-collapse: collapse; }
        .totals td { padding: 4px 0; }
        .totals td:last-child { text-align: right; white-space: nowrap; }
        .totals .grand td { padding-top: 8px; border-top: 1px solid #806448; font-size: 11pt; font-weight: bold; }
        .fulfillment { width: 100%; margin-top: 22px; border-top: 1px solid #e1dcd6; border-bottom: 1px solid #e1dcd6; border-collapse: collapse; }
        .fulfillment td { width: 50%; padding: 11px 12px 11px 0; vertical-align: top; }
        .fulfillment p { margin: 0 0 3px; }
        .note { margin-top: 20px; color: #746c63; font-size: 7.5pt; }
        .note strong { color: #806448; }
        .empty { padding: 12px 6px; color: #746c63; }
    </style>
</head>
<body>
<div class="topline"></div>
<table class="head"><tr>
    <td><?= $logoData !== '' ? '<img class="logo" src="' . $logoData . '" alt="Ilena Furniture">' : '<strong>ILENA FURNITURE</strong>' ?></td>
    <td class="invoice-title">INVOICE<div class="number muted"><?= esc($transaksi['id_midtrans'] ?? '—') ?></div>
        <?php if ($timestamp): ?><div class="number muted"><?= esc(date('d/m/Y H:i', $timestamp)) ?> WIB</div><?php endif; ?>
        <div class="status"><?= $paid ? 'LUNAS' : esc(strtoupper($status ?: 'STATUS PESANAN')) ?></div>
    </td>
</tr></table>

<table class="parties"><tr>
    <td><p class="eyebrow">Penjual</p><p><strong>Ilena Furniture</strong></p><p class="muted">Invoice resmi pesanan Ilena Furniture</p></td>
    <td><p class="eyebrow">Informasi pembeli</p><p><strong><?= esc($transaksi['nama'] ?? '—') ?></strong></p>
        <?php if (!empty($transaksi['email'])): ?><p><?= esc($transaksi['email']) ?></p><?php endif; ?>
        <?php if (!empty($transaksi['nohp'])): ?><p><?= esc($transaksi['nohp']) ?></p><?php endif; ?>
        <p class="muted"><?= nl2br(esc($transaksi['alamat'] ?? 'Alamat tidak tersedia')) ?></p>
    </td>
</tr></table>

<p class="section-title">Rincian produk</p>
<table class="items"><thead><tr><th class="product">Produk & spesifikasi</th><th class="qty">Qty</th><th class="unit">Harga satuan</th><th class="total">Jumlah</th></tr></thead><tbody>
<?php foreach ($products as $product): ?>
    <tr><td class="product"><div class="product-name"><?= esc($product['name']) ?></div><div class="meta">
        <?php if ($product['sku'] !== ''): ?><div>Kode produk: <?= esc($product['sku']) ?></div><?php endif; ?>
        <?php if ($product['collection'] !== ''): ?><div>Koleksi: <?= esc($product['collection']) ?></div><?php endif; ?>
        <?php if ($product['variant'] !== ''): ?><div>Varian: <?= esc($product['variant']) ?></div><?php endif; ?>
        <?php if ($product['dimensions'] !== ''): ?><div>Dimensi: <?= esc($product['dimensions']) ?></div><?php endif; ?>
        <?php if ($product['weight'] !== '' && $product['weight'] !== '-'): ?><div>Berat: <?= esc($product['weight']) ?> kg</div><?php endif; ?>
    </div></td><td class="qty"><?= number_format($product['qty']) ?></td>
        <td class="unit"><?php if ($product['original'] > $product['price']): ?><div class="strike"><?= esc($rupiah($product['original'])) ?></div><?php endif; ?><?= esc($rupiah($product['price'])) ?></td>
        <td class="total"><?= esc($rupiah($product['total'])) ?></td></tr>
<?php endforeach; ?>
<?php if (!$products): ?><tr><td class="empty" colspan="4">Rincian produk tidak tersedia.</td></tr><?php endif; ?>
</tbody></table>

<table class="totals">
    <tr><td>Subtotal harga produk</td><td><?= esc($rupiah($productSubtotal)) ?></td></tr>
    <?php if ($productDiscount > 0): ?><tr><td>Diskon produk</td><td>− <?= esc($rupiah($productDiscount)) ?></td></tr><?php endif; ?>
    <?php if ($voucherDiscount > 0): ?><tr><td>Diskon voucher<?= $voucherCodes ? ' (' . esc(implode(', ', array_unique($voucherCodes))) . ')' : '' ?></td><td>− <?= esc($rupiah($voucherDiscount)) ?></td></tr><?php endif; ?>
    <?php if ($flashDiscount > 0): ?><tr><td>Diskon Flash Sale</td><td>− <?= esc($rupiah($flashDiscount)) ?></td></tr><?php endif; ?>
    <?php if ($hasShipping): ?><tr><td>Ongkos kirim</td><td><?= $shipping > 0 ? esc($rupiah($shipping)) : 'Gratis' ?></td></tr><?php endif; ?>
    <?php if ($adminFee > 0): ?><tr><td>Biaya admin pembayaran</td><td><?= esc($rupiah($adminFee)) ?></td></tr><?php endif; ?>
    <tr class="grand"><td>Total tagihan</td><td><?= esc($rupiah($gross)) ?></td></tr>
</table>

<table class="fulfillment"><tr>
    <td><p class="eyebrow">Pengiriman</p><p><strong><?= esc($courierLabel) ?></strong></p>
        <?php $tracking = trim((string)($transaksi['resi'] ?? '')); if ($tracking !== '' && strcasecmp($tracking, 'Menunggu pengiriman') !== 0): ?><p class="muted">Nomor resi: <?= esc($tracking) ?></p><?php endif; ?>
    </td>
    <td><p class="eyebrow">Pembayaran</p><p><strong><?= esc($paymentLabel) ?></strong></p>
        <?php if ($paymentReference !== ''): ?><p class="muted">Nomor VA: <?= esc($paymentReference) ?></p><?php endif; ?>
        <p class="muted">Status: <?= esc($status ?: 'Tidak tersedia') ?></p>
    </td>
</tr></table>
<p class="note">Invoice ini diterbitkan secara elektronik oleh <strong>Ilena Furniture</strong>. Simpan dokumen ini sebagai bukti transaksi. Jika membutuhkan bantuan, hubungi layanan pelanggan Ilena Furniture.</p>
</body></html>
