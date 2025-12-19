<!-- app/Views/admin/projectInteriorDetail.php -->

<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --merah: #b31217;
    --merah-600: #a50e12;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --slate-300: #d1d5db;
    --slate-700: #334155;
    --slate-800: #1f2937;
    --ring: rgba(255, 180, 180, .35);
}

/* judul */
h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    letter-spacing: -.2px;
}

h1.teks-sedang::after {
    content: "";
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(179, 18, 23, .25), transparent);
    border-radius: 999px;
}

/* badge status */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
    white-space: nowrap;
}

.badge-status.draft {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #4b5563;
}

.badge-status.dp {
    background: #ecfeff;
    border-color: #a5f3fc;
    color: #0369a1;
}

.badge-status.termin {
    background: #fef9c3;
    border-color: #facc15;
    color: #854d0e;
}

.badge-status.lunas {
    background: #ecfdf3;
    border-color: #4ade80;
    color: #166534;
}

/* card & table */
.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
    box-shadow: 0 10px 28px rgba(2, 6, 23, .06);
}

.table-sm th,
.table-sm td {
    padding: .4rem .5rem;
    font-size: 13px;
    vertical-align: middle;
}

.table-sm thead th {
    background: #f9fafb;
    border-bottom: 1px solid var(--slate-100);
}

/* form */
.form-control,
.form-select {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    transition: border-color .15s, box-shadow .15s, background .15s;
    background: #fff;
    font-weight: 500;
    font-size: 13px;
}

.form-control:focus,
.form-select:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none;
}

textarea.form-control {
    height: auto;
    min-height: 80px;
    resize: vertical;
}

/* buttons */
.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 700;
    letter-spacing: .1px;
    padding: .6em 1em;
    border-radius: 10px;
    box-shadow: 0 10px 28px rgba(179, 18, 23, .26);
    transition: transform .08s, filter .08s, box-shadow .18s, opacity .2s;
    font-size: 13px;
}

.btn-default-merah:hover {
    filter: brightness(.98);
}

.btn-default-merah:active {
    transform: translateY(1px);
    box-shadow: 0 7px 20px rgba(179, 18, 23, .22);
}

.btn-default-merah[disabled] {
    opacity: .5;
    pointer-events: none;
}

.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .55em .9em;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
}

.btn-ghost:hover {
    background: #e5e7eb;
}

/* info chip */
.info-chip {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    padding: 10px 12px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 12px;
    color: #475569;
}

.info-chip i.material-icons {
    font-size: 18px;
    color: #64748b;
    margin-top: 1px;
}

/* quick stats */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.stat {
    border: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    border-radius: 14px;
    padding: 10px 12px;
}

.stat .label {
    font-size: 11.5px;
    color: #64748b;
}

.stat .val {
    font-weight: 800;
    font-size: 13.5px;
    margin-top: 2px;
}

@media (max-width: 768px) {
    .stat-grid {
        grid-template-columns: 1fr;
    }
}

/* notif toast */
.notif {
    position: fixed;
    bottom: 50px;
    right: 0;
    padding: .6em 2em;
    color: #e84a49;
    border-radius: 10px;
    font-weight: 600;
    letter-spacing: -.2px;
    font-size: 13.5px;
    background: #fff8f8;
    border: 1px solid #ffd0d0;
    box-shadow: 0 10px 30px rgba(184, 27, 29, .15);
    transition: .5s;
    transform: translateX(100%);
    z-index: 9999;
}

.notif.show {
    right: 50px;
    transform: translateX(0%);
}

/* small action links */
a.link-soft {
    color: #0f172a;
    text-decoration: none;
    font-weight: 700;
}

a.link-soft:hover {
    text-decoration: underline;
}
</style>

<?php
  // Ekspektasi dari controller:
  // $project = ['kode_project','nama_project','kode_sp'(optional), 'kode_sj','status','nilai_kontrak','total_bayar','created_at', ...]
  // $payments = [ ['id','tanggal','jenis','nominal','catatan'], ... ]
  // $msg = optional

  $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
  $formatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'IDR');
  $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

  function rupiah_local($n, $formatter){
    return $formatter->formatCurrency((int)$n, 'IDR');
  }

  $nilaiKontrak = (int)($project['nilai_kontrak'] ?? 0);
  $totalBayar   = (int)($project['total_bayar'] ?? 0);
  $sisa         = max(0, $nilaiKontrak - $totalBayar);

  $hasDp = false;
  $terminCount = 0;
  $hasPelunasan = false;

  if (!empty($payments) && is_array($payments)) {
    foreach ($payments as $p) {
      $jenis = strtolower($p['jenis'] ?? '');
      if ($jenis === 'dp') $hasDp = true;
      elseif ($jenis === 'termin') $terminCount++;
      elseif ($jenis === 'pelunasan') $hasPelunasan = true;
    }
  }

  $statusLower = strtolower($project['status'] ?? 'draft');
  $iconStatus = 'payments';
  if ($statusLower === 'lunas') $iconStatus = 'verified';
  elseif ($statusLower === 'draft') $iconStatus = 'hourglass_empty';

  $canPay = $sisa > 0 && !$hasPelunasan;

  // Dokumen utama (SJ/NF) yang di-reserve
  $kodeDokUtama = (string)($project['kode_sj'] ?? '');
  $labelDokUtama = 'Dokumen Utama';
  if (preg_match('/^NF/i', $kodeDokUtama)) $labelDokUtama = 'NF';
  elseif (preg_match('/^SJ/i', $kodeDokUtama)) $labelDokUtama = 'SJ';
?>

<div style="padding: 2em;" class="h-100 d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="teks-sedang mb-0">Project Interior</h1>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/order/offline/interior'); ?>" class="btn-ghost">
                <i class="material-icons" style="font-size:16px;vertical-align:-3px;">arrow_back</i>
                Kembali
            </a>
            <a href="<?= site_url('admin/project-interior/add'); ?>" class="btn-ghost">
                <i class="material-icons" style="font-size:16px;vertical-align:-3px;">add</i>
                Project Baru
            </a>
        </div>
    </div>

    <?php if (!empty($msg)) : ?>
    <div class="notif show" id="notif-msg"><?= esc($msg) ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <!-- KIRI -->
        <div class="col-lg-7">
            <div class="card-soft p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1" style="font-weight:800;letter-spacing:-.2px;">
                            <?= esc($project['nama_project'] ?? '-'); ?>
                        </h5>
                        <div style="font-size:13px;color:#64748b;">
                            Kode Project: <code><?= esc($project['kode_project'] ?? '-'); ?></code><br>

                            <?= esc($labelDokUtama); ?>:
                            <code><?= esc($kodeDokUtama !== '' ? $kodeDokUtama : '-'); ?></code>
                        </div>
                    </div>

                    <span class="badge-status <?= esc($statusLower); ?>">
                        <i class="material-icons" style="font-size:15px;"><?= esc($iconStatus); ?></i>
                        <?= strtoupper($statusLower); ?>
                    </span>
                </div>

                <div class="stat-grid mt-3">
                    <div class="stat">
                        <div class="label">Nilai Kontrak</div>
                        <div class="val"><?= rupiah_local($nilaiKontrak, $formatter); ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Total Pembayaran</div>
                        <div class="val"><?= rupiah_local($totalBayar, $formatter); ?></div>
                    </div>
                    <div class="stat">
                        <div class="label">Sisa Tagihan</div>
                        <div class="val" style="color:<?= $sisa > 0 ? '#b91c1c' : '#166534'; ?>;">
                            <?= rupiah_local($sisa, $formatter); ?>
                        </div>
                    </div>
                </div>

                <div class="info-chip mt-3">
                    <i class="material-icons">info</i>
                    <div>
                        Invoice akhir hanya bisa dibuat saat status <b>LUNAS</b>.
                        Dokumen utama (SJ/NF) untuk project ini sudah di-reserve.
                    </div>
                </div>
            </div>

            <!-- Riwayat pembayaran -->
            <div class="card-soft p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0" style="font-weight:800;">Riwayat Pembayaran</h6>
                    <?php if (!empty($payments)) : ?>
                    <div style="font-size:12px;color:#64748b;">
                        <?= (int)count($payments); ?> transaksi
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($payments)) : ?>
                <p class="mb-0" style="font-size:13px;color:#6b7280;"><i>Belum ada pembayaran.</i></p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:140px;">Tanggal</th>
                                <th style="width:90px;">Jenis</th>
                                <th class="text-end" style="width:140px;">Nominal</th>
                                <th>Catatan</th>
                                <th class="text-center" style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $p) : ?>
                            <?php $jenisLower = strtolower($p['jenis'] ?? ''); ?>
                            <tr>
                                <td style="font-size:12px;">
                                    <?= esc(date('d/m/Y H:i', strtotime($p['tanggal'] ?? 'now'))); ?>
                                </td>
                                <td style="font-size:12px;text-transform:uppercase;">
                                    <?= esc($p['jenis'] ?? '-'); ?>
                                </td>
                                <td class="text-end" style="font-size:13px;font-weight:700;">
                                    <?= rupiah_local((int)($p['nominal'] ?? 0), $formatter); ?>
                                </td>
                                <td style="font-size:12px;max-width:260px;">
                                    <?= !empty($p['catatan']) ? esc($p['catatan']) : '<span class="text-muted">-</span>'; ?>
                                </td>
                                <td class="text-center" style="font-size:12px;">
                                    <a href="#" class="btn-ghost btn-print-payment"
                                        data-url="<?= site_url('admin/project-interior/' . ($project['kode_project'] ?? '') . '/payment-invoice/' . ($p['id'] ?? '')); ?>"
                                        data-jenis="<?= esc($jenisLower); ?>"
                                        style="padding:.25rem .6rem;font-size:11px;">
                                        Cetak Invoice
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- KANAN -->
        <div class="col-lg-5">
            <div class="card-soft p-3 mb-3">
                <h6 class="mb-2" style="font-weight:800;">Tambah Pembayaran</h6>

                <div class="mb-2" style="font-size:12px;color:#6b7280;">
                    Nilai kontrak: <b><?= rupiah_local($nilaiKontrak, $formatter); ?></b><br>
                    Total dibayar: <b><?= rupiah_local($totalBayar, $formatter); ?></b><br>
                    Sisa tagihan: <b id="label-sisa-tagihan"><?= rupiah_local($sisa, $formatter); ?></b>
                </div>

                <?php if (!$canPay): ?>
                <div class="info-chip mb-2" style="background:#fff7ed;border-color:#fed7aa;color:#7c2d12;">
                    <i class="material-icons" style="color:#c2410c;">lock</i>
                    <div>
                        <?php if ($sisa <= 0): ?>
                        Sisa tagihan sudah <b>0</b>. Project siap diarahkan ke <b>LUNAS</b> dan buat invoice akhir.
                        <?php else: ?>
                        Project sudah memiliki <b>PELUNASAN</b>. Pembayaran baru dikunci biar tidak “dobel lunas”.
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <form id="form-payment"
                    action="<?= site_url('admin/project-interior/' . ($project['kode_project'] ?? '') . '/payment'); ?>"
                    method="post">
                    <?= csrf_field(); ?>

                    <div class="mb-2">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:700;">Jenis Pembayaran</label>
                        <select name="jenis" class="form-select" id="jenis-pembayaran" required
                            <?= !$canPay ? 'disabled' : ''; ?>>
                            <option value="">-- Pilih jenis --</option>
                            <option value="dp">DP</option>
                            <option value="termin">Termin</option>
                            <option value="pelunasan">Pelunasan</option>
                        </select>
                    </div>

                    <div class="mb-2" id="wrap-dp-percent" style="display:none;">
                        <label class="form-label mb-1" id="label-percent"
                            style="font-size:13px;font-weight:700;"></label>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select" id="dp-percent" <?= !$canPay ? 'disabled' : ''; ?>>
                                <option value="">-- Pilih % --</option>
                                <option value="10">10%</option>
                                <option value="20">20%</option>
                                <option value="25">25%</option>
                                <option value="30">30%</option>
                                <option value="40">40%</option>
                                <option value="50">50%</option>
                                <option value="60">60%</option>
                            </select>
                            <small id="help-percent" style="font-size:11px;color:#6b7280;"></small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:700;">Nominal (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="nominal" id="input-nominal" class="form-control rupiah"
                                placeholder="Misal: 25.000.000" required <?= !$canPay ? 'disabled' : ''; ?>>
                            <button class="btn-ghost" type="button" id="btn-fill-sisa"
                                style="white-space:nowrap;font-size:11px;" <?= !$canPay ? 'disabled' : ''; ?>>
                                Isi sisa
                            </button>
                        </div>
                        <small style="font-size:11px;color:#6b7280;">Nominal tidak boleh melebihi sisa tagihan.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:700;">Catatan
                            (opsional)</label>
                        <textarea name="catatan" id="catatan-input" class="form-control"
                            placeholder="Misal: DP 30%, Termin 1, dll." <?= !$canPay ? 'disabled' : ''; ?>></textarea>
                    </div>

                    <button type="submit" class="btn-default-merah w-100" <?= !$canPay ? 'disabled' : ''; ?>>
                        Simpan Pembayaran
                    </button>

                    <div class="mt-2" style="font-size:11.5px;color:#6b7280;">
                        Total setelah pembayaran akan otomatis mengubah status project:
                        <br>- <b>DP</b> / <b>TERMIN</b> jika belum penuh
                        <br>- <b>LUNAS</b> jika total pembayaran ≥ nilai kontrak.
                    </div>
                </form>
            </div>

            <div class="card-soft p-3">
                <h6 class="mb-2" style="font-weight:800;">Invoice &amp; Surat Jalan</h6>

                <?php if (!empty($kodeDokUtama)): ?>
                <div class="mb-2">
                    <p class="mb-1" style="font-size:12px;color:#6b7280;">
                        Dokumen utama project ini:
                        <code><?= esc($kodeDokUtama); ?></code>
                    </p>

                    <a href="<?= site_url('admin/project-interior/' . ($project['kode_project'] ?? '') . '/sj'); ?>"
                        target="_blank" class="btn-ghost w-100" style="margin-bottom:.35rem;">
                        Cetak Surat Jalan (<?= esc($kodeDokUtama); ?>)
                    </a>
                </div>
                <?php else: ?>
                <p class="mb-2" style="font-size:13px;color:#9ca3af;">
                    Dokumen utama belum tersedia / belum di-reserve.
                </p>
                <?php endif; ?>

                <?php if ($statusLower !== 'lunas') : ?>
                <p class="mb-2" style="font-size:13px;color:#9ca3af;">
                    Invoice akhir belum bisa dibuat karena project belum <b>lunas</b>.
                </p>
                <button type="button" class="btn-default-merah w-100" disabled>Buat Invoice (Akhir)</button>
                <?php else : ?>
                <p class="mb-2" style="font-size:13px;color:#16a34a;">
                    Project sudah lunas. Klik tombol di bawah untuk membuat invoice akhir dari dokumen:
                    <code><?= esc($kodeDokUtama ?: '-'); ?></code>
                </p>
                <a href="<?= site_url('admin/project-interior/' . ($project['kode_project'] ?? '') . '/invoice'); ?>"
                    class="btn-default-merah w-100">
                    Buat Invoice (Akhir)
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const NILAI_KONTRAK = <?= (int)$nilaiKontrak; ?>;
let sisaTagihan = <?= (int)$sisa; ?>;

// status pembayaran dari PHP
const HAS_DP = <?= $hasDp ? 'true' : 'false'; ?>;
const TERMIN_COUNT = <?= (int)$terminCount; ?>;
const HAS_PELUNASAN = <?= $hasPelunasan ? 'true' : 'false'; ?>;

/* ---------- helpers ---------- */
function stripNonDigit(v) {
    return (v || '').toString().replace(/[^\d]/g, '');
}

function formatRupiah(val) {
    const raw = stripNonDigit(val);
    if (!raw) return '';
    return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function saWarning(title, text) {
    if (typeof Swal !== 'undefined') Swal.fire({
        icon: 'warning',
        title,
        text
    });
    else alert(title + ' - ' + text);
}

function saError(title, text) {
    if (typeof Swal !== 'undefined') Swal.fire({
        icon: 'error',
        title,
        text
    });
    else alert(title + ' - ' + text);
}

/* rupiah typing */
document.querySelectorAll('.rupiah').forEach((el) => {
    el.addEventListener('input', function() {
        this.value = formatRupiah(this.value);
    });
});

/* auto hide notif */
const notif = document.getElementById('notif-msg');
if (notif) setTimeout(() => notif.classList.remove('show'), 3500);

/* ---------- pembayaran logic ---------- */
const jenisSelect = document.getElementById('jenis-pembayaran');
const dpWrap = document.getElementById('wrap-dp-percent');
const dpPercent = document.getElementById('dp-percent');
const inputNom = document.getElementById('input-nominal');
const btnSisa = document.getElementById('btn-fill-sisa');
const catatanInput = document.getElementById('catatan-input');
const labelPercent = document.getElementById('label-percent');
const helpPercent = document.getElementById('help-percent');

function resetPercentUI() {
    if (dpWrap) dpWrap.style.display = 'none';
    if (dpPercent) dpPercent.value = '';
    if (labelPercent) labelPercent.textContent = '';
    if (helpPercent) helpPercent.textContent = '';
}

function setNominal(n) {
    if (!inputNom) return;
    inputNom.value = formatRupiah(String(Math.max(0, n || 0)));
}

if (jenisSelect) {
    jenisSelect.addEventListener('change', function() {
        const jenis = (this.value || '').toLowerCase();

        resetPercentUI();
        if (inputNom) inputNom.value = '';

        if (HAS_PELUNASAN && jenis) {
            saWarning('Sudah Pelunasan',
                'Project sudah memiliki pembayaran Pelunasan. Tidak dapat menambah pembayaran baru.');
            this.value = '';
            return;
        }

        if (jenis === 'dp') {
            if (HAS_DP) {
                saWarning('DP sudah ada', 'DP sudah pernah dibuat. Tidak bisa menambah DP lagi.');
                this.value = '';
                return;
            }
            if (labelPercent) labelPercent.textContent = 'DP (Persen dari nilai kontrak)';
            if (helpPercent) helpPercent.textContent = 'Nominal akan dihitung otomatis dari nilai kontrak.';
            if (dpWrap) dpWrap.style.display = '';
            if (catatanInput && !catatanInput.value.trim()) catatanInput.value = 'Uang Muka';
            return;
        }

        if ((jenis === 'termin' || jenis === 'pelunasan') && !HAS_DP) {
            saWarning('Belum ada DP',
                'Tidak bisa menambahkan Termin atau Pelunasan sebelum ada pembayaran DP/Uang Muka.');
            this.value = '';
            return;
        }

        if (jenis === 'termin') {
            if (labelPercent) labelPercent.textContent = 'Termin (Persen dari sisa tagihan)';
            if (helpPercent) helpPercent.textContent = 'Nominal dihitung dari sisa tagihan saat ini.';
            if (dpWrap) dpWrap.style.display = '';
            const nextTermin = (TERMIN_COUNT || 0) + 1;
            if (catatanInput && !catatanInput.value.trim()) catatanInput.value = 'Termin ' + nextTermin;
            return;
        }

        if (jenis === 'pelunasan') {
            if (catatanInput && !catatanInput.value.trim()) catatanInput.value = 'Pelunasan';
            setNominal(sisaTagihan);
            return;
        }
    });
}

if (dpPercent && inputNom) {
    dpPercent.addEventListener('change', function() {
        const persen = parseInt(this.value || '0', 10);
        if (!persen) return;

        const jenis = (jenisSelect?.value || '').toLowerCase();
        let basis = 0;

        if (jenis === 'dp') basis = NILAI_KONTRAK;
        else if (jenis === 'termin') basis = sisaTagihan;

        if (!basis) return;

        let nominal = Math.round(basis * (persen / 100));
        if (nominal > sisaTagihan) nominal = sisaTagihan; // penjagaan
        setNominal(nominal);
    });
}

if (btnSisa && inputNom) {
    btnSisa.addEventListener('click', () => setNominal(sisaTagihan));
}

/* validasi sebelum submit */
const formPayment = document.getElementById('form-payment');
if (formPayment) {
    formPayment.addEventListener('submit', function(e) {
        const jenis = (jenisSelect?.value || '').toLowerCase();
        const rawNom = stripNonDigit(inputNom?.value || '');
        const nominal = rawNom ? parseInt(rawNom, 10) : 0;

        if (!jenis) {
            e.preventDefault();
            saError('Jenis kosong', 'Pilih jenis pembayaran dulu.');
            return;
        }
        if (!nominal || nominal <= 0) {
            e.preventDefault();
            saError('Nominal kosong', 'Isi nominal pembayaran.');
            return;
        }
        if (nominal > sisaTagihan) {
            e.preventDefault();
            saError('Nominal terlalu besar', 'Nominal tidak boleh melebihi sisa tagihan.');
            return;
        }

        // sanitize: kirim angka murni ke backend
        if (inputNom) inputNom.value = String(nominal);
    });
}

/* ---------- popup cetak invoice payment ---------- */
document.querySelectorAll('.btn-print-payment').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.getAttribute('data-url') || '';
        const jenis = (this.getAttribute('data-jenis') || '').toLowerCase();
        if (!url) return;

        // DP: langsung
        if (jenis === 'dp') {
            window.open(url, '_blank');
            return;
        }

        const defaultChecked = (jenis === 'termin' || jenis === 'pelunasan');

        if (typeof Swal === 'undefined') {
            const includeSj = confirm('Tampilkan Surat Jalan di invoice juga?');
            let finalUrl = url;
            if (includeSj && defaultChecked) finalUrl += (url.includes('?') ? '&' : '?') + 'show_sj=1';
            window.open(finalUrl, '_blank');
            return;
        }

        Swal.fire({
            title: 'Cetak Invoice',
            html: `
        <div style="text-align:left;font-size:13px;">
          <p style="margin-bottom:8px;">Jenis pembayaran: <b>${jenis.toUpperCase()}</b></p>
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;">
            <input type="checkbox" id="swal-show-sj" ${defaultChecked ? 'checked' : ''}>
            <span>Tampilkan nomor Surat Jalan di invoice</span>
          </label>
          <p style="margin-top:8px;color:#6b7280;font-size:11.5px;">
            Opsi ini berlaku untuk Termin/Pelunasan. Untuk DP, SJ tidak ditampilkan.
          </p>
        </div>
      `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Cetak',
            cancelButtonText: 'Batal',
        }).then(res => {
            if (!res.isConfirmed) return;
            const includeSj = document.getElementById('swal-show-sj')?.checked &&
                defaultChecked;
            let finalUrl = url;
            if (includeSj) finalUrl += (url.includes('?') ? '&' : '?') + 'show_sj=1';
            window.open(finalUrl, '_blank');
        });
    });
});
</script>

<?= $this->endSection(); ?>