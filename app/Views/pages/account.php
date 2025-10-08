<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<!-- Modal ganti sandi -->
<div id="edit-sandi"
    style="position: fixed; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.7); z-index: 100; top: 0; left: 0;"
    class="<?= $msgSandi ? 'd-flex' : 'd-none'; ?> justify-content-center align-items-center">
    <div class="p-4 bg-light" style="border-radius: 1em;">
        <h2 style="letter-spacing: -1px">Ganti Sandi</h2>
        <?php if ($msgSandi) { ?>
        <div class="pemberitahuan my-1 mx-auto" style="width: fit-content;" role="alert">
            <?= $msgSandi; ?>
        </div>
        <?php } ?>
        <form action="/editsandi/account" method="post">
            <div class="mb-1">
                <label for="">Sandi baru Anda</label>
                <input type="password" name="sandi" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="">Konfirmasi Sandi</label>
                <input type="password" class="form-control" oninput="konfirmasiSandi(event)" required
                    name="sandiKonfirm">
                <div class="invalid-feedback">Kata sandi tidak cocok</div>
            </div>
            <div class="d-flex justify-content-center gap-1">
                <button type="submit" class="btn-default">Ubah</button>
                <button class="btn-teks-aja" onclick="closeEditSandi()">Batal</button>
            </div>
        </form>
    </div>
</div>

<style>
:root {
    --ink: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --card: #fff;
    --card-weak: #fafafa;
    --brand: var(--merah);
    --fs-base: 15px;
    --fs-small: 13.5px;
    --fs-xsmall: 13px;
}

.konten {
    max-width: 1100px
}

.baris-ke-kolom {
    display: flex;
    gap: 18px;
    align-items: flex-start
}

.baris-ke-kolom>.tigapuluh-ke-seratus {
    width: 360px
}

.baris-ke-kolom>div[style*="flex:1"] {
    flex: 1
}

@media (max-width:992px) {
    .baris-ke-kolom {
        flex-direction: column
    }

    .baris-ke-kolom>.tigapuluh-ke-seratus {
        width: 100%
    }
}

/* Card & buttons */
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

.teks-sedang {
    font-weight: 800;
    letter-spacing: -.02em
}

.btn-default-merah {
    background: var(--brand);
    border: 1px solid var(--brand);
    color: #fff;
    border-radius: 10px;
    padding: .6rem 1rem;
    font-weight: 700
}

.btn-default-abu {
    background: #f3f4f6;
    border: 1px solid var(--line);
    color: #111827;
    border-radius: 10px;
    padding: .6rem 1rem;
    font-weight: 700
}

.btn-teks-aja {
    border: none;
    background: transparent;
    color: var(--brand);
    font-weight: 600
}

/* Address list */
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

/* Voucher grid */
.vch-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px
}

@media (max-width:768px) {
    .vch-grid {
        grid-template-columns: 1fr
    }
}

.vch-card {
    border: 1px dashed var(--line);
    border-radius: 12px;
    padding: 12px;
    background: var(--card-weak)
}

.vch-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px
}

.vch-code {
    font-weight: 900;
    letter-spacing: .06em;
    font-size: 1rem
}

.vch-badge {
    border-radius: 999px;
    padding: .2rem .5rem;
    font-size: .75rem;
    font-weight: 700;
    background: #eef2ff;
    color: #4338ca;
    border: 1px dashed #c7d2fe
}

.vch-body {
    font-size: .92rem;
    color: #374151;
    margin-top: 6px
}

.vch-meta {
    font-size: .8rem;
    color: var(--muted)
}

.vch-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px
}

.btn-chip {
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: .4rem .7rem;
    font-weight: 700;
    background: #fff
}

.btn-chip.primary {
    border-color: var(--brand);
    color: var(--brand)
}

.path {
    font-size: .95rem;
    color: var(--muted)
}

.path a {
    text-decoration: none
}

/* Base font */
.container,
.card {
    font-size: var(--fs-base);
}

/* ===== Mobile: kecilkan font & hilangkan bold ===== */
@media (max-width:768px) {

    .container,
    .card {
        font-size: var(--fs-small);
    }

    .btn-default-merah,
    .btn-default-abu {
        font-size: .92rem;
        padding: .55rem .9rem;
    }

    /* turunkan bold di HP */
    .fw-bold,
    .teks-sedang,
    .item-address .nama,
    .vch-code,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    b,
    strong {
        font-weight: 500 !important;
    }
}

@media (max-width:360px) {

    .container,
    .card {
        font-size: var(--fs-xsmall);
    }

    .btn-default-merah,
    .btn-default-abu {
        font-size: .9rem
    }
}
</style>

<div class="container konten baris-ke-kolom">
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h2 style="letter-spacing: -1px">Akun Saya</h2>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">Email</p>
                <p class="fw-bold m-0"><?= esc($email); ?></p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">Sandi</p>
                <a class="btn-teks-aja" onclick="openGantiSandi()">Ganti Sandi</a>
            </div>
            <span class="garis my-2"></span>
            <a href="/account" class="mb-1 btn-default-abu" style="width: 100%; text-align:center;">Profile</a>
            <a href="/order" class="mb-1 btn-default-abu" style="width: 100%; text-align:center;">Pesanan</a>
            <a href="/logout" class="btn-default-merah" style="width: 100%; text-align:center;">Keluar</a>
        </div>
    </div>

    <div style="flex:1;">
        <div class="mb-4">
            <h1 class="teks-sedang">Tentang Saya</h1>
            <p style="color: grey;">1 Pemesanan</p>
        </div>

        <div class="mt-2 d-flex justify-content-between py-1">
            <p class="m-0">Email</p>
            <p class="fw-bold m-0"><?= esc($email); ?></p>
        </div>
        <div class="d-flex justify-content-between py-1">
            <p class="m-0">Nama Lengkap</p>
            <p class="fw-bold m-0"><?= esc($nama); ?></p>
        </div>
        <div class="mb-4 d-flex justify-content-between py-1">
            <p class="m-0">Sandi</p>
            <a class="btn-teks-aja" onclick="openGantiSandi()">Ganti Sandi</a>
        </div>
        <span class="garis"></span>

        <?php if (session()->get('active') != '0') { ?>
        <div class="my-4">
            <h1 class="teks-sedang">Alamat</h1>
            <p style="color: grey;"><?= count($alamat) <= 0 ? 'Tidak Ada' : count($alamat) ?> tujuan yang dapat dipilih
            </p>
        </div>

        <?php if ($msg) { ?>
        <div class="pemberitahuan" role="alert">
            <?= $msg; ?>
        </div>
        <?php } ?>

        <div class="container-address my-4">
            <?php foreach ($alamat as $ind_a => $a) { ?>
            <div class="item-address">
                <div style="flex: 1;">
                    <p class="mb-1 nama"><?= esc($a['nama_penerima']) ?></p>
                    <p class="mb-1"><?= esc($a['alamat_lengkap']) ?></p>
                    <p class="mb-1"><?= esc($a['nohp_penerima']) ?></p>
                    <p class="mb-1"><b style="font-weight: 600;">Pemesan</b> : <?= esc($a['email_pemesan']) ?></p>
                </div>
                <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                    <a class="btn-teks-aja text-dark" data-bs-toggle="modal" data-bs-target="#editModal" type="button"
                        onclick="openEditModal(<?= (int)$ind_a; ?>)">Edit</a>
                    <a href="/deleteaddress/<?= (int)$ind_a; ?>/<?= $_SERVER['REQUEST_URI']; ?>"
                        class="btn-teks-aja">Hapus</a>
                </div>
            </div>
            <?php } ?>
        </div>

        <button type="button" class="btn-teks-aja" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="material-icons">add</i> Tambah Alamat
        </button>

        <!-- ====== Voucher Saya ====== -->
        <?php
          $voucherUser = isset($voucherUser) && is_array($voucherUser) ? $voucherUser : [];
          $claimedIds = (array)(session()->get('voucher_claimed') ?? []);
          $voucherClaimedDetail = isset($voucherClaimedDetail) && is_array($voucherClaimedDetail) ? $voucherClaimedDetail : [];
          $showVoucherSection = (count($voucherUser) > 0) || (count($voucherClaimedDetail) > 0) || (count($claimedIds) > 0);
        ?>

        <?php if ($showVoucherSection): ?>
        <div class="my-4">
            <h1 class="teks-sedang">Voucher Saya</h1>
            <p style="color: grey;">Voucher yang terikat akun / sudah kamu klaim.</p>

            <div class="vch-grid">
                <?php if (count($voucherUser) > 0): ?>
                <?php foreach ($voucherUser as $v): 
                $kode = $v['kode'] ?? '';
                $nama = $v['nama'] ?? 'Voucher';
                $tipe = ($v['tipe'] ?? ($v['satuan'] ?? 'persen')) === 'persen' ? 'persen' : 'nominal';
                $nilai= (float)($v['nilai'] ?? ($v['nominal'] ?? 0));
                $target = strtolower($v['target'] ?? 'semua');
                $aktif = !empty($v['aktif']);
                $mulai = !empty($v['mulai']) ? substr($v['mulai'],0,10) : '';
                $akhir = !empty($v['berakhir']) ? substr($v['berakhir'],0,10) : '';
              ?>
                <div class="vch-card">
                    <div class="vch-hd">
                        <div class="vch-code"><?= esc($kode) ?></div>
                        <?php if($target==='baru'): ?><span class="vch-badge">Member Baru</span><?php endif; ?>
                    </div>
                    <div class="vch-body"><b><?= esc($nama) ?></b></div>
                    <div class="vch-meta">
                        Tipe:
                        <?= $tipe==='persen' ? (number_format($nilai,0).'%') : ('Rp '.number_format($nilai,0,',','.')) ?>
                        <?php if($mulai || $akhir): ?> · Periode: <?= esc($mulai) ?> – <?= esc($akhir) ?><?php endif; ?>
                        · Status: <?= $aktif ? 'Aktif' : 'Nonaktif'; ?>
                    </div>
                    <div class="vch-actions">
                        <button class="btn-chip primary" type="button"
                            onclick="salinKode('<?= esc($kode, 'attr') ?>')">Salin Kode</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($voucherClaimedDetail) > 0): ?>
                <?php foreach ($voucherClaimedDetail as $v):
                $kode = $v['kode'] ?? '';
                $nama = $v['nama'] ?? 'Voucher';
                $tipe = ($v['tipe'] ?? ($v['satuan'] ?? 'persen')) === 'persen' ? 'persen' : 'nominal';
                $nilai= (float)($v['nilai'] ?? ($v['nominal'] ?? 0));
                $mulai = !empty($v['mulai']) ? substr($v['mulai'],0,10) : '';
                $akhir = !empty($v['berakhir']) ? substr($v['berakhir'],0,10) : '';
              ?>
                <div class="vch-card">
                    <div class="vch-hd">
                        <div class="vch-code"><?= esc($kode) ?></div>
                        <span class="vch-badge">Diklaim</span>
                    </div>
                    <div class="vch-body"><b><?= esc($nama) ?></b></div>
                    <div class="vch-meta">
                        Tipe:
                        <?= $tipe==='persen' ? (number_format($nilai,0).'%') : ('Rp '.number_format($nilai,0,',','.')) ?>
                        <?php if($mulai || $akhir): ?> · Periode: <?= esc($mulai) ?> – <?= esc($akhir) ?><?php endif; ?>
                    </div>
                    <div class="vch-actions">
                        <button class="btn-chip primary" type="button"
                            onclick="salinKode('<?= esc($kode, 'attr') ?>')">Salin Kode</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php elseif (count($claimedIds) > 0 && count($voucherClaimedDetail) === 0 && count($voucherUser)===0): ?>
                <?php foreach ($claimedIds as $cid): ?>
                <div class="vch-card">
                    <div class="vch-hd">
                        <div class="vch-code">#<?= (int)$cid ?></div>
                        <span class="vch-badge">Diklaim</span>
                    </div>
                    <div class="vch-body"><b>Voucher diklaim</b></div>
                    <div class="vch-meta">Detail voucher akan tampil saat checkout.</div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <!-- ====== End Voucher ====== -->

        <!-- Add Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="/addaddress" method="post">
                <input type="text" value="account" name="checkpage" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title" id="exampleModalLabel">Alamat Baru</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="pb-3 border-bottom">
                                <h5 class="mb-2">Informasi Pemesan</h5>
                                <div class="form-floating mb-1">
                                    <input type="email" class="form-control" placeholder="Email" name="emailPem"
                                        required value="<?= esc($email); ?>">
                                    <label for="floatingInput">Email</label>
                                </div>
                            </div>

                            <div class="py-3">
                                <h5 class="mb-2">Informasi Penerima</h5>
                                <div class="form-floating mb-1">
                                    <input type="text" class="form-control" placeholder="Nama" name="nama" required
                                        value="<?= esc($nama); ?>">
                                    <label for="floatingInput">Nama Lengkap</label>
                                </div>
                                <div class="form-floating mb-1">
                                    <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp"
                                        required value="<?= esc($nohp); ?>">
                                    <label for="floatingInput">No. HP (WA)</label>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="provinsi"
                                            required>
                                            <option value="">-- Pilih provinsi --</option>
                                            <?php if (!empty($provinsi) && is_array($provinsi)): ?>
                                            <?php foreach ($provinsi as $p):
                                                    $pid   = $p['province_id'] ?? ($p['id'] ?? ($p['value'] ?? ''));
                                                    $pname = $p['province']    ?? ($p['label'] ?? ($p['name'] ?? ''));
                                                    $pname = explode('/', (string)$pname)[0];
                                                ?>
                                            <option value="<?= esc($pid) ?>-<?= esc($pname) ?>">
                                                <?= esc($pname) ?>
                                            </option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <label for="floatingProvinsi">Provinsi</label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kota"
                                            required>
                                            <option value="">-- Pilih kota --</option>
                                        </select>
                                        <label for="floatingProvinsi">Kabupaten/Kota</label>
                                    </div>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kecamatan"
                                            required>
                                            <option value="">-- Pilih kecamatan --</option>
                                        </select>
                                        <label for="floatingProvinsi">Kecamatan</label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kodepos"
                                            required>
                                            <option value="">-- Pilih Desa --</option>
                                        </select>
                                        <label for="floatingProvinsi">Desa/Kelurahan</label>
                                    </div>
                                </div>

                                <div class="form-alamat form-floating mb-1">
                                    <input type="text" class="form-control" placeholder="Alamat" name="alamat_add"
                                        required>
                                    <label for="floatingInput">Jalan, Nomor Rumah, RT-RW</label>
                                    <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-lonjong">Tambah</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form method="post" id="formEdit">
                <input type="text" value="<?= $_SERVER['REQUEST_URI']; ?>" class="d-none" name="url">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title" id="exampleModalLabel">Edit Alamat</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="pb-3 border-bottom">
                                <h5 class="mb-2">Informasi Pemesan</h5>
                                <div class="form-floating mb-1">
                                    <input type="email" class="form-control" placeholder="Email" name="emailPem"
                                        required id="inputEmail">
                                    <label for="floatingInput">Email</label>
                                </div>
                            </div>

                            <div class="py-3">
                                <h5 class="mb-2">Informasi Penerima</h5>
                                <div class="form-floating mb-1">
                                    <input type="text" class="form-control" placeholder="Nama" name="nama" required
                                        id="inputNama">
                                    <label for="floatingInput">Nama Lengkap</label>
                                </div>
                                <div class="form-floating mb-1">
                                    <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp"
                                        required id="inputNohp">
                                    <label for="floatingInput">No. HP (WA)</label>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example"
                                            name="provinsiEdit" required>
                                            <option value="">-- Pilih provinsi --</option>
                                            <?php if (!empty($provinsi) && is_array($provinsi)): ?>
                                            <?php foreach ($provinsi as $p):
                                                    $pid   = $p['province_id'] ?? ($p['id'] ?? ($p['value'] ?? ''));
                                                    $pname = $p['province']    ?? ($p['label'] ?? ($p['name'] ?? ''));
                                                    $pname = explode('/', (string)$pname)[0];
                                                ?>
                                            <option value="<?= esc($pid) ?>-<?= esc($pname) ?>">
                                                <?= esc($pname) ?>
                                            </option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <label for="floatingProvinsi">Provinsi</label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kotaEdit"
                                            required>
                                            <option value="">-- Pilih kota --</option>
                                        </select>
                                        <label for="floatingProvinsi">Kabupaten/Kota</label>
                                    </div>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example"
                                            name="kecamatanEdit" required>
                                            <option value="">-- Pilih kecamatan --</option>
                                        </select>
                                        <label for="floatingProvinsi">Kecamatan</label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example"
                                            name="kodeposEdit" required>
                                            <option value="">-- Pilih Desa --</option>
                                        </select>
                                        <label for="floatingProvinsi">Desa/Kelurahan</label>
                                    </div>
                                </div>

                                <div class="form-alamat form-floating mb-1">
                                    <input type="text" class="form-control" placeholder="Alamat" name="alamat_add"
                                        required>
                                    <label for="floatingInput">Jalan, Nomor Rumah, RT-RW</label>
                                    <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-lonjong">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</div>

<script>
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');

const provElmEdit = document.querySelector('select[name="provinsiEdit"]');
const kotaElmEdit = document.querySelector('select[name="kotaEdit"]');
const kecElmEdit = document.querySelector('select[name="kecamatanEdit"]');
const kodeElmEdit = document.querySelector('select[name="kodeposEdit"]');

const inputEmailElm = document.getElementById("inputEmail");
const inputNamaElm = document.getElementById("inputNama");
const inputNohpElm = document.getElementById("inputNohp");
const formEditElm = document.getElementById("formEdit");

const alamatJson = JSON.parse('<?= $alamatJson ?>');
const sandiElm = document.querySelector('input[name="sandi"]');
const editSandiElm = document.getElementById('edit-sandi');

function openGantiSandi() {
    editSandiElm.classList.remove('d-none');
    editSandiElm.classList.add('d-flex');
}

function closeEditSandi() {
    editSandiElm.classList.add('d-none');
    editSandiElm.classList.remove('d-flex');
}

function konfirmasiSandi(e) {
    if (sandiElm.value != e.target.value) e.target.classList.add('is-invalid');
    else e.target.classList.remove('is-invalid');
}

function titleCase(str = '') {
    return String(str).toLowerCase().split(' ').map(s => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
}

/* ======= Helper: Salin Kode Voucher ======= */
async function salinKode(k) {
    try {
        await navigator.clipboard.writeText(k);
        alert('Kode ' + k + ' disalin.');
    } catch (e) {
        alert('Gagal menyalin kode.');
    }
}

/* ---------- FETCH DAERAH (ADD) ---------- */
/* gunakan path absolut supaya tidak gagal saat berada di sub-route */
async function getKota(idprov) {
    try {
        const res = await fetch(`/getkota/${idprov}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
        kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(el => {
            const id = el.city_id ?? el.id ?? el.value;
            const nama0 = el.city_name ?? el.label ?? el.name ?? '';
            const nama = String(nama0).split('/')[0];
            const type = el.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = id + "-" + nama;
            opt.innerHTML = el.city_name ? `${nama}${type}` : nama;
            kotaElm.appendChild(opt);
        });
    } catch (e) {
        kotaElm.innerHTML = '<option value="">(Gagal memuat kota)</option>';
        console.error(e);
    }
}
async function getKec(idkota) {
    try {
        const res = await fetch(`/getkec/${idkota}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(el => {
            const id = el.subdistrict_id ?? el.id ?? el.value;
            const nama = String(el.subdistrict_name ?? el.label ?? el.name ?? '').split('/')[0];
            const opt = document.createElement("option");
            opt.value = id + "-" + nama; // simpan "id-nama"
            opt.innerHTML = nama;
            kecElm.appendChild(opt);
        });
    } catch (e) {
        kecElm.innerHTML = '<option value="">(Gagal memuat kecamatan)</option>';
        console.error(e);
    }
}
async function getKode(kecId) {
    try {
        const res = await fetch(`/getkode/${kecId}`); // kirim ID kecamatan
        const json = await res.json();
        const hasil = json?.results ?? json?.data?.results ?? json ?? [];
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(el => {
            const desa = titleCase(String(el.DesaKelurahan ?? el.desa ?? el.label ?? el.name ?? '').split(
                '/')[0]);
            const kp = String(el.KodePos ?? el.kodepos ?? el.zip ?? '');
            const opt = document.createElement("option");
            opt.value = desa + "-" + kp;
            opt.innerHTML = desa;
            kodeElm.appendChild(opt);
        });
    } catch (e) {
        kodeElm.innerHTML = '<option value="">(Gagal memuat desa)</option>';
        console.error(e);
    }
}
provElm?.addEventListener("change", (e) => {
    kotaElm.innerHTML = '<option value="">Loading..</option>';
    kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const idprov = Number(String(e.target.value || '').split("-")[0]);
    if (idprov > 0) getKota(idprov);
});
kotaElm?.addEventListener("change", (e) => {
    kecElm.innerHTML = '<option value="">Loading..</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const idkota = Number(String(e.target.value || '').split("-")[0]);
    if (idkota > 0) getKec(idkota);
});
kecElm?.addEventListener("change", (e) => {
    kodeElm.innerHTML = '<option value="">Loading..</option>';
    const idkec = String(e.target.value || '').split("-")[0]; // kirim ID kecamatan ke endpoint
    if (idkec) getKode(idkec);
});

/* ---------- FETCH DAERAH (EDIT) ---------- */
async function getKotaEdit(idprov) {
    try {
        const res = await fetch(`/getkota/${idprov}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kotaElmEdit.innerHTML = '<option value="">-- Pilih kota --</option>';
        kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(el => {
            const id = el.city_id ?? el.id ?? el.value;
            const nama0 = el.city_name ?? el.label ?? el.name ?? '';
            const nama = String(nama0).split('/')[0];
            const type = el.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = id + "-" + nama;
            opt.innerHTML = el.city_name ? `${nama}${type}` : nama;
            kotaElmEdit.appendChild(opt);
        });
    } catch (e) {
        kotaElmEdit.innerHTML = '<option value="">(Gagal memuat kota)</option>';
        console.error(e);
    }
}
async function getKecEdit(idkota) {
    try {
        const res = await fetch(`/getkec/${idkota}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(el => {
            const id = el.subdistrict_id ?? el.id ?? el.value;
            const nama = String(el.subdistrict_name ?? el.label ?? el.name ?? '').split('/')[0];
            const opt = document.createElement("option");
            opt.value = id + "-" + nama; // simpan "id-nama"
            opt.innerHTML = nama;
            kecElmEdit.appendChild(opt);
        });
    } catch (e) {
        kecElmEdit.innerHTML = '<option value="">(Gagal memuat kecamatan)</option>';
        console.error(e);
    }
}
async function getKodeEdit(kecId) {
    try {
        const res = await fetch(`/getkode/${kecId}`);
        const json = await res.json();
        const hasil = json?.results ?? json?.data?.results ?? json ?? [];
        kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
        hasil.forEach(el => {
            const desa = titleCase(String(el.DesaKelurahan ?? el.desa ?? el.label ?? el.name ?? '').split(
                '/')[0]);
            const kp = String(el.KodePos ?? el.kodepos ?? el.zip ?? '');
            const opt = document.createElement("option");
            opt.value = desa + "-" + kp;
            opt.innerHTML = desa;
            kodeElmEdit.appendChild(opt);
        });
    } catch (e) {
        kodeElmEdit.innerHTML = '<option value="">(Gagal memuat desa)</option>';
        console.error(e);
    }
}
provElmEdit?.addEventListener("change", (e) => {
    kotaElmEdit.innerHTML = '<option value="">Loading..</option>';
    kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const idprov = Number(String(e.target.value || '').split("-")[0]);
    if (idprov > 0) getKotaEdit(idprov);
});
kotaElmEdit?.addEventListener("change", (e) => {
    kecElmEdit.innerHTML = '<option value="">Loading..</option>';
    kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const idkota = Number(String(e.target.value || '').split("-")[0]);
    if (idkota > 0) getKecEdit(idkota);
});
kecElmEdit?.addEventListener("change", (e) => {
    kodeElmEdit.innerHTML = '<option value="">Loading..</option>';
    const idkec = String(e.target.value || '').split("-")[0]; // kirim ID kecamatan
    if (idkec) getKodeEdit(idkec);
});

/* ---------- EDIT modal bind ---------- */
function openEditModal(ind_add) {
    const a = (<?= $alamatJson ?>)[ind_add] || {};
    inputEmailElm.value = a.email_pemesan || '';
    inputNamaElm.value = a.nama_penerima || '';
    inputNohpElm.value = a.nohp_penerima || '';
    formEditElm.action = "/editaddress/" + ind_add;
}
</script>

<?= $this->endSection(); ?>