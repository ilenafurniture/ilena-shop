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
    --ring: #fde2e2;
}

* {
    box-sizing: border-box
}

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

/* form & buttons */
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

.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 700;
    letter-spacing: .1px;
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

<div style="padding: 2em;" class="h-100 d-flex flex-column">
    <h1 class="teks-sedang mb-4">
        Buat Project Interior
    </h1>

    <?php if (!empty($msg)) : ?>
    <div class="notif show" id="notif-msg">
        <?= esc($msg) ?>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm" style="border-radius:16px;border:1px solid var(--slate-100);">
        <div class="card-body">
            <p class="mb-3" style="font-size:13px;color:#475569;">
                Form ini akan:
                <br>- Membuat <b>Project Interior</b>
                <br>- Mengklaim <b>nomor SP &amp; SJ / NF</b> di pemesanan offline (status <code>reserved</code>)
                <br>- Belum membuat invoice, sampai nanti status project <b>lunas</b>.
                <br>- <b>Catatan penting:</b> Nilai kontrak adalah <b>total tagihan (grand total)
                    yang sudah termasuk PPN 11%</b>. Harga satuan adalah <b>DPP (belum PPN)</b>.
            </p>

            <form action="<?= site_url('admin/project-interior/add'); ?>" method="post" class="mt-3">
                <?= csrf_field(); ?>

                <!-- ===== Mode Pengisian Nilai ===== -->
                <div class="row g-3 mb-2">
                    <div class="col-12">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">
                            Mode pengisian nilai
                        </label>
                        <div class="d-flex flex-column gap-1" style="font-size:13px;">
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="mode_nilai" value="kontrak" checked style="margin:0;">
                                <span>
                                    <b>Isi Nilai Kontrak</b> (grand total sudah termasuk PPN 11%).<br>
                                    Sistem akan otomatis menghitung <b>Harga Satuan (DPP)</b>.
                                </span>
                            </label>
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="mode_nilai" value="satuan" style="margin:0;">
                                <span>
                                    <b>Isi Harga Satuan (DPP)</b> per unit.<br>
                                    Sistem akan otomatis menghitung <b>Nilai Kontrak (total + PPN 11%)</b>.
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ===== Baris Project & Kontrak ===== -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Nama Project</label>
                        <input type="text" name="nama_project" class="form-control"
                            placeholder="Misal: Interior Kantor PT Maju Jaya" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">
                            Nilai Kontrak (Rp, sudah termasuk PPN 11%)
                        </label>
                        <input type="text" name="nilai_kontrak" id="nilai_kontrak" class="form-control rupiah"
                            placeholder="Contoh: 150.000.000">
                        <small class="text-muted" style="font-size:11.5px;">
                            <b>Grand total yang ditagihkan ke klien (DPP + PPN 11%)</b>.<br>
                            Jika mode ini aktif, kolom <b>Harga Satuan</b> akan dihitung otomatis.
                        </small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Rencana DP
                            (opsional)</label>
                        <input type="text" name="dp" class="form-control rupiah" placeholder="Misal: 50.000.000">
                        <small class="text-muted" style="font-size:11.5px;">
                            Hanya info awal, DP/termin sebenarnya diinput di menu pembayaran project.
                        </small>
                    </div>
                </div>

                <!-- ===== Detail Produk yang Ditawarkan ===== -->
                <h6 class="mb-2" style="font-weight:700;font-size:13px;color:#0f172a;">Detail Produk / Pekerjaan</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Nama Barang /
                            Produk</label>
                        <input type="text" name="nama_barang" class="form-control"
                            placeholder="Misal: Kitchen Set Full + Wardrobe" required>
                        <small class="text-muted" style="font-size:11.5px;">
                            Ini yang akan tampil di kolom <b>Nama Barang</b> di Surat Jalan / Invoice.
                        </small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">
                            Harga Satuan Barang (Rp, <u>belum termasuk PPN 11%</u>)
                        </label>
                        <input type="text" name="harga_satuan" id="harga_satuan" class="form-control rupiah"
                            placeholder="Misal: 100.000.000">
                        <small class="text-muted" style="font-size:11.5px;">
                            <b>Harga dasar (DPP) per unit</b>, tanpa PPN.<br>
                            Jika mode ini aktif, kolom <b>Nilai Kontrak</b> akan dihitung otomatis.
                        </small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Jumlah</label>
                        <input type="number" name="qty" id="qty" class="form-control" value="1" min="1">
                        <small class="text-muted" style="font-size:11.5px;">
                            Dipakai dalam perhitungan otomatis Nilai Kontrak / Harga Satuan.
                        </small>
                    </div>
                </div>

                <!-- ===== Jenis Dokumen Utama (SJ vs NF) ===== -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Jenis Dokumen
                            Utama</label>
                        <div class="d-flex flex-column gap-1" style="font-size:13px;">
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="jenis_faktur" value="sale" checked style="margin:0;">
                                <span>
                                    <b>SJ (Faktur Pajak)</b> &mdash; dokumen utama menggunakan SJ, dengan faktur pajak.
                                </span>
                            </label>
                            <label class="d-flex align-items-center gap-2">
                                <input type="radio" name="jenis_faktur" value="nf" style="margin:0;">
                                <span>
                                    <b>NF (Non Faktur)</b> &mdash; dokumen utama Non Faktur (tanpa faktur pajak).
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- ===== Data Klien (mengikuti sistem lama) ===== -->
                <h6 class="mb-2" style="font-weight:700;font-size:13px;color:#0f172a;">Data Klien</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Nama Klien</label>
                        <input type="text" name="nama_customer" class="form-control"
                            placeholder="Nama yang tampil di SP/SJ" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">No HP Kontak</label>
                        <input type="text" name="nohp" class="form-control" placeholder="Misal: 0812xxxxxxx" required>
                        <small class="text-muted" style="font-size:11.5px;">
                            Wajib diisi (kolom <code>nohp</code> tidak boleh kosong).
                        </small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Nama NPWP
                            (opsional)</label>
                        <input type="text" name="nama_npwp" class="form-control"
                            placeholder="Nama sesuai NPWP (jika ada)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">No NPWP
                            (opsional)</label>
                        <input type="text" name="npwp" class="form-control"
                            placeholder="NPWP untuk penagihan (jika ada)">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Alamat Pengiriman</label>
                        <textarea name="alamat_pengiriman" id="alamat_pengiriman" class="form-control"
                            placeholder="Alamat lengkap pengiriman / lokasi project"></textarea>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="form-label mb-1" style="font-size:13px;font-weight:600;">Alamat Tagihan
                                (opsional)</label>
                            <label class="d-flex align-items-center gap-1" style="font-size:11.5px;cursor:pointer;">
                                <input type="checkbox" id="same-billing" style="margin:0;">
                                <span>Samakan dengan pengiriman</span>
                            </label>
                        </div>
                        <textarea name="alamat_tagihan" id="alamat_tagihan" class="form-control"
                            placeholder="Kosongkan jika sama dengan alamat pengiriman"></textarea>
                    </div>
                </div>

                <!-- ===== Catatan Internal ===== -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label mb-1" style="font-size:13px;font-weight:600;">
                            Catatan Internal Project (opsional)
                        </label>
                        <textarea name="catatan" class="form-control"
                            placeholder="Catatan internal project, tidak muncul di invoice."></textarea>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge-soft">
                            <i class="material-icons" style="font-size:16px;">info</i>
                            SP &amp; SJ / NF akan langsung direservasi dan tidak bisa dipakai order lain.
                        </span>
                    </div>
                    <button type="submit" class="btn-default-merah">
                        Simpan Project &amp; Klaim SP/SJ atau NF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// format angka >> rupiah saat ketik
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

// helper: ambil integer dari input rupiah
function getIntFromRupiah(el) {
    if (!el) return 0;
    const raw = (el.value || '').replace(/[^\d]/g, '');
    return raw ? parseInt(raw, 10) : 0;
}

// ====== MODE NILAI KONTRAK vs HARGA SATUAN ======
const inputNilaiKontrak = document.getElementById('nilai_kontrak');
const inputHargaSatuan = document.getElementById('harga_satuan');
const inputQty = document.getElementById('qty');
const modeRadios = document.querySelectorAll('input[name="mode_nilai"]');

function setReadonlyState() {
    const mode = document.querySelector('input[name="mode_nilai"]:checked')?.value || 'kontrak';

    if (mode === 'kontrak') {
        // User input Nilai Kontrak, Harga Satuan auto
        inputNilaiKontrak.readOnly = false;
        inputNilaiKontrak.style.backgroundColor = '#ffffff';

        inputHargaSatuan.readOnly = true;
        inputHargaSatuan.style.backgroundColor = '#f9fafb';
    } else {
        // User input Harga Satuan, Nilai Kontrak auto
        inputNilaiKontrak.readOnly = true;
        inputNilaiKontrak.style.backgroundColor = '#f9fafb';

        inputHargaSatuan.readOnly = false;
        inputHargaSatuan.style.backgroundColor = '#ffffff';
    }
}

// hitung harga_satuan (DPP/unit) dari nilai_kontrak (total incl PPN)
function syncFromNilaiKontrak() {
    const total = getIntFromRupiah(inputNilaiKontrak);
    let qty = parseInt(inputQty.value || '1', 10);
    if (!qty || qty <= 0) qty = 1;

    if (total <= 0) {
        inputHargaSatuan.value = '';
        return;
    }

    // DPP total ≈ total / 1.11
    const dppTotal = Math.round(total / 1.11);
    const hargaPerUnit = Math.round(dppTotal / qty);

    inputHargaSatuan.value = formatRupiah(String(hargaPerUnit));
}

// hitung nilai_kontrak (total incl PPN) dari harga_satuan (DPP/unit)
function syncFromHargaSatuan() {
    const dppPerUnit = getIntFromRupiah(inputHargaSatuan);
    let qty = parseInt(inputQty.value || '1', 10);
    if (!qty || qty <= 0) qty = 1;

    if (dppPerUnit <= 0) {
        inputNilaiKontrak.value = '';
        return;
    }

    const total = Math.round(dppPerUnit * qty * 1.11); // DPP * qty * 1.11
    inputNilaiKontrak.value = formatRupiah(String(total));
}

// listener untuk ganti mode
modeRadios.forEach(function(r) {
    r.addEventListener('change', function() {
        setReadonlyState();
        // setiap ganti mode, hitung ulang biar konsisten
        const mode = this.value;
        if (mode === 'kontrak') {
            syncFromNilaiKontrak();
        } else {
            syncFromHargaSatuan();
        }
    });
});

// listener perubahan nilai_kontrak & qty saat mode "kontrak"
if (inputNilaiKontrak) {
    inputNilaiKontrak.addEventListener('input', function() {
        const mode = document.querySelector('input[name="mode_nilai"]:checked')?.value || 'kontrak';
        if (mode === 'kontrak') {
            syncFromNilaiKontrak();
        }
    });
}

// listener perubahan harga_satuan & qty saat mode "satuan"
if (inputHargaSatuan) {
    inputHargaSatuan.addEventListener('input', function() {
        const mode = document.querySelector('input[name="mode_nilai"]:checked')?.value || 'kontrak';
        if (mode === 'satuan') {
            syncFromHargaSatuan();
        }
    });
}

if (inputQty) {
    inputQty.addEventListener('input', function() {
        const mode = document.querySelector('input[name="mode_nilai"]:checked')?.value || 'kontrak';
        if (mode === 'kontrak') {
            syncFromNilaiKontrak();
        } else {
            syncFromHargaSatuan();
        }
    });
}

// inisialisasi awal
setReadonlyState();

// Samakan alamat tagihan dengan alamat pengiriman
const alamatPengiriman = document.getElementById('alamat_pengiriman');
const alamatTagihan = document.getElementById('alamat_tagihan');
const sameBilling = document.getElementById('same-billing');

if (alamatPengiriman && alamatTagihan && sameBilling) {
    const syncBilling = () => {
        if (sameBilling.checked) {
            alamatTagihan.value = alamatPengiriman.value || '';
        }
    };

    sameBilling.addEventListener('change', function() {
        if (this.checked) {
            // copy value & lock textarea
            syncBilling();
            alamatTagihan.readOnly = true;
            alamatTagihan.style.backgroundColor = '#f9fafb';
        } else {
            // unlock textarea
            alamatTagihan.readOnly = false;
            alamatTagihan.style.backgroundColor = '#ffffff';
        }
    });

    // kalau alamat pengiriman diubah dan checkbox aktif, ikut update
    alamatPengiriman.addEventListener('input', function() {
        syncBilling();
    });
}

// auto hide notif flashdata
const notif = document.getElementById('notif-msg');
if (notif) {
    setTimeout(() => {
        notif.classList.remove('show');
    }, 3500);
}
</script>

<?= $this->endSection(); ?>