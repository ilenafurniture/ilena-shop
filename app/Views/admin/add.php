<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <h1 class="teks-sedang mb-3">Tambah Produk</h1>
    <form method="post" action="/addproduct" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="baris-ke-kolom">
            <div class="limapuluh-ke-seratus">
                <table class="table-input w-100">
                    <tbody>
                        <tr>
                            <td>Nama Produk</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="nama" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Harga Produk</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="harga" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Diskon Produk</td>
                            <td>
                                <div class="baris">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="diskon" step="any" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Stok</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="stok" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="kategori" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Sub Kategori</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="subkategori" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Shopee</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="shopee"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Tokopedia</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tokped"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Deskripsi</td>
                            <td>
                                <div class="baris"><textarea type="text" class="form-control" name="deskripsi"
                                        required></textarea></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Perawatan</td>
                            <td>
                                <div class="baris"><textarea type="text" class="form-control" name="perawatan"
                                        required></textarea></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="mt-4">Bentuk Paket</h5>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Panjang (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <td>Dimensi Lebar (cm)</td>
                        <td>
                            <div class="baris"><input type="text" class="form-control" name="dimensi" required>
                            </div>
                        </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="berat" step="any"
                                        required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h5 class="mt-4">Bentuk Asli</h5>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Panjang (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <td>Dimensi Lebar (cm)</td>
                        <td>
                            <div class="baris"><input type="text" class="form-control" name="dimensi" required>
                            </div>
                        </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="berat" step="any"
                                        required>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class=" show-flex-ke-hide mt-4 justify-content-center gap-2">
                    <button class="btn-default" type="submit">Simpan</button>
                </div>
            </div>
            <div class="limapuluh-ke-seratus">
                <h5 class="jdl-section">Varian</h5>
                <div id="container-varian">
                    <div class="item-varian">
                        <div class="container-gambar">
                            <div class="item-gambar">
                                <p>X</p>
                                <img src="https://plus.unsplash.com/premium_photo-1669048776605-28ea2e52ae66?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt="">
                            </div>
                            <div class="item-gambar">
                                <p>X</p>
                                <img src="https://plus.unsplash.com/premium_photo-1669048776605-28ea2e52ae66?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt="">
                            </div>
                            <div id="container-input-gambar">
                                <input type="file" id="gambar1-1" style="display: none;"
                                    onchange="fileTerupload(event)">
                                <label for="gambar1-1" class="btn-default">+</label>
                            </div>
                        </div>
                        <table class="table-input w-100 mt-2">
                            <tbody>
                                <tr>
                                    <td>Nama</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control" name="nama-var1"
                                                required>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kode Warna</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control" name="kode-var1"
                                                required>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Stok</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control" name="stok-var1"
                                                required>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <button class="btn-default-merah mt-2" type="button">Tambah Varian</button>
            </div>
        </div>
        <div class="hide-ke-show-flex justify-content-center mt-3">
            <button class="btn-default" type="submit">Simpan</button>
        </div>
    </form>
</div>
<script>
function fileTerupload() {

}
</script>

<?= $this->endSection(); ?>