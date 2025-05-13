<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
[data-bs-toggle="tooltip"] {
    color: gray;
}
</style>
<div id="input-buat-invoice" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.5)">
    <div class="bg-white p-4 rounded" style="width: 80%; max-height: 90%; overflow-y:scroll;">
        <form method="post" action="/admin/actionbuatinvoice">
            <h5 class="m-0 fw-bold">Buat Invoice</h5>
            <p class="mb-3 text-sm" style="color: var(--merah); font-size: 12px">ID Order : <input type="text"
                    name="id_pesanan" id="input-idpesanan"
                    style="border: none; color: var(--merah); pointer-events: none;" class="fw-bold"></p>
            <div class="mb-1">
                <p class="mb-1">Tanggal</p>
                <input type="datetime-local" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-1">
                <p class="mb-1">Alamat</p>
                <textarea name="alamat" id="input-alamat-invoice" required class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <p class="mb-1">NPWP</p>
                <input type="text" name="npwp" class="form-control" required>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Closes</button>
                <button type="submit" class="btn btn-default-merah w-100">Submit</button>
            </div>
        </form>
    </div>
</div>
<!-- MODAL INPUT DP -->
<div id="input-buat-dp" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.5)">
    <div class="bg-white p-4 rounded" style="width: 80%; max-height: 90%; overflow-y:scroll;">
        <form method="post" action="/admin/actionbuatdp">
            <h5 class="m-0 fw-bold">Buat Invoice</h5>
            <p class="mb-3 text-sm" style="color: var(--merah); font-size: 12px">ID Order : <input type="text"
                    name="id_pesanan" id="input-idpesanan"
                    style="border: none; color: var(--merah); pointer-events: none;" class="fw-bold"></p>

            <div class="d-flex gap-3 mb-4">
                <div style="flex: 1;">
                    <p class="mb-1">Tanggal</p>
                    <input type="datetime-local" name="tanggal" class="form-control" required>
                </div>
                <div style="flex: 1;">
                    <p class="mb-1">NPWP</p>
                    <input type="text" name="npwp" class="form-control" placeholder="kosongin kalau Invoice menyusul">
                </div>
            </div>
            <div class="mb-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th class="text-center">Jumlah Barang</th>
                            <th class="text-end">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="table-dp">

                    </tbody>
                </table>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Closes</button>
                <button type="submit" class="btn btn-default-merah w-100">Submit</button>
            </div>
        </form>
    </div>
</div>
<!-- END MODAL INPUT DP -->

<div id="input-koreksi" class="d-none justify-content-center align-items-center"
    style="position: fixed; left: 0; top: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.5)">
    <div class="bg-white p-4 rounded" style="width: 80%; max-height: 90%; overflow-y:scroll;">
        <form method="post" action="/admin/order-offline/koreksisp">
            <h5 class="m-0 fw-bold">Koreksi Surat Pengantar</h5>
            <p class="m-0 text-sm" style="color: var(--merah); font-size: 12px">ID Order : <input type="text"
                    name="id_pesanan" style="border: none; color: var(--merah); pointer-events: none;" class="fw-bold">
            </p>
            <hr>
            <div class="mb-3">
                <p class="mb-1">Tanggal</p>
                <input type="datetime-local" name="tanggal" class="form-control" required>
            </div>
            <p class="mb-1 fw-bold">Alamat Tagihan</p>
            <div class="ps-3 alamat-taghihan" style="border-left: 1px solid black;">
                <div class="d-flex gap-2 mb-1">
                    <select name="provinsi" class="form-select">
                        <option value="">-- Pilih provinsi --</option>
                        <?php foreach ($provinsi as $p) { ?>
                        <option value="<?= $p['province_id']; ?>-<?= $p['province']; ?>"><?= $p['province']; ?></option>
                        <?php } ?>
                    </select>
                    <select name="kota" class="form-select">
                        <option value="">-- Pilih kabupaten --</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mb-1">
                    <select name="kecamatan" class="form-select">
                        <option value="">-- Pilih kecamatan --</option>
                    </select>
                    <select name="kodepos" class="form-select">
                        <option value="">-- Pilih Kelurahan --</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mb-1">
                    <input type="text" placeholder="Detail" class="form-control" name="detail">
                </div>
            </div>
            <div class="ps-3 d-none alamat-taghihan" style="border-left: 1px solid black;">
                <textarea name="alamatTagihan" class="info-pesanan form-control"></textarea>
            </div>
            <label class="d-flex gap-2 align-items-center mb-3">
                <input name="checkAlamat" type="checkbox" onchange="handleChangeAlamatTagihan(event)">
                <p class="m-0">Sama dengan alamat pengiriman</p>
            </label>

            <div class="mb-3">
                <p class="mb-1">Pilih barang</p>
                <div id="container-items" class="d-flex flex-column gap-1">
                    <label class="d-flex gap-3 align-items-center justify-content-between">
                        <div class="d-flex gap-3 align-items-center">
                            <input type="checkbox" onchange="handleChangeInputItem(0, event)">
                            <div>
                                <p class="fw-bold m-0">Coffe table (HITAM)</p>
                                <p class="text-secondary text-sm m-0">1023142</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <a href="/admin/surat-koreksi/" class="btn-teks-aja">Lihat koreksi</a>
                            <p class="text-secondary" style="font-size: 12px;">SK0000001</p>
                        </div>
                    </label>
                    <label class="d-flex gap-3 align-items-center">
                        <input type="checkbox" onchange="handleChangeInputItem(1, event)">
                        <div>
                            <p class="fw-bold m-0">Coffe table (HITAM)</p>
                            <p class="text-secondary text-sm m-0">1023142</p>
                        </div>
                    </label>
                    <label class="d-flex gap-3 align-items-center">
                        <input type="checkbox" onchange="handleChangeInputItem(2, event)">
                        <div>
                            <p class="fw-bold m-0">Coffe table (HITAM)</p>
                            <p class="text-secondary text-sm m-0">1023142</p>
                        </div>
                    </label>
                </div>
            </div>
            <input type="text" name="index_items_selected" required class="d-none">

            <div class="mb-1">
                <p class="mb-1">NPWP</p>
                <input type="text" name="npwp" class="form-control" placeholder="kosongin kalau Invoice menyusul">
            </div>
            <div class="mb-3">
                <p class="mb-1">Keterangan</p>
                <input type="text" name="keterangan" class="form-control" required>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-default w-100" onclick="closeModal()">Closes</button>
                <button type="submit" class="btn btn-default-merah w-100">Submit</button>
            </div>
        </form>
    </div>
</div>
<div style="padding: 2em;">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-2">
        <div>
            <div class="d-flex gap-2">
                <h1 class="teks-sedang" style="text-wrap: nowrap;">Pesanan Offline</h1>
                <select class="form-select" onchange="selectJenis(event)">
                    <option value="sale" class="fw-bold" <?= $jenis == 'sale' ? 'selected' : ''; ?>>Sale</option>
                    <option value="display" class="fw-bold" <?= $jenis == 'display' ? 'selected' : ''; ?>>Display
                    </option>
                </select>
            </div>
            <p style="color: grey;"><?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pesanan</p>
        </div>
        <a class="btn-default-merah" href="/admin/order-offline/add">Tambah</a>
    </div>
    <?php if ($msg) { ?>
    <div class="pemberitahuan"><?= $msg; ?></div>
    <?php } ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">ID ORDER</th>
                    <th scope="col">TANGGAL</th>
                    <th scope="col">NAMA</th>
                    <th class="text-center" scope="col">STATUS</th>
                    <th class="text-center" scope="col">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesanan as $ind_p => $p) { ?>
                <tr>
                    <th scope="row"><?= $ind_p + 1; ?></th>
                    <td><?= $p['id_pesanan']; ?></td>
                    <td><?= date("d M Y", strtotime($p['tanggal'])); ?></td>
                    <td><?= $p['nama']; ?></td>
                    <td align="center">
                        <span
                            class="badge <?= $p['status'] == 'pending' ? 'bg-secondary' : 'bg-success'; ?> rounded-pill"><?= ucfirst($p['status']); ?></span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            <?php if ($jenis == 'sale') { ?>
                            <?php if($p['down_payment']) { ?>
                            <?php if((int)$p['down_payment'] < 0) { ?>
                            <a class="btn" href="/admin/surat-offline/<?= $p['id_pesanan']; ?>" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="Surat Jalan"><i
                                    class="material-icons">local_shipping</i></a>
                            <a class="btn <?= $p['npwp'] ? '' : 'text-danger'; ?>"
                                <?= $p['npwp'] ? 'href="/admin/invoice-offline/' . $p['id_pesanan'] . '"' : 'onclick="buatInvoice(' . $ind_p . ')"'; ?>
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-title="<?= $p['npwp'] ? 'Invoice' : 'Buat invoice'; ?>"><i
                                    class="material-icons">description</i></a>
                            <?php } else { ?>
                            <a class="btn" href="/admin/invoice-offline-dp/<?= $p['id_pesanan']; ?>"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice DP"><i
                                    class="material-icons">description</i></a>
                            <?php if($p['status'] == 'DP') { ?>
                            <a class="btn text-danger" data-bs-toggle="tooltip" data-bs-placement="top"
                                onclick="buatInvoiceDP(<?= $ind_p; ?>)" data-bs-title="Buat Surat Jalan"><i
                                    class="material-icons">note_add</i></a>
                            <?php } ?>
                            <?php } ?>
                            <?php } else { ?>
                            <a class="btn" href="/admin/surat-offline/<?= $p['id_pesanan']; ?>" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="Surat Jalan"><i
                                    class="material-icons">local_shipping</i></a>
                            <a class="btn <?= $p['npwp'] ? '' : 'text-danger'; ?>"
                                <?= $p['npwp'] ? 'href="/admin/invoice-offline/' . $p['id_pesanan'] . '"' : 'onclick="buatInvoice(' . $ind_p . ')"'; ?>
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-title="<?= $p['npwp'] ? 'Invoice' : 'Buat invoice'; ?>"><i
                                    class="material-icons">description</i></a>
                            <?php } ?>
                            <?php } else { ?>
                            <a class="btn" href="/admin/surat-offline/<?= $p['id_pesanan']; ?>" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="Surat Pengantar"><i
                                    class="material-icons">description</i></a>
                            <?php if ($p['status'] != 'return') { ?>
                            <a class="btn" onclick="pilihPesanan(<?= $ind_p; ?>)" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="Buat Surat Jalan"><i
                                    class="material-icons">insert_drive_file</i></a>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
<script>
function selectJenis(event) {
    window.location.replace(`/admin/order/offline/${event.target.value}`)
}
</script>
<script>
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');

function titleCase(str) {
    var splitStr = str.toLowerCase().split(' ');
    for (var i = 0; i < splitStr.length; i++) {
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
    }
    return splitStr.join(' ');
}
async function getKota(idprov) {
    const response = await fetch("/getkota/" + idprov);
    const kota = await response.json();
    const hasil = kota.rajaongkir.results;
    kotaElm.innerHTML = '<option value="">-- Pilih kota --</option>';
    hasil.forEach(element => {
        const optElm = document.createElement("option");
        optElm.value = element.city_id + "-" + element.city_name.split("/")[0]
        optElm.innerHTML = element.type == 'Kota' ? `${element.city_name} Kota` : element.city_name
        kotaElm.appendChild(optElm);
    });
}
async function getKec(idkota) {
    const response = await fetch("/getkec/" + idkota);
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
    const response = await fetch("/getkode/" + kec);
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
</script>
<script>
const tableDpElm = document.getElementById('table-dp');
const containerItemsElm = document.getElementById('container-items');
const indexItemsSelectedElm = document.querySelector('input[name="index_items_selected"]');
const inputIdpesananElm = document.querySelectorAll('input[name="id_pesanan"]');
const alamatTaghihanElm = document.querySelectorAll('.alamat-taghihan')
const inputKoreksiElm = document.getElementById('input-koreksi');
const inputBuatInvoiceElm = document.getElementById('input-buat-invoice');
const inputBuatDPElm = document.getElementById('input-buat-dp');
const inputAlamatInvoiceElm = document.getElementById('input-alamat-invoice');
const pesanan = JSON.parse('<?= $pesananJson; ?>')
const alamatTagihanElm = document.querySelector('textarea[name="alamatTagihan"]');
let pesananSelected = {};

function handleChangeInputItem(index, event) {
    let arrIndexItem = indexItemsSelectedElm.value.split(',');
    arrIndexItem[index] = event.target.checked ? '1' : '0';
    indexItemsSelectedElm.value = arrIndexItem.join(',');
}

function pilihPesanan(index) {
    console.log(pesanan[index])
    pesananSelected = pesanan[index];
    indexItemsSelectedElm.value = '';
    containerItemsElm.innerHTML = '';
    (async () => {
        const fetchItems = await fetch(`/admin/getitemsoffline/${pesananSelected.id_pesanan}`);
        const fetchItemsJson = await fetchItems.json();
        console.log(fetchItemsJson)
        if (fetchItemsJson.items.length == 0) {
            return window.alert('Produk sudah di beli semua');
        }
        fetchItemsJson.items.forEach((item, index) => {
            indexItemsSelectedElm.value += `${index == 0 ? '' : ','}${0}`
            containerItemsElm.innerHTML += `
                    <label class="d-flex gap-3 align-items-center justify-content-between">
                        <div class="d-flex gap-3 align-items-center">
                            ${item.id_return == '' ? `
                            <input type="checkbox" onchange="handleChangeInputItem(${index}, event)">
                            ` : '<div style="width: 13px"></div>'
                            }
                            <div>
                                <p class="fw-bold m-0">${item.nama} (${item.varian})</p>
                                <p class="text-secondary text-sm m-0">${item.id_barang}</p>
                            </div>
                        </div>
                        ${item.id_return != '' ? `
                            <div class="d-flex flex-column align-items-end">
                                <a href="/admin/surat-koreksi/${item.id_return}" class="btn-teks-aja">Lihat koreksi</a>
                                <p class="text-secondary" style="font-size: 12px;">${item.id_return}</p>
                            </div>
                        ` : ''
                        }
                    </label>
                `
        })
        inputIdpesananElm.forEach((e) => {
            e.value = pesananSelected.id_pesanan;
        })
        inputKoreksiElm.classList.remove('d-none')
        inputKoreksiElm.classList.add('d-flex')
    })();
}

function closeModal() {
    inputBuatDPElm.classList.add('d-none')
    inputBuatDPElm.classList.remove('d-flex')
    inputKoreksiElm.classList.add('d-none')
    inputKoreksiElm.classList.remove('d-flex')
    inputBuatInvoiceElm.classList.add('d-none')
    inputBuatInvoiceElm.classList.remove('d-flex')
    indexItemsSelectedElm.value = '';
}

function handleChangeAlamatTagihan(event) {
    alamatTagihanElm.value = pesananSelected.alamat_pengiriman
    if (event.target.checked) {
        alamatTaghihanElm[0].classList.add('d-none');
        alamatTaghihanElm[1].classList.remove('d-none');
    } else {
        alamatTaghihanElm[1].classList.add('d-none');
        alamatTaghihanElm[0].classList.remove('d-none');
    }
}

function buatInvoice(index) {
    const pesananInvoice = pesanan[index]
    console.log(pesananInvoice)
    inputAlamatInvoiceElm.value = pesananInvoice.alamat_pengiriman
    inputIdpesananElm.forEach((e) => {
        e.value = pesananInvoice.id_pesanan;
    })
    inputBuatInvoiceElm.classList.remove('d-none')
    inputBuatInvoiceElm.classList.add('d-flex')
}

async function buatInvoiceDP(index) {
    const pesananInvoice = pesanan[index]
    console.log(pesananInvoice)
    inputIdpesananElm.forEach((e) => {
        e.value = pesananInvoice.id_pesanan;
    })

    tableDpElm.innerHTML = '';

    const fetchItems = await fetch(`/admin/getitemsoffline/${pesananInvoice.id_pesanan}`);
    const fetchItemsJson = await fetchItems.json();
    console.log(fetchItemsJson.items)
    const result = fetchItemsJson.items.reduce((acc, item) => {
        const key = `${item.id_barang}-${item.varian}`;
        const existingItem = acc.find(i => i.key === key);
        if (existingItem) {
            existingItem.jumlah += 1;
        } else {
            acc.push({
                ...item,
                key,
                jumlah: 1
            });
        }
        return acc;
    }, []);
    let totalHargaBarang = 0;
    result.forEach((item, index) => {
        tableDpElm.innerHTML += `
        <tr>
            <td>
                <p class="m-0">${item.nama} (${item.varian})</p>
                <p class="m-0">${item.dimensi.panjang} x ${item.dimensi.lebar} x ${item.dimensi.tinggi}</p>
            </td>
            <td class="text-center">${item.jumlah}</td>
            <td class="text-end">Rp ${parseInt(item.harga).toLocaleString('id-ID')}</td>
        </tr>
        `
        totalHargaBarang += item.jumlah * parseInt(item.harga)
    })
    tableDpElm.innerHTML += `
    <tr>
        <td colspan="2" class="fw-bold">TOTAL HARGA BARANG</td>
        <td class="text-end fw-bold" colspan=" 2">Rp ${totalHargaBarang.toLocaleString('id-ID')}</td>
    </tr>`
    if (totalHargaBarang - parseInt(pesananInvoice.total_akhir) > 0) {
        tableDpElm.innerHTML += `
        <tr>
            <td colspan="2" class="fw-bold">POTONGAN</td>
            <td class="text-end fw-bold" colspan=" 2">Rp ${(totalHargaBarang - parseInt(pesananInvoice.total_akhir)).toLocaleString('id-ID')}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">TOTAL TAGIHAN</td>
            <td class="text-end fw-bold" colspan=" 2">Rp ${parseInt(pesananInvoice.total_akhir).toLocaleString('id-ID')}</td>
        </tr>
        `
    }
    tableDpElm.innerHTML += `
    <tr>
        <td colspan="2" class="fw-bold">DP</td>
        <td class="text-end fw-bold" colspan=" 2">Rp ${parseInt(pesananInvoice.down_payment.replace('-', '')).toLocaleString('id-ID')}</td>
    </tr>
    <tr>
        <td colspan="2" class="fw-bold">SISA TAGIHAN</td>
        <td class="text-end fw-bold" colspan=" 2">Rp ${(parseInt(pesananInvoice.total_akhir) - parseInt(pesananInvoice.down_payment.replace('-', ''))).toLocaleString('id-ID')}</td>
    </tr>
    `
    inputBuatDPElm.classList.remove('d-none')
    inputBuatDPElm.classList.add('d-flex')
}
</script>
<?= $this->endSection(); ?>