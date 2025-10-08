<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --ink: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --card: #fff;
    --card-weak: #fafafa;
    --brand: var(--merah);
    --fs-base: 15px;
    /* desktop/tablet */
    --fs-small: 13.5px;
    /* HP */
    --fs-xsmall: 13px;
    /* HP kecil (<=360px) */
}

.konten,
.modal-card,
.toast-stack {
    font-size: var(--fs-base);
}

.konten {
    max-width: 1100px
}

.path {
    font-size: .95rem;
    color: var(--muted)
}

.path a {
    text-decoration: none
}

.card {
    border: 1px solid var(--line);
    border-radius: 14px;
    background: var(--card);
    box-shadow: 0 1px 10px rgba(0, 0, 0, .03)
}

.garis {
    display: block;
    height: 1px;
    background: var(--line)
}

.baris-ke-kolom {
    display: flex;
    gap: 18px;
    align-items: flex-start
}

.baris-ke-kolom>div:first-child {
    flex: 1
}

.tigapuluh-ke-seratus {
    width: 360px
}

@media (max-width:992px) {
    .baris-ke-kolom {
        flex-direction: column
    }

    .tigapuluh-ke-seratus {
        width: 100%
    }
}

.container-address {
    display: flex;
    flex-direction: column;
    gap: 10px
}

.item-address {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: #fff;
    transition: .15s
}

.item-address .nama {
    font-weight: 800;
    letter-spacing: -.01em;
    font-size: clamp(1rem, 0.95rem + .3vw, 1.08rem);
    margin-bottom: .25rem
}

.item-address:hover {
    background: #fafafa
}

input[type="radio"][name="address"] {
    position: absolute;
    opacity: 0;
    pointer-events: none
}

input[type="radio"][name="address"]:checked+.item-address {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px color-mix(in oklab, var(--brand) 18%, transparent)
}

.btn-teks-aja {
    border: none;
    background: transparent;
    color: var(--brand);
    font-weight: 600
}

.btn-lonjong {
    background: var(--brand);
    border: 1px solid var(--brand);
    color: #fff;
    border-radius: 999px;
    padding: .55rem 1.1rem;
    font-weight: 700;
    font-size: .95rem
}

.btn-lonjong:hover {
    filter: brightness(.96);
    color: #fff
}

/* Modals */
.modal-layer {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, .4);
    backdrop-filter: blur(2px);
    padding: 18px
}

.modal-layer.d-flex {
    display: flex
}

.modal-card {
    width: min(780px, 92vw);
    max-height: 90svh;
    overflow: auto;
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--line);
    box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
    animation: pop .18s ease-out
}

@keyframes pop {
    from {
        transform: translateY(6px);
        opacity: 0
    }

    to {
        transform: translateY(0);
        opacity: 1
    }
}

.modal-header,
.modal-footer {
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between
}

.modal-body {
    padding: 14px 16px
}

.modal-title {
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em
}

.btn-close {
    border: none;
    background: transparent
}

/* Floating inputs */
.form-floating>.form-control,
.form-floating>.form-select {
    border-radius: 12px
}

.form-select,
.form-control {
    border: 1px solid var(--line)
}

.form-floating>label {
    color: #6b7280
}

/* Toasts */
.toast-stack {
    position: fixed;
    right: 18px;
    bottom: 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 9999;
    pointer-events: none
}

.toast {
    pointer-events: auto;
    display: flex;
    gap: 10px;
    min-width: 260px;
    max-width: 360px;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 10px 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
    animation: toastIn .18s ease-out
}

.toast .ti {
    font-size: 20px;
    color: #2563eb
}

.toast.success {
    border-color: #10b98133
}

.toast.success .ti {
    color: #059669
}

.toast.error {
    border-color: #ef444433
}

.toast.error .ti {
    color: #ef4444
}

.toast .tt {
    font-weight: 700;
    letter-spacing: -.01em;
    font-size: .95rem
}

.toast .tm {
    color: var(--ink);
    font-size: .92rem;
    line-height: 1.2
}

.toast .x {
    border: none;
    background: transparent;
    color: #9ca3af;
    cursor: pointer
}

@keyframes toastIn {
    from {
        opacity: 0;
        transform: translateY(6px)
    }

    to {
        opacity: 1;
        transform: translateY(0)
    }
}

/* Pesanan card */
.btn-default-merah {
    background: var(--brand);
    border: 1px solid var(--brand);
    color: #fff;
    border-radius: 10px;
    padding: .6rem 1rem;
    font-weight: 700;
    font-size: .96rem
}

.btn-default-merah:hover {
    filter: brightness(.96);
    color: #fff
}

/* Mobile tuning */
@media (max-width:768px) {

    .konten,
    .modal-card,
    .toast-stack {
        font-size: var(--fs-small);
    }

    .modal-card {
        width: min(820px, 96vw);
    }

    .modal-header,
    .modal-footer,
    .modal-body {
        padding: 12px 12px;
    }

    .modal-title {
        font-size: 1.05rem;
    }

    .item-address {
        padding: 10px;
        gap: 10px;
    }

    .item-address .nama {
        font-size: 1rem;
    }

    .btn-lonjong {
        padding: .5rem 1rem;
        font-size: .92rem
    }

    .btn-default-merah {
        padding: .55rem .9rem;
        font-size: .92rem
    }
}

@media (max-width:360px) {

    .konten,
    .modal-card,
    .toast-stack {
        font-size: var(--fs-xsmall);
    }

    .btn-lonjong,
    .btn-default-merah {
        font-size: .9rem
    }

    .toast {
        min-width: 230px
    }
}
</style>

<!-- ===== Add Modal ===== -->
<div id="addModalContainer" class="modal-layer d-none">
    <div class="modal-card">
        <form action="/addaddress" method="post">
            <input type="text" value="address" name="checkpage" class="d-none">
            <div class="modal-content">
                <?php if(!session()->get('isLogin')) { ?>
                <div class="d-flex align-items-start p-3">
                    <div style="flex: 2" class="d-flex flex-column justify-content-center align-items-center">
                        <p class="mb-2">Ingin menyimpan alamat?</p>
                        <a href="/login" class="btn-default-merah">Login member</a>
                    </div>
                    <div stype="flex: 1">
                        <button onclick="closeAllModal()" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                </div>
                <hr class="m-0">
                <?php } ?>

                <div class="modal-header">
                    <h2 class="modal-title">Alamat Baru</h2>
                    <?php if(session()->get('isLogin')) { ?>
                    <button onclick="closeAllModal()" type="button" class="btn-close" aria-label="Close"></button>
                    <?php } ?>
                </div>

                <div class="modal-body">
                    <div class="pb-3 border-bottom">
                        <h5 class="mb-2" style="font-weight:700; letter-spacing:-.01em;">Informasi Pemesan</h5>
                        <div class="form-floating mb-2">
                            <input type="email" class="form-control" placeholder="Email" name="emailPem" required
                                value="<?= $email; ?>">
                            <label>Email</label>
                        </div>
                    </div>

                    <div class="py-3">
                        <h5 class="mb-2" style="font-weight:700; letter-spacing:-.01em;">Informasi Penerima</h5>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" placeholder="Nama" name="nama" required
                                value="<?= $nama; ?>">
                            <label>Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp" required
                                value="<?= $nohp; ?>">
                            <label>No. HP (WA)</label>
                        </div>

                        <div class="d-flex gap-2 mb-2">
                            <div class="form-floating w-50">
                                <select class="form-select" name="provinsi" required>
                                    <option value="">-- Pilih provinsi --</option>
                                    <?php foreach ($provinsi as $p) { ?>
                                    <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                                    <?php } ?>
                                </select>
                                <label>Provinsi</label>
                            </div>
                            <div class="form-floating w-50">
                                <select class="form-select" name="kota" required>
                                    <option value="">-- Pilih kota --</option>
                                </select>
                                <label>Kabupaten/Kota</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-2">
                            <div class="form-floating w-50">
                                <select class="form-select" name="kecamatan" required>
                                    <option value="">-- Pilih kecamatan --</option>
                                </select>
                                <label>Kecamatan</label>
                            </div>
                            <div class="form-floating w-50">
                                <select class="form-select" name="kodepos" required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                                <label>Desa/Kelurahan</label>
                            </div>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" placeholder="Alamat" name="alamat_add" required
                                id="alamatAddInput">
                            <label>Jalan, Nomor Rumah, RT-RW</label>
                            <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                            <div class="small-hint mt-1">Contoh: Jl. Melati No. 12 RT 03 RW 05</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-teks-aja" onclick="closeAllModal()">Batal</button>
                    <button type="submit" class="btn btn-lonjong">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ===== Edit Modal ===== -->
<div id="editModalContainer" class="modal-layer d-none">
    <div class="modal-card">
        <form method="post" id="formEdit">
            <input type="text" value="<?= $_SERVER['REQUEST_URI']; ?>" class="d-none" name="url">
            <div class="modal-content">
                <?php if(!session()->get('isLogin')) { ?>
                <div class="d-flex align-items-start p-3">
                    <div style="flex: 2" class="d-flex flex-column justify-content-center align-items-center">
                        <p class="mb-2">Ingin menyimpan alamat?</p>
                        <a href="/login" class="btn-default-merah">Login member</a>
                    </div>
                    <div stype="flex: 1">
                        <button onclick="closeAllModal()" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                </div>
                <hr class="m-0">
                <?php } ?>

                <div class="modal-header">
                    <h1 class="modal-title" style="font-size:1.08rem">Edit Alamat</h1>
                    <?php if(session()->get('isLogin')) { ?>
                    <button onclick="closeAllModal()" type="button" class="btn-close" aria-label="Close"></button>
                    <?php } ?>
                </div>

                <div class="modal-body">
                    <div class="pb-3 border-bottom">
                        <h5 class="mb-2" style="font-weight:700; letter-spacing:-.01em;">Informasi Pemesan</h5>
                        <div class="form-floating mb-2">
                            <input type="email" class="form-control" placeholder="Email" name="emailPem" required
                                id="inputEmail">
                            <label>Email</label>
                        </div>
                    </div>

                    <div class="py-3">
                        <h5 class="mb-2" style="font-weight:700; letter-spacing:-.01em;">Informasi Penerima</h5>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" placeholder="Nama" name="nama" required
                                id="inputNama">
                            <label>Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp" required
                                id="inputNohp">
                            <label>No. HP (WA)</label>
                        </div>

                        <div class="d-flex gap-2 mb-2">
                            <div class="form-floating w-50">
                                <select class="form-select" name="provinsiEdit" required>
                                    <option value="">-- Pilih provinsi --</option>
                                    <?php foreach ($provinsi as $p) { ?>
                                    <option value="<?= $p['id']; ?>-<?= $p['label']; ?>"><?= $p['label']; ?></option>
                                    <?php } ?>
                                </select>
                                <label>Provinsi</label>
                            </div>
                            <div class="form-floating w-50">
                                <select class="form-select" name="kotaEdit" required>
                                    <option value="">-- Pilih kota --</option>
                                </select>
                                <label>Kabupaten/Kota</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-2">
                            <div class="form-floating w-50">
                                <select class="form-select" name="kecamatanEdit" required>
                                    <option value="">-- Pilih kecamatan --</option>
                                </select>
                                <label>Kecamatan</label>
                            </div>
                            <div class="form-floating w-50">
                                <select class="form-select" name="kodeposEdit" required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                                <label>Desa/Kelurahan</label>
                            </div>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" placeholder="Alamat" name="alamat_add" required
                                id="alamatEditInput">
                            <label>Jalan, Nomor Rumah, RT-RW</label>
                            <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-teks-aja" onclick="closeAllModal()">Batal</button>
                    <button type="submit" class="btn btn-lonjong">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container konten">
    <?php if ($msg) { ?>
    <div class="pemberitahuan mb-2" role="alert"><?= $msg; ?></div>
    <?php } ?>

    <h5 class="path" style="letter-spacing:-1px; font-weight:100;">
        <a class="me-3 text-dark fw-bold">Alamat</a> >
        <a class="mx-3 text-secondary">Rincian Pembayaran</a>
    </h5>

    <div class="baris-ke-kolom">
        <!-- LEFT -->
        <div>
            <div class="container-address my-4">
                <?php foreach ($alamat as $ind_a => $a) { ?>
                <input type="radio" name="address" id="address<?= $ind_a ?>" <?= $ind_a <= 0 ? 'checked' : '' ?>>
                <label for="address<?= $ind_a ?>" class="item-address radio">
                    <div style="flex:1;">
                        <p class="mb-1 nama"><?= $a['nama_penerima'] ?></p>
                        <p class="mb-1"><?= $a['alamat_lengkap'] ?></p>
                        <p class="mb-1"><?= $a['nohp_penerima'] ?></p>
                        <p class="mb-1"><b style="font-weight:600;">Pemesan</b> : <?= $a['email_pemesan'] ?></p>
                    </div>
                    <div style="width:100px" class="d-flex gap-3 justify-content-end align-items-start">
                        <a class="btn-teks-aja text-dark" data-bs-toggle="modal" data-bs-target="#editModal"
                            type="button" onclick="openEditModal(<?= $ind_a; ?>)">Edit</a>
                        <a href="/deleteaddress/<?= $ind_a; ?>/<?= $_SERVER['REQUEST_URI']; ?>"
                            class="btn-teks-aja">Hapus</a>
                    </div>
                </label>
                <?php } ?>
            </div>

            <button id="btn-add-address" type="button" class="btn-teks-aja">
                <i class="material-icons">add</i> Tambah Alamat Penerima
            </button>
        </div>

        <!-- RIGHT -->
        <div class="tigapuluh-ke-seratus">
            <div class="card p-4">
                <h4 style="letter-spacing:-1px">Pesanan</h4>

                <div class="mt-2 d-flex justify-content-between py-1">
                    <p class="m-0">harga</p>
                    <p class="fw-bold m-0">Rp <?= number_format($hargaTotal, 0, ',', '.'); ?></p>
                </div>

                <span class="garis my-2"></span>

                <div class="d-flex justify-content-between py-1">
                    <p class="m-0">TOTAL</p>
                    <p class="fw-bold m-0">Rp <?= number_format($hargaKeseluruhan, 0, ',', '.'); ?></p>
                </div>

                <a id="btn-shipping" <?= count($alamat) > 0 ? 'href="/payment/0"' : '' ?>
                    class="btn-default-merah <?= count($alamat) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center">
                    Pilih Rincian Pembayaran
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-stack" id="toastStack"></div>

<script>
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');

const provElmEdit = document.querySelector('select[name="provinsiEdit"]');
const kotaElmEdit = document.querySelector('select[name="kotaEdit"]');
const kecElmEdit = document.querySelector('select[name="kecamatanEdit"]');
const kodeElmEdit = document.querySelector('select[name="kodeposEdit"]');

const radioAddressElm = document.querySelectorAll('input[name="address"]');
const inputEmailElm = document.getElementById("inputEmail");
const inputNamaElm = document.getElementById("inputNama");
const inputNohpElm = document.getElementById("inputNohp");
const alamatJson = JSON.parse('<?= $alamatJson ?>');
const formEditElm = document.getElementById("formEdit");
const btnShippingElm = document.getElementById("btn-shipping");
const btnAddAddressElm = document.getElementById("btn-add-address");
const addModalContainer = document.getElementById("addModalContainer");
const editModalContainer = document.getElementById("editModalContainer");

/* Toast */
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `
    <div class="ti material-icons">${type==='error'?'error':'check_circle'}</div>
    <div class="tc">
      <div class="tt">${type==='error'?'Gagal':'Berhasil'}</div>
      <div class="tm">${msg||''}</div>
    </div>
    <button class="x material-icons" aria-label="Tutup">close</button>
  `;
    const stack = document.getElementById('toastStack');
    stack.appendChild(el);
    const kill = () => el.remove();
    el.querySelector('.x').addEventListener('click', kill);
    setTimeout(kill, 3500);
}
<?php if ($msg) { ?> toast(`<?= addslashes($msg); ?>`, 'success');
<?php } ?>

if (alamatJson.length == 0) {
    addModalContainer.classList.remove("d-none");
    addModalContainer.classList.add("d-flex");
}
btnAddAddressElm.addEventListener("click", () => {
    addModalContainer.classList.remove("d-none");
    addModalContainer.classList.add("d-flex");
});
radioAddressElm.forEach((elm, ind_radio) => {
    elm.addEventListener('change', () => {
        btnShippingElm.href = "/payment/" + ind_radio;
    });
});

/* Helpers */
function titleCase(str) {
    return str.toLowerCase().split(' ').map(s => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
}

/* ================== ADD (prov -> kota -> kec -> kode) ================== */
async function getKota(idprov) {
    kotaElm.innerHTML = '<option value="">Loading kota…</option>';
    try {
        const res = await fetch(`/getkota/${idprov}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        const list = Array.isArray(payload) ? payload :
            Array.isArray(payload.label) ? payload.label :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
        if (list.length === 0) {
            kotaElm.innerHTML = '<option value="">(Tidak ada kota)</option>';
            return;
        }
        list.forEach(item => {
            const id = item.city_id ?? item.id;
            const nama = (item.city_name?.split("/")[0] ?? item.label).trim();
            const type = item.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = `${nama}${type}`;
            kotaElm.appendChild(opt);
        });
    } catch (err) {
        kotaElm.innerHTML = '<option value="">(Gagal memuat kota)</option>';
        toast('Gagal memuat daftar kota.', 'error');
    }
}
async function getKec(idkota) {
    kecElm.innerHTML = '<option value="">Loading kecamatan…</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    try {
        const res = await fetch(`/getkec/${idkota}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        const list = Array.isArray(payload) ? payload :
            Array.isArray(payload.rajaongkir?.results) ? payload.rajaongkir.results :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        if (list.length === 0) {
            kecElm.innerHTML = '<option value="">(Tidak ada kecamatan)</option>';
            return;
        }
        list.forEach(el => {
            const id = el.subdistrict_id ?? el.id;
            const nama = (el.subdistrict_name ?? el.label).split("/")[0].trim();
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = nama;
            kecElm.appendChild(opt);
        });
    } catch (err) {
        kecElm.innerHTML = '<option value="">(Gagal memuat kecamatan)</option>';
        toast('Gagal memuat daftar kecamatan.', 'error');
    }
}
async function getKode(kecId) {
    kodeElm.innerHTML = '<option value="">Loading desa…</option>';
    try {
        const res = await fetch(`/getkode/${kecId}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        const list = Array.isArray(payload) ? payload :
            Array.isArray(payload.rajaongkir?.results) ? payload.rajaongkir.results :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (list.length === 0) {
            kodeElm.innerHTML = '<option value="">(Tidak ada desa)</option>';
            return;
        }
        list.forEach(el => {
            const nama = titleCase((el.DesaKelurahan || el.label)).split("/")[0].trim();
            const pos = el.KodePos || el.kodepos || '';
            const opt = document.createElement("option");
            opt.value = `${nama}-${pos}`;
            opt.textContent = nama;
            kodeElm.appendChild(opt);
        });
    } catch (err) {
        kodeElm.innerHTML = '<option value="">(Gagal memuat desa)</option>';
        toast('Gagal memuat daftar desa.', 'error');
    }
}
/* Bind ADD */
if (provElm) {
    provElm.addEventListener("change", (e) => {
        kotaElm.innerHTML = '<option value="">Loading..</option>';
        kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        const idprov = Number((e.target.value.split("-")[0]) || 0);
        if (idprov > 0) getKota(idprov);
    });
}
if (kotaElm) {
    kotaElm.addEventListener("change", (e) => {
        kecElm.innerHTML = '<option value="">Loading..</option>';
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        const idkota = Number((e.target.value.split("-")[0]) || 0);
        if (idkota > 0) getKec(idkota);
    });
}
if (kecElm) {
    kecElm.addEventListener("change", (e) => {
        kodeElm.innerHTML = '<option value="">Loading..</option>';
        const idkec = (e.target.value.split("-")[0]) || '';
        if (idkec) getKode(idkec);
    });
}

/* ================ EDIT (prov -> kota -> kec -> kode) ================= */
/* PENTING: semua fetch pakai PATH ABSOLUT */
async function getKotaEdit(idprov) {
    try {
        const response = await fetch(`/getkota/${idprov}`);
        if (!response.ok) throw new Error(response.statusText);
        const kota = await response.json();
        const hasil = kota.rajaongkir?.results || kota.results || kota.data?.results || kota.label || kota || [];
        kotaElmEdit.innerHTML = '<option value="">-- Pilih kota --</option>';
        hasil.forEach(element => {
            const id = element.city_id ?? element.id;
            const nama = (element.city_name || element.label).split("/")[0].trim();
            const optElm = document.createElement("option");
            optElm.value = id + "-" + nama;
            optElm.innerHTML = (element.type === 'Kota' ? `${nama} Kota` : nama);
            kotaElmEdit.appendChild(optElm);
        });
    } catch (err) {
        kotaElmEdit.innerHTML = '<option value="">(Gagal memuat kota)</option>';
        toast('Gagal memuat daftar kota (Edit).', 'error');
    }
}
async function getKecEdit(idkota) {
    try {
        const response = await fetch(`/getkec/${idkota}`);
        if (!response.ok) throw new Error(response.statusText);
        const kecamatan = await response.json();
        const hasil = kecamatan.rajaongkir?.results || kecamatan.results || kecamatan.data?.results || kecamatan ||
            [];
        kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(element => {
            const id = element.subdistrict_id ?? element.id;
            const nama = (element.subdistrict_name || element.label).split("/")[0].trim();
            const optElm = document.createElement("option");
            optElm.value = id + "-" + nama; // simpan "id-nama"
            optElm.innerHTML = nama;
            kecElmEdit.appendChild(optElm);
        });
    } catch (err) {
        kecElmEdit.innerHTML = '<option value="">(Gagal memuat kecamatan)</option>';
        toast('Gagal memuat daftar kecamatan (Edit).', 'error');
    }
}
async function getKodeEdit(kecId) {
    try {
        const response = await fetch(`/getkode/${kecId}`);
        if (!response.ok) throw new Error(response.statusText);
        const kode = await response.json();
        const hasil = Array.isArray(kode) ? kode : (kode.results || kode.data?.results || kode || []);
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(element => {
            const nama = titleCase((element.DesaKelurahan || element.label)).split("/")[0].trim();
            const pos = element.KodePos || element.kodepos || '';
            const optElm = document.createElement("option");
            optElm.value = `${nama}-${pos}`;
            optElm.innerHTML = nama;
            kodeElmEdit.appendChild(optElm);
        });
    } catch (err) {
        kodeElmEdit.innerHTML = '<option value="">(Gagal memuat desa)</option>';
        toast('Gagal memuat daftar desa (Edit).', 'error');
    }
}
/* Bind EDIT */
if (provElmEdit) {
    provElmEdit.addEventListener("change", (e) => {
        kotaElmEdit.innerHTML = '<option value="">Loading..</option>';
        kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        const idprov = Number((e.target.value.split("-")[0]) || 0);
        if (idprov > 0) getKotaEdit(idprov);
    });
}
if (kotaElmEdit) {
    kotaElmEdit.addEventListener("change", (e) => {
        kecElmEdit.innerHTML = '<option value="">Loading..</option>';
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        const idkota = Number((e.target.value.split("-")[0]) || 0);
        if (idkota > 0) getKecEdit(idkota);
    });
}
if (kecElmEdit) {
    kecElmEdit.addEventListener("change", (e) => {
        kodeElmEdit.innerHTML = '<option value="">Loading..</option>';
        /* FIX: kirim ID kecamatan (bagian sebelum '-') ke /getkode/  */
        const idkec = (e.target.value.split("-")[0]) || '';
        if (idkec) getKodeEdit(idkec);
    });
}

/* Open / Close modal (flow tetap) */
function openEditModal(ind_add) {
    const alamatSelected = alamatJson[ind_add] || {};
    inputEmailElm.value = alamatSelected.email_pemesan || '';
    inputNamaElm.value = alamatSelected.nama_penerima || '';
    inputNohpElm.value = alamatSelected.nohp_penerima || '';
    formEditElm.action = "/editaddress/" + ind_add;
    editModalContainer.classList.remove("d-none");
    editModalContainer.classList.add("d-flex");
}

function closeAllModal() {
    addModalContainer.classList.remove("d-flex");
    addModalContainer.classList.add("d-none");
    editModalContainer.classList.remove("d-flex");
    editModalContainer.classList.add("d-none");
}

/* Inline validation: blok karakter "/" */
const alamatAddInput = document.getElementById('alamatAddInput');
const alamatEditInput = document.getElementById('alamatEditInput');

function validateSlash(e) {
    const hasSlash = (e.target.value || '').includes('/');
    e.target.setCustomValidity(hasSlash ? 'Tidak boleh mengandung karakter /' : '');
    if (hasSlash) {
        e.target.reportValidity();
    }
}
if (alamatAddInput) {
    alamatAddInput.addEventListener('input', validateSlash);
}
if (alamatEditInput) {
    alamatEditInput.addEventListener('input', validateSlash);
}
</script>

<?= $this->endSection(); ?>