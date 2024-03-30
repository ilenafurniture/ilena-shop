<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;"><a href="/address" class="me-3 text-secondary" style="text-decoration: none;">Alamat</a> >
            <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">Kurir</a> > <a class="mx-3 text-secondary" style="text-decoration: none;">
                Pembayaran</a>
        </h5>
        <div class="container-kurir my-4">
            <input type="radio" name="kurir" id="kurir1">
            <label for="kurir1" class="item-kurir">
                <div style="flex: 1;">
                    <p class="mb-1 nama">JNE Ongkos Kirim Ekonomis</p>
                    <p class="mb-1">Estimasi pengiriman 3-5 Hari</p>
                    <p class="mb-1" style="font-weight: 600;">Rp 13.000</p>
                </div>
                <div style="width:fit-content">
                    <img src="/img/kurir/jne.png" alt="">
                </div>
            </label>

            <input type="radio" name="kurir" id="kurir2">
            <label for="kurir2" class="item-kurir">
                <div style="flex: 1;">
                    <p class="mb-1 nama">J&T Regular Service</p>
                    <p class="mb-1">Estimasi pengiriman 1-2 Hari</p>
                    <p class="mb-1" style="font-weight: 600;">Rp 15.000</p>
                </div>
                <div style="width:fit-content">
                    <img src="/img/kurir/J&T.png" alt="">
                </div>
            </label>
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
            <a href="/payment" class="btn-default-merah w-100 mt-4 text-center">Pembayaran</a>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>