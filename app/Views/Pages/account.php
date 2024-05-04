<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h2 style="letter-spacing: -1px">Akun Saya</h2>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    Email
                </p>
                <p class="fw-bold m-0">
                    galihsuks@gmail.com
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Sandi
                </p>
                <a href="" class="btn-teks-aja">Ganti Sandi</a>
            </div>
            <span class="garis my-2"></span>
            <!-- <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp
                </p>
            </div> -->
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
            <p class="m-0">
                Email
            </p>
            <p class="fw-bold m-0">
                galihsuks@gmail.com
            </p>
        </div>
        <div class="d-flex justify-content-between py-1">
            <p class="m-0">
                Nama Lengkap
            </p>
            <p class="fw-bold m-0">
                Galih Sukmamukti H.
            </p>
        </div>
        <div class="mb-4 d-flex justify-content-between py-1">
            <p class="m-0">
                Sandi
            </p>
            <a href="" class="btn-teks-aja">Ganti Sandi</a>
        </div>
        <span class="garis"></span>
        <div class="my-4">
            <h1 class="teks-sedang">Alamat</h1>
            <p style="color: grey;">2 Tujuan yang dapat dipilih</p>
        </div>
        <div class="container-address my-4">
            <?php foreach ($alamat as $ind_a => $a) { ?>
                <div for="address<?= $ind_a ?>" class="item-address">
                    <div style="flex: 1;">
                        <p class="mb-1 nama"><?= $a['nama_penerima'] ?></p>
                        <p class="mb-1"><?= $a['alamat_lengkap'] ?></p>
                        <p class="mb-1"><?= $a['nohp_penerima'] ?></p>
                        <p class="mb-1"><b style="font-weight: 600;">Pemesan</b> : <?= $a['email_pemesan'] ?></p>
                    </div>
                    <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                        <a href="#" class="btn-teks-aja text-dark">Edit</a>
                        <a href="/deleteaddress/<?= $ind_a; ?>" class="btn-teks-aja">Hapus</a>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button type="button" class="btn-teks-aja" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="material-icons">add</i> Tambah Alamat</button>
    </div>
</div>

<?= $this->endSection(); ?>