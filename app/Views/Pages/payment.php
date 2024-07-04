<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;" class="path"><a href="/address" class="me-3 text-secondary" style="text-decoration: none;">Alamat</a> >
            <a href="/shipping/<?= $indKurir; ?>" class="mx-3 text-secondary" style="text-decoration: none;">Kurir</a> >
            <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">
                Rincian Pembayaran</a>
        </h5>
        <div class="my-4">
            <div class="container-pembayaran mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample3" aria-expanded="true" aria-controls="collapseExample3">
                    Informasi Pembeli
                </div>
                <div class="collapse py-2 show" id="collapseExample3">
                    <hr>
                    <div class="d-flex">
                        <div style="flex:1" class="my-2">
                            <p class="m-0 fw-normal">Nama</p>
                            <p class="m-0 fw-normal">No Handphone</p>
                            <p class="m-0 fw-normal">Email</p>
                            <p class="m-0 fw-normal">Alamat</p>
                        </div>
                        <div style="flex:4" class="my-2">
                            <p class="m-0 fw-bold">: <?= $user['nama'] ?></p>
                            <p class="m-0 fw-bold">: <?= $user['no_hp'] ?></p>
                            <p class="m-0 fw-bold">: <?= $user['email'] ?></p>
                            <p class="m-0 fw-bold">: <?= $user['alamat'] ?></p>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>

            <div class="container-pembayaran mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="true" aria-controls="collapseExample2">
                    Informasi Barang
                </div>
                <div class="collapse py-2 show" id="collapseExample2">
                    <hr>
                    <?php foreach ($keranjang as $index_k => $k) { ?>
                        <div class="d-flex gap-3 m-2">
                            <img src="<?= $k['src_gambar'] ?>" style="width:100px; height:100px; border-radius:8px;" alt=" gambar-produk">
                            <div class="d-flex gap-3">
                                <div class="my-2">
                                    <p class="m-0 fw-normal">Nama</p>
                                    <p class="m-0 fw-normal">Varian</p>
                                    <p class="m-0 fw-normal">Jumlah</p>
                                    <p class="m-0 fw-normal">Harga Satuan</p>
                                </div>
                                <div class="my-2">
                                    <p class="m-0 fw-bold">: <?= $k['detail']['nama'] ?></p>
                                    <p class="m-0 fw-bold">: <?= $k['varian'] ?></p>
                                    <p class="m-0 fw-bold">: <?= $k['jumlah'] ?> Buah</p>
                                    <p class="m-0 fw-bold">: Rp
                                        <?= number_format((int)$k['detail']['harga'] * (100 - (float)$k['detail']['diskon']) / 100, 0, ',', '.'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <hr>
                </div>
            </div>

            <div class="container-pembayaran mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="true" aria-controls="collapseExample1">
                    Informasi Kurir
                </div>
                <div class="collapse py-2 show" id="collapseExample1">
                    <hr>
                    <div class="d-flex">
                        <div style="flex:1" class="my-2">
                            <p class="m-0 fw-normal">Expedisi</p>
                            <p class="m-0 fw-normal">Paket kurir</p>
                            <p class="m-0 fw-normal">Estimasi</p>
                        </div>
                        <div style="flex:4" class="my-2">
                            <p class="m-0 fw-bold">: <?= strtoupper($kurir['nama']) ?></p>
                            <p class="m-0 fw-bold">: <?= $kurir['deskripsi'] ?></p>
                            <p class="m-0 fw-bold">: <?= $kurir['estimasi'] ?> Hari</p>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>

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
            <button onclick="bayar(event)" class="btn-default-merah  w-100 mt-4 text-center">Bayar</button>
        </div>
    </div>
</div>

<script>
    function bayar(e) {
        // console.log(e.target);
        e.target.innerHTML = "Loading";
        async function getToken() {
            const res = await fetch('../actionpaysnap', {
                method: 'POST',
                headers: {
                    'content-type': 'application/json'
                },
                body: JSON.stringify({
                    content: '<?= $dataMidJson ?>'
                })
            })
            const snapToken = await res.json();
            console.log(snapToken);
            window.snap.pay(snapToken.token);
        }
        getToken();
    }
</script>

<?= $this->endSection(); ?>