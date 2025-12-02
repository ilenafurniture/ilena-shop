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
    padding: 4px 9px;
    border-radius: 999px;
    border: 1px solid transparent;
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
}

.table-sm th,
.table-sm td {
    padding: .4rem .5rem;
    font-size: 13px;
}

.table-sm thead th {
    background: #f9fafb;
    border-bottom: 1px solid var(--slate-100);
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

/* notif */
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
</style>

<?php
    $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
    $formatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'IDR');
    $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

    function rupiah_local($n, $formatter) {
        return $formatter->formatCurrency((int)$n, 'IDR');
    }

    $nilaiKontrak = (int)$project['nilai_kontrak'];
    $sisa = max(0, $nilaiKontrak - (int)$project['total_bayar']);
?>

<div style="padding: 2em;" class="h-100 d-flex flex-column">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="teks-sedang mb-0">
            Project Interior
        </h1>
        <a href="<?= site_url('admin/project-interior/add'); ?>" class="btn-ghost">
            <i class="material-icons" style="font-size:16px;vertical-align:-3px;">add</i>
            Project Baru
        </a>
    </div>

    <?php if (!empty($msg)) : ?>
    <div class="notif show" id="notif-msg">
        <?= esc($msg) ?>
    </div>
    <?php endif; ?>

    <div class="row g-3">
        <!-- KIRI: Info Project -->
        <div class="col-lg-7">
            <div class="card-soft p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1" style="font-weight:700;letter-spacing:-.2px;">
                            <?= esc($project['nama_project']); ?>
                        </h5>
                        <div style="font-size:13px;color:#64748b;">
                            Kode Project: <code><?= esc($project['kode_project']); ?></code><br>
                            SP: <code><?= esc($project['kode_sp']); ?></code> • Dokumen utama:
                            <code><?= esc($project['kode_sj']); ?></code>
                        </div>
                    </div>
                    <span class="badge-status <?= esc($project['status']); ?>">
                        <i class="material-icons" style="font-size:15px;">
                            <?php if ($project['status'] === 'lunas') : ?>
                            verified
                            <?php elseif ($project['status'] === 'draft') : ?>
                            hourglass_empty
                            <?php else : ?>
                            payments
                            <?php endif; ?>
                        </i>
                        <?= strtoupper($project['status']); ?>
                    </span>
                </div>

                <hr class="my-2">

                <div class="row" style="font-size:13px;">
                    <div class="col-md-4 mb-2">
                        <div class="text-muted">Nilai Kontrak</div>
                        <div style="font-weight:700;">
                            <?= rupiah_local($project['nilai_kontrak'], $formatter); ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="text-muted">Total Pembayaran</div>
                        <div style="font-weight:700;">
                            <?= rupiah_local($project['total_bayar'], $formatter); ?>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="text-muted">Sisa Tagihan</div>
                        <div style="font-weight:700; color:<?= $sisa > 0 ? '#b91c1c' : '#166534'; ?>">
                            <?= rupiah_local($sisa, $formatter); ?>
                        </div>
                    </div>
                </div>

                <div class="mt-2" style="font-size:12px;color:#64748b;">
                    <i class="material-icons" style="font-size:14px;vertical-align:-3px;">info</i>
                    Invoice NF/SP (invoice akhir) hanya bisa dibuat saat status <b>LUNAS</b>.
                    SP &amp; dokumen utama untuk project ini sudah di-reserve.
                </div>
            </div>

            <!-- Riwayat pembayaran -->
            <div class="card-soft p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0" style="font-weight:700;">Riwayat Pembayaran</h6>
                </div>

                <?php if (empty($payments)) : ?>
                <p class="mb-0" style="font-size:13px;color:#6b7280;"><i>Belum ada pembayaran.</i></p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-end">Nominal</th>
                                <th>Catatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $p) : ?>
                            <tr>
                                <td style="font-size:12px;">
                                    <?= esc(date('d/m/Y H:i', strtotime($p['tanggal']))); ?>
                                </td>
                                <td style="font-size:12px;text-transform:uppercase;">
                                    <?= esc($p['jenis']); ?>
                                </td>
                                <td class="text-end" style="font-size:13px;font-weight:600;">
                                    <?= rupiah_local($p['nominal'], $formatter); ?>
                                </td>
                                <td style="font-size:12px;max-width:260px;">
                                    <?= $p['catatan'] ? esc($p['catatan']) : '<span class="text-muted">-</span>'; ?>
                                </td>
                                <td class="text-center" style="font-size:12px;">
                                    <a href="<?= site_url('admin/project-interior/' . $project['kode_project'] . '/payment-invoice/' . $p['id']); ?>"
                                        class="btn-ghost" target="_blank" style="padding:.25rem .6rem;font-size:11px;">
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

        <!-- KANAN: Form tambah pembayaran + tombol invoice -->
        <div class="col-lg-5">
            <div class="card-soft p-3 mb-3">
                <h6 class="mb-2" style="font-weight:700;">Tambah Pembayaran</h6>

                <div class="mb-2" style="font-size:12px;color:#6b7280;">
                    Nilai kontrak: <b><?= rupiah_local($nilaiKontrak, $formatter); ?></b><br>
                    Total dibayar: <b><?= rupiah_local($project['total_bayar'], $formatter); ?></b><br>
                    Sisa tagihan: <b id="label-sisa-tagihan"><?= rupiah_local($sisa, $formatter); ?></b>
                </div>

                <form action="<?= site_url('admin/project-interior/' . $project['kode_project'] . '/payment'); ?>"
                    method="post">
                    <?= csrf_field(); ?>

                    <div class="mb-2">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Jenis Pembayaran</label>
                        <select name="jenis" class="form-select" id="jenis-pembayaran" required>
                            <option value="">-- Pilih jenis --</option>
                            <option value="dp">DP</option>
                            <option value="termin">Termin</option>
                            <option value="pelunasan">Pelunasan</option>
                        </select>
                    </div>

                    <!-- DP by persen -->
                    <div class="mb-2" id="wrap-dp-percent" style="display:none;">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">DP (Persen dari nilai
                            kontrak)</label>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select" id="dp-percent">
                                <option value="">-- Pilih % DP --</option>
                                <option value="10">10%</option>
                                <option value="20">20%</option>
                                <option value="25">25%</option>
                                <option value="30">30%</option>
                                <option value="40">40%</option>
                                <option value="50">50%</option>
                                <option value="60">60%</option>
                            </select>
                            <small style="font-size:11px;color:#6b7280;">
                                Nominal akan dihitung otomatis dari nilai kontrak.
                            </small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Nominal (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="nominal" id="input-nominal" class="form-control rupiah"
                                placeholder="Misal: 25.000.000" required>
                            <button class="btn-ghost" type="button" id="btn-fill-sisa"
                                style="white-space:nowrap;font-size:11px;">
                                Isi sisa
                            </button>
                        </div>
                        <small style="font-size:11px;color:#6b7280;">
                            Nominal tidak boleh melebihi sisa tagihan.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Catatan
                            (opsional)</label>
                        <textarea name="catatan" class="form-control"
                            placeholder="Misal: DP 30%, Termin 1, dll."></textarea>
                    </div>

                    <button type="submit" class="btn-default-merah w-100" <?= $sisa <= 0 ? 'disabled' : ''; ?>>
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
                <h6 class="mb-2" style="font-weight:700;">Invoice &amp; Surat Jalan</h6>

                <?php if (!empty($project['kode_sj'])): ?>
                <div class="mb-2">
                    <p class="mb-1" style="font-size:12px;color:#6b7280;">
                        Dokumen utama project ini:
                        <code><?= esc($project['kode_sj']); ?></code>
                    </p>
                    <!-- UPDATED: pakai route khusus SJ project interior -->
                    <a href="<?= site_url('admin/project-interior/' . $project['kode_project'] . '/sj'); ?>"
                        target="_blank" class="btn-ghost w-100" style="margin-bottom:.35rem;">
                        Cetak Surat Jalan / SP (<?= esc($project['kode_sj']); ?>)
                    </a>
                </div>
                <?php endif; ?>

                <?php if ($project['status'] !== 'lunas') : ?>
                <p class="mb-2" style="font-size:13px;color:#9ca3af;">
                    Invoice NF/SP (invoice akhir) belum bisa dibuat karena project belum <b>lunas</b>.
                </p>
                <button type="button" class="btn-default-merah w-100" disabled>
                    Buat Invoice NF/SP
                </button>
                <?php else : ?>
                <p class="mb-2" style="font-size:13px;color:#16a34a;">
                    Project sudah lunas. Klik tombol di bawah untuk membuat invoice dari dokumen:
                    <code><?= esc($project['kode_sj']); ?></code>
                </p>
                <a href="<?= site_url('admin/project-interior/' . $project['kode_project'] . '/invoice'); ?>"
                    class="btn-default-merah w-100">
                    Buat Invoice NF/SP
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const NILAI_KONTRAK = <?= (int)$nilaiKontrak; ?>;
let sisaTagihan = <?= (int)$sisa; ?>;

// format rupiah
function formatRupiah(val) {
    val = val.replace(/[^\d]/g, '');
    if (!val) return '';
    return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
document.querySelectorAll('.rupiah').forEach(function(el) {
    el.addEventListener('input', function() {
        this.value = formatRupiah(this.value);
    });
});

// auto hide notif
const notif = document.getElementById('notif-msg');
if (notif) {
    setTimeout(() => {
        notif.classList.remove('show');
    }, 3500);
}

// DP persen logic + isi sisa otomatis
const jenisSelect = document.getElementById('jenis-pembayaran');
const dpWrap = document.getElementById('wrap-dp-percent');
const dpPercent = document.getElementById('dp-percent');
const inputNom = document.getElementById('input-nominal');
const btnSisa = document.getElementById('btn-fill-sisa');

if (jenisSelect) {
    jenisSelect.addEventListener('change', function() {
        const jenis = this.value;

        // Kalau DP -> tampilkan pilihan persen
        if (jenis === 'dp') {
            dpWrap.style.display = '';
        } else {
            dpWrap.style.display = 'none';
            dpPercent.value = '';
        }

        // Kalau pelunasan -> isi sisa otomatis (kalau ada sisa)
        if (jenis === 'pelunasan' && sisaTagihan > 0 && inputNom) {
            inputNom.value = formatRupiah(String(sisaTagihan));
        }
    });
}

// ketika pilih persen DP → hitung nominal otomatis
if (dpPercent && inputNom) {
    dpPercent.addEventListener('change', function() {
        const persen = parseInt(this.value || '0', 10);
        if (!persen || !NILAI_KONTRAK) return;
        const nominal = Math.round(NILAI_KONTRAK * (persen / 100));
        inputNom.value = formatRupiah(String(nominal));
    });
}

// tombol "Isi sisa"
if (btnSisa && inputNom) {
    btnSisa.addEventListener('click', function() {
        if (sisaTagihan > 0) {
            inputNom.value = formatRupiah(String(sisaTagihan));
        }
    });
}
</script>

<?= $this->endSection(); ?>