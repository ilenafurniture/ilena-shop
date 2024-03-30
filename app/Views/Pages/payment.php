<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;"><a href="/address" class="me-3 text-secondary" style="text-decoration: none;">Alamat</a> >
            <a href="/shipping" class="mx-3 text-secondary" style="text-decoration: none;">Kurir</a> > <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">
                Pembayaran</a>
        </h5>
        <div class="my-4">
            <p class="teks-sedang">Metode Pembayaran</p>
            <div class="container-pembayaran mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="false" aria-controls="collapseExample1">
                    Transfer Bank
                </div>
                <div class="collapse py-2" id="collapseExample1">
                    <input type="radio" name="pembayaran" id="pembayaran1">
                    <label for="pembayaran1" class="item-logo-pembayaran"><img src="/img/pembayaran/bca.png" alt=""></label>
                    <input type="radio" name="pembayaran" id="pembayaran2">
                    <label for="pembayaran2" class="item-logo-pembayaran"><img src="/img/pembayaran/bni.webp" alt=""></label>
                    <input type="radio" name="pembayaran" id="pembayaran3">
                    <label for="pembayaran3" class="item-logo-pembayaran"><img src="/img/pembayaran/bri.png" alt=""></label>
                    <input type="radio" name="pembayaran" id="pembayaran4">
                    <label for="pembayaran4" class="item-logo-pembayaran"><img src="/img/pembayaran/mandiri.png" alt=""></label>
                </div>
            </div>
            <div class="container-pembayaran">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="false" aria-controls="collapseExample2">
                    Credit Card
                </div>
                <div class="collapse py-2" id="collapseExample2">
                    <input type="radio" name="pembayaran" id="pembayaran5">
                    <label for="pembayaran5" class="item-logo-pembayaran"><img src="/img/pembayaran/visa.png" alt=""></label>
                    <input type="radio" name="pembayaran" id="pembayaran6">
                    <label for="pembayaran6" class="item-logo-pembayaran"><img src="/img/pembayaran/jcb.png" alt=""></label>
                    <input type="radio" name="pembayaran" id="pembayaran7">
                    <label for="pembayaran7" class="item-logo-pembayaran"><img src="/img/pembayaran/mastercard.png" alt=""></label>
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
                    Rp 5,000.000
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
                    Pengiriman
                </p>
                <p class="fw-bold m-0">
                    Rp 15,000
                </p>
            </div>
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp 5,500.000
                </p>
            </div>
            <a href="/payment" class="btn-default-merah w-100 mt-4 text-center">Bayar</a>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>