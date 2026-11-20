<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<main class="invoice-gap-detail">
  <style>
    .invoice-gap-detail{max-width:850px;margin:0 auto;padding:12px 0 40px;color:#28231f}.invoice-gap-detail__head{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;padding-bottom:18px;border-bottom:1px solid #e9e3da}.invoice-gap-detail h1{margin:3px 0;font-size:clamp(22px,3vw,30px);letter-spacing:-.035em}.invoice-gap-detail p{color:#756b62;line-height:1.6}.invoice-gap-detail__eyebrow{color:#756b62;font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase}.invoice-gap-detail__actions{display:flex;gap:8px;flex-wrap:wrap}.invoice-gap-detail__button{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:0 13px;border:1px solid #ded7ce;border-radius:7px;color:#28231f;text-decoration:none;font-size:13px;font-weight:650}.invoice-gap-detail__button--primary{border-color:#a65043;background:#a65043;color:#fff}.invoice-gap-detail__notice{margin:20px 0;padding:14px 16px;border-left:3px solid #a65043;background:#faf4f2;color:#513b36;font-size:13px;line-height:1.6}.invoice-gap-detail__fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 24px;border-top:1px solid #e9e3da}.invoice-gap-detail__field{padding:15px 0;border-bottom:1px solid #e9e3da}.invoice-gap-detail__field dt{margin-bottom:5px;color:#756b62;font-size:12px}.invoice-gap-detail__field dd{margin:0;font-size:14px;font-weight:600;overflow-wrap:anywhere}.invoice-gap-detail__number{font:700 18px ui-monospace,SFMono-Regular,Consolas,monospace;color:#6b503a}@media(max-width:620px){.invoice-gap-detail__head{flex-direction:column}.invoice-gap-detail__fields{grid-template-columns:1fr}.invoice-gap-detail__actions{width:100%}.invoice-gap-detail__button{flex:1}}
  </style>
  <header class="invoice-gap-detail__head"><div><div class="invoice-gap-detail__eyebrow">Administrasi pesanan · Rekonsiliasi</div><h1>Detail nomor yang belum direkonsiliasi</h1><p>Rincian ini hanya memuat informasi register yang tersedia.</p></div><nav class="invoice-gap-detail__actions" aria-label="Aksi"><a class="invoice-gap-detail__button" href="/admin/order/online">Kembali ke pesanan</a><a class="invoice-gap-detail__button invoice-gap-detail__button--primary" href="/admin/order/number-gap/<?= rawurlencode((string) $gap['invoice_number']) ?>/pdf">Unduh laporan PDF</a></nav></header>
  <?php if ($error = session()->getFlashdata('order_gap_error')): ?><div class="invoice-gap-detail__notice" role="alert"><?= esc($error) ?></div><?php endif; ?>
  <?php if ($success = session()->getFlashdata('order_gap_success')): ?><div class="invoice-gap-detail__notice" role="status"><?= esc($success) ?></div><?php endif; ?>
  <div class="invoice-gap-detail__notice"><strong>Ini bukan invoice atau pesanan.</strong> Register ini tidak berisi data pelanggan, produk, pembayaran, atau transaksi. Dev dapat mencatat hasil pencocokan dan referensi bukti; data pesanan hanya berubah melalui pemulihan record asli.</div>
  <dl class="invoice-gap-detail__fields">
    <div class="invoice-gap-detail__field"><dt>Nomor yang dicari</dt><dd class="invoice-gap-detail__number"><?= esc($gap['invoice_number']) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Status rekonsiliasi</dt><dd><?= esc(match ((string) ($gap['status'] ?? '')) { 'evidence_found' => 'Bukti ditemukan · perlu rekonsiliasi', 'not_issued' => 'Diverifikasi tidak diterbitkan', default => 'Belum direkonsiliasi' }) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Pencocokan di tabel pesanan</dt><dd><?= !empty($gap['matching_order_found']) ? 'Ada nomor pesanan yang cocok; perlu pemeriksaan admin.' : 'Belum ditemukan nomor pesanan yang cocok.' ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Catatan</dt><dd><?= esc($gap['note']) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Jenis bukti</dt><dd><?= esc((string) ($gap['evidence_type'] ?? 'Belum dicatat')) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Referensi bukti</dt><dd><?= esc((string) ($gap['evidence_reference'] ?? 'Belum dicatat')) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Ditinjau oleh</dt><dd><?= esc((string) ($gap['reviewed_by'] ?? 'Belum ditinjau')) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Waktu peninjauan</dt><dd><?= esc((string) ($gap['reviewed_at'] ?? 'Belum ditinjau')) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Dicatat pada</dt><dd><?= esc((string) ($gap['created_at'] ?? 'Tidak tersedia')) ?></dd></div>
    <div class="invoice-gap-detail__field"><dt>Terakhir diperbarui</dt><dd><?= esc((string) ($gap['updated_at'] ?? 'Tidak tersedia')) ?></dd></div>
  </dl>
  <?php if ((string) session()->get('role') === '1'): ?>
    <section class="invoice-gap-detail__review" aria-labelledby="reviewHeading">
      <h2 id="reviewHeading">Pembaruan Dev</h2>
      <p>Catat referensi sumber yang benar-benar diperiksa, misalnya ID arsip backup atau nomor referensi pembayaran. Jangan masukkan data perkiraan.</p>
      <form method="post" action="/admin/order/number-gap/<?= rawurlencode((string) $gap['invoice_number']) ?>">
        <?= csrf_field() ?>
        <label>Status tinjauan<select name="status" required><option value="unverified_gap" <?= ($gap['status'] ?? '') === 'unverified_gap' ? 'selected' : '' ?>>Belum direkonsiliasi</option><option value="evidence_found" <?= ($gap['status'] ?? '') === 'evidence_found' ? 'selected' : '' ?>>Bukti ditemukan · perlu rekonsiliasi</option><option value="not_issued" <?= ($gap['status'] ?? '') === 'not_issued' ? 'selected' : '' ?>>Diverifikasi tidak diterbitkan</option></select></label>
        <label>Jenis bukti<select name="evidence_type" required><option value="backup" <?= ($gap['evidence_type'] ?? '') === 'backup' ? 'selected' : '' ?>>Backup database</option><option value="payment_gateway" <?= ($gap['evidence_type'] ?? '') === 'payment_gateway' ? 'selected' : '' ?>>Catatan payment gateway</option><option value="invoice_archive" <?= ($gap['evidence_type'] ?? '') === 'invoice_archive' ? 'selected' : '' ?>>Arsip invoice</option><option value="accounting_record" <?= ($gap['evidence_type'] ?? '') === 'accounting_record' ? 'selected' : '' ?>>Catatan pembukuan</option><option value="other" <?= ($gap['evidence_type'] ?? '') === 'other' ? 'selected' : '' ?>>Lainnya</option></select></label>
        <label>Referensi bukti<input name="evidence_reference" maxlength="200" value="<?= esc((string) ($gap['evidence_reference'] ?? ''), 'attr') ?>" placeholder="Contoh: nama arsip / ID referensi" autocomplete="off"></label>
        <label>Catatan pemeriksaan<textarea name="note" maxlength="255" required><?= esc((string) ($gap['note'] ?? '')) ?></textarea></label>
        <button class="invoice-gap-detail__button invoice-gap-detail__button--primary" type="submit">Simpan tinjauan</button>
      </form>
    </section>
  <?php endif; ?>
  <style>
    .invoice-gap-detail__review{margin-top:26px;padding-top:18px;border-top:1px solid #e9e3da}.invoice-gap-detail__review h2{margin:0 0 5px;font-size:18px}.invoice-gap-detail__review>p{margin:0 0 15px;font-size:13px}.invoice-gap-detail__review form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px 16px}.invoice-gap-detail__review label{display:grid;gap:5px;color:#62594f;font-size:12px;font-weight:650}.invoice-gap-detail__review input,.invoice-gap-detail__review select,.invoice-gap-detail__review textarea{width:100%;min-height:42px;padding:9px 10px;border:1px solid #ded7ce;border-radius:7px;background:#fff;color:#28231f;font:inherit;font-size:13px}.invoice-gap-detail__review textarea{min-height:82px;resize:vertical}.invoice-gap-detail__review button{align-self:end;width:max-content}.invoice-gap-detail__review label:last-of-type{grid-column:1/-1}@media(max-width:620px){.invoice-gap-detail__review form{grid-template-columns:1fr}.invoice-gap-detail__review label:last-of-type{grid-column:auto}.invoice-gap-detail__review button{width:100%}}
  </style>
</main>
<?= $this->endSection(); ?>
