<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;"><a class="me-3 text-dark fw-bold" style="text-decoration: none;">Alamat</a> >
            <a class="mx-3 text-secondary" style="text-decoration: none;">Kurir</a> > <a class="mx-3 text-secondary" style="text-decoration: none;">
                Pembayaran</a>
        </h5>
        <div class="container-address my-4">
            <input type="radio" name="address" id="address1">
            <label for="address1" class="item-address">
                <div style="flex: 1;">
                    <p class="mb-1 nama">Nama Penerima</p>
                    <p class="mb-1">Alamat Penerima</p>
                    <p class="mb-1">0812313512361</p>
                    <p class="mb-1"><b style="font-weight: 600;">Pemesan</b> : galihsuks@gmail.com</p>
                </div>
                <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                    <a href="#" class="btn-teks-aja text-dark">Edit</a>
                    <a href="#" class="btn-teks-aja">Hapus</a>
                </div>
            </label>

            <input type="radio" name="address" id="address2">
            <label for="address2" class="item-address">
                <div style="flex: 1;">
                    <p class="mb-1 nama">Nama Penerima</p>
                    <p class="mb-1">Alamat Penerima</p>
                    <p class="mb-1">0812313512361</p>
                    <p class="mb-1"><b style="font-weight: 600;">Pemesan</b> : galihsuks@gmail.com</p>
                </div>
                <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                    <a href="#" class="btn-teks-aja text-dark">Edit</a>
                    <a href="#" class="btn-teks-aja">Hapus</a>
                </div>
            </label>
        </div>
        <button type="button" class="btn-teks-aja" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="material-icons">add</i> Tambah Alamat</button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <input type="email" class="form-control" placeholder="Email" name="emailPem" required value="<?= session()->getFlashdata('emailPem') ? session()->getFlashdata('emailPem') : ''; ?>">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                        <div class="py-3">
                            <h5 class="mb-2">Informasi Penerima</h5>
                            <div class="form-floating mb-1">
                                <input type="text" class="form-control" placeholder="Email" name="nama" required>
                                <label for="floatingInput">Nama Lengkap</label>
                            </div>
                            <div class="form-floating mb-1">
                                <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp" required>
                                <label for="floatingInput">No. HP</label>
                            </div>
                            <div class="form-alamat d-flex mb-1 gap-1">
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="provinsi">
                                        <option value="-1">-- Pilih provinsi --</option>
                                        <?php foreach ($provinsi as $p) { ?>
                                            <option value="<?= $p['province_id']; ?>-<?= $p['province']; ?>"><?= $p['province']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <label for="floatingProvinsi">Provinsi</label>
                                </div>
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="kota">
                                        <option value="-1">-- Pilih kota --</option>
                                    </select>
                                    <label for="floatingProvinsi">Kabupaten/Kota</label>
                                </div>
                            </div>
                            <div class="form-alamat d-flex mb-1 gap-1">
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="kecamatan">
                                        <option selected value="-1">-- Pilih kecamatan --</option>
                                    </select>
                                    <label for="floatingProvinsi">Kecamatan</label>
                                </div>
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="kodepos">
                                        <option value="-1">-- Pilih Desa --</option>
                                    </select>
                                    <label for="floatingProvinsi">Desa/Kelurahan</label>
                                </div>
                            </div>
                            <div class="form-alamat form-floating mb-1">
                                <input type="text" class="form-control" placeholder="Email" name="alamat_add" required>
                                <label for="floatingInput">Jalan, Nomor Rumah, RT-RW</label>
                                <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-lonjong">Tambah</button>
                    </div>
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
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp 5,500.000
                </p>
            </div>
            <a href="/shipping" class="btn-default-merah w-100 mt-4 text-center">Pilih Kurir</a>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>