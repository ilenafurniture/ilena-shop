<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">List Konfirmasi Marketplace</h1>
        </div>
    </div>
    <div class="container-table mt-4">
        <div class="header-table">
            <div style="flex: 1;">No</div>
            <div style="flex: 2;">Id Ilena</div>
            <div style="flex: 2;">Id Marketplace</div>
            <div style="flex: 2;">Nama Pembeli</div>
            <div style="flex: 2;">Alamat Pembeli</div>
            <div style="flex: 2;">Action</div>
        </div>

        <?php foreach ($pemesanan as $indx_p => $p) { ?>
            <div class="isi-table">
                <div style="flex: 1;">
                    <?= $indx_p + 1 ?></div>
                <div style="flex: 2;"><?= $p['id_midtrans'] ?></div>
                <div style="flex: 2;"><?= $p['id_marketplace'] ?></div>
                <div style="flex: 2;"><?= $p['nama'] ?></div>
                <div style="flex: 2;"><?= $p['alamat'] ?></div>
                <div style="flex: 2;" class="gap-2">
                    <a class="btn-default" href="/admin/confirm-mp/<?= $p['id'] ?>">Konfim</a>
                    <a class="btn-default-merah" href="/admin/edit-mp/<?= $p['id'] ?>">Edit</a>
                    <button class="btn btn-light" onclick="openDetail(<?= $indx_p ?>)"><i class=" material-icons">more_vert</i></button>
                </div>
            </div>
        <?php } ?>

        <!-- <div class="isi-table">
            <div style="flex: 1;">2</div>
            <div style="flex: 2;">TILN0001</div>
            <div style="flex: 2;">MTV 100 - Putih</div>
            <div style="flex: 2;">bali,indonesia</div>
            <div style="flex: 2;">1</div>
            <div style="flex: 2; color:green;">Selesai</div>
        </div> -->
    </div>
</div>
<div class="d-none justify-content-center align-items-center w-100" id="container-detail" style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white p-4" style="width: 50%; border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <div class=" gap-2">
            <h6 class="text-center">Detail:</h6>
            <div class="d-flex justify-content-between">
                <div class="d-flex gap-3">
                    <div>
                        <p class="m-0 fw-bold">Nama Pembeli:</p>
                        <p class="m-0 fw-bold">Alamat Pembeli:</p>
                        <p class="m-0 fw-bold">No Handphone:</p>
                        <p class="m-0 fw-bold">No Resi:</p>
                    </div>
                    <div>
                        <p class="m-0" id="nama">Udin</p>
                        <p class="m-0" id="alamat">Jl. Salaman</p>
                        <p class="m-0" id="nohp">08132426632</p>
                        <p class="m-0" id="noresi">SPG00033</p>
                    </div>
                </div>
                <div>
                    <div>
                        <p id="tanggal">Tanggal: 10 Oktober 2024</p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container-table detaill">
                <div class="header-table">
                    <div style="flex: 2;">Nama Barang</div>
                    <div style="flex: 2;">Jumlah Barang</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 2;">36365252</div>
                    <div style="flex: 2;">1 </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center align-content-center">
            <button class="btn-default-merah" onclick="closemodal()">Batal</button>
        </div>
    </div>
</div>

<script>
    const pemesanan = JSON.parse('<?= $pemesananJson ?>')
    const namaElm = document.getElementById('nama');
    const alamatElm = document.getElementById('alamat');
    const nohpElm = document.getElementById('nohp');
    const noresiElm = document.getElementById('noresi');
    const tanggalElm = document.getElementById('tanggal');
    const tabelitemsELm = document.querySelector('.detaill');

    const isiTabelElm = document.getElementById('container-detail');



    function openDetail(index_pemesanan) {
        console.log(pemesanan[index_pemesanan]);
        const pemesanancurr = pemesanan[index_pemesanan];

        namaElm.innerHTML = pemesanancurr.nama
        alamatElm.innerHTML = pemesanancurr.alamat
        nohpElm.innerHTML = pemesanancurr.nohp
        noresiElm.innerHTML = pemesanancurr.resi
        tanggalElm.innerHTML = pemesanancurr.data_mid.transaction_time

        tabelitemsELm.innerHTML =
            '<div class="header-table"><div style="flex: 2;">Nama Barang</div><div style="flex: 2;">Jumlah Barang</div></div>'

        pemesanancurr.items.forEach(element => {
            tabelitemsELm.innerHTML += '<div class="isi-table"><div style="flex: 2;">' + element.name +
                '</div><div style="flex: 2;">' + element.quantity + '</div></div>'
        });
        isiTabelElm.classList.add("d-flex");
        isiTabelElm.classList.remove("d-none");


    }

    function closemodal() {
        isiTabelElm.classList.remove("d-flex");
        isiTabelElm.classList.add("d-none");
    }
</script>
<?= $this->endSection(); ?>