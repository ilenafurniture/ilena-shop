<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <h1 class="teks-sedang mb-3">Tambah Produk</h1>
    <form method="post" action="/editproduct/<?= $produk['id']; ?>" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="baris-ke-kolom mb-2">
            <div class="limapuluh-ke-seratus">
                <table class="table-input w-100">
                    <tbody>
                        <tr>
                            <td>Nama</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['nama']; ?>" name="nama" required
                                        onchange="isiPencarian(event)"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Harga</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control"
                                        value="<?= $produk['harga']; ?>" name="harga" required></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Diskon</td>
                            <td>
                                <div class="baris">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" value="<?= $produk['diskon']; ?>"
                                            name="diskon" step="any" required onchange="isiPencarian(event)">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Stok</td>
                            <td>
                                <div class="baris"><input type="number" class="form-control"
                                        value="<?= $produk['varian'][0]['stok']; ?>" name="stok" required></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['kategori']; ?>" name="kategori" required
                                        onchange="isiPencarian(event)"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Sub Kategori</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['subkategori']; ?>" name="subkategori" required
                                        onchange="isiPencarian(event)"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Varian</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['varian'][0]['nama']; ?>" name="varian" required
                                        onchange="isiPencarian(event)"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Jumlah Varian</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['varian'][0]['urutan_gambar']; ?>" name="jml_varian"
                                        required></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Shopee</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['shopee']; ?>" name="shopee"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Tokopedia</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['tokped']; ?>" name="tokped"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Link Tiktok Shop</td>
                            <td>
                                <div class="baris"><input type="text" class="form-control"
                                        value="<?= $produk['tiktok']; ?>" name="tiktok"></div>
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
                            <td>
                                <h5 class="mt-4">Bentuk Paket</h5>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Panjang (cm)</td>
                            <td>
                                <div class=" baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['panjang']; ?>" type="text"
                                        class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Lebar (cm)</td>
                            <td>
                                <div class="baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['lebar']; ?>" type="text"
                                        class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['tinggi']; ?>" type="text"
                                        class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['paket']['berat']; ?>" type="number"
                                        class="form-control" name="berat" step="any" required>
                                </div>
                            </td>
                        </tr>

                        <td>
                            <h5 class="mt-4">Bentuk Asli</h5>
                        </td>
                        <tr>
                            <td>Dimensi Panjang (cm)</td>
                            <td>
                                <div class=" baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['panjang']; ?>" type="text"
                                        class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Lebar (cm)</td>
                            <td>
                                <div class="baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['panjang']; ?>" type="text"
                                        class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Dimensi Tinggi (cm)</td>
                            <td>
                                <div class="baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['tinggi']; ?>" type="text"
                                        class="form-control" name="dimensi" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Berat (kg)</td>
                            <td>
                                <div class="baris"><input
                                        value="<?= $produk['deskripsi']['dimensi']['asli']['berat']; ?>" type="number"
                                        class="form-control" name="berat" step="any" required>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>


                <div class=" show-flex-ke-hide mt-4 justify-content-center gap-2">
                    <button class="btn-default" type="submit">Simpan</button>
                    <button class="btn-default-merah" href="/listproduct">Batal</button>
                </div>
            </div>
            <div class="limapuluh-ke-seratus">
                <h5 class="jdl-section">Gambar Produk</h5>
                <div class="add-gambar mb-1">
                    <p
                        style="position: relative; transform: translate(15px, 10px); color: rgba(0, 0, 0, 0.5); margin-bottom: -20px;">
                        Preview</p>
                    <img src="/img/nopic.jpg" id="addProduct_PreviewUtama" class="border border-dark">
                </div>
                <div id="foto-varian" class="d-flex gap-2" style="overflow-y: auto; width:100%">
                </div>
            </div>
        </div>
        <div class="justify-content-center hide-ke-show-flex gap-2">
            <button class="btn-default" type="submit">Simpan</button>
            <button class="btn-default-merah" href="/listproduct">Batal</button>
        </div>
    </form>
</div>
<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
const elmFotoVarian = document.getElementById('foto-varian');
const elmKategori = document.querySelector('input[name="kategori"]');
const elmSubkategori = document.querySelector('input[name="subkategori"]');
const elmPencarian = document.querySelector('input[name="pencarian"]');
const elmVarian = document.querySelector('input[name="varian"]');
const elmJmlvarian = document.querySelector('input[name="jml_varian"]');
const ambilVarian = "<?= count($produk['varian'], true); ?>"
const ambilJmlvarian = "<?= $produk['varian'][0]['urutan_gambar']; ?>"
let varian = Number(ambilVarian);
let jmlVarian = Number(ambilJmlvarian);
let hasilVarian = jmlVarian + varian - 1
console.log(varian, jmlVarian, hasilVarian, ambilVarian, ambilJmlvarian)
inputElement(hasilVarian);

function isiPencarian(e) {
    console.log(e);
    if (e.srcElement.value != '') {
        if (e.srcElement.name == 'nama') {
            elmPencarian.value += `${e.srcElement.value} `
        } else if (e.srcElement.name == 'kategori') {
            elmPencarian.value +=
                `${e.srcElement.value} elegan ${e.srcElement.value} simpel ${e.srcElement.value} minimalis ${e.srcElement.value} estetik ${e.srcElement.value} modern `
        } else if (e.srcElement.name == 'subkategori') {
            const subkategorinya = e.srcElement.value.replace(/-/g, ' ')
            elmPencarian.value +=
                `${subkategorinya} elegan ${subkategorinya} simpel ${subkategorinya} minimalis ${subkategorinya} estetik ${subkategorinya} modern `
        } else if (e.srcElement.name == 'varian') {
            const arrVar = e.srcElement.value.split(",");
            const subkategorinya = elmSubkategori.value.replace(/-/g, ' ')
            arrVar.forEach((va) => {
                elmPencarian.value += `${elmKategori.value} ${va} ${subkategorinya} ${va} `
            })
        } else if (e.srcElement.name == 'diskon') {
            const subkategorinya = elmSubkategori.value.replace(/-/g, ' ')
            if (Number(e.srcElement.value) > 0) elmPencarian.value +=
                `${elmKategori.value} promo ${elmKategori.value} diskon ${subkategorinya} promo ${subkategorinya} diskon `
        }
    }
}

elmVarian.addEventListener("change", (e) => {
    const varianArray = e.target.value.split(",");
    console.log(varianArray);
    varian = varianArray.length;
    console.log(varian);
    hasilVarian = jmlVarian + varian - 1;
    console.log(hasilVarian);
    inputElement(hasilVarian);
});

elmJmlvarian.addEventListener("change", (e) => {
    jmlVarian = Number(e.target.value);
    hasilVarian = jmlVarian + varian - 1;
    console.log(hasilVarian);
    inputElement(hasilVarian);
});

function inputElement(hasilVarian) {
    elmFotoVarian.innerHTML = "";
    for (let i = 1; i <= hasilVarian; i++) {
        const cardVarian = document.createElement('div');
        cardVarian.classList.add('input-group-gambar');
        const cardAnkvarian = document.createElement('div');
        cardAnkvarian.classList.add('addProduct_Input');
        cardAnkvarian.setAttribute('id', 'addProduct_Input' + i);
        cardAnkvarian.setAttribute('data-bs-toggle', 'tooltip');
        cardAnkvarian.setAttribute('data-bs-placement', 'top');
        cardAnkvarian.setAttribute('data-bs-title', 'Wajib diisi');
        const cardlabel = document.createElement('label');
        cardlabel.classList.add('input-gambar-label');
        cardlabel.setAttribute('for', 'addProduct_InputGambar' + i);
        const cardIlabel = document.createElement('i');
        cardIlabel.classList.add('material-icons');
        cardIlabel.innerHTML = "add";
        const cardinput = document.createElement('input');
        cardinput.classList.add('input-gambar');
        cardinput.setAttribute('type', 'file');
        cardinput.setAttribute('id', 'addProduct_InputGambar' + i);
        cardinput.setAttribute('name', 'gambar' + i);
        // cardinput.setAttribute('required', '');
        const cardImg = document.createElement('img');
        cardImg.src = "img/nopic.jpg";
        cardImg.setAttribute('id', 'addProduct_PreviewGambar' + i);
        cardImg.classList.add('addProduct_Preview');
        cardlabel.appendChild(cardIlabel);
        cardAnkvarian.appendChild(cardlabel);
        cardAnkvarian.appendChild(cardinput);
        cardVarian.appendChild(cardAnkvarian);
        cardVarian.appendChild(cardImg);
        elmFotoVarian.appendChild(cardVarian);
    }
    const addProduct_inputGambar = document.querySelectorAll(".input-gambar");
    const addProduct_previewGambar = document.querySelectorAll(".addProduct_Preview");
    const addProduct_labelInput = document.querySelectorAll(".input-gambar-label");
    const addProduct_input = document.querySelectorAll(".addProduct_Input");
    const addProduct_previewUtama = document.getElementById("addProduct_PreviewUtama");
    const addProduct_form = document.querySelector("form");
    addProduct_inputGambar.forEach((item, index) => {
        item.addEventListener("change", () => {
            const file = addProduct_inputGambar[index].files[0];
            const blobFile = new Blob([file], {
                type: file.type
            });
            var blobUrl = URL.createObjectURL(blobFile);
            addProduct_previewGambar[index].src = blobUrl;
            addProduct_previewUtama.src = blobUrl;
            addProduct_previewGambar[index].style.display = "block";
            addProduct_input[index].style.display = 'none';
        })
    })
}
// const addProduct_inputGambar = document.querySelectorAll(".input-gambar");
// const addProduct_previewGambar = document.querySelectorAll(".addProduct_Preview");
// const addProduct_input = document.querySelectorAll(".addProduct_Input");
// const addProduct_previewUtama = document.getElementById("addProduct_PreviewUtama");
// const addProduct_form = document.querySelector("form");

// addProduct_inputGambar.forEach((item, index) => {
//     item.addEventListener("change", () => {
//         addProduct_inputGambar.forEach((element, ind) => {
//             if (ind < 3) element.required = true;
//         });
//         const file = addProduct_inputGambar[index].files[0];
//         const blobFile = new Blob([file], {
//             type: file.type
//         });
//         var blobUrl = URL.createObjectURL(blobFile);
//         addProduct_previewGambar[index].src = blobUrl;
//         addProduct_previewUtama.src = blobUrl;
//         addProduct_previewGambar[index].style.display = "block";
//         addProduct_input[index].style.display = 'none';
//     })
// })
</script>
<?= $this->endSection(); ?>