<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<!-- Add Modal -->
<div id="addModalContainer"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; z-index: 1000; background-color: rgba(0, 0, 0, 0.5);"
    class="d-none justify-content-center align-items-center">
    <div class="p-4 bg-light rounded-4"
        style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-height: 90svh; overflow-y: auto;">
        <form action="/addaddress" method="post">
            <input type="text" value="address" name="checkpage" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <?php if(!session()->get('isLogin')) { ?>
                    <div class="d-flex align-items-start">
                        <div style="flex: 2" class="d-flex flex-column justify-content-center align-items-center">
                            <p class="mb-2">Ingin menyimpan alamat?</p>
                            <a href="/login" class="btn-default-merah">Login member</a>
                        </div>
                        <div stype="flex: 1">
                            <button onclick="closeAllModal()" type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>
                    <hr>
                    <?php } ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="modal-title" id="exampleModalLabel">Alamat Baru</h2>
                        <?php if(session()->get('isLogin')) { ?>
                        <button onclick="closeAllModal()" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <?php } ?>
                    </div>
                    <div class="modal-body">
                        <div class="pb-3 border-bottom">
                            <h5 class="mb-2">Informasi Pemesan</h5>
                            <div class="form-floating mb-1">
                                <input type="email" class="form-control" placeholder="Email" name="emailPem" required
                                    value="<?= $email; ?>">
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
                                <label for="floatingInput">No. HP (WA)</label>
                            </div>
                            <div class="form-alamat d-flex mb-1 gap-1">
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="provinsi"
                                        required>
                                        <option value="">-- Pilih provinsi --</option>
                                        <?php foreach ($provinsi as $p) { ?>
                                        <option value="<?= $p['id']; ?>-<?= $p['label']; ?>">
                                            <?= $p['label']; ?>
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
                                <input type="text" class="form-control" placeholder="Email" name="alamat_add" required>
                                <label for="floatingInput">Jalan, Nomor Rumah, RT-RW</label>
                                <div class="invalid-feedback">Tidak boleh mengandung karakter /</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal"
                            onclick="closeAllModal()">Batal</button>
                        <button type="submit" class="btn btn-lonjong">Tambah</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModalContainer"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; z-index: 1000; background-color: rgba(0, 0, 0, 0.5);"
    class="d-none justify-content-center align-items-center">
    <div class="p-4 bg-light rounded-4"
        style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-height: 90svh; overflow-y: auto;">
        <form method="post" id="formEdit">
            <input type="text" value="<?= $_SERVER['REQUEST_URI']; ?>" class="d-none" name="url">
            <div class="modal-dialog">
                <div class="modal-content">
                    <?php if(!session()->get('isLogin')) { ?>
                    <div class="d-flex align-items-start">
                        <div style="flex: 2" class="d-flex flex-column justify-content-center align-items-center">
                            <p class="mb-2">Ingin menyimpan alamat?</p>
                            <a href="/login" class="btn-default-merah">Login member</a>
                        </div>
                        <div stype="flex: 1">
                            <button onclick="closeAllModal()" type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>
                    <hr>
                    <?php } ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="modal-title" id="exampleModalLabel">Edit Alamat</h1>
                        <?php if(session()->get('isLogin')) { ?>
                        <button onclick="closeAllModal()" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <?php } ?>
                    </div>
                    <div class="modal-body">
                        <div class="pb-3 border-bottom">
                            <h5 class="mb-2">Informasi Pemesan</h5>
                            <div class="form-floating mb-1">
                                <input type="email" class="form-control" placeholder="Email" name="emailPem" required
                                    id="inputEmail">
                                <label for="floatingInput">Email</label>
                            </div>
                        </div>
                        <div class="py-3">
                            <h5 class="mb-2">Informasi Penerima</h5>
                            <div class="form-floating mb-1">
                                <input type="text" class="form-control" placeholder="Email" name="nama" required
                                    id="inputNama">
                                <label for="floatingInput">Nama Lengkap</label>
                            </div>
                            <div class="form-floating mb-1">
                                <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp"
                                    required id="inputNohp">
                                <label for="floatingInput">No. HP (WA)</label>
                            </div>
                            <div class="form-alamat d-flex mb-1 gap-1">
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="provinsiEdit"
                                        required>
                                        <option value="">-- Pilih provinsi --</option>
                                        <?php foreach ($provinsi as $p) { ?>
                                        <option value="<?= $p['id']; ?>-<?= $p['label']; ?>">
                                            <?= $p['label']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <label for="floatingProvinsi">Provinsi</label>
                                </div>
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="kotaEdit"
                                        required>
                                        <option value="">-- Pilih kota --</option>
                                    </select>
                                    <label for="floatingProvinsi">Kabupaten/Kota</label>
                                </div>
                            </div>
                            <div class="form-alamat d-flex mb-1 gap-1">
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="kecamatanEdit"
                                        required>
                                        <option value="">-- Pilih kecamatan --</option>
                                    </select>
                                    <label for="floatingProvinsi">Kecamatan</label>
                                </div>
                                <div class="form-floating w-50">
                                    <select class="form-select" aria-label="Default select example" name="kodeposEdit"
                                        required>
                                        <option value="">-- Pilih Desa --</option>
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
                        <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal"
                            onclick="closeAllModal()">Batal</button>
                        <button type="submit" class="btn btn-lonjong">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="container konten">
    <?php if ($msg) { ?>
    <div class="pemberitahuan mb-2" role="alert">
        <?= $msg; ?>
    </div>
    <?php } ?>
    <h5 style="letter-spacing: -1px; font-weight:100;" class="path"><a class="me-3 text-dark fw-bold"
            style="text-decoration: none;">Alamat</a> >
        <a class="mx-3 text-secondary" style="text-decoration: none;">Rincian Pembayaran</a>
    </h5>
    <div class="baris-ke-kolom">
        <div style="flex:1;">
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
                    <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                        <a class="btn-teks-aja text-dark" data-bs-toggle="modal" data-bs-target="#editModal"
                            type="button" onclick="openEditModal(<?= $ind_a; ?>)">Edit</a>
                        <a href="/deleteaddress/<?= $ind_a; ?>/<?= $_SERVER['REQUEST_URI']; ?>"
                            class="btn-teks-aja">Hapus</a>
                    </div>
                </label>
                <?php } ?>
            </div>
            <button id="btn-add-address" type="button" class="btn-teks-aja"><i class="material-icons">add</i> Tambah
                Alamat
                Penerima</button>
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
                <!-- <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        Biaya Admin
                    </p>
                    <p class="fw-bold m-0">
                        Rp 5,000
                    </p>
                </div> -->
                <span class="garis my-2"></span>
                <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        TOTAL
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format($hargaKeseluruhan, 0, ',', '.'); ?>
                    </p>
                </div>
                <a id="btn-shipping" <?= count($alamat) > 0 ? 'href="/payment/0"' : '' ?>
                    class="btn-default-merah <?= count($alamat) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center">Pilih
                    Rincian Pembayaran</a>
            </div>
        </div>
    </div>
</div>

<script>
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');
const provElmEdit = document.querySelector('select[name="provinsiEdit"]');
const kotaElmEdit = document.querySelector('select[name="kotaEdit"]');
const kecElmEdit = document.querySelector('select[name="kecamatanEdit"]');
const kodeElmEdit = document.querySelector('select[name="kodeposEdit"]');
const radioAddressElm = document.querySelectorAll('input[name="address"]');
const inputEmailElm = document.getElementById("inputEmail");
const inputNamaElm = document.getElementById("inputNama");
const inputNohpElm = document.getElementById("inputNohp");
const alamatJson = JSON.parse('<?= $alamatJson ?>')
const formEditElm = document.getElementById("formEdit");
const btnShippingElm = document.getElementById("btn-shipping");
const btnAddAddressElm = document.getElementById("btn-add-address");
const addModalContainer = document.getElementById("addModalContainer");
const editModalContainer = document.getElementById("editModalContainer");

if (alamatJson.length == 0) {
    addModalContainer.classList.remove("d-none");
    addModalContainer.classList.add("d-flex");
}

btnAddAddressElm.addEventListener("click", (e) => {
    addModalContainer.classList.remove("d-none");
    addModalContainer.classList.add("d-flex");
})

radioAddressElm.forEach((elm, ind_radio) => {
    elm.addEventListener('change', (e) => {
        btnShippingElm.href = "/payment/" + ind_radio
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
    kotaElm.innerHTML = '<option value="">Loading kota…</option>';
    try {
        const res = await fetch(`/getkota/${idprov}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        // console.log("getKota payload:", payload);
        const list =
            Array.isArray(payload) ? payload :
            Array.isArray(payload.label) ? payload.label :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
        if (list.length === 0) {
            kotaElm.innerHTML = '<option value="">(Tidak ada kota)</option>';
            return;
        }
        list.forEach(item => {
            const id = item.city_id ?? item.id;
            const nama = (item.city_name?.split("/")[0] ?? item.label).trim();
            const type = item.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = `${nama}${type}`;
            kotaElm.appendChild(opt);
        });

    } catch (err) {
        console.error("getKota error:", err);
        kotaElm.innerHTML = '<option value="">(Gagal memuat kota)</option>';
    }
}
async function getKota(idprov) {
    kotaElm.innerHTML = '<option value="">Loading kota…</option>';
    try {
        const res = await fetch(`/getkota/${idprov}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        // console.log("getKota payload:", payload);
        const list =
            Array.isArray(payload) ? payload :
            Array.isArray(payload.label) ? payload.label :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
        if (list.length === 0) {
            kotaElm.innerHTML = '<option value="">(Tidak ada kota)</option>';
            return;
        }
        list.forEach(item => {
            const id = item.city_id ?? item.id;
            const nama = (item.city_name?.split("/")[0] ?? item.label).trim();
            const type = item.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = `${nama}${type}`;
            kotaElm.appendChild(opt);
        });

    } catch (err) {
        console.error("getKota error:", err);
        kotaElm.innerHTML = '<option value="">(Gagal memuat kota)</option>';
    }
}

async function getKec(idkota) {
    kecElm.innerHTML = '<option value="">Loading kecamatan…</option>';
    kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
    try {
        const res = await fetch(`/getkec/${idkota}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        // console.log("getKec payload:", payload);
        const list =
            Array.isArray(payload) ? payload :
            Array.isArray(payload.rajaongkir?.results) ? payload.rajaongkir.results :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kecElm.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
        if (list.length === 0) {
            kecElm.innerHTML = '<option value="">(Tidak ada kecamatan)</option>';
            return;
        }
        list.forEach(el => {
            const id = el.id;
            const nama = (el.subdistrict_name ?? el.label).split("/")[0].trim();
            const opt = document.createElement("option");
            opt.value = `${id}-${nama}`;
            opt.textContent = nama;
            kecElm.appendChild(opt);
        });

    } catch (err) {
        console.error("getKec error:", err);
        kecElm.innerHTML = '<option value="">(Gagal memuat kecamatan)</option>';
    }
}
async function getKode(kec) {
    kodeElm.innerHTML = '<option value="">Loading desa…</option>';
    try {
        const res = await fetch(`/getkode/${kec}`);
        if (!res.ok) throw new Error(res.statusText);
        const payload = await res.json();
        console.log("getKode payload:", payload);
        const list =
            Array.isArray(payload) ? payload :
            Array.isArray(payload.rajaongkir?.results) ? payload.rajaongkir.results :
            Array.isArray(payload.results) ? payload.results :
            Array.isArray(payload.data?.results) ? payload.data.results : [];
        kodeElm.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (list.length === 0) {
            kodeElm.innerHTML = '<option value="">(Tidak ada desa)</option>';
            return;
        }
        list.forEach(el => {
            const nama = titleCase(el.label).split("/")[0].trim();
            const opt = document.createElement("option");
            opt.value = `${nama}-${el.kodepos}`;
            opt.textContent = nama;
            kodeElm.appendChild(opt);
        });

    } catch (err) {
        console.error("getKode error:", err);
        kodeElm.innerHTML = '<option value="">(Gagal memuat desa)</option>';
    }
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
        getKode(value[0])
    }
})


// ---------------- EDIT ALAMAT -------------- //
async function getKotaEdit(idprov) {
    const response = await fetch("getkota/" + idprov);
    const kota = await response.json();
    const hasil = kota.rajaongkir.results;
    // console.log(hasil)
    kotaElmEdit.innerHTML = '<option value="">-- Pilih kota --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = element.city_id + "-" + element.city_name.split("/")[0]
        optElm.innerHTML = element.type == 'Kota' ? `${element.city_name} Kota` : element.city_name
        kotaElmEdit.appendChild(optElm);
    });
}
async function getKecEdit(idkota) {
    const response = await fetch("getkec/" + idkota);
    const kecamatan = await response.json();
    const hasil = kecamatan.rajaongkir.results;
    // console.log(hasil)
    kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = element.subdistrict_id + "-" + element.subdistrict_name.split("/")[0]
        optElm.innerHTML = element.subdistrict_name
        kecElmEdit.appendChild(optElm);
    });
}
async function getKodeEdit(kec) {
    const response = await fetch("getkode/" + kec);
    const kode = await response.json();
    const hasil = kode;
    // console.log(hasil)
    kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = titleCase(element.DesaKelurahan).split("/")[0] + "-" + element.KodePos
        optElm.innerHTML = titleCase(element.DesaKelurahan)
        kodeElmEdit.appendChild(optElm);
    });
}
provElmEdit.addEventListener("change", (e) => {
    kotaElmEdit.innerHTML = '<option value="">Loading..</option>'
    kecElmEdit.innerHTML = '<option value="">-- Pilih kecamatan --</option>';
    kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const valuenya = e.target.value.split("-");
    const idprov = Number(valuenya[0]);
    if (idprov > 0) {
        getKotaEdit(idprov)
    }
})
kotaElmEdit.addEventListener("change", (e) => {
    kecElmEdit.innerHTML = '<option value="">Loading..</option>'
    kodeElmEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
    const value = e.target.value.split("-")
    const idkota = Number(value[0])
    if (idkota > 0) {
        getKecEdit(idkota)
    }
})
kecElmEdit.addEventListener("change", (e) => {
    kodeElmEdit.innerHTML = '<option value="">Loading..</option>'
    const value = e.target.value.split("-")
    const idkec = Number(value[0])
    if (idkec > 0) {
        getKodeEdit(value[1])
    }
})

function openEditModal(ind_add) {
    const alamatSelected = alamatJson[ind_add];
    console.log(alamatJson[ind_add])
    inputEmailElm.value = alamatSelected.email_pemesan
    inputNamaElm.value = alamatSelected.nama_penerima
    inputNohpElm.value = alamatSelected.nohp_penerima
    formEditElm.action = "/editaddress/" + ind_add
    editModalContainer.classList.remove("d-none");
    editModalContainer.classList.add("d-flex");
}

function closeAllModal() {
    addModalContainer.classList.remove("d-flex");
    addModalContainer.classList.add("d-none");
    editModalContainer.classList.remove("d-flex");
    editModalContainer.classList.add("d-none");
}
</script>
<?= $this->endSection(); ?>