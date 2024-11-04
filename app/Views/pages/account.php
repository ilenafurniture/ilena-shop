<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div id="edit-sandi" style="position: fixed; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.7); z-index: 100; top: 0; left: 0;" class="<?= $msgSandi ? 'd-flex' : 'd-none'; ?> justify-content-center align-items-center">
    <div class="p-4 bg-light" style="border-radius: 1em;">
        <h2 style="letter-spacing: -1px">Ganti Sandi</h2>
        <?php if ($msgSandi) { ?>
            <div class="pemberitahuan my-1 mx-auto" style="width: fit-content;" role="alert">
                <?= $msgSandi; ?>
            </div>
        <?php } ?>
        <form action="/editsandi/account" method="post">
            <div class="mb-1">
                <label for="">Sandi baru Anda</label>
                <input type="password" name="sandi" class="form-control" required>
            </div>
            <div class="mb-2">
                <label for="">Konfirmasi Sandi</label>
                <input type="password" class="form-control" oninput="konfirmasiSandi(event)" required name="sandiKonfirm">
                <div class="invalid-feedback">
                    Kata sandi tidak cocok
                </div>
            </div>
            <div class="d-flex justify-content-center gap-1">
                <button type="submit" class="btn-default">Ubah</button>
                <button class="btn-teks-aja" onclick="closeEditSandi()">Batal</button>
            </div>
        </form>
    </div>
</div>
<div class="container konten baris-ke-kolom">
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h2 style="letter-spacing: -1px">Akun Saya</h2>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    Email
                </p>
                <p class="fw-bold m-0">
                    <?= $email; ?>
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Sandi
                </p>
                <a class="btn-teks-aja" onclick="openGantiSandi()">Ganti Sandi</a>
            </div>
            <span class="garis my-2"></span>
            <!-- <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0">
                    Rp
                </p>
            </div> -->
            <a href="/account" class="mb-1 btn-default-abu" style="width: 100%; text-align:center;">Profile</a>
            <a href="/order" class="mb-1 btn-default-abu" style="width: 100%; text-align:center;">Pesanan</a>
            <a href="/logout" class="btn-default-merah" style="width: 100%; text-align:center;">Keluar</a>
        </div>
    </div>
    <div style="flex:1;">
        <div class="mb-4">
            <h1 class="teks-sedang">Tentang Saya</h1>
            <p style="color: grey;">1 Pemesanan</p>
        </div>
        <div class="mt-2 d-flex justify-content-between py-1">
            <p class="m-0">
                Email
            </p>
            <p class="fw-bold m-0">
                <?= $email; ?>
            </p>
        </div>
        <div class="d-flex justify-content-between py-1">
            <p class="m-0">
                Nama Lengkap
            </p>
            <p class="fw-bold m-0">
                <?= $nama; ?>
            </p>
        </div>
        <div class="mb-4 d-flex justify-content-between py-1">
            <p class="m-0">
                Sandi
            </p>
            <a class="btn-teks-aja" onclick="openGantiSandi()">Ganti Sandi</a>
        </div>
        <span class="garis"></span>
        <?php if (session()->get('active') != '0') { ?>
            <div class="my-4">
                <h1 class="teks-sedang">Alamat</h1>
                <p style="color: grey;"><?= count($alamat) <= 0 ? 'Tidak Ada' : count($alamat) ?> tujuan yang dapat dipilih</p>
            </div>
            <?php if ($msg) { ?>
                <div class="pemberitahuan" role="alert">
                    <?= $msg; ?>
                </div>
            <?php } ?>
            <div class="container-address my-4">
                <?php foreach ($alamat as $ind_a => $a) { ?>
                    <div for="address<?= $ind_a ?>" class="item-address">
                        <div style="flex: 1;">
                            <p class="mb-1 nama"><?= $a['nama_penerima'] ?></p>
                            <p class="mb-1"><?= $a['alamat_lengkap'] ?></p>
                            <p class="mb-1"><?= $a['nohp_penerima'] ?></p>
                            <p class="mb-1"><b style="font-weight: 600;">Pemesan</b> : <?= $a['email_pemesan'] ?></p>
                        </div>
                        <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                            <a class="btn-teks-aja text-dark" data-bs-toggle="modal" data-bs-target="#editModal" type="button" onclick="openEditModal(<?= $ind_a; ?>)">Edit</a>
                            <a href="/deleteaddress/<?= $ind_a; ?>/<?= $_SERVER['REQUEST_URI']; ?>" class="btn-teks-aja">Hapus</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <button type="button" class="btn-teks-aja" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="material-icons">add</i> Tambah Alamat</button>

            <!-- Add Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <form action="/addaddress" method="post">
                    <input type="text" value="account" name="checkpage" style="display: none;">
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
                                        <input type="email" class="form-control" placeholder="Email" name="emailPem" required value="<?= $email; ?>">
                                        <label for="floatingInput">Email</label>
                                    </div>
                                </div>
                                <div class="py-3">
                                    <h5 class="mb-2">Informasi Penerima</h5>
                                    <div class="form-floating mb-1">
                                        <input type="text" class="form-control" placeholder="Email" name="nama" required value="<?= $nama; ?>">
                                        <label for="floatingInput">Nama Lengkap</label>
                                    </div>
                                    <div class="form-floating mb-1">
                                        <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp" required value="<?= $nohp; ?>">
                                        <label for="floatingInput">No. HP (WA)</label>
                                    </div>
                                    <div class="form-alamat d-flex mb-1 gap-1">
                                        <div class="form-floating w-50">
                                            <select class="form-select" aria-label="Default select example" name="provinsi" required>
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
                                            <select class="form-select" aria-label="Default select example" name="kota" required>
                                                <option value="">-- Pilih kota --</option>
                                            </select>
                                            <label for="floatingProvinsi">Kabupaten/Kota</label>
                                        </div>
                                    </div>
                                    <div class="form-alamat d-flex mb-1 gap-1">
                                        <div class="form-floating w-50">
                                            <select class="form-select" aria-label="Default select example" name="kecamatan" required>
                                                <option value="">-- Pilih kecamatan --</option>
                                            </select>
                                            <label for="floatingProvinsi">Kecamatan</label>
                                        </div>
                                        <div class="form-floating w-50">
                                            <select class="form-select" aria-label="Default select example" name="kodepos" required>
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
                                <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-lonjong">Tambah</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <form method="post" id="formEdit">
                    <input type="text" value="<?= $_SERVER['REQUEST_URI']; ?>" class="d-none" name="url">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title" id="exampleModalLabel">Edit Alamat</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="pb-3 border-bottom">
                                    <h5 class="mb-2">Informasi Pemesan</h5>
                                    <div class="form-floating mb-1">
                                        <input type="email" class="form-control" placeholder="Email" name="emailPem" required id="inputEmail">
                                        <label for="floatingInput">Email</label>
                                    </div>
                                </div>
                                <div class="py-3">
                                    <h5 class="mb-2">Informasi Penerima</h5>
                                    <div class="form-floating mb-1">
                                        <input type="text" class="form-control" placeholder="Email" name="nama" required id="inputNama">
                                        <label for="floatingInput">Nama Lengkap</label>
                                    </div>
                                    <div class="form-floating mb-1">
                                        <input type="number" class="form-control" placeholder="Nomor Handphone" name="nohp" required id="inputNohp">
                                        <label for="floatingInput">No. HP (WA)</label>
                                    </div>
                                    <div class="form-alamat d-flex mb-1 gap-1">
                                        <div class="form-floating w-50">
                                            <select class="form-select" aria-label="Default select example" name="provinsiEdit" required>
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
                                            <select class="form-select" aria-label="Default select example" name="kotaEdit" required>
                                                <option value="">-- Pilih kota --</option>
                                            </select>
                                            <label for="floatingProvinsi">Kabupaten/Kota</label>
                                        </div>
                                    </div>
                                    <div class="form-alamat d-flex mb-1 gap-1">
                                        <div class="form-floating w-50">
                                            <select class="form-select" aria-label="Default select example" name="kecamatanEdit" required>
                                                <option value="">-- Pilih kecamatan --</option>
                                            </select>
                                            <label for="floatingProvinsi">Kecamatan</label>
                                        </div>
                                        <div class="form-floating w-50">
                                            <select class="form-select" aria-label="Default select example" name="kodeposEdit" required>
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
                                <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-lonjong">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
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
    const inputEmailElm = document.getElementById("inputEmail");
    const inputNamaElm = document.getElementById("inputNama");
    const inputNohpElm = document.getElementById("inputNohp");
    const formEditElm = document.getElementById("formEdit");
    const alamatJson = JSON.parse('<?= $alamatJson ?>')
    const sandiElm = document.querySelector('input[name="sandi"]');
    const editSandiElm = document.getElementById('edit-sandi')
    console.log(alamatJson)

    function openGantiSandi() {
        editSandiElm.classList.remove('d-none')
        editSandiElm.classList.add('d-flex')
    }

    function closeEditSandi() {
        editSandiElm.classList.add('d-none')
        editSandiElm.classList.remove('d-flex')
    }

    function konfirmasiSandi(e) {
        if (sandiElm.value != e.target.value) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    }

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
        const response = await fetch("getkode/" + kec);
        const kode = await response.json();
        const hasil = kode;
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
    }
</script>
<?= $this->endSection(); ?>