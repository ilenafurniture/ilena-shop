<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <h1 class="teks-sedang mb-3">Edit Produk</h1>
    <form method="post" action="/admin/editproduct" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="baris-ke-kolom">
            <div class="limapuluh-ke-seratus">
                <table class="table-input w-100">
                    <tbody>
                        <tr>
                            <td>Nama Produk</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="nama" required
                                        value="<?= $produk['nama']; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Harga Produk</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="harga" required
                                        value="<?= $produk['harga']; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Diskon Produk</td>
                            <td>
                                <div class="baris">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="diskon" step="any" required
                                            value="<?= $produk['diskon']; ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="kategori" required
                                        value="<?= $produk['kategori']; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Sub Kategori</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="subkategori" required
                                        value="<?= $produk['subkategori']; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Shopee</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="shopee"
                                        value="<?= $produk['shopee']; ?>"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Tokopedia</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tokped"
                                        value="<?= $produk['tokped']; ?>"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Deskripsi</td>
                            <td>
                                <div class="baris"><textarea type="text" class="form-control" name="deskripsi"
                                        required><?= $produk['deskripsi']['deskripsi']; ?></textarea></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Perawatan</td>
                            <td>
                                <div class="baris"><textarea type="text" class="form-control" name="perawatan"
                                        required><?= $produk['deskripsi']['perawatan']; ?></textarea></div>
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
                                <div class="baris"><input type="text" class="form-control" name="panjang-paket" required
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['panjang']; ?>">
                                </div>
                            </td>
                        </tr>
                        <td>Dimensi Lebar (cm)</td>
                        <td>
                            <div class="baris"><input type="text" class="form-control" name="lebar-paket" required
                                    value="<?= $produk['deskripsi']['dimensi']['paket']['lebar']; ?>">
                            </div>
                        </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tinggi-paket" required
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['tinggi']; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="berat-paket"
                                        step="any" required
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['berat']; ?>">
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
                                <div class="baris"><input type="text" class="form-control" name="panjang-asli" required
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['panjang']; ?>">
                                </div>
                            </td>
                        </tr>
                        <td>Dimensi Lebar (cm)</td>
                        <td>
                            <div class="baris"><input type="text" class="form-control" name="lebar-asli" required
                                    value="<?= $produk['deskripsi']['dimensi']['asli']['lebar']; ?>">
                            </div>
                        </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tinggi-asli" required
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['tinggi']; ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="berat-asli"
                                        step="any" required
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['berat']; ?>">
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
                    <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
                    <div class="item-varian">
                        <div class="container-gambar" id="container-gambar<?= $ind_v + 1; ?>">
                            <div id="container-input-gambar<?= $ind_v + 1; ?>">
                                <div>
                                    <input type="file" id="input-gambar-<?= $ind_v + 1; ?>-1"
                                        name="gambar-<?= $ind_v + 1; ?>-1" style="display: none;"
                                        onchange="uploadFile(event)">
                                    <label for="input-gambar-<?= $ind_v + 1; ?>-1" class="btn-default">+</label>
                                </div>
                            </div>
                            <?php foreach (explode(",", $v['urutan_gambar']) as $u) { ?>
                            <div class="item-gambar">
                                <p>X</p>
                                <img src="/viewvar/<?= $produk['id'] ?>/<?= $u; ?>" alt="">
                            </div>
                            <?php } ?>
                        </div>
                        <table class="table-input w-100 mt-2">
                            <tbody>
                                <tr>
                                    <td>Nama</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control"
                                                name="nama-var<?= $ind_v + 1; ?>" required value="<?= $v['nama']; ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kode Warna</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control"
                                                name="kode-var<?= $ind_v + 1; ?>" required value="<?= $v['kode']; ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Stok</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control"
                                                name="stok-var<?= $ind_v + 1; ?>" required value="<?= $v['stok']; ?>">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn-teks-aja m-0 ms-auto mt-1"
                            onclick="deleteVarian(event)">Hapus</button>
                    </div>
                    <?php } ?>
                </div>
                <button class="btn-default-merah mt-2" type="button" onclick="addVarian()">Tambah Varian</button>
            </div>
        </div>
        <div class="hide-ke-show-flex justify-content-center mt-3">
            <button class="btn-default" type="submit">Simpan</button>
        </div>
        <input type="text" name="hitung-varian" style="display: none;" value="1">
    </form>
</div>
<script>
let counterJmlVarian = 1;
const hitungVarianInputElm = document.querySelector('input[name="hitung-varian"]')

function buatElementDariHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild;
}

function uploadFile(event) {
    const eventTargetElm = event.target
    const varianNum = event.target.id.split("-")[2]
    const subVarianNum = event.target.id.split("-")[3]

    const inputBaru = '<div><input onchange="uploadFile(event)" type="file" id="input-gambar-' + varianNum + '-' + (
            Number(subVarianNum) + 1) + '" name="gambar-' + varianNum + '-' + (Number(subVarianNum) + 1) +
        '" style="display: none;"><label for="input-gambar-' + varianNum + '-' + (Number(subVarianNum) + 1) +
        '" class="btn-default">+</label></div>'
    const inputBaruElm = buatElementDariHTML(inputBaru);
    const containerInputGambar = document.getElementById("container-input-gambar" + varianNum)
    containerInputGambar.append(inputBaruElm);
    const parentNode = event.target.parentNode;
    console.log(parentNode)

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    parentNode.style.padding = "5px";
    parentNode.style.backgroundColor = getRandomColor();
    // parentNode.style.display = "none";

    const file = eventTargetElm.files[0];
    const blobFile = new Blob([file], {
        type: file.type
    });
    var blobUrl = URL.createObjectURL(blobFile);
    const itemGambar = '<div class="item-gambar"><p>X</p><img src="' + blobUrl + '" alt=""></div>'
    const itemGambarElm = buatElementDariHTML(itemGambar)
    const containerGambar = document.getElementById("container-gambar" + varianNum)
    itemGambarElm.addEventListener("click", () => {
        console.log(varianNum, subVarianNum)
        containerGambar.removeChild(itemGambarElm)
        containerInputGambar.removeChild(parentNode)
    })
    containerGambar.append(itemGambarElm);
}

function addVarian() {
    const containerVarian = document.getElementById("container-varian");
    const jumlahVarian = counterJmlVarian;
    const itemVarianBaru = '<div class="item-varian"><div class="container-gambar" id="container-gambar' + (Number(
            jumlahVarian) + 1) + '"><div id="container-input-gambar' + (Number(jumlahVarian) + 1) +
        '"><div><input type="file" id="input-gambar-' + (Number(jumlahVarian) + 1) + '-1" name="gambar-' + (Number(
            jumlahVarian) + 1) + '-1" style="display: none;" onchange="uploadFile(event)"><label for="input-gambar-' + (
            Number(jumlahVarian) + 1) +
        '-1" class="btn-default">+</label></div></div></div><table class="table-input w-100 mt-2"><tbody><tr><td>Nama</td><td><div class="baris"><input type="text" class="form-control" name="nama-var' +
        (Number(jumlahVarian) + 1) +
        '" required></div></td></tr><tr><td>Kode Warna</td><td><div class="baris"><input type="text" class="form-control" name="kode-var' +
        (Number(jumlahVarian) + 1) +
        '" required></div></td></tr><tr><td>Stok</td><td><div class="baris"><input type="text" class="form-control" name="stok-var' +
        (Number(jumlahVarian) + 1) +
        '" required></div></td></tr></tbody></table><button type="button" class="btn-teks-aja m-0 ms-auto mt-1" onclick="deleteVarian(event)">Hapus</button></div>'
    containerVarian.innerHTML += itemVarianBaru
    counterJmlVarian++;
    hitungVarianInputElm.value += "," + (Number(jumlahVarian) + 1);
    console.log(hitungVarianInputElm.value)
}

function deleteVarian(event) {
    const parentNodeElm = event.target.parentNode;
    const urutanVarianKe = parentNodeElm.children[0].id.substring(16);
    const containerVarian = document.getElementById("container-varian");
    containerVarian.removeChild(parentNodeElm);

    let varianArr = hitungVarianInputElm.value.split(",")
    const index = varianArr.indexOf(urutanVarianKe)
    varianArr.splice(index, 1)
    hitungVarianInputElm.value = varianArr.join(",")
    console.log(hitungVarianInputElm.value)
}
</script>

<?= $this->endSection(); ?>