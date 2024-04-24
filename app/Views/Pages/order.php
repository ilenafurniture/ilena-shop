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
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp
                </p>
            </div>
        </div>
    </div>
    <div style="flex:1;">
        <div class="mb-4">
            <h1 class="teks-sedang">Pesanan</h1>
            <p style="color: grey;"><?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pemesanan</p>
        </div>
        <div class="container-table">
            <div class="header-table">
                <div>ID Pesanan</div>
                <div>Tanggal</div>
                <div>Penerima</div>
                <div>Harga</div>
                <div>Status</div>
            </div>
            <div class="isi-table">
                <div>IL00001</div>
                <div>17 Okt 2023</div>
                <div>Galih Sukmamukti</div>
                <div>Rp 120.000</div>
                <div>Menunggu Pembayaran</div>
            </div>
            <div class="isi-table">
                <div>IL00001</div>
                <div>17 Okt 2023</div>
                <div>Galih Sukmamukti</div>
                <div>Rp 120.000</div>
                <div>Menunggu Pembayaran</div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>