<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;"><a class="me-3 text-dark fw-bold"
                style="text-decoration: none;">Alamat</a> >
            <a class="mx-3 text-secondary" style="text-decoration: none;">Kurir</a> > <a class="mx-3 text-secondary"
                style="text-decoration: none;">
                Pembayaran</a>
        </h5>
        <div class="container-address my-4">
            <input type="radio" name="address" id="address1">
            <label for="address1" class="item-address">
                <div style="flex: 1;">
                    <p class="mb-1 nama">Nama Pemesan</p>
                    <p class="mb-1">Alamat Penerima</p>
                    <p class="mb-1">Kontak : 0812313512361</p>
                </div>
                <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                    <a href="#" class="btn-teks-aja text-dark">Edit</a>
                    <a href="#" class="btn-teks-aja">Hapus</a>
                </div>
            </label>

            <input type="radio" name="address" id="address2">
            <label for="address2" class="item-address">
                <div style="flex: 1;">
                    <p class="mb-1 nama">Nama Pemesan</p>
                    <p class="mb-1">Alamat Penerima</p>
                    <p class="mb-1">Kontak : 0812313512361</p>
                </div>
                <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                    <a href="#" class="btn-teks-aja text-dark">Edit</a>
                    <a href="#" class="btn-teks-aja">Hapus</a>
                </div>
            </label>
        </div>
        <button class="btn-teks-aja"><i class="material-icons">add</i> Tambah Alamat</button>
    </div>
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h4 style="letter-spacing: -1px">Pesanan</h4>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    harga
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
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp 5,500.000
                </p>
            </div>
            <a href="#" class="btn-default-merah w-100 mt-4 text-center">Checkout</a>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>