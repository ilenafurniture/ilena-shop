<?= $this->extend("market/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <form action="/maket/submitorder" method="post">
        <div class="d-flex gap-4">
            <div class="limapuluh-ke-seratus">
                <h1 class="teks-sedang mb-3">Informasi Pesanan</h1>
                <div class="form-floating mb-1">
                    <input type="date" class="form-control" id="floatingInput6" placeholder="" name="tanggal" required>
                    <label for="floatingInput6">tanggal pembelian</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com"
                        name="email" required>
                    <label for="floatingInput">Email</label>
                </div>
                <hr>
                <h1 class="teks-sedang mb-3">Informasi Ekpedisi</h1>
                <div class="form-floating mb-1">
                    <select type="text" class="form-control" id="floatingInput4" name="nama_ekspedisi">
                        <option value="indah">Indah Cargo</option>
                        <option value="sentral">Sentral Cargo</option>
                        <option value="jne">JNE Cargo</option>
                        <option value="jnt">J&T Cargo</option>
                        <option value="dakota">Dakota Cargo</option>
                    </select>
                    <label for="floatingInput4">Kurir</label>
                </div>
                <div class="form-floating mb-1">
                    <input type="text" class="form-control" id="floatingInput5" placeholder="" name="jenis_kurir"
                        required>
                    <label for="floatingInput5">Jenis Kurir</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput6" placeholder="" name="no_resi" required>
                    <label for="floatingInput6">No resi</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput6" placeholder="" name="harga_pengiriman"
                        required>
                    <label for="floatingInput6">Harga Pengiriman</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput6" placeholder="" name="estimasi" required>
                    <label for="floatingInput6">Estimasi</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput6" placeholder="" name="id_marketplace"
                        required>
                    <label for="floatingInput6">Id Marketplace</label>
                </div>
            </div>

            <div class="limapuluh-ke-seratus">
                <h1 class="teks-sedang mb-3">Informasi Penerima</h1>
                <div class="form-floating mb-1">
                    <input type="text" class="form-control" id="floatingInput1" placeholder="Nama Lengkap"
                        name="nama_lengkap" required>
                    <label for="floatingInput1">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-1">
                    <input type="number" class="form-control" id="floatingInput2" placeholder="No Hp" name="no_hp"
                        required>
                    <label for="floatingInput2">No hp</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea type="text" class="form-control" id="floatingInput3" placeholder="" name="alamat_lengkap"
                        required></textarea>
                    <label for="floatingInput3">Alamat Lengkap</label>
                </div>
                <hr>

                <h1 class="teks-sedang mb-3">Produk yang dipesan</h1>
                <div class="container-table">
                    <div class="header-table ">
                        <div style="flex:3;">Items</div>
                        <div style="flex:1;">Jumlah</div>
                    </div>
                    <?php foreach ($keranjang as $k) { ?>
                    <div class="isi-table">
                        <div style="flex:3;"><?= $k['nama'] ?> (<?= $k['varian'] ?>)</div>
                        <div style="flex:1;"><?= $k['jumlah'] ?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <button class="btn-default-merah mt-2" type="submit">Kirim</button>
    </form>
</div>

<?= $this->endSection(); ?>