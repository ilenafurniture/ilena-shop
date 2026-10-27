<?php
$invoiceMid = is_array($transaksi['data_mid'] ?? null) ? $transaksi['data_mid'] : [];
$invoiceItems = is_array($transaksi['items'] ?? null) ? $transaksi['items'] : [];
$invoiceCourier = is_array($transaksi['kurir'] ?? null) ? $transaksi['kurir'] : [];
$transactionTimestamp = strtotime((string)($invoiceMid['transaction_time'] ?? '')) ?: 0;
$courierName = trim((string)($invoiceCourier['nama'] ?? ''));
$courierService = trim((string)($invoiceCourier['deskripsi'] ?? ''));
$courierPending = $courierName === '' || strcasecmp($courierName, 'Menunggu pengiriman') === 0;
$courierDisplay = $courierPending ? 'Menunggu pengiriman' : $courierName;
if (!$courierPending && $courierService !== '') {
    $namePrefix = preg_quote($courierName, '/');
    $serviceAlreadyIncludesName = preg_match('/^' . $namePrefix . '(?:\b|$)/iu', $courierService) === 1;
    $courierDisplay = $serviceAlreadyIncludesName ? $courierService : strtoupper($courierName) . ' ' . $courierService;
}
$grossAmount = isset($invoiceMid['gross_amount']) ? (int)$invoiceMid['gross_amount'] : null;
$invoiceStatus = (string)($transaksi['status'] ?? '');
$paymentType = strtolower((string)($invoiceMid['payment_type'] ?? ''));
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title><?= esc($title); ?> | Ilena Furniture</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/style_pdf.css" />
</head>

<body>
    <main class="print ilena-invoice">
        <?php if ($transaksi['status'] == 'Proses' || $transaksi['status'] == 'Dikirim' || $transaksi['status'] == 'Selesai') { ?>
        <div class="print-lunas">
            <p>L U N A S</p>
        </div>
        <?php } else if ($transaksi['status'] == 'Menunggu Pembayaran') {
        } else { ?>
        <div class="print-lunas">
            <p>G A G A L</p>
        </div>
        <?php } ?>
        <div class="d-flex justify-content-between mb-2 invoice-head">
            <img src="<?= esc(base_url('img/LogoIlena.png')); ?>" class="logo" alt="Ilena Furniture" />
            <div class="d-flex flex-column align-items-end">
                <p class="mb-0">INVOICE</p>
                <p class="mb-0"><?= $transactionTimestamp ? date('dmY', $transactionTimestamp) : '—' ?>/CBM/<?= esc($transaksi['id_midtrans'] ?? ''); ?></p>
            </div>
        </div>
        <div class="d-flex justify-content-between mb-2 invoice-parties">
            <div class="w-100">
                <p class="fw-bold mb-0">DITERBITKAN ATAS NAMA</p>
                <p class="mb-0">Penjual : <b>Ilena Furniture</b></p>
            </div>
            <div class="w-100">
                <p class="fw-bold mb-0">PENERIMA</p>
                <div class="w-100">
                    <div class="w-100 d-flex">
                        <div style="flex: 2.7">
                            <p class="mb-0">Pembeli</p>
                        </div>
                        <div style="flex: 0.4">
                            <p class="text-center mb-0">:</p>
                        </div>
                        <div style="flex: 5">
                            <p class="mb-0"><?= esc($transaksi['nama'] ?? '—'); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($transaksi['email'])): ?><div class="w-100 d-flex"><div style="flex:2.7"><p class="mb-0">Email</p></div><div style="flex:.4"><p class="text-center mb-0">:</p></div><div style="flex:5"><p class="mb-0"><?= esc($transaksi['email']); ?></p></div></div><?php endif; ?>
                    <?php if (!empty($transaksi['nohp'])): ?><div class="w-100 d-flex"><div style="flex:2.7"><p class="mb-0">Telepon</p></div><div style="flex:.4"><p class="text-center mb-0">:</p></div><div style="flex:5"><p class="mb-0"><?= esc($transaksi['nohp']); ?></p></div></div><?php endif; ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2.7">
                            <p class="mb-0">Tanggal Pembelian</p>
                        </div>
                        <div style="flex: 0.4">
                            <p class="text-center mb-0">:</p>
                        </div>
                        <div style="flex: 5">
                            <p class="mb-0"><?= $transactionTimestamp ? date('d', $transactionTimestamp) . ' ' . esc($bulan[(int)date('m', $transactionTimestamp) - 1]) . ' ' . date('Y', $transactionTimestamp) : 'Tanggal tidak tersedia' ?></p>
                        </div>
                    </div>
                    <div class="w-100 d-flex">
                        <div style="flex: 2.7">
                            <p class="mb-0">Alamat Pengiriman</p>
                        </div>
                        <div style="flex: 0.4">
                            <p class="text-center mb-0">:</p>
                        </div>
                        <div style="flex: 5">
                            <p class="mb-0">
                                <?= nl2br(esc($transaksi['alamat'] ?? '')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container List Barang -->
        <div class="w-100 border-bottom pb-2 invoice-items">
            <div class="w-100 d-flex border-bottom border-top border-dark py-2 invoice-items__head">
                <div style="flex: 2.9">
                    <p class="mb-0 fw-bold">INFO PRODUK</p>
                </div>
                <div style="flex: 1">
                    <p class="text-center mb-0 fw-bold">KUANTITAS</p>
                </div>
                <div style="flex: 1">
                    <p class="text-center mb-0 fw-bold">HARGA SATUAN</p>
                </div>
                <div style="flex: 1">
                    <p class="text-center mb-0 fw-bold">JUMLAH</p>
                </div>
            </div>
            <?php
            $totalHarga = 0;
            $diskonProduk = 0;
            $biayaOngkir = 0;
            $biayaAdmin = 0;
            $diskonVoucher = 0;
            $diskonFlash = 0;
            $voucherCodes = [];
            $hasBiayaOngkir = false;
            foreach ($invoiceItems as $item) {
                $itemName = strtolower((string)($item['name'] ?? $item['id'] ?? ''));
                if ($itemName == 'biaya ongkir') {
                    $biayaOngkir += (int)$item['price'];
                    $hasBiayaOngkir = true;
                    continue;
                }
                if ($itemName == 'biaya admin') {
                    $biayaAdmin += (int)$item['price'];
                    continue;
                }
                if ($itemName == 'voucher') {
                    $diskonVoucher += abs((int)$item['price']);
                    $voucherCode = trim((string)($item['id'] ?? ''));
                    if ($voucherCode !== '' && strcasecmp($voucherCode, 'voucher') !== 0) $voucherCodes[] = $voucherCode;
                    continue;
                }
                if ($itemName == 'flash sale') {
                    $diskonFlash += abs((int)$item['price']);
                    continue;
                }
                $quantity = max(0, (int)($item['quantity'] ?? 0));
                $unitPrice = max(0, (int)($item['price'] ?? 0));
                $originalPrice = max($unitPrice, (int)($item['original_price'] ?? $unitPrice));
                $totalHarga += $originalPrice * $quantity;
                $diskonProduk += ($originalPrice - $unitPrice) * $quantity;
            ?>
            <div class="w-100 d-flex invoice-items__row">
                <div style="flex: 3">
                    <p class="mb-0">| <?= esc(mb_strtoupper((string)($item['collection'] ?? ''), 'UTF-8')) ?> | <?= esc($item['name'] ?? $item['id'] ?? 'Produk'); ?></p>
                </div>
                <div style="flex: 1">
                    <p class="text-center mb-0"><?= number_format($quantity); ?></p>
                </div>
                <div style="flex: 1">
                    <p class="text-center mb-0">
                        <?php if ($originalPrice > $unitPrice): ?><del class="price-before">Rp <?= number_format($originalPrice, 0, ',', '.'); ?></del><br><?php endif; ?>
                        Rp <?= number_format($unitPrice, 0, ',', '.'); ?>
                    </p>
                </div>
                <div style="flex: 1">
                    <p class="text-end mb-0">Rp <?= number_format($unitPrice * $quantity, 0, ',', '.'); ?>
                    </p>
                </div>
            </div>
            <?php } ?>
            <?php if (!$invoiceItems): ?><div class="invoice-items__row"><p class="mb-0">Rincian produk tidak tersedia.</p></div><?php endif; ?>
        </div>

        <!-- Container perhitungan -->
        <div class="w-100 d-flex mt-auto border-bottom invoice-totals">
            <div class="flex-grow-1 w-100"></div>
            <div class="flex-grow-1 w-100">
                <div class="w-100 py-2">
                    <div class="w-100 d-flex">
                        <div style="flex: 2">
                            <p class="mb-0 fw-bold">SUBTOTAL PRODUK</p>
                        </div>
                        <div style="flex: 1">
                            <p class="text-end mb-0 fw-bold">
                                Rp <?= number_format($totalHarga, 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                    <?php if ($diskonProduk > 0) { ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2"><p class="mb-0">Diskon Produk</p></div>
                        <div style="flex: 1"><p class="text-end mb-0">- Rp <?= number_format($diskonProduk, 0, ',', '.'); ?></p></div>
                    </div>
                    <?php } ?>
                    <?php if ($diskonVoucher > 0) { ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2">
                            <p class="mb-0">Diskon Voucher<?= $voucherCodes ? ' (' . esc(implode(', ', array_unique($voucherCodes))) . ')' : '' ?></p>
                        </div>
                        <div style="flex: 1">
                            <p class="text-end mb-0">- Rp <?= number_format($diskonVoucher, 0, ",", "."); ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($diskonFlash > 0) { ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2">
                            <p class="mb-0">Flash Sale</p>
                        </div>
                        <div style="flex: 1">
                            <p class="text-end mb-0">- Rp <?= number_format($diskonFlash, 0, ",", "."); ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($hasBiayaOngkir) { ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2">
                            <p class="mb-0">Ongkos Kirim</p>
                        </div>
                        <div style="flex: 1">
                            <p class="text-end mb-0"><?= $biayaOngkir > 0 ? 'Rp ' . number_format($biayaOngkir, 0, ',', '.') : 'Gratis' ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($biayaAdmin > 0) { ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2">
                            <p class="mb-0">Biaya Admin Pembayaran</p>
                        </div>
                        <div style="flex: 1">
                            <p class="text-end mb-0">Rp <?= number_format($biayaAdmin, 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="w-100 d-flex">
                        <div style="flex: 2">
                            <p class="mb-0 fw-bold">TOTAL TAGIHAN</p>
                        </div>
                        <div style="flex: 1">
                            <p class="text-end mb-0 fw-bold">
                                Rp <?= number_format($grossAmount ?? ($totalHarga - $diskonProduk - $diskonVoucher - $diskonFlash + $biayaOngkir + $biayaAdmin), 0, ',', '.'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container footer -->
        <div class="w-100 d-flex py-2 invoice-fulfillment">
            <div class="w-100">
                <p class="mb-0 text-black-50">Ekspedisi</p>
                <p class="mb-0 fw-bold"><?= esc($courierDisplay); ?></p>
                <?php $trackingNumber = trim((string)($transaksi['resi'] ?? '')); ?>
                <?php if (!$courierPending && $trackingNumber !== '' && strcasecmp($trackingNumber, 'Menunggu pengiriman') !== 0): ?><p class="mb-0">No. resi: <strong><?= esc($trackingNumber); ?></strong></p><?php endif; ?>
            </div>
            <div class="w-100">
                <p class="mb-0 text-black-50">Metode Pembayaran</p>
                <p class="mb-0">
                    <?php
                    switch ($paymentType) {
                        case 'credit_card':
                            echo esc('Credit Card');
                            $cardDetails = trim(strtoupper((string)($invoiceMid['bank'] ?? '')) . ' ' . ucfirst((string)($invoiceMid['card_type'] ?? '')));
                            if ($cardDetails !== '') echo '<br>' . esc($cardDetails);
                            break;
                        case 'echannel':
                            switch ((string)($invoiceMid['biller_code'] ?? '')) {
                                case '70012':
                                    echo "Mandiri Bill";
                                    break;
                                default:
                                    echo "EChannel";
                                    break;
                            }
                            break;
                        case 'bank_transfer':
                            if (isset($invoiceMid['va_numbers'][0]['bank']))
                                echo esc(strtoupper((string)$invoiceMid['va_numbers'][0]['bank']) . ' VA');
                            else if (isset($invoiceMid['permata_va_number']))
                                echo "Bank Permata VA";
                            else if (isset($invoiceMid['bca_va_number']))
                                echo "BCA VA";
                            else
                                echo 'Bank transfer';
                            break;
                        case 'permata':
                            echo 'Bank Permata VA';
                            break;
                        case 'gopay':
                            echo 'GoPay';
                            break;
                        case 'qris':
                            echo 'QRIS';
                            break;
                        case 'shopeepay':
                            echo 'ShopeePay';
                            break;
                        case 'admin':
                            echo 'Pembayaran manual';
                            break;
                        case 'market':
                        case 'toko':
                            echo 'Pembayaran marketplace';
                            break;
                        default:
                            echo esc($invoiceMid['payment_type'] ?? 'Metode tidak tersedia');
                            break;
                    }
                    ?></p>
            </div>
        </div>
        <div class="w-100 d-flex py-2 justify-content-between align-items-end invoice-note">
            <div style="width: 50%">
                <p class="mb-0">
                    Invoice ini sah dan diproses oleh sistem<br />
                    Silakan hubungi
                    <a style="color: var(--merahLogo)"
                        class="link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover">Ilena
                        Furniture
                        CS</a>
                    apabila kamu membutuhkan bantuan.
                </p>
            </div>
            <div>
                <p class="mb-0 text-black-50">
                    Terakhir diupdate: <?php
                                        echo $transactionTimestamp ? date("d", $transactionTimestamp) . " " . esc($bulan[(int)date("m", $transactionTimestamp) - 1]) . " " . date("Y", $transactionTimestamp) . " " . date("H:i:s", $transactionTimestamp) : 'Waktu tidak tersedia'
                                        ?> WIB
                </p>
            </div>
        </div>
    </main>
</body>
<?php if (in_array($invoiceStatus, ['Proses', 'Dikirim', 'Selesai', 'Menunggu Pembayaran'], true)) { ?>
<script>
window.print();
</script>
<?php } ?>

</html>
