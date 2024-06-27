<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;" class="path"><a class="me-3 text-dark fw-bold"
                style="text-decoration: none;">Alamat</a> >
            <a class="mx-3 text-secondary" style="text-decoration: none;">Kurir</a> > <a class="mx-3 text-secondary"
                style="text-decoration: none;">
                Rincian Pembayaran</a>
        </h5>
        <div class="container-address my-4">
            <?php foreach ($alamat as $ind_a => $a) { ?>
            <input type="radio" name="address" id="address<?= $ind_a ?>" <?= $ind_a <= 0 ? 'checked' : '' ?>>
            <label for="address<?= $ind_a ?>" class="item-address radio">
                <div style="flex: 1;">
                    <p class="mb-1 nama"><?= $a['nama_penerima'] ?></p>
                    <p class="mb-1"><?= $a['alamat_lengkap'] ?></p>
                    <p class="mb-1"><?= $a['nohp_penerima'] ?></p>
                    <p class="mb-1"><b style="font-weight: 600;">Pemesan</b> : <?= $a['email_pemesan'] ?></p>
                </div>
                <!-- <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                        <a href="#" class="btn-teks-aja text-dark">Edit</a>
                        <a href="/deleteaddress/<?= $ind_a; ?>" class="btn-teks-aja">Hapus</a>
                    </div> -->
            </label>
            <?php } ?>
        </div>
        <button type="button" class="btn-teks-aja" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                class="material-icons">add</i> Tambah Alamat</button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="/addaddress" method="post">
                <input type="text" value="address" name="checkpage" style="display: none;">
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
                                    <input type="email" class="form-control" placeholder="Email" name="emailPem"
                                        required value="<?= $email; ?>">
                                    <label for="floatingInput">Email</label>
                                </div>
                            </div>
                            <div class="py-3">
                                <h5 class="mb-2">Informasi Penerima</h5>
                                <div class="form-floating mb-1">
                                    <input type="text" class="form-control" placeholder="Email" name="nama" required
                                        value="<?= $nama; ?>">
                                    <label for="floatingInput">Nama Lengkap</label>
                                </div>
                                <div class="form-floating mb-1">
                                    <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp"
                                        required value="<?= $nohp; ?>">
                                    <label for="floatingInput">No. HP</label>
                                </div>
                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="provinsi"
                                            required>
                                            <option value="">-- Pilih provinsi --</option>
                                            <?php foreach ($provinsi as $p) { ?>
                                            <option value="<?= $p['province_id']; ?>-<?= $p['province']; ?>">
                                                <?= $p['province']; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                        <label for="floatingProvinsi">Provinsi</label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kota"
                                            required>
                                            <option value="">-- Pilih kota --</option>
                                        </select>
                                        <label for="floatingProvinsi">Kabupaten/Kota</label>
                                    </div>
                                </div>
                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kecamatan"
                                            required>
                                            <option value="">-- Pilih kecamatan --</option>
                                        </select>
                                        <label for="floatingProvinsi">Kecamatan</label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kodepos"
                                            required>
                                            <option value="">-- Pilih Desa --</option>
                                        </select>
                                        <label for="floatingProvinsi">Desa/Kelurahan</label>
                                    </div>
                                </div>
                                <div class="form-alamat form-floating mb-1">
                                    <input type="text" class="form-control" placeholder="Email" name="alamat_add"
                                        required>
                                    <label for="floatingInput">Jalan, Nomor Rumah, RT-RW</label>
                                    <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-lonjong">Tambah</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h4 style="letter-spacing: -1px">Pesanan</h4>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    harga
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
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp <?= number_format($hargaKeseluruhan, 0, ',', '.'); ?>
                </p>
            </div>
            <a id="btn-shipping" <?= count($alamat) > 0 ? 'href="/shipping/0"' : '' ?>
                class="btn-default-merah <?= count($alamat) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center">Pilih
                Kurir</a>
        </div>
    </div>
</div>

<script>
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');
const radioAddressElm = document.querySelectorAll('input[name="address"]');
const btnShippingElm = document.getElementById("btn-shipping");

radioAddressElm.forEach((elm, ind_radio) => {
    elm.addEventListener('change', (e) => {
        btnShippingElm.href = "/shipping/" + ind_radio
    })
})

function titleCase(str) {
    var splitStr = str.toLowerCase().split(' ');
    for (var i = 0; i < splitStr.length; i++) {
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
    }
    return splitStr.join(' ');
}

async function getKota(idprov) {
    const response = await fetch("getkota/" + idprov);
    const kota = await response.json();
    const hasil = kota.rajaongkir.results;
    // console.log(hasil)
    kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = element.city_id + "-" + element.city_name.split("/")[0]
        optElm.innerHTML = element.type == 'Kota' ? `${element.city_name} Kota` : element.city_name
        kotaElm.appendChild(optElm);
    });
}
async function getKec(idkota) {
    const response = await fetch("getkec/" + idkota);
    const kecamatan = await response.json();
    const hasil = kecamatan.rajaongkir.results;
    // console.log(hasil)
    kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = element.subdistrict_id + "-" + element.subdistrict_name.split("/")[0]
        optElm.innerHTML = element.subdistrict_name
        kecElm.appendChild(optElm);
    });
}
async function getKode(kec) {
    const response = await fetch("https://api.jasminefurniture.co.id/getkode/" + kec);
    const kode = await response.json();
    const hasil = kode.data;
    // console.log(hasil)
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = titleCase(element.DesaKelurahan).split("/")[0] + "-" + element.KodePos
        optElm.innerHTML = titleCase(element.DesaKelurahan)
        kodeElm.appendChild(optElm);
    });
}

provElm.addEventListener("change", (e) => {
    kotaElm.innerHTML = '<option value="">Loading..</option>'
    kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const valuenya = e.target.value.split("-");
    const idprov = Number(valuenya[0]);
    if (idprov > 0) {
        getKota(idprov)
    }
})
kotaElm.addEventListener("change", (e) => {
    kecElm.innerHTML = '<option value="">Loading..</option>'
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const value = e.target.value.split("-")
    const idkota = Number(value[0])
    if (idkota > 0) {
        getKec(idkota)
    }
})
kecElm.addEventListener("change", (e) => {
    kodeElm.innerHTML = '<option value="">Loading..</option>'
    const value = e.target.value.split("-")
    const idkec = Number(value[0])
    if (idkec > 0) {
        getKode(value[1])
    }
})
</script>
<?= $this->endSection(); ?>