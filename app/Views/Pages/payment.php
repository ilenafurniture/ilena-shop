<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;"><a href="/address" class="me-3 text-secondary"
                style="text-decoration: none;">Alamat</a> >
            <a href="/shipping/<?= $indKurir; ?>" class="mx-3 text-secondary" style="text-decoration: none;">Kurir</a> >
            <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">
                Rincian Pembayaran</a>
        </h5>
        <div class="my-4">
            <h4 style="letter-spacing: -1px">Informasi pembeli</h4>
            <hr>
            <div class="d-flex">
                <div style="flex:1" class="my-2">
                    <p class="fw-normal">Nama</p>
                    <p class="fw-normal">No Handphone</p>
                    <p class="fw-normal">Email</p>
                    <p class="fw-normal">Alamat</p>
                </div>
                <div style="flex:4" class="my-2">
                    <p class="fw-bold">: <?= $user['nama'] ?></p>
                    <p class="fw-bold">: <?= $user['no_hp'] ?></p>
                    <p class="fw-bold">: <?= $user['email'] ?></p>
                    <p class="fw-bold">: <?= $user['alamat'] ?></p>
                </div>
            </div>
            <hr>
            <h4 style="letter-spacing: -1px">Informasi barang</h4>
            <hr>
            <?php foreach ($keranjang as $index_k => $k) { ?>
            <div class="d-flex gap-3 m-2">
                <img src="<?= $k['src_gambar'] ?>" style="width:100px; height:100px; border-radius:8px;"
                    alt=" gambar-produk">
                <div class="d-flex gap-2">
                    <div class="my-2">
                        <p class="m-0 fw-normal">Nama</p>
                        <p class="m-0 fw-normal">Varian</p>
                        <p class="m-0 fw-normal">Jumlah</p>
                    </div>
                    <div class="my-2">
                        <p class="m-0 fw-bold">: <?= $k['detail']['nama'] ?></p>
                        <p class="m-0 fw-bold">: <?= $k['varian'] ?></p>
                        <p class="m-0 fw-bold">: <?= $k['jumlah'] ?> Buah</p>
                    </div>
                </div>
            </div>
            <?php } ?>

            <hr>
            <h4 style="letter-spacing: -1px">Informasi kurir</h4>
            <hr>
            <div class="d-flex">
                <div style="flex:1" class="my-2">
                    <p class="fw-normal">Expedisi</p>
                    <p class="fw-normal">Paket kurir</p>
                    <p class="fw-normal">Estimasi</p>
                </div>
                <div style="flex:4" class="my-2">
                    <p class="fw-bold">: <?= strtoupper($kurir['nama']) ?></p>
                    <p class="fw-bold">: <?= $kurir['deskripsi'] ?></p>
                    <p class="fw-bold">: <?= $kurir['estimasi'] ?> Hari</p>
                </div>
            </div>
            <hr>
        </div>
    </div>
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h4 style="letter-spacing: -1px">Pesanan</h4>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    Harga
                </p>
                <p class="fw-bold m-0">
                    Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Biaya Admin
                </p>
                <p class="fw-bold m-0">
                    Rp 5,000
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Biaya Ongkir
                </p>
                <p class="fw-bold m-0">
                    Rp <?= number_format($hargaOngkir, 0, ',', '.'); ?>
                </p>
            </div>
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp <?= number_format($hargaKeseluruhan, 0, ',', '.'); ?>
                </p>
            </div>
            <a id="btn-bayar" class="btn-default-merah disabled w-100 mt-4 text-center">Bayar</a>
        </div>
    </div>
</div>

<script>
const radioPembayaranElm = document.querySelectorAll('input[name="pembayaran"]');
const btnBayarElm = document.getElementById('btn-bayar');

radioPembayaranElm.forEach(element => {
    element.addEventListener('change', (e) => {
        btnBayarElm.href = "/actionpay/" + e.target.value;
        btnBayarElm.classList.remove('disabled');
    })
});
</script>

<?= $this->endSection(); ?>