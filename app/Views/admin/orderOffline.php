<!-- View/Admin/OrderOffline -->

<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<?php
// ===== Helper kecil untuk hitung JATUH TEMPO dari tanggal SJ =====
// JT = tanggal SJ + 14 hari; kembalikan [tglJT(Format d M Y), sisaHari(int bisa negatif), status('ok'|'warning'|'overdue')]
$jtInfo = function ($sjDateStr) {
    if (empty($sjDateStr)) return [null, null, null];
    try {
        $tz = new DateTimeZone('Asia/Jakarta');
        $sj = new DateTime($sjDateStr, $tz);
        $jt = clone $sj;
        $jt->modify('+14 days');

        $now = new DateTime('now', $tz);
        $diffDays = (int)$now->diff($jt)->format('%r%a'); // negatif jika lewat

        $status = $diffDays < 0 ? 'overdue' : ($diffDays <= 3 ? 'warning' : 'ok');
        return [$jt->format('d M Y'), $diffDays, $status];
    } catch (\Throwable $e) {
        return [null, null, null];
    }
};
?>

<!-- ===== Modern Look & Feel (tanpa ubah sistem) ===== -->
<style>
:root {
    --ink: #0f172a;
    --muted: #6b7280;
    --line: #e5e7eb;
    --line2: #f3f4f6;
    --brand: #b31217;
    --brand-600: #a50e12;
    --surface: #ffffff;
    --surface-2: #fafafa;
    --shadow: 0 10px 30px rgba(2, 6, 23, .06), 0 2px 6px rgba(2, 6, 23, .04);
}

html,
body {
    color: var(--ink);
    background: #f7f7fb
}

* {
    font-size: 13px;
    line-height: 1.42
}

h1,
h2,
h3,
h4,
h5 {
    letter-spacing: -.2px
}

/* ====== Page chrome ====== */
.page-card {
    background: var(--surface);
    border: 1px solid var(--line2);
    border-radius: 16px;
    box-shadow: var(--shadow);
    padding: 1.2rem 1.4rem;
}

.page-head {
    display: flex;
    gap: .8rem;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: .5rem;
}

.page-title {
    display: flex;
    align-items: center;
    gap: .8rem;
}

.page-title h1 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
}

.page-sub {
    margin: .15rem 0 0;
    color: var(--muted);
    font-size: 12px
}

/* ====== Toolbar atas (jenis + tambah) ====== */
.toolbar {
    display: flex;
    gap: .6rem;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.toolbar .form-select {
    min-width: 200px;
    border-radius: 10px;
    border: 1px solid var(--line);
    padding: .5rem .9rem;
    background: #fff;
    box-shadow: inset 0 1px 0 rgba(2, 6, 23, .02);
}

/* ====== Filter bar (search di bawah judul) ====== */
.page-filters {
    margin-top: .5rem;
    margin-bottom: 1rem;
}

.page-filters-inner {
    display: flex;
    justify-content: flex-start;
}

.toolbar-search {
    position: relative;
    width: min(380px, 100%);
}

.toolbar-search .material-icons {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: var(--muted);
    pointer-events: none;
}

.toolbar-search .search-input {
    width: 100%;
    border-radius: 999px;
    border: 1px solid var(--line);
    padding: .45rem .9rem .45rem 2.1rem;
    box-shadow: inset 0 1px 0 rgba(2, 6, 23, .02);
    font-size: 13px;
    background: #fff;
}

/* ====== Buttons ====== */
.btn-default-merah {
    --tw: var(--brand);
    background: linear-gradient(180deg, var(--tw), var(--brand-600));
    color: #fff !important;
    border: 0;
    border-radius: 12px;
    padding: .7rem 1rem;
    font-weight: 700;
    box-shadow: 0 8px 24px rgba(179, 18, 23, .22);
    transition: .18s ease;
}

.btn-default-merah:hover {
    filter: brightness(.97);
    transform: translateY(-1px)
}

.btn-default {
    background: #fff;
    border: 1px solid var(--line);
    padding: .7rem 1rem;
    border-radius: 12px;
    font-weight: 600;
}

.btn-teks-aja {
    color: var(--brand);
    text-decoration: none;
    font-weight: 600
}

.btn-teks-aja:hover {
    text-decoration: underline
}

/* Icon-only buttons (ACTION kolom) */
.table .btn {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    border: 1px solid var(--line);
    background: #fff;
    transition: .16s ease;
}

.table .btn:hover {
    box-shadow: 0 8px 20px rgba(2, 6, 23, .08);
    transform: translateY(-1px)
}

/* ====== Table modern ====== */
.table-wrap {
    border: 1px solid var(--line2);
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
    box-shadow: var(--shadow)
}

.table {
    margin: 0;
}

.table thead th {
    background: linear-gradient(#f8fafc, #f4f6fa) !important;
    font-size: 11px;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #0f172a;
    border-bottom: 1px solid var(--line);
    padding: .65rem .8rem;
}

.table tbody td,
.table tbody th {
    vertical-align: middle;
    border-color: var(--line2);
    padding: .7rem .8rem;
    background: #fff;
}

.table tbody tr:hover td {
    background: #fcfcff
}

.table-striped>tbody>tr:nth-of-type(odd)>* {
    --bs-table-accent-bg: #fafbff;
}

/* Status badge */
.badge.rounded-pill {
    font-weight: 700;
    letter-spacing: .02em;
    padding: .45rem .7rem;
    border: 1px solid rgba(15, 23, 42, .06)
}

/* Keterangan kecil */
.text-muted-12 {
    color: var(--muted);
    font-size: 12px
}

/* JT Pills */
.badge-jt {
    border-radius: 999px;
    padding: .25rem .6rem;
    font-size: 12px;
    display: inline-block;
    border: 1px solid;
}

.badge-jt.ok {
    background: #ecfdf5;
    color: #065f46;
    border-color: #a7f3d0
}

.badge-jt.warning {
    background: #fff7ed;
    color: #9a3412;
    border-color: #fed7aa
}

.badge-jt.overdue {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fecaca
}

/* Tooltips trigger */
[data-bs-toggle="tooltip"] {
    color: #64748b
}

/* ====== Modal sheet (tanpa ganti id/struktur) ====== */
#input-buat-invoice,
#input-buat-dp,
#input-koreksi,
#input-edit-order {
    backdrop-filter: blur(4px);
}

.modal-card-modern {
    width: min(960px, 88vw) !important;
    max-height: 90vh !important;
    overflow: hidden !important;
    border-radius: 16px !important;
    border: 1px solid var(--line2) !important;
    box-shadow: 0 24px 72px rgba(2, 6, 23, .20) !important;
    background: #fff !important;
}

.modal-card-modern form {
    overflow: auto;
    max-height: calc(90vh - 2rem)
}

.modal-card-modern h5 {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.2px
}

.modal-card-modern .form-control,
.modal-card-modern .form-select,
.modal-card-modern textarea {
    border-radius: 12px;
    border: 1px solid var(--line);
}

.modal-card-modern .btn-default,
.modal-card-modern .btn-default-merah {
    border-radius: 12px
}

/* Divider halus */
hr {
    border-color: var(--line2)
}

/* ==== Small helpers ==== */
.mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Roboto Mono", "Liberation Mono", "Courier New", monospace
}
</style>

<!-- MODAL: BUAT INVOICE -->
<div id="input-buat-invoice" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.45)">
    <div class="bg-white p-4 rounded modal-card-modern">
        <form method="post" action="/admin/actionbuatinvoice">
            <h5 class="m-0 fw-bold">Buat Invoice</h5>
            <p class="mb-3 text-sm" style="color: var(--brand); font-size: 12px">
                ID Order :
                <input type="text" name="id_pesanan" style="border: none; color: var(--brand); pointer-events: none;"
                    class="fw-bold">
            </p>
            <div class="mb-2">
                <p class="mb-1">Tanggal</p>
                <input type="datetime-local" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-2">
                <p class="mb-1">Alamat</p>
                <textarea name="alamat" id="input-alamat-invoice" required class="form-control" rows="3"></textarea>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-12 col-md-6">
                    <p class="mb-1">Nama</p>
                    <input type="text" name="nama_npwp" placeholder="Nama sesuai di NPWP" class="form-control" required>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1">NPWP</p>
                    <input type="text" name="npwp" class="form-control" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Tutup</button>
                <button type="submit" class="btn btn-default-merah w-100">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: INPUT DP -->
<div id="input-buat-dp" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.45)">
    <div class="bg-white p-4 rounded modal-card-modern">
        <form method="post" action="/admin/actionbuatdp">
            <h5 class="m-0 fw-bold">Buat Invoice DP</h5>
            <p class="mb-3 text-sm" style="color: var(--brand); font-size: 12px">ID Order :
                <input type="text" name="id_pesanan" style="border: none; color: var(--brand); pointer-events: none;"
                    class="fw-bold">
            </p>
            <div class="d-flex flex-column flex-md-row gap-3 mb-3">
                <div class="d-flex flex-column gap-2" style="flex:1">
                    <div>
                        <p class="mb-1">Nama</p>
                        <input type="text" name="nama_npwp" placeholder="Nama sesuai di NPWP" class="form-control">
                    </div>
                    <div>
                        <p class="mb-1">NPWP</p>
                        <input type="text" name="npwp" class="form-control"
                            placeholder="kosongin kalau Invoice menyusul">
                    </div>
                </div>
                <div class="d-flex flex-column gap-2" style="flex:1">
                    <div>
                        <p class="mb-1">Tanggal</p>
                        <input type="datetime-local" name="tanggal" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="mb-3 table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th class="text-center">Jumlah Barang</th>
                            <th class="text-end">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="table-dp"></tbody>
                </table>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Tutup</button>
                <button type="submit" class="btn btn-default-merah w-100">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: KOREKSI SP -->
<div id="input-koreksi" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.45)">
    <div class="bg-white p-4 rounded modal-card-modern">
        <form method="post" action="/admin/order-offline/koreksisp">
            <h5 class="m-0 fw-bold">Koreksi Surat Pengantar</h5>
            <p class="m-0 text-sm" style="color: var(--brand); font-size: 12px">ID Order :
                <input type="text" name="id_pesanan" style="border: none; color: var(--brand); pointer-events: none;"
                    class="fw-bold">
            </p>
            <hr>
            <div class="mb-3">
                <p class="mb-1">Tanggal</p>
                <input type="datetime-local" name="tanggal" class="form-control" required>
            </div>
            <p class="mb-1 fw-bold">Alamat Tagihan</p>
            <div class="ps-3 alamat-taghihan" style="border-left: 1px solid var(--line);">
                <div class="d-flex flex-column flex-md-row gap-2 mb-1">
                    <select name="provinsi" class="form-select">
                        <option value="">-- Pilih provinsi --</option>
                        <?php foreach ($provinsi as $p) { ?>
                        <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                        <?php } ?>
                    </select>
                    <select name="kota" class="form-select">
                        <option value="">-- Pilih kabupaten --</option>
                    </select>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2 mb-1">
                    <select name="kecamatan" class="form-select">
                        <option value="">-- Pilih kecamatan --</option>
                    </select>
                    <select name="kodepos" class="form-select">
                        <option value="">-- Pilih Kelurahan --</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mb-1">
                    <input type="text" placeholder="Detail" class="form-control" name="detail">
                </div>
            </div>
            <div class="ps-3 d-none alamat-taghihan" style="border-left: 1px solid var(--line);">
                <textarea name="alamatTagihan" class="info-pesanan form-control" rows="3"></textarea>
            </div>
            <label class="d-flex gap-2 align-items-center mb-3">
                <input name="checkAlamat" type="checkbox" onchange="handleChangeAlamatTagihan(event)">
                <p class="m-0">Sama dengan alamat pengiriman</p>
            </label>

            <div class="mb-3">
                <p class="mb-1">Pilih barang</p>
                <div id="container-items" class="d-flex flex-column gap-2">
                    <label class="d-flex gap-3 align-items-center justify-content-between">
                        <div class="d-flex gap-3 align-items-center">
                            <input type="checkbox" onchange="handleChangeInputItem(0, event)">
                            <div>
                                <p class="fw-bold m-0">Coffe table (HITAM)</p>
                                <p class="text-secondary text-sm m-0">1023142</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <a href="/admin/surat-koreksi/" class="btn-teks-aja">Lihat koreksi</a>
                            <p class="text-secondary" style="font-size: 12px;">SK0000001</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <p class="mb-1">Koreksi ini akan dibuat menjadi</p>
                <div class="d-flex flex-wrap gap-3">
                    <label class="d-flex align-items-center gap-2">
                        <input type="radio" name="convert_to" value="sale" checked>
                        <span>Surat Jalan (SALE)</span>
                    </label>
                    <label class="d-flex align-items-center gap-2">
                        <input type="radio" name="convert_to" value="nf">
                        <span>Non Faktur (NF)</span>
                    </label>
                </div>
            </div>

            <input type="hidden" name="diskon" value="0">
            <input type="text" name="index_items_selected" required class="d-none">

            <div class="d-flex flex-column flex-md-row gap-2 w-100">
                <div class="mb-1" style="flex: 1;">
                    <p class="mb-1">Nama NPWP</p>
                    <small class="text-danger d-block mb-1">*ini adalah nama untuk di SJ</small>
                    <input type="text" name="nama_npwp" class="form-control" placeholder="Nama yang ada di NPWP">
                </div>
                <div class="mb-1" style="flex: 1;">
                    <p class="mb-1">NPWP</p>
                    <small class="text-danger d-block mb-1">*ini adalah nomor NPWP yang dipakai di Invoice</small>
                    <input type="text" name="npwp" class="form-control" placeholder="kosongin kalau Invoice menyusul">
                </div>
            </div>

            <div class="mb-3">
                <p class="mb-1">Keterangan</p>
                <input type="text" name="keterangan" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Tutup</button>
                <button type="submit" class="btn btn-default-merah w-100">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT ORDER -->
<div id="input-edit-order" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.45)">
    <div class="bg-white p-4 rounded modal-card-modern">
        <form method="post" action="/admin/order-offline/update">
            <h5 class="m-0 fw-bold">Edit Order</h5>
            <p class="mb-3 text-sm" style="color: var(--brand); font-size: 12px">
                ID Order :
                <input type="text" name="id_pesanan" style="border: none; color: var(--brand); pointer-events: none;"
                    class="fw-bold">
            </p>

            <input type="hidden" name="jenis" value="<?= esc($jenis) ?>">

            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="mb-1">Tanggal</label>
                    <input type="datetime-local" name="tanggal" class="form-control">
                </div>
                <div class="col-12 col-md-4">
                    <label class="mb-1">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="mb-1">No HP</label>
                    <input type="text" name="nohp" class="form-control">
                </div>

                <div class="col-12">
                    <label class="mb-1">Alamat Pengiriman</label>
                    <textarea name="alamat_pengiriman" class="form-control" rows="3" required></textarea>
                </div>

                <div class="col-12 col-md-6">
                    <label class="mb-1">NPWP (opsional)</label>
                    <input type="text" name="npwp" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="mb-1">Down Payment (opsional)</label>
                    <input type="text" name="down_payment" class="form-control" placeholder="contoh: 1.500.000">
                </div>

                <div class="col-12">
                    <label class="mb-1">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Tutup</button>
                <button type="submit" class="btn btn-default-merah w-100">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: DETAIL PESANAN -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Detail Pesanan <span class="mono" id="detail-id-pesanan"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <small class="text-muted">Berikut daftar item untuk pesanan ini.</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Varian</th>
                                <th class="text-center">Dimensi</th>
                                <th class="text-end">Harga</th>
                            </tr>
                        </thead>
                        <tbody id="detail-items-body">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- LIST PAGE -->
<div style="padding: 2em;">
    <div class="page-card">
        <div class="mb-2">
            <p class="text-muted-12" data-bs-toggle="tooltip" data-bs-title="JT = tanggal SJ + 14 hari.">
                *JT mulai dihitung saat Surat Jalan (SJ) FINAL terbit, maksimal 14 hari. Draft belum dihitung JT.
            </p>
        </div>

        <div class="page-head">
            <div class="page-title">
                <div>
                    <h1>Pesanan Offline</h1>
                    <p class="page-sub" id="page-subtitle">
                        <?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pesanan
                    </p>
                </div>
            </div>
            <div class="toolbar">
                <select class="form-select" onchange="selectJenis(event)">
                    <option value="sale" class="fw-bold" <?= $jenis == 'sale' ? 'selected' : ''; ?>>Surat Jalan (FAKTUR)
                    </option>
                    <option value="display" class="fw-bold" <?= $jenis == 'display'   ? 'selected' : ''; ?>>Surat
                        Display
                        (DISPLAY)
                    </option>
                    <option value="nf" class="fw-bold" <?= $jenis == 'nf'   ? 'selected' : ''; ?>>Non Faktur (NON
                        FAKTUR)
                    </option>
                </select>
                <a class="btn-default-merah" href="/admin/order-offline/add">Tambah</a>
            </div>
        </div>

        <!-- FILTER BAR (SEARCH) -->
        <div class="page-filters">
            <div class="page-filters-inner">
                <div class="toolbar-search">
                    <span class="material-icons">search</span>
                    <input type="search" id="order-search" class="search-input"
                        placeholder="Cari ID, nama, status, atau tanggal..." autocomplete="off">
                </div>
            </div>
        </div>

        <?php if ($msg) { ?>
        <div class="pemberitahuan"
            style="border:1px solid #fde68a;background:#fffbeb;border-radius:12px;padding:.6rem .8rem; color:#92400e">
            <?= $msg; ?>
        </div>
        <?php } ?>

        <div class="table-wrap mt-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">ID ORDER</th>
                            <th scope="col">TANGGAL</th>
                            <th scope="col">NAMA</th>
                            <th class="text-center" scope="col">STATUS</th>
                            <th class="text-center" scope="col">JT</th>
                            <th class="text-center" scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-pesanan">
                        <?php foreach ($pesanan as $ind_p => $p) { ?>
                        <tr <?= (($p['is_draft'] ?? 0) == 1) ? 'class="table-warning"' : ''; ?>>
                            <th scope="row"><?= $ind_p + 1; ?></th>
                            <td class="mono"><?= esc($p['id_pesanan']); ?></td>
                            <td><?= !empty($p['tanggal']) ? date("d M Y", strtotime($p['tanggal'])) : '-'; ?></td>
                            <td><?= esc($p['nama'] ?? '-'); ?></td>

                            <td align="center">
                                <?php
                                    $st = strtolower((string)($p['status'] ?? ''));
                                    $badgeClass = [
                                        'pending'  => 'bg-secondary',
                                        'dp'       => 'bg-warning text-dark',
                                        'dp paid'  => 'bg-info text-dark',
                                        'success'  => 'bg-success',
                                        'return'   => 'bg-danger',
                                        'draft'    => 'bg-light text-dark',
                                    ][$st] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass; ?> rounded-pill">
                                    <?= strtoupper($st ?: '-'); ?>
                                    <?php if (($p['is_draft'] ?? 0) == 1): ?>
                                    &nbsp;• DRAFT
                                    <?php endif; ?>
                                </span>

                                <?php if ($jenis === 'sale' || $jenis === 'nf'): ?>
                                <?php
                                        $sjNo   = $p['sj_last_no'] ?? null;
                                        $sjStat = strtolower((string)($p['sj_last_status'] ?? ''));
                                    ?>
                                <div class="mt-1">
                                    <?php if (!empty($sjNo)): ?>
                                    <span class="badge rounded-pill bg-light text-dark">
                                        SJ: <span class="mono"><?= esc($sjNo) ?></span>
                                    </span>
                                    <?php elseif ($sjStat === 'draft'): ?>
                                    <span class="badge rounded-pill bg-warning text-dark">SJ: DRAFT</span>
                                    <?php else: ?>
                                    <span class="badge rounded-pill bg-light text-muted">SJ: —</span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </td>

                            <?php
                                // ===== JT START ONLY WHEN SJ FINAL EXISTS =====
                                $tglSj = null;
                                $isSaleLike = ($jenis === 'sale' || $jenis === 'nf');

                                if ($isSaleLike) {
                                    $sjLastStatus  = strtolower((string)($p['sj_last_status'] ?? ''));
                                    $sjFinalTgl    = $p['sj_final_tanggal'] ?? null;
                                    $sjLastTgl     = $p['sj_last_tanggal'] ?? null;

                                    if (!empty($sjFinalTgl)) {
                                        $tglSj = $sjFinalTgl;
                                    } elseif ($sjLastStatus === 'final' && !empty($sjLastTgl)) {
                                        $tglSj = $sjLastTgl;
                                    } else {
                                        $tglSj = null; // draft / belum ada SJ
                                    }
                                } else {
                                    $tglSj = null; // SP
                                }

                                list($jtStr, $sisaHari, $jtStatus) = $jtInfo($tglSj);

                                $jtBadgeText = '—';
                                if ($jtStr) {
                                    if ($sisaHari < 0)       $jtBadgeText = "Lewat " . abs($sisaHari) . " hari";
                                    elseif ($sisaHari === 0) $jtBadgeText = "Hari ini";
                                    else                     $jtBadgeText = $sisaHari . " hari lagi";
                                }
                            ?>
                            <td class="text-center">
                                <?php if ($jtStr) { ?>
                                <div class="d-flex flex-column align-items-center" data-bs-toggle="tooltip"
                                    data-bs-title="Jatuh tempo dimulai saat SJ FINAL terbit. Maks 14 hari dari tanggal SJ.">
                                    <span class="badge-jt <?= esc($jtStatus); ?>"><?= esc($jtBadgeText); ?></span>
                                    <small class="text-muted-12"><?= esc($jtStr); ?></small>
                                </div>
                                <?php } else { ?>
                                <span class="text-muted-12">—</span>
                                <?php } ?>
                            </td>

                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <?php
                                        $isDraft      = isset($p['is_draft']) && (int)$p['is_draft'] === 1;
                                        $downPayment  = (int)($p['down_payment'] ?? 0);
                                        $hasDP        = $downPayment !== 0;
                                        $dpIsNegative = $downPayment < 0;
                                        $isSaleLike   = ($jenis === 'sale' || $jenis === 'nf');

                                        // data SJ (optional)
                                        $sjId   = $p['sj_last_id'] ?? null;
                                        $sjNo   = $p['sj_last_no'] ?? null;
                                        $sjStat = strtolower((string)($p['sj_last_status'] ?? ''));
                                    ?>

                                    <!-- Tombol DETAIL -->
                                    <button type="button" class="btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Detail pesanan"
                                        onclick="openDetail('<?= esc($p['id_pesanan']); ?>')">
                                        <i class="material-icons">visibility</i>
                                    </button>

                                    <?php if ($isDraft): ?>
                                    <!-- DRAFT MODE: finalisasi draft + edit -->
                                    <form method="post" action="/admin/order-offline/finalize"
                                        onsubmit="return confirm('Finalisasi draft <?= esc($p['id_pesanan']); ?> ? Stok akan dipotong dan status berubah. Lanjutkan?');"
                                        style="display:inline;">
                                        <input type="hidden" name="id_pesanan" value="<?= esc($p['id_pesanan']); ?>">
                                        <button type="submit" class="btn" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-title="Finalisasi draft (potong stok & ubah status)">
                                            <i class="material-icons">rocket_launch</i>
                                        </button>
                                    </form>
                                    <?php else: ?>

                                    <?php if ($isSaleLike): ?>
                                    <!-- ====== SJ BUTTONS (NEW) ====== -->
                                    <?php if (!empty($sjId)): ?>
                                    <!-- Edit SJ -->
                                    <a class="btn"
                                        href="<?= base_url('admin/surat-jalan/offline/' . $sjId . '/edit') ?>"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Edit Surat Jalan (<?= $sjNo ? esc($sjNo) : 'Draft' ?>)">
                                        <i class="material-icons">local_shipping</i>
                                    </a>

                                    <?php if (!empty($sjNo)): ?>
                                    <!-- Print SJ (only final) -->
                                    <a class="btn" href="<?= base_url('admin/surat-jalan/offline/' . $sjId) ?>"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Preview/Print SJ (Final)">
                                        <i class="material-icons">print</i>
                                    </a>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <!-- Buat SJ (FINAL, nomor global) -->
                                    <a class="btn text-danger"
                                        href="<?= base_url('admin/surat-jalan/offline/create/' . urlencode($p['id_pesanan'])) ?>"
                                        onclick="return confirm('Buat Surat Jalan (FINAL) sekarang? Nomor SJ global akan dibuat. Lanjutkan?')"
                                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Buat SJ">
                                        <i class="material-icons">add</i>
                                    </a>
                                    <?php endif; ?>

                                    <!-- ====== INVOICE BUTTONS (existing behavior) ====== -->
                                    <?php if ($hasDP): ?>
                                    <?php if ($dpIsNegative): ?>
                                    <a class="btn <?= ($p['npwp'] ?? null) ? '' : 'text-danger'; ?>"
                                        <?= ($p['npwp'] ?? null) ? 'href="/admin/invoice-offline/' . $p['id_pesanan'] . '"' : 'onclick="buatInvoice(' . $ind_p . ')"'; ?>
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="<?= ($p['npwp'] ?? null) ? 'Invoice' : 'Buat invoice'; ?>">
                                        <i class="material-icons">description</i>
                                    </a>
                                    <?php if (strtolower((string)($p['status'] ?? '')) === 'pending'): ?>
                                    <a class="btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                        onclick="alertSuccess('<?= esc($p['id_pesanan']); ?>')"
                                        data-bs-title="Tandai lunas"><i class="material-icons">check</i></a>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <a class="btn" href="/admin/invoice-offline-dp/<?= esc($p['id_pesanan']); ?>"
                                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice DP">
                                        <i class="material-icons">description</i>
                                    </a>
                                    <?php if (strtoupper((string)($p['status'] ?? '')) === 'DP'): ?>
                                    <a class="btn text-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                        onclick="buatInvoiceDP(<?= (int)$ind_p; ?>)"
                                        data-bs-title="Buat Surat Jalan dari DP">
                                        <i class="material-icons">local_shipping</i>
                                    </a>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <a class="btn <?= ($p['npwp'] ?? null) ? '' : 'text-danger'; ?>"
                                        <?= ($p['npwp'] ?? null) ? 'href="/admin/invoice-offline/' . $p['id_pesanan'] . '"' : 'onclick="buatInvoice(' . $ind_p . ')"'; ?>
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="<?= ($p['npwp'] ?? null) ? 'Invoice' : 'Buat invoice'; ?>">
                                        <i class="material-icons">description</i>
                                    </a>
                                    <?php if (strtolower((string)($p['status'] ?? '')) === 'pending'): ?>
                                    <a class="btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                        onclick="alertSuccess('<?= esc($p['id_pesanan']); ?>')"
                                        data-bs-title="Tandai lunas"><i class="material-icons">check</i></a>
                                    <?php endif; ?>
                                    <?php endif; ?>

                                    <?php elseif ($jenis === 'display'): ?>
                                    <a class="btn" href="/admin/surat-offline/<?= esc($p['id_pesanan']); ?>"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="Surat Pengantar"><i class="material-icons">description</i></a>
                                    <?php if (($p['status'] ?? '') != 'return'): ?>
                                    <a class="btn" onclick="pilihPesanan(<?= (int)$ind_p; ?>)" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-title="Buat Surat Jalan"><i
                                            class="material-icons">insert_drive_file</i></a>
                                    <?php endif; ?>
                                    <?php endif; ?>

                                    <?php endif; ?>

                                    <!-- Tombol EDIT (selalu ada) -->
                                    <a class="btn" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit"
                                        onclick="openEdit(<?= (int)$ind_p; ?>)"><i class="material-icons">edit</i></a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
/* init tooltips (bootstrap) */
(function() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    window.tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el))
})();

function selectJenis(event) {
    window.location.replace(`/admin/order/offline/${event.target.value}`)
}
</script>

<script>
/* ===== Address dropdown chain ===== */
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');

function titleCase(str = "") {
    return String(str).toLowerCase().split(' ').map(s => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
}

function normalizeList(payload) {
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.label)) return payload.label;
    if (Array.isArray(payload?.results)) return payload.results;
    if (Array.isArray(payload?.data?.results)) return payload.data.results;
    return [];
}

function safeStripSlash(text) {
    return String(text ?? "").split("/")[0].trim();
}

async function getKota(idprov) {
    kotaElm.innerHTML = '<option value="">Loading kota…</option>';
    try {
        const res = await fetch(`/getkota/${idprov}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        const list = normalizeList(payload);
        kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
        if (list.length === 0) {
            kotaElm.innerHTML = '<option value="">(Tidak ada kota)</option>';
            return;
        }
        list.forEach(item => {
            const id = item.city_id ?? item.id ?? item.value ?? "";
            const nama0 = item.city_name ?? item.label ?? item.name ?? "";
            const nama = safeStripSlash(nama0);
            const type = item.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = (item.city_name ? `${nama}${type}` : nama);
            kotaElm.appendChild(opt);
        });
    } catch (err) {
        console.error("getKota error:", err);
        kotaElm.innerHTML = '<option value="">(Gagal memuat kota)</option>';
    }
}

async function getKec(idkota) {
    kecElm.innerHTML = '<option value="">Loading kecamatan…</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    try {
        const res = await fetch(`/getkec/${idkota}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        const list = normalizeList(payload);
        kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        if (list.length === 0) {
            kecElm.innerHTML = '<option value="">(Tidak ada kecamatan)</option>';
            return;
        }
        list.forEach(item => {
            const id = item.subdistrict_id ?? item.id ?? item.value ?? "";
            const nama = safeStripSlash(item.subdistrict_name ?? item.label ?? item.name ?? "");
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = nama;
            kecElm.appendChild(opt);
        });
    } catch (err) {
        console.error("getKec error:", err);
        kecElm.innerHTML = '<option value="">(Gagal memuat kecamatan)</option>';
    }
}

async function getKode(idkecOrName) {
    kodeElm.innerHTML = '<option value="">Loading desa…</option>';
    try {
        const res = await fetch(`/getkode/${encodeURIComponent(idkecOrName)}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        const list = normalizeList(payload);
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (list.length === 0) {
            kodeElm.innerHTML = '<option value="">(Tidak ada desa)</option>';
            return;
        }
        list.forEach(item => {
            const desa = titleCase(safeStripSlash(item.DesaKelurahan ?? item.desa ?? item.label ?? item
                .name ?? ""));
            const kp = String(item.KodePos ?? item.kodepos ?? item.zip ?? "").trim();
            const opt = document.createElement("option");
            opt.value = `${desa}-${kp}`;
            opt.textContent = desa + (kp ? ` (${kp})` : "");
            kodeElm.appendChild(opt);
        });
    } catch (err) {
        console.error("getKode error:", err);
        kodeElm.innerHTML = '<option value="">(Gagal memuat desa)</option>';
    }
}

/* EVENTS */
provElm?.addEventListener("change", (e) => {
    kotaElm.innerHTML = '<option value="">Loading kota…</option>';
    kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const [idprovStr] = String(e.target.value || "").split("-");
    const idprov = Number(idprovStr);
    if (idprov > 0) getKota(idprov);
    else kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
});
kotaElm?.addEventListener("change", (e) => {
    kecElm.innerHTML = '<option value="">Loading kecamatan…</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const [idkotaStr] = String(e.target.value || "").split("-");
    const idkota = Number(idkotaStr);
    if (idkota > 0) getKec(idkota);
    else kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
});
kecElm?.addEventListener("change", (e) => {
    kodeElm.innerHTML = '<option value="">Loading desa…</option>';
    const parts = String(e.target.value || "").split("-");
    const idkec = parts[0];
    if (idkec) getKode(idkec);
    else kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
});
</script>

<script>
/* ===== Core modal controls ===== */
const tableDpElm = document.getElementById('table-dp');
const containerItemsElm = document.getElementById('container-items');
const indexItemsSelectedElm = document.querySelector('input[name="index_items_selected"]');
const inputIdpesananElm = document.querySelectorAll('input[name="id_pesanan"]');
const alamatTaghihanElm = document.querySelectorAll('.alamat-taghihan')
const inputKoreksiElm = document.getElementById('input-koreksi');
const inputBuatInvoiceElm = document.getElementById('input-buat-invoice');
const inputBuatDPElm = document.getElementById('input-buat-dp');
const inputAlamatInvoiceElm = document.getElementById('input-alamat-invoice');

var pesanan = <?= $pesananJson ?>;

const alamatTagihanElm = document.querySelector('textarea[name="alamatTagihan"]');
let pesananSelected = {};

function alertSuccess(idPesanan) {
    const check = window.confirm(`ID pesanan ${idPesanan} sudah lunas?`);
    if (check) {
        window.location.replace(`/admin/actionaccorderoffline/${idPesanan}`);
    }
}

function handleChangeInputItem(index, event) {
    let arrIndexItem = (indexItemsSelectedElm.value || '').split(',');
    arrIndexItem[index] = event.target.checked ? '1' : '0';
    indexItemsSelectedElm.value = arrIndexItem.join(',');
}

function pilihPesanan(index) {
    if (!window.pesanan || !Array.isArray(window.pesanan)) {
        console.error('pesanan belum tersedia');
        alert('Data pesanan belum siap. Coba reload halaman.');
        return;
    }
    pesananSelected = pesanan[index];
    indexItemsSelectedElm.value = '';
    containerItemsElm.innerHTML = '';

    (async () => {
        const fetchItems = await fetch(`/admin/getitemsoffline/${pesananSelected.id_pesanan}`);
        const fetchItemsJson = await fetchItems.json();

        const items = fetchItemsJson.items || fetchItemsJson.data?.items || [];
        if (!items.length) {
            return window.alert('Produk sudah di beli semua');
        }

        items.forEach((item, idx) => {
            indexItemsSelectedElm.value += `${idx == 0 ? '' : ','}${0}`
            containerItemsElm.innerHTML += `
                <label class="d-flex gap-3 align-items-center justify-content-between">
                    <div class="d-flex gap-3 align-items-center">
                        ${item.id_return == '' ? `
                        <input type="checkbox" onchange="handleChangeInputItem(${idx}, event)">
                        ` : '<div style="width: 13px"></div>'}
                        <div>
                            <p class="fw-bold m-0">${item.nama} (${item.varian})</p>
                            <p class="text-secondary text-sm m-0">${item.id_barang}</p>
                        </div>
                    </div>
                    ${item.id_return != '' ? `
                        <div class="d-flex flex-column align-items-end">
                            <a href="/admin/surat-koreksi/${item.id_return}" class="btn-teks-aja">Lihat koreksi</a>
                            <p class="text-secondary" style="font-size: 12px;">${item.id_return}</p>
                        </div>
                    ` : ''}
                </label>
            `
        })

        inputIdpesananElm.forEach((e) => {
            e.value = pesananSelected.id_pesanan;
        })

        inputKoreksiElm.classList.remove('d-none')
        inputKoreksiElm.classList.add('d-flex')
    })();
}

function closeModal() {
    inputBuatDPElm.classList.add('d-none')
    inputBuatDPElm.classList.remove('d-flex')
    inputKoreksiElm.classList.add('d-none')
    inputKoreksiElm.classList.remove('d-flex')
    inputBuatInvoiceElm.classList.add('d-none')
    inputBuatInvoiceElm.classList.remove('d-flex')
    indexItemsSelectedElm.value = '';
}

function handleChangeAlamatTagihan(event) {
    alamatTagihanElm.value = pesananSelected.alamat_pengiriman
    if (event.target.checked) {
        alamatTaghihanElm[0].classList.add('d-none');
        alamatTaghihanElm[1].classList.remove('d-none');
    } else {
        alamatTaghihanElm[1].classList.add('d-none');
        alamatTaghihanElm[0].classList.remove('d-none');
    }
}

function buatInvoice(index) {
    const pesananInvoice = pesanan[index]
    inputAlamatInvoiceElm.value = pesananInvoice.alamat_pengiriman
    inputIdpesananElm.forEach((e) => {
        e.value = pesananInvoice.id_pesanan;
    })
    inputBuatInvoiceElm.classList.remove('d-none')
    inputBuatInvoiceElm.classList.add('d-flex')
}

async function buatInvoiceDP(index) {
    const pesananInvoice = pesanan[index]
    inputIdpesananElm.forEach((e) => {
        e.value = pesananInvoice.id_pesanan;
    })

    tableDpElm.innerHTML = '';

    const fetchItems = await fetch(`/admin/getitemsoffline/${pesananInvoice.id_pesanan}`);
    const fetchItemsJson = await fetchItems.json();

    const items = fetchItemsJson.items || fetchItemsJson.data?.items || [];
    const result = items.reduce((acc, item) => {
        const key = `${item.id_barang}-${item.varian}`;
        const existingItem = acc.find(i => i.key === key);
        if (existingItem) {
            existingItem.jumlah += 1;
        } else {
            acc.push({
                ...item,
                key,
                jumlah: 1
            });
        }
        return acc;
    }, []);

    let totalHargaBarang = 0;
    result.forEach((item) => {
        tableDpElm.innerHTML += `
        <tr>
            <td>
                <p class="m-0">${item.nama} (${item.varian})</p>
                <p class="m-0">${item.dimensi?.panjang ?? '-'} x ${item.dimensi?.lebar ?? '-'} x ${item.dimensi?.tinggi ?? '-'}</p>
            </td>
            <td class="text-center">${item.jumlah}</td>
            <td class="text-end">Rp ${parseInt(item.harga || 0).toLocaleString('id-ID')}</td>
        </tr>`
        totalHargaBarang += item.jumlah * parseInt(item.harga || 0)
    })

    tableDpElm.innerHTML += `
    <tr>
        <td colspan="2" class="fw-bold">TOTAL HARGA BARANG</td>
        <td class="text-end fw-bold" colspan="2">Rp ${totalHargaBarang.toLocaleString('id-ID')}</td>
    </tr>`

    if (totalHargaBarang - Number(pesananInvoice.total_akhir || 0) > 0) {
        tableDpElm.innerHTML += `
        <tr>
            <td colspan="2" class="fw-bold">POTONGAN</td>
            <td class="text-end fw-bold" colspan="2">Rp ${(totalHargaBarang - Number(pesananInvoice.total_akhir || 0)).toLocaleString('id-ID')}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">TOTAL TAGIHAN</td>
            <td class="text-end fw-bold" colspan="2">Rp ${Number(pesananInvoice.total_akhir || 0).toLocaleString('id-ID')}</td>
        </tr>`
    }

    const totalAkhir = Number(pesananInvoice.total_akhir || 0);
    const dpAbs = Math.abs(Number(pesananInvoice.down_payment || 0));

    tableDpElm.innerHTML += `
    <tr>
        <td colspan="2" class="fw-bold">DP</td>
        <td class="text-end fw-bold" colspan="2">Rp ${dpAbs.toLocaleString('id-ID')}</td>
    </tr>
    <tr>
        <td colspan="2" class="fw-bold">SISA TAGIHAN</td>
        <td class="text-end fw-bold" colspan="2">Rp ${(totalAkhir - dpAbs).toLocaleString('id-ID')}</td>
    </tr>`

    inputBuatDPElm.classList.remove('d-none')
    inputBuatDPElm.classList.add('d-flex')
}
</script>

<script>
/* ===== Modal overlay click-to-close (UX) ===== */
(function() {
    const overlays = ['input-buat-invoice', 'input-buat-dp', 'input-koreksi', 'input-edit-order'];
    overlays.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('click', (e) => {
            if (e.target === el) closeModal();
        });
    });
})();
</script>

<!-- ========== JS TAMBAHAN: EDIT ORDER ========== -->
<script>
const inputEditOrderElm = document.getElementById('input-edit-order');

function toValueDatetimeLocal(dbDateStr) {
    if (!dbDateStr) return '';
    const s = String(dbDateStr).trim().replace('T', ' ');
    return s.substring(0, 16).replace(' ', 'T');
}

function rupiah(x) {
    if (x === null || x === undefined || x === '') return '';
    const n = String(x).replace(/[^\d\-]/g, '');
    return n.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function openEdit(index) {
    if (!Array.isArray(window.pesanan)) return alert('Data belum siap');
    const p = window.pesanan[index];
    if (!p) return;

    const form = inputEditOrderElm.querySelector('form');

    form.querySelector('input[name="id_pesanan"]').value = p.id_pesanan || '';
    form.querySelector('input[name="tanggal"]').value = toValueDatetimeLocal(p.tanggal || '');
    form.querySelector('input[name="nama"]').value = p.nama || '';
    form.querySelector('input[name="nohp"]').value = p.nohp || '';
    form.querySelector('textarea[name="alamat_pengiriman"]').value = p.alamat_pengiriman || '';
    form.querySelector('input[name="npwp"]').value = p.npwp || '';
    form.querySelector('input[name="down_payment"]').value =
        (p.down_payment !== undefined && p.down_payment !== null) ? rupiah(p.down_payment) : '';
    form.querySelector('input[name="keterangan"]').value = p.keterangan || '';

    inputEditOrderElm.classList.remove('d-none');
    inputEditOrderElm.classList.add('d-flex');
}

/* extend closeModal agar modal edit ikut nutup */
(function() {
    const _close = window.closeModal;
    window.closeModal = function() {
        _close && _close();
        inputEditOrderElm.classList.add('d-none');
        inputEditOrderElm.classList.remove('d-flex');
    }
})();
</script>

<!-- ========== JS TAMBAHAN: DETAIL PESANAN ========== -->
<script>
function openDetail(idPesanan) {
    const tbody = document.getElementById('detail-items-body');
    const titleId = document.getElementById('detail-id-pesanan');

    titleId.textContent = idPesanan || '';
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Memuat data...</td></tr>';

    fetch(`/admin/getitemsoffline/${idPesanan}`)
        .then(r => r.json())
        .then(res => {
            const items = res?.items || res?.data?.items || [];
            if (!items.length) {
                tbody.innerHTML =
                    '<tr><td colspan="4" class="text-center text-muted">Tidak ada item untuk pesanan ini.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            items.forEach(i => {
                const dim = i.dimensi || {};
                const dimStr = (dim.panjang && dim.lebar && dim.tinggi) ?
                    `${dim.panjang} × ${dim.lebar} × ${dim.tinggi}` : '-';

                tbody.innerHTML += `
                    <tr>
                        <td>${i.nama || '-'}</td>
                        <td>${i.varian || '-'}</td>
                        <td class="text-center">${dimStr}</td>
                        <td class="text-end">Rp ${parseInt(i.harga || 0).toLocaleString('id-ID')}</td>
                    </tr>`;
            });
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML =
                '<tr><td colspan="4" class="text-center text-danger">Terjadi kesalahan saat memuat data.</td></tr>';
        })
        .finally(() => {
            const modalEl = document.getElementById('modalDetail');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
}
</script>

<!-- ========== JS TAMBAHAN: SEARCH / FILTER PESANAN ========== -->
<script>
(function() {
    const inputSearch = document.getElementById('order-search');
    const tbody = document.getElementById('tbody-pesanan');
    const subtitle = document.getElementById('page-subtitle');
    if (!inputSearch || !tbody) return;

    const allRows = [...tbody.querySelectorAll('tr')];
    const total = allRows.length;
    const defaultSubtitle = subtitle ? subtitle.textContent : '';

    function applyFilter() {
        const q = (inputSearch.value || '').toLowerCase().trim();
        let visible = 0;

        allRows.forEach((row) => {
            const text = row.innerText.toLowerCase();
            const match = !q || text.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (!subtitle) return;
        if (!q) {
            subtitle.textContent = defaultSubtitle || (total <= 0 ? 'Tidak Ada Pesanan' : total + ' Pesanan');
        } else {
            subtitle.textContent = `${visible} dari ${total} pesanan sesuai filter`;
        }
    }

    inputSearch.addEventListener('input', applyFilter);
})();
</script>

<?= $this->endSection(); ?>