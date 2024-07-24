<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div class="d-none justify-content-center align-items-center w-100" id="model-scan" style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white w-50" style="border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <div style="padding: 2em;" class="d-flex justify-content-center align-items-center flex-column gap-4">
            <div class="pemberitahuan my-1 d-none" role="alert" id="alert-no-correct">Produk tidak sesuai</div>
            <div class="child-modal-scan">
                <h6 class="text-center">Scan barang yang telah di packing disini:</h6>
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div style="width: 500px" id="reader"></div>
                </div>
            </div>
            <div class="child-modal-scan d-none">
                <h6 class="text-center">Pilih varian</h6>
                <div id="container-varian" class="d-flex flex-column gap-1">
                    <!-- <button class="btn-default" onclick="pilihWarna('')">Warna</button> -->
                </div>
            </div>
            <button class="btn-default-merah" onclick="closemodal()">Batal</button>
        </div>
    </div>
</div>
<div style="padding: 2em;" onload="tabel()">
    <!-- <div class="d-flex justify-content-between gap-4"> -->
    <div>
        <h6 class="text-center mb-3">Pesanan belum diproses</h6>
        <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1" role="alert">
                <?= $val['msg']; ?>
            </div>
        <?php } ?>
        <div class="container-table show-block-ke-hide">
            <!-- <div class="header-table border-buttom border-dark">
                <div style="flex: 1;">No</div>
                <div style="flex: 2;">ID Pesanan</div>
                <div style="flex: 2;">Tanggal</div>
                <div style="flex: 4;">Nama dan varian</div>
                <div style="flex: 2;">ID Barang</div>
                <div style="flex: 1;">Stok Barang</div>
                <div style="flex: 3;">Target Selesai</div>
                <div style="flex: 2;">Action</div>
            </div>

            <?php
            $no = 1;
            if (count($pesanan) > 0) {
                foreach ($pesanan as $p) {
                    if (!$p['packed']) {
            ?>
                        <div class="isi-table">
                            <div style="flex: 1;"><?= $no; ?></div>
                            <div style="flex: 2;"><?= $p['id_pesanan']; ?></div>
                            <div style="flex: 2;"><?= $p['tanggal']; ?></div>
                            <div style="flex: 4;"><?= strtoupper($p['detail_barang']['kategori']); ?> <?= $p['nama']; ?></div>
                            <div style="flex: 2;"><?= $p['id_barang']; ?></div>
                            <div style="flex: 1;"><?= $p['stok']; ?></div>
                            <div style="flex: 3;"><?= $p['target_selesai']; ?></div>
                            <div style="flex: 2;">
                                <button class="btn-default" onclick="openScan('<?= $p['id_barang'] ?>','<?= $p['nama'] ?>')">Scan</button>
                            </div>
                        </div>
                <?php
                        $no++;
                    }
                }
            } else { ?>
                <div class="isi-table">
                    <div style="flex: 1;">Tidak ada pesanan</div>
                </div>
            <?php } ?> -->
        </div>
        <div style="overflow-x: auto;" class="hide-ke-show-block">
            <div class="container-table" style="width: 500px;">
                <!-- <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Pesanan</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 4;">Nama dan varian</div>
                    <div style="flex: 2;">ID Barang</div>
                    <div style="flex: 1;">Stok Barang</div>
                    <div style="flex: 3;">Target Selesai</div>
                    <div style="flex: 2;">Action</div>
                </div>

                <?php
                $no = 1;
                if (count($pesanan) > 0) {
                    foreach ($pesanan as $p) {
                        if (!$p['packed']) {
                ?>
                            <div class="isi-table">
                                <div style="flex: 1;"><?= $no; ?></div>
                                <div style="flex: 2;"><?= $p['id_pesanan']; ?></div>
                                <div style="flex: 2;"><?= $p['tanggal']; ?></div>
                                <div style="flex: 4;"><?= $p['nama']; ?></div>
                                <div style="flex: 2;"><?= $p['id_barang']; ?></div>
                                <div style="flex: 1;"><?= $p['stok']; ?></div>
                                <div style="flex: 3;"><?= $p['target_selesai']; ?></div>
                                <div style="flex: 2;">
                                    <button class="btn-default" onclick="openScan('<?= $p['id_barang'] ?>','<?= $p['nama'] ?>','<?= $p['detail_barang']['varian'] ?>')">Scan</button>
                                </div>
                            </div>
                    <?php
                            $no++;
                        }
                    }
                } else { ?>
                    <div class="isi-table">
                        <div style="flex: 1;">Tidak ada pesanan</div>
                    </div>
                <?php } ?> -->
            </div>
        </div>
    </div>
    <!-- </div> -->
</div>
<script>
    const containerTable = document.querySelectorAll('.container-table');
    const modelScanElm = document.getElementById('model-scan');
    const alertNoCorrectElm = document.getElementById('alert-no-correct');
    let idSelected = ''
    let namaSelected = ''
    var html5QrcodeScanner;
    const childModalScanElm = document.querySelectorAll('.child-modal-scan');
    const containerVarianElm = document.getElementById('container-varian');

    function openScan(idBarang, nama, varian) {
        // console.log(idBarang, nama);
        const varianObj = JSON.parse(atob(varian))
        console.log(varianObj)
        idSelected = idBarang;
        namaSelected = nama;
        modelScanElm.classList.add("d-flex");
        modelScanElm.classList.remove("d-none");

        html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: 250,
        });
        html5QrcodeScanner.render((decodedText, decodedResult) => {
            // console.log(`Scan result: ${decodedText}`, decodedResult);
            html5QrcodeScanner.clear();
            console.log(idSelected, namaSelected)
            containerVarianElm.innerHTML = ''
            if (decodedText == idSelected) {
                if (varianObj.length > 1) {
                    childModalScanElm[0].classList.add('d-none')
                    childModalScanElm[1].classList.remove('d-none')
                    varianObj.forEach(macam => {
                        containerVarianElm.innerHTML += '<button class="btn-default" onclick="pilihWarna(`' + idSelected + '`,`' + macam.nama + '`)">' + macam.nama + '</button>'
                    })
                } else {
                    console.log("/gudang/actionscan/" + decodedText + "/" + namaSelected.split("(")[1].slice(0, -1))
                    window.location.href = "/gudang/actionscan/" + decodedText + "/" + namaSelected.split("(")[1].slice(0, -1);
                }
            } else {
                alertNoCorrectElm.classList.remove('d-none');
            }
            // idSelected = ''
            // namaSelected = ''
        });
    }

    function pilihWarna(id, warna) {
        console.log(namaSelected)
        if (warna.toUpperCase() == namaSelected.split("(")[1].slice(0, -1).toUpperCase())
            // console.log("/gudang/actionscan/" + id + "/" + warna)
            window.location.href = "/gudang/actionscan/" + id + "/" + warna;
        else alertNoCorrectElm.classList.remove('d-none');
    }

    function closemodal() {
        html5QrcodeScanner.clear();
        modelScanElm.classList.remove("d-flex");
        modelScanElm.classList.add("d-none");
    }

    function tabel() {
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            containerTable.forEach(container => {
                container.innerHTML = this.responseText;
            })
        }
        xhttp.open("GET", "/gudang/listordertable");
        xhttp.send();
    }

    tabel();
    // setInterval(() => {
    //     tabel();
    // }, 1000);
</script>

<?= $this->endSection(); ?>