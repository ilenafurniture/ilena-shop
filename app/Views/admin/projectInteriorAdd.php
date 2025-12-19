<!-- app/Views/admin/projectInteriorAdd.php -->
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
    --ok: #16a34a;
    --warn: #b91c1c;
    --info: #2563eb;
}

* {
    box-sizing: border-box;
}

h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    letter-spacing: -.2px;
}

h1.teks-sedang::after {
    content: "";
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(179, 18, 23, .25), transparent);
    border-radius: 999px;
}

.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
    box-shadow: 0 10px 26px rgba(2, 8, 23, .06);
}

.form-control,
.form-select {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    transition: border-color .15s, box-shadow .15s, background .15s;
    background: #fff;
    font-weight: 600;
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
    min-height: 88px;
    resize: vertical;
    font-weight: 600;
}

.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 800;
    padding: .9em 1.1em;
    border-radius: 12px;
    box-shadow: 0 14px 36px rgba(179, 18, 23, .28);
    transition: transform .08s, filter .08s, box-shadow .18s, opacity .2s;
}

.btn-default-merah:hover {
    filter: brightness(.98);
}

.btn-default-merah:active {
    transform: translateY(1px);
    box-shadow: 0 10px 22px rgba(179, 18, 23, .24);
}

.btn-default-merah[disabled] {
    opacity: .55;
    pointer-events: none;
}

.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .65em .95em;
    border-radius: 12px;
    font-weight: 800;
    font-size: 13px;
}

.btn-ghost:hover {
    background: #e5e7eb;
}

.badge-soft {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #111827;
}

.section-title {
    font-weight: 900;
    font-size: 13px;
    color: #0f172a;
    margin-bottom: 4px;
}

.section-subtitle {
    font-size: 11.5px;
    color: #64748b;
    margin-bottom: 10px;
}

.step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    margin-bottom: 10px;
}

.step .num {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-weight: 900;
    font-size: 12px;
}

.step .txt {
    line-height: 1.2;
}

.step .txt b {
    font-weight: 900;
}

.helper {
    position: relative;
    display: block;
    margin: 8px 0 0;
    padding: 8px 12px 8px 36px;
    font-size: 12px;
    line-height: 1.35;
    letter-spacing: -.2px;
    background: #e8f4ff;
    color: #1e4e79;
    border: 1px solid #cfe7ff;
    border-radius: 10px;
}

.helper:before {
    content: "info";
    font-family: "Material Icons";
    font-size: 18px;
    line-height: 1;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: .9;
}

.helper.warn {
    background: #ffe8e8;
    border-color: #ffc9c9;
    color: #8b2a2b;
}

.helper.warn:before {
    content: "priority_high";
}

.helper.ok {
    background: #ecfdf3;
    border-color: #4ade80;
    color: #166534;
}

.helper.ok:before {
    content: "check_circle";
}

.preview-box {
    border: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    border-radius: 14px;
    padding: 12px;
    display: grid;
    gap: 10px;
}

.preview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    background: #fff;
}

.preview-item .k {
    font-size: 11.5px;
    color: #64748b;
}

.preview-item .v {
    font-weight: 900;
    font-variant-numeric: tabular-nums;
    font-size: 12.5px;
}

.preview-item .v.good {
    color: var(--ok);
}

.preview-item .v.bad {
    color: var(--warn);
}

.inline-radio {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 13px;
    padding: 10px 12px;
    border: 1px solid var(--slate-100);
    border-radius: 14px;
    background: #fff;
}

.inline-radio label {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin: 0;
}

.inline-radio input {
    margin-top: 3px;
}

.readonly {
    background: #f9fafb !important;
    border-style: dashed;
}

.notif {
    position: fixed;
    bottom: 50px;
    right: 0;
    padding: .6em 2em;
    color: #e84a49;
    border-radius: 10px;
    font-weight: 700;
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

@media (max-width: 768px) {
    .btn-default-merah {
        width: 100%;
    }
}
</style>

<div style="padding:2em;" class="h-100 d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="teks-sedang mb-0">Buat Project Interior</h1>
        <a href="<?= site_url('admin/project-interior'); ?>" class="btn-ghost">
            <i class="material-icons" style="font-size:16px;vertical-align:-3px;">arrow_back</i>
            Kembali
        </a>
    </div>

    <?php if (!empty($msg)) : ?>
    <div class="notif show" id="notif-msg"><?= esc($msg) ?></div>
    <?php endif; ?>

    <div class="card-soft">
        <div class="card-body p-3 p-md-4">

            <!-- Step guidance -->
            <div class="row g-3 mb-3">
                <div class="col-lg-8">
                    <div class="step">
                        <div class="num">1</div>
                        <div class="txt"><b>Isi Info Utama</b><br><span style="font-size:12px;color:#64748b;">Nama
                                project + data klien (ini yang paling sering dipakai di dokumen).</span></div>
                    </div>
                    <div class="step">
                        <div class="num">2</div>
                        <div class="txt"><b>Isi Barang & Qty</b><br><span style="font-size:12px;color:#64748b;">Nama
                                barang + kode barang + jumlah unit.</span></div>
                    </div>
                    <div class="step">
                        <div class="num">3</div>
                        <div class="txt"><b>Isi Nilai</b><br><span style="font-size:12px;color:#64748b;">Pilih: isi
                                total kontrak <i>atau</i> isi harga satuan (yang lain otomatis).</span></div>
                    </div>
                    <div class="step">
                        <div class="num">4</div>
                        <div class="txt"><b>Pilih Dokumen Utama</b><br><span style="font-size:12px;color:#64748b;">SJ
                                (pakai faktur) atau NF (tanpa faktur).</span></div>
                    </div>

                    <span class="helper">
                        Form ini akan <b>mengklaim nomor SP &amp; SJ/NF</b> (status <code>reserved</code>) di pemesanan
                        offline.
                        Invoice akhir baru bisa dibuat saat project <b>LUNAS</b>.
                    </span>
                </div>

                <div class="col-lg-4">
                    <div class="preview-box">
                        <div style="font-weight:900; display:flex; align-items:center; gap:6px;">
                            <i class="material-icons" style="font-size:18px;color:#64748b;">analytics</i>
                            Ringkasan Otomatis
                        </div>
                        <div class="preview-item">
                            <div class="k">Mode</div>
                            <div class="v" id="pv-mode">KONTRAK</div>
                        </div>
                        <div class="preview-item">
                            <div class="k">Qty</div>
                            <div class="v" id="pv-qty">1</div>
                        </div>
                        <div class="preview-item">
                            <div class="k">DPP Total</div>
                            <div class="v" id="pv-dpp-total">Rp 0</div>
                        </div>
                        <div class="preview-item">
                            <div class="k">PPN 11%</div>
                            <div class="v" id="pv-ppn">Rp 0</div>
                        </div>
                        <div class="preview-item">
                            <div class="k">Grand Total</div>
                            <div class="v" id="pv-grand">Rp 0</div>
                        </div>

                        <span class="helper ok" style="margin:0;">
                            Nilai kontrak = <b>Grand Total</b> (DPP + PPN 11%).<br>
                            Harga satuan = <b>DPP per unit</b> (tanpa PPN).
                        </span>
                    </div>
                </div>
            </div>

            <form id="form-project" action="<?= site_url('admin/project-interior/add'); ?>" method="post">
                <?= csrf_field(); ?>

                <!-- ================= STEP 1: INFO UTAMA ================= -->
                <div class="section-title">1) Info Utama</div>
                <div class="section-subtitle">Isi identitas project dan klien. Ini yang paling sering muncul di dokumen.
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Nama Project <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="nama_project" class="form-control"
                            placeholder="Misal: Renovasi Lobby - KOSQ" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Nama Klien <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="nama_customer" class="form-control"
                            placeholder="Nama yang tampil di SP/SJ" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">No HP Kontak <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="nohp" class="form-control" placeholder="Misal: 0812xxxxxxx" required>
                        <small class="text-muted" style="font-size:11.5px;">Wajib diisi (kolom <code>nohp</code> tidak
                            boleh kosong).</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Nomor PO / SPK
                            (opsional)</label>
                        <input type="text" name="no_po" class="form-control"
                            placeholder="Misal: 018/SPK/CVSIP-CVCBM/XI/2025">
                        <small class="text-muted" style="font-size:11.5px;">Kalau klien kasih nomor PO/SPK, isi di
                            sini.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Alamat Pengiriman
                            (opsional)</label>
                        <textarea name="alamat_pengiriman" id="alamat_pengiriman" class="form-control"
                            placeholder="Alamat lengkap pengiriman / lokasi project"></textarea>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Alamat Tagihan
                                (opsional)</label>
                            <label class="d-flex align-items-center gap-1" style="font-size:11.5px;cursor:pointer;">
                                <input type="checkbox" id="same-billing" style="margin:0;">
                                <span>Samakan</span>
                            </label>
                        </div>
                        <textarea name="alamat_tagihan" id="alamat_tagihan" class="form-control"
                            placeholder="Kosongkan jika sama dengan alamat pengiriman"></textarea>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Nama NPWP
                            (opsional)</label>
                        <input type="text" name="nama_npwp" class="form-control"
                            placeholder="Nama sesuai NPWP (jika ada)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">No NPWP
                            (opsional)</label>
                        <input type="text" name="npwp" class="form-control"
                            placeholder="NPWP untuk penagihan (jika ada)">
                    </div>
                </div>

                <!-- ================= STEP 2: BARANG & QTY ================= -->
                <div class="section-title">2) Barang / Pekerjaan</div>
                <div class="section-subtitle">Isi barang utama yang jadi ringkasan dokumen (boleh generik).</div>

                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Nama Barang / Produk
                            <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="nama_barang" class="form-control"
                            placeholder="Misal: Furniture Interior Lokal" required>
                        <small class="text-muted" style="font-size:11.5px;">Tampil di kolom keterangan barang di
                            SJ/Invoice.</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Kode Barang <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="kode_barang" class="form-control" placeholder="Misal: CI001122025"
                            required>
                        <small class="text-muted" style="font-size:11.5px;">Kode unik yang konsisten di dokumen.</small>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Jumlah (Qty)</label>
                        <input type="number" name="qty" id="qty" class="form-control" value="1" min="1">
                        <small class="text-muted" style="font-size:11.5px;">Dipakai untuk hitung otomatis.</small>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Rencana DP
                            (opsional)</label>
                        <input type="text" name="dp" id="dp" class="form-control rupiah"
                            placeholder="Misal: 50.000.000">
                        <small class="text-muted" style="font-size:11.5px;">Hanya catatan awal (bukan
                            transaksi).</small>
                    </div>
                </div>

                <!-- ================= STEP 3: NILAI ================= -->
                <div class="section-title">3) Nilai & Perhitungan</div>
                <div class="section-subtitle">Pilih salah satu: isi total kontrak <b>atau</b> isi harga satuan (DPP).
                    Yang lain otomatis.</div>

                <div class="row g-3 mb-2">
                    <div class="col-md-5">
                        <div class="inline-radio">
                            <label>
                                <input type="radio" name="mode_nilai" value="kontrak" checked>
                                <span><b>Isi Nilai Kontrak (Grand Total)</b><br><span
                                        style="font-size:12px;color:#64748b;">Sudah termasuk PPN 11%.</span></span>
                            </label>
                            <label>
                                <input type="radio" name="mode_nilai" value="satuan">
                                <span><b>Isi Harga Satuan (DPP)</b><br><span style="font-size:12px;color:#64748b;">Per
                                        unit, belum termasuk PPN.</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Nilai Kontrak (Rp) <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="nilai_kontrak" id="nilai_kontrak" class="form-control rupiah"
                            placeholder="Contoh: 150.000.000">
                        <small class="text-muted" style="font-size:11.5px;">Grand total (DPP + PPN 11%).</small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:800;">Harga Satuan (Rp) <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="harga_satuan" id="harga_satuan" class="form-control rupiah"
                            placeholder="Misal: 100.000.000">
                        <small class="text-muted" style="font-size:11.5px;">DPP per unit (tanpa PPN).</small>
                    </div>
                </div>

                <span class="helper warn" id="warn-negative" style="display:none;">
                    Nilai tidak valid (qty harus ≥ 1 dan angka tidak boleh negatif). Jangan bikin project dari angka
                    “khayalan”, nanti invoice-nya ikut halu.
                </span>

                <!-- ================= STEP 4: DOKUMEN UTAMA ================= -->
                <div class="section-title mt-4">4) Dokumen Utama</div>
                <div class="section-subtitle">Pilih jenis dokumen utama yang akan di-reserve saat project dibuat.</div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="inline-radio">
                            <label>
                                <input type="radio" name="jenis_faktur" value="sale" checked>
                                <span><b>SJ (Faktur)</b><br><span style="font-size:12px;color:#64748b;">Dipakai kalau
                                        transaksi pakai faktur pajak.</span></span>
                            </label>
                            <label>
                                <input type="radio" name="jenis_faktur" value="nf">
                                <span><b>NF (Non Faktur)</b><br><span style="font-size:12px;color:#64748b;">Dipakai
                                        kalau tanpa faktur pajak.</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="helper" style="margin-top:0;">
                            Setelah disimpan: sistem langsung <b>klaim nomor SP &amp; SJ/NF</b> agar tidak dipakai order
                            lain.
                            Surat Jalan pengiriman bisa banyak (parsial). Invoice akhir dibuat saat <b>LUNAS</b>.
                        </div>
                    </div>
                </div>

                <!-- ================= CATATAN INTERNAL ================= -->
                <div class="section-title">Catatan Internal (opsional)</div>
                <div class="section-subtitle">Tidak muncul di invoice, hanya untuk catatan admin.</div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <textarea name="catatan" class="form-control"
                            placeholder="Catatan internal project..."></textarea>
                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="badge-soft">
                        <i class="material-icons" style="font-size:16px;">info</i>
                        SP &amp; SJ/NF langsung di-reserve. Tidak bisa dipakai order lain.
                    </span>
                    <button type="submit" class="btn-default-merah" id="btn-submit">
                        Simpan Project &amp; Klaim SP/SJ atau NF
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
/* =================== Rupiah helpers =================== */
function stripNonDigit(v) {
    return (v || '').toString().replace(/[^\d]/g, '');
}

function formatRupiah(val) {
    const raw = stripNonDigit(val);
    if (!raw) return '';
    return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function toInt(v) {
    const raw = stripNonDigit(v);
    return raw ? parseInt(raw, 10) : 0;
}

function formatIDR(n) {
    const x = Number(n || 0);
    return 'Rp ' + x.toLocaleString('id-ID');
}

/* format saat ketik */
document.querySelectorAll('.rupiah').forEach((el) => {
    el.addEventListener('input', function() {
        this.value = formatRupiah(this.value);
    });
});

/* =================== Mode nilai =================== */
const inputNilaiKontrak = document.getElementById('nilai_kontrak');
const inputHargaSatuan = document.getElementById('harga_satuan');
const inputQty = document.getElementById('qty');
const modeRadios = document.querySelectorAll('input[name="mode_nilai"]');

const pvMode = document.getElementById('pv-mode');
const pvQty = document.getElementById('pv-qty');
const pvDppT = document.getElementById('pv-dpp-total');
const pvPpn = document.getElementById('pv-ppn');
const pvGrand = document.getElementById('pv-grand');

const warnNegative = document.getElementById('warn-negative');
const btnSubmit = document.getElementById('btn-submit');

function getCurrentMode() {
    return (document.querySelector('input[name="mode_nilai"]:checked')?.value) || 'kontrak';
}

/* ===== Aturan hitung =====
 - Nilai Kontrak = Grand Total (DPP total + PPN 11%)
 - Harga Satuan = DPP per unit
*/
function computeFromGrand(grandIncl, qty) {
    if (grandIncl <= 0 || qty <= 0) return {
        dppTotal: 0,
        ppn: 0,
        grand: 0,
        perUnit: 0
    };
    const dppTotal = Math.ceil(grandIncl / 1.11);
    const ppn = Math.max(0, grandIncl - dppTotal);
    const perUnit = Math.round(dppTotal / qty);
    return {
        dppTotal,
        ppn,
        grand: grandIncl,
        perUnit
    };
}

function computeFromUnit(dppPerUnit, qty) {
    if (dppPerUnit <= 0 || qty <= 0) return {
        dppTotal: 0,
        ppn: 0,
        grand: 0,
        perUnit: 0
    };
    const dppTotal = dppPerUnit * qty;
    const grand = Math.ceil(dppTotal * 1.11);
    const ppn = Math.max(0, grand - dppTotal);
    return {
        dppTotal,
        ppn,
        grand,
        perUnit: dppPerUnit
    };
}

/* disable + style biar jelas mana yang diisi */
function setFieldMode() {
    const mode = getCurrentMode();

    if (mode === 'kontrak') {
        if (inputNilaiKontrak) {
            inputNilaiKontrak.disabled = false;
            inputNilaiKontrak.readOnly = false;
            inputNilaiKontrak.classList.remove('readonly');
        }
        if (inputHargaSatuan) {
            inputHargaSatuan.disabled = true;
            inputHargaSatuan.readOnly = true;
            inputHargaSatuan.classList.add('readonly');
            inputHargaSatuan.value = inputHargaSatuan.value; // keep
        }
    } else {
        if (inputNilaiKontrak) {
            inputNilaiKontrak.disabled = true;
            inputNilaiKontrak.readOnly = true;
            inputNilaiKontrak.classList.add('readonly');
        }
        if (inputHargaSatuan) {
            inputHargaSatuan.disabled = false;
            inputHargaSatuan.readOnly = false;
            inputHargaSatuan.classList.remove('readonly');
        }
    }

    if (pvMode) pvMode.textContent = mode.toUpperCase();
}

function syncFromNilaiKontrak() {
    if (!inputNilaiKontrak || !inputHargaSatuan) return;
    const grand = toInt(inputNilaiKontrak.value);
    let qty = parseInt(inputQty?.value || '1', 10);
    if (!qty || qty <= 0) qty = 1;

    if (grand <= 0) {
        inputHargaSatuan.value = '';
        return;
    }
    const r = computeFromGrand(grand, qty);
    inputHargaSatuan.value = formatRupiah(String(r.perUnit));
}

function syncFromHargaSatuan() {
    if (!inputNilaiKontrak || !inputHargaSatuan) return;
    const unit = toInt(inputHargaSatuan.value);
    let qty = parseInt(inputQty?.value || '1', 10);
    if (!qty || qty <= 0) qty = 1;

    if (unit <= 0) {
        inputNilaiKontrak.value = '';
        return;
    }
    const r = computeFromUnit(unit, qty);
    inputNilaiKontrak.value = formatRupiah(String(r.grand));
}

function updatePreview() {
    const mode = getCurrentMode();
    let qty = parseInt(inputQty?.value || '1', 10);
    if (!qty || qty <= 0) qty = 0;

    const grand = toInt(inputNilaiKontrak?.value || '');
    const unit = toInt(inputHargaSatuan?.value || '');

    let r = {
        dppTotal: 0,
        ppn: 0,
        grand: 0,
        perUnit: 0
    };
    if (mode === 'kontrak') r = computeFromGrand(grand, qty || 1);
    else r = computeFromUnit(unit, qty || 1);

    if (pvQty) pvQty.textContent = String(qty || 0);
    if (pvDppT) pvDppT.textContent = formatIDR(r.dppTotal);
    if (pvPpn) pvPpn.textContent = formatIDR(r.ppn);
    if (pvGrand) pvGrand.textContent = formatIDR(r.grand);

    const invalid = (qty <= 0) || (mode === 'kontrak' ? grand < 0 : unit < 0);
    if (warnNegative) warnNegative.style.display = invalid ? '' : 'none';
    if (btnSubmit) btnSubmit.disabled = invalid;
}

function recompute() {
    const mode = getCurrentMode();
    if (mode === 'kontrak') syncFromNilaiKontrak();
    else syncFromHargaSatuan();
    updatePreview();
}

modeRadios.forEach(r => r.addEventListener('change', () => {
    setFieldMode();
    recompute();
}));

if (inputNilaiKontrak) inputNilaiKontrak.addEventListener('input', () => {
    if (getCurrentMode() === 'kontrak') syncFromNilaiKontrak();
    updatePreview();
});
if (inputHargaSatuan) inputHargaSatuan.addEventListener('input', () => {
    if (getCurrentMode() === 'satuan') syncFromHargaSatuan();
    updatePreview();
});
if (inputQty) inputQty.addEventListener('input', recompute);

/* init */
setFieldMode();
recompute();

/* =================== Same billing =================== */
const alamatPengiriman = document.getElementById('alamat_pengiriman');
const alamatTagihan = document.getElementById('alamat_tagihan');
const sameBilling = document.getElementById('same-billing');

if (alamatPengiriman && alamatTagihan && sameBilling) {
    const syncBilling = () => {
        if (sameBilling.checked) alamatTagihan.value = alamatPengiriman.value || '';
    };

    sameBilling.addEventListener('change', function() {
        if (this.checked) {
            syncBilling();
            alamatTagihan.readOnly = true;
            alamatTagihan.classList.add('readonly');
        } else {
            alamatTagihan.readOnly = false;
            alamatTagihan.classList.remove('readonly');
        }
    });

    alamatPengiriman.addEventListener('input', syncBilling);
}

/* =================== Sanitize before submit ===================
   Pastikan backend dapat angka murni (tanpa titik).
*/
const form = document.getElementById('form-project');
if (form) {
    form.addEventListener('submit', function() {
        ['nilai_kontrak', 'dp', 'harga_satuan'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.value = stripNonDigit(el.value);
        });

        if (inputQty) {
            const q = parseInt(inputQty.value || '1', 10);
            if (!q || q <= 0) inputQty.value = '1';
        }
    });
}

/* auto hide notif */
const notif = document.getElementById('notif-msg');
if (notif) setTimeout(() => notif.classList.remove('show'), 3500);
</script>

<?= $this->endSection(); ?>