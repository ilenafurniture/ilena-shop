<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <h1 class="teks-sedang mb-3">Tambah Produk</h1>
    <?php if ($val['msg']) { ?>
        <div class="pemberitahuan my-1" role="alert">
            <?= $val['msg']; ?>
        </div>
    <?php } ?>
    <form method="post" action="/admin/addproduct" enctype="multipart/form-data">
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
                            <td>Koleksi</td>
                            <td>
                                <div class="baris">
                                    <!-- <input type="text" class="form-control" name="kategori" required> -->
                                    <select name="kategori" onchange="generateId(event)">
                                        <?php foreach ($koleksi as $ind_k => $k) { ?>
                                            <option value="<?= $k['id']; ?>" <?= $ind_k == '0' ? 'selected' : ''; ?>>
                                                <?= $k['nama']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis</td>
                            <td>
                                <div class="baris">
                                    <!-- <input type="text" class="form-control" name="subkategori" required> -->
                                    <select name="subkategori" onchange="generateId(event)">
                                        <?php foreach ($jenis as $ind_k => $k) { ?>
                                            <option value="<?= $k['id']; ?>" <?= $ind_k == '0' ? 'selected' : ''; ?>>
                                                <?= $k['nama']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>ID Produk</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="id" required placeholder="akan tergenerate">
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
                            <td>Link Tiktok</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tiktok"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Deskripsi</td>
                            <td>
                                <div class="baris"><textarea type="text" class="form-control" name="deskripsi" required></textarea></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Spesifikasi Produk</td>
                            <td>
                                <div class="baris"><textarea type="text" class="form-control" name="perawatan" required></textarea></div>
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
                                <div class="baris"><input type="text" class="form-control" name="panjang-paket" required>
                                </div>
                            </td>
                        </tr>
                        <td>Dimensi Lebar (cm)</td>
                        <td>
                            <div class="baris"><input type="text" class="form-control" name="lebar-paket" required>
                            </div>
                        </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tinggi-paket" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="berat-paket" step="any" required>
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
                                <div class="baris"><input type="text" class="form-control" name="panjang-asli" required>
                                </div>
                            </td>
                        </tr>
                        <td>Dimensi Lebar (cm)</td>
                        <td>
                            <div class="baris"><input type="text" class="form-control" name="lebar-asli" required>
                            </div>
                        </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control" name="tinggi-asli" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control" name="berat-asli" step="any" required>
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
                        <div class="container-gambar" id="container-gambar1">
                            <!-- <div class="item-gambar">
                                <p>X</p>
                                <img src="https://plus.unsplash.com/premium_photo-1669048776605-28ea2e52ae66?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                            </div> -->
                            <div id="container-input-gambar1">
                                <div>
                                    <input type="file" id="input-gambar-1-1" name="gambar-1-1" style="display: none;" onchange="uploadFile(event)">
                                    <label for="input-gambar-1-1" class="btn-default">+</label>
                                </div>
                            </div>
                        </div>
                        <table class="table-input w-100 mt-2">
                            <tbody>
                                <tr>
                                    <td>Nama</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control" name="nama-var1" required>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kode Warna</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control" name="kode-var1" required>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Stok</td>
                                    <td>
                                        <div class="baris"><input type="text" class="form-control" name="stok-var1" required>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn-teks-aja m-0 ms-auto mt-1" onclick="deleteVarian(event)">Hapus</button>
                    </div>
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
    let idStr = "1-0-0-01"
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
        if (hitungVarianInputElm.value == '')
            hitungVarianInputElm.value += (Number(jumlahVarian) + 1);
        else
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

    function generateId(event) {
        const name = event.target.name;
        const value = event.target.value;
        const idInputElm = document.querySelector('input[name="id"]');
        let idStrArr = idStr.split("-");
        switch (name) {
            case 'kategori':
                idStrArr[1] = value.toString().padStart(2, '0');
                idStr = idStrArr.join("-");
                idInputElm.value = idStrArr.join("")
                break;
            case 'subkategori':
                idStrArr[2] = value.toString().padStart(3, '0');
                idStr = idStrArr.join("-");
                idInputElm.value = idStrArr.join("")
                break;
        }
    }
</script>

<?= $this->endSection(); ?>