<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Rekonsiliasi <?= esc($gap['invoice_number']) ?></title>
  <style>
    @page{size:A4;margin:22mm 20mm}*{box-sizing:border-box}body{margin:0;color:#28231f;font-family:DejaVu Sans,Arial,sans-serif;font-size:11px;line-height:1.55}.brand{font-size:10px;font-weight:700;letter-spacing:2px;color:#6b503a}.title{margin:8px 0 4px;font-size:21px}.subtitle{margin:0;color:#756b62}.stamp{margin:23px 0;padding:13px;border:1px solid #a65043;color:#8d3f35;text-align:center;font-size:15px;font-weight:700;letter-spacing:1px}.number{margin:20px 0;font-size:22px;font-weight:700;color:#6b503a}.rows{width:100%;border-collapse:collapse}.rows th,.rows td{padding:10px 8px;border-bottom:1px solid #ddd7cf;text-align:left;vertical-align:top}.rows th{width:34%;color:#756b62;font-weight:500}.notice{margin-top:24px;padding:12px;background:#f7f4ef;color:#554b41}.footer{position:fixed;bottom:-10mm;left:0;right:0;color:#8a8177;font-size:9px;text-align:center}
  </style>
</head>
<body>
  <div class="brand">ILENA FURNITURE</div>
  <h1 class="title">Laporan Rekonsiliasi Nomor</h1>
  <p class="subtitle">Dokumen administrasi untuk meninjau nomor yang belum ditemukan pada data pesanan.</p>
  <div class="stamp">BUKAN INVOICE · BUKAN BUKTI TRANSAKSI</div>
  <div class="number"><?= esc($gap['invoice_number']) ?></div>
  <table class="rows"><tbody>
    <tr><th>Status register</th><td><?= esc($gap['status']) ?></td></tr>
    <tr><th>Pencocokan data pesanan</th><td><?= !empty($gap['matching_order_found']) ? 'Ada nomor pesanan yang cocok; perlu pemeriksaan admin.' : 'Belum ditemukan nomor pesanan yang cocok.' ?></td></tr>
    <tr><th>Catatan</th><td><?= esc($gap['note']) ?></td></tr>
    <tr><th>Jenis bukti</th><td><?= esc((string) ($gap['evidence_type'] ?? 'Belum dicatat')) ?></td></tr>
    <tr><th>Referensi bukti</th><td><?= esc((string) ($gap['evidence_reference'] ?? 'Belum dicatat')) ?></td></tr>
    <tr><th>Ditinjau oleh</th><td><?= esc((string) ($gap['reviewed_by'] ?? 'Belum ditinjau')) ?></td></tr>
    <tr><th>Waktu peninjauan</th><td><?= esc((string) ($gap['reviewed_at'] ?? 'Belum ditinjau')) ?></td></tr>
    <tr><th>Dicatat pada</th><td><?= esc((string) ($gap['created_at'] ?? 'Tidak tersedia')) ?></td></tr>
    <tr><th>Terakhir diperbarui</th><td><?= esc((string) ($gap['updated_at'] ?? 'Tidak tersedia')) ?></td></tr>
  </tbody></table>
  <?php if (!empty($gap['reported_details'])): $reported = $gap['reported_details']; ?>
    <h2>Rincian yang dilaporkan</h2>
    <p><?= esc($reported['source_label'] ?? 'Informasi sumber belum diverifikasi.') ?> Rincian berikut adalah snapshot rekonsiliasi, bukan bukti transaksi.</p>
    <table class="rows"><tbody>
      <tr><th>Penerima</th><td><?= esc((string) ($reported['recipient_name'] ?? '—')) ?></td></tr>
      <tr><th>Alamat penerima</th><td><?= esc((string) ($reported['recipient_address'] ?? '—')) ?></td></tr>
      <tr><th>Email</th><td><?= esc((string) ($reported['email'] ?? '—')) ?></td></tr>
      <tr><th>Produk</th><td><?= esc((string) ($reported['product_name'] ?? '—')) ?></td></tr>
      <tr><th>Varian</th><td><?= esc((string) ($reported['variant'] ?? '—')) ?></td></tr>
      <tr><th>Jumlah</th><td><?= esc((string) ($reported['quantity'] ?? '—')) ?></td></tr>
      <tr><th>Harga satuan</th><td>Rp <?= number_format((int) ($reported['unit_price'] ?? 0), 0, ',', '.') ?></td></tr>
      <tr><th>Total item</th><td>Rp <?= number_format((int) ($reported['line_total'] ?? 0), 0, ',', '.') ?></td></tr>
      <tr><th>Status yang dilaporkan</th><td><?= esc((string) ($reported['reported_order_status'] ?? '—')) ?></td></tr>
    </tbody></table>
  <?php endif; ?>
  <div class="notice">Laporan ini hanya memuat data register rekonsiliasi. Dokumen ini tidak menyatakan bahwa pesanan pernah terjadi, invoice pernah diterbitkan, atau nomor telah dibatalkan.</div>
  <div class="footer">Ilena Furniture · Laporan rekonsiliasi internal · <?= esc(date('d-m-Y H:i')) ?></div>
</body>
</html>
