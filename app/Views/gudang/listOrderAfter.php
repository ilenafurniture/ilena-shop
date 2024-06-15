<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="container-table">
        <div class="header-table border-buttom border-dark">
            <div style="flex: 1;">ID Pesanan</div>
            <div style="flex: 2;">Tanggal</div>
            <div style="flex: 2;">Penerima</div>
            <div style="flex: 1;">Action</div>
        </div>
        <?php foreach ($pesanan as $ind_p => $p) { ?>
            <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>, event)">
                <div style="flex: 1;"><?= $p['id_midtrans']; ?></div>
                <?php
                $transactionTime = strtotime($p['data_mid']['transaction_time']);
                $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                ?>
                <div style="flex: 2;"><?= $tgl; ?></div>
                <div style="flex: 2;"><?= $p['nama']; ?></div>
                <div style="flex: 1;">
                    <a class="btn-default" href="/gudang/suratjalan/<?= $p['id_midtrans']; ?>">Print</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="container-offcanvas">
    <div class="d-flex justify-content-end mb-2"><button class="btn btn-light" onclick="closeDetail()"><i class="material-icons">close</i></button></div>
    <div style="border-radius: 10px; padding: 1em 1.5em; background-color: whitesmoke;" class="mb-2">
        <h3 style="letter-spacing: -1px;" class="mb-2">Customer</h3>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="m-0 w-50" style="letter-spacing: -1px;">
                ID Pesanan
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="id">
                #ILENA00001
            </p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">
                Nama Penerima
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="nama">
                #ILENA00001
            </p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">
                Nohp Penerima
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="nohp">
                #ILENA00001
            </p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">
                Alamat Penerima
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="alamat">
                #ILENA00001
            </p>
        </div>
        <div class="mt-2 d-flex justify-content-between pb-1">
            <p class="w-50 m-0" style="letter-spacing: -1px;">
                Email Pemesan
            <p class="w-50 fw-bold m-0" style="letter-spacing: -1px;" id="email">
                #ILENA00001
            </p>
        </div>
    </div>
    <div style="border-radius: 10px; padding: 1em 1.5em; background-color: whitesmoke;">
        <h3 style="letter-spacing: -1px;" class="mb-2">Produk</h3>
        <div id="item">
            <div class="d-flex mb-1 border-bottom pb-1">
                <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama
                </p>
                <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah
                </p>
                <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    const pesanan = JSON.parse('<?= $pesananJson; ?>');
    console.log(pesanan);
    const idElm = document.getElementById("id");
    const namaElm = document.getElementById("nama");
    const nohpElm = document.getElementById("nohp");
    const alamatElm = document.getElementById("alamat");
    const emailElm = document.getElementById("email");
    const itemElm = document.getElementById("item");
    const containerOffcanvasElm = document.querySelector(".container-offcanvas");

    function openDetail(indP, event) {
        const pesananSelected = pesanan[indP];
        console.log(pesananSelected)
        idElm.innerHTML = pesananSelected.id_midtrans
        namaElm.innerHTML = pesananSelected.nama
        nohpElm.innerHTML = pesananSelected.nohp
        alamatElm.innerHTML = pesananSelected.alamat
        emailElm.innerHTML = pesananSelected.email
        itemElm.innerHTML =
            '<div class="d-flex mb-1 border-bottom pb-1"><p class="d-block fw-bold m-0 text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p></div>';
        pesananSelected.items.forEach(element => {
            itemElm.innerHTML +=
                '<div class="d-flex py-1"><p class="d-block fw-bold m-0" style="flex: 1; letter-spacing: -1px;">' +
                element.name +
                '</p><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + element
                .quantity +
                '</p><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp ' +
                element.price + '</p></div>'
            // if (element.name != 'Biaya Ongkir' && element.name != 'Biaya Admin') {
            // }
        });
        containerOffcanvasElm.classList.add("show")
    }

    function closeDetail() {
        containerOffcanvasElm.classList.remove("show")
    }
</script>

<?= $this->endSection(); ?>