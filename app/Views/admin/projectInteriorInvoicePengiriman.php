<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
@media print {
    .no-print { display: none !important; }
    body { margin: 0; padding: 0; }
    .invoice-paper { 
        box-shadow: none !important; 
        margin: 0 !important;
        padding: 1.5cm !important;
    }
}

.invoice-paper {
    background: #fff;
    max-width: 210mm;
    margin: 0 auto;
    padding: 25mm 20mm;
    box-shadow: 0 0 30px rgba(0,0,0,0.1);
    font-family: 'Arial', sans-serif;
    font-size: 12px;
    line-height: 1.4;
}

.header-company {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 3px solid #b31217;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.company-info h1 {
    margin: 0;
    font-size: 22px;
    color: #b31217;
    font-weight: 800;
}

.company-info p {
    margin: 3px 0 0 0;
    font-size: 11px;
    color: #555;
}

.invoice-meta {
    text-align: right;
}

.invoice-meta .inv-number {
    font-size: 14px;
    font-weight: 800;
    color: #b31217;
}

.invoice-meta .inv-date {
    font-size: 11px;
    color: #666;
}

.title-invoice {
    text-align: center;
    font-size: 20px;
    font-weight: 800;
    margin: 20px 0;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.customer-section {
    background: #f9f9f9;
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.customer-section p {
    margin: 2px 0;
    font-size: 12px;
}

.customer-section .customer-name {
    font-weight: 700;
    color: #b31217;
    font-size: 14px;
}

.sj-section {
    background: #fff8f8;
    border: 1px solid #ffdddd;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.sj-section strong {
    color: #b31217;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.items-table th {
    background: #b31217;
    color: #fff;
    padding: 10px 8px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
}

.items-table td {
    border-bottom: 1px solid #eee;
    padding: 10px 8px;
    font-size: 12px;
}

.items-table tbody tr:nth-child(even) {
    background: #fafafa;
}

.items-table .text-end {
    text-align: right;
}

.items-table .text-center {
    text-align: center;
}

.total-section {
    margin-top: 20px;
    text-align: right;
}

.total-section .total-row {
    display: flex;
    justify-content: flex-end;
    gap: 20px;
    padding: 8px 0;
    font-size: 13px;
}

.total-section .total-row.grand {
    font-size: 16px;
    font-weight: 800;
    color: #b31217;
    border-top: 2px solid #b31217;
    padding-top: 12px;
}

.terbilang-section {
    background: #f5f5f5;
    padding: 12px 15px;
    border-radius: 6px;
    margin: 20px 0;
    font-style: italic;
    font-size: 11px;
}

.terbilang-section strong {
    color: #b31217;
}

.footer-section {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
}

.signature-box {
    text-align: center;
    width: 200px;
}

.signature-box .label {
    font-size: 11px;
    color: #666;
    margin-bottom: 60px;
}

.signature-box .name {
    font-weight: 700;
    border-top: 1px solid #333;
    padding-top: 5px;
}

.btn-action {
    background: #b31217;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    margin: 5px;
}

.btn-action:hover {
    background: #8a0e12;
}

.btn-ghost-action {
    background: #f3f4f6;
    color: #333;
    border: 1px solid #ddd;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin: 5px;
    text-decoration: none;
}

.action-bar {
    max-width: 210mm;
    margin: 0 auto 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<?php
// Helper function for Terbilang
function terbilang($n) {
    $n = abs($n);
    $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    
    if ($n < 12) return $words[$n];
    if ($n < 20) return terbilang($n - 10) . ' Belas';
    if ($n < 100) return terbilang(floor($n / 10)) . ' Puluh' . ($n % 10 ? ' ' . terbilang($n % 10) : '');
    if ($n < 200) return 'Seratus' . ($n - 100 > 0 ? ' ' . terbilang($n - 100) : '');
    if ($n < 1000) return terbilang(floor($n / 100)) . ' Ratus' . ($n % 100 ? ' ' . terbilang($n % 100) : '');
    if ($n < 2000) return 'Seribu' . ($n - 1000 > 0 ? ' ' . terbilang($n - 1000) : '');
    if ($n < 1000000) return terbilang(floor($n / 1000)) . ' Ribu' . ($n % 1000 ? ' ' . terbilang($n % 1000) : '');
    if ($n < 1000000000) return terbilang(floor($n / 1000000)) . ' Juta' . ($n % 1000000 ? ' ' . terbilang($n % 1000000) : '');
    if ($n < 1000000000000) return terbilang(floor($n / 1000000000)) . ' Miliar' . ($n % 1000000000 ? ' ' . terbilang($n % 1000000000) : '');
    return terbilang(floor($n / 1000000000000)) . ' Triliun' . ($n % 1000000000000 ? ' ' . terbilang($n % 1000000000000) : '');
}

$noInvoice = $invoice['no_invoice'] ?? '-';
$tanggal = date('d F Y', strtotime($invoice['tanggal'] ?? 'now'));

// Customer info
$customerName = $offline['nama'] ?? ($project['nama_project'] ?? '-');
$customerAddress = $offline['alamat_pengiriman'] ?? '-';
$customerPhone = $offline['nohp'] ?? '';

// Total
$totalNilaiNum = (int)$total_nilai;
$terbilangText = terbilang($totalNilaiNum) . ' Rupiah';
?>

<div class="action-bar no-print">
    <a href="<?= site_url('admin/project-interior/' . ($project['kode_project'] ?? '')); ?>" class="btn-ghost-action">
        &larr; Kembali
    </a>
    <button onclick="window.print();" class="btn-action">
        <i class="material-icons" style="font-size:16px;vertical-align:-3px;">print</i> Cetak Invoice
    </button>
</div>

<div class="invoice-paper">
    <!-- Header -->
    <div class="header-company">
        <div class="company-info">
            <h1>CV.CATUR BHAKTI MANDIRI</h1>
            <p>Kawasan Industri BSB, A 3A, 5-6 Jatibarang, Mijen, Semarang</p>
        </div>
        <div class="invoice-meta">
            <div class="inv-number">Nomor: <?= esc($noInvoice); ?></div>
            <div class="inv-date">Tanggal: <?= esc($tanggal); ?></div>
        </div>
    </div>

    <!-- Title -->
    <h2 class="title-invoice">Invoice</h2>

    <!-- Customer Info -->
    <div class="customer-section">
        <p>Kepada Yth.</p>
        <p class="customer-name"><?= esc($customerName); ?></p>
        <p><?= esc($customerAddress); ?></p>
        <?php if ($customerPhone): ?>
        <p>Telp: <?= esc($customerPhone); ?></p>
        <?php endif; ?>
    </div>

    <!-- SJ List -->
    <div class="sj-section">
        <strong>Surat Jalan:</strong>
        <?= esc(implode(', ', $sj_numbers)); ?>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:40px;" class="text-center">NO</th>
                <th style="width:120px;">KODE BARANG</th>
                <th>KETERANGAN</th>
                <th style="width:80px;" class="text-center">KUANTITAS</th>
                <th style="width:100px;" class="text-end">HARGA</th>
                <th style="width:120px;" class="text-end">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($items as $item): ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= esc($item['kode_barang']); ?></td>
                <td><?= esc($item['nama_barang']); ?></td>
                <td class="text-center"><?= number_format($item['qty'], 0, ',', '.'); ?></td>
                <td class="text-end">Rp <?= number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
                <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Total -->
    <div class="total-section">
        <div class="total-row grand">
            <span>TOTAL INVOICE</span>
            <span>Rp <?= number_format($totalNilaiNum, 0, ',', '.'); ?></span>
        </div>
    </div>

    <!-- Terbilang -->
    <div class="terbilang-section">
        <strong>Terbilang:</strong> <?= esc($terbilangText); ?>
    </div>

    <!-- Footer -->
    <div class="footer-section">
        <div class="signature-box">
            <div class="label">Diterima oleh,</div>
            <div class="name">________________</div>
        </div>
        <div class="signature-box">
            <div class="label">Bagian Keuangan</div>
            <div class="name">Amaroh U'un Setiawan</div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
