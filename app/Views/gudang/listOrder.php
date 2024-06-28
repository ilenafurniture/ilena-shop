<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div class="d-none justify-content-center align-items-center w-100" id="model-scan"
    style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white w-50" style="border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <div style="padding: 2em;" class="d-flex justify-content-center align-items-center flex-column gap-4">
            <div>
                <h6 class="text-center">Scan barang yang telah di packing disini:</h6>
                <div class="pemberitahuan my-1 d-none" role="alert" id="alert-no-correct">Code tidak sesuai</div>
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div style="width: 500px" id="reader"></div>
                </div>
            </div>
            <button class="btn-default-merah" onclick="closemodal()">Batal</button>
        </div>
    </div>
</div>
<div style="padding: 2em;">
    <!-- <div class="d-flex justify-content-between gap-4"> -->
    <div>
        <h6 class="text-center mb-3">Pesanan belum diproses</h6>
        <?php if ($val['msg']) { ?>
        <div class="pemberitahuan my-1" role="alert">
            <?= $val['msg']; ?>
        </div>
        <?php } ?>
        <div style="overflow: auto;">
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Pesanan</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 4;">Nama dan varian</div>
                    <div style="flex: 3;">ID Barang</div>
                    <div style="flex: 1;">Stok Barang</div>
                    <div style="flex: 2;">Target Selesai</div>
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
                    <div style="flex: 3;"><?= $p['id_barang']; ?></div>
                    <div style="flex: 1;"><?= $p['stok']; ?></div>
                    <div style="flex: 2;"><?= $p['target_selesai']; ?></div>
                    <div style="flex: 2;">
                        <button class="btn-default"
                            onclick="openScan('<?= $p['id_barang'] ?>','<?= $p['nama'] ?>')">Scan</button>
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
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- </div> -->
</div>
<script>
const modelScanElm = document.getElementById('model-scan');
const alertNoCorrectElm = document.getElementById('alert-no-correct');
let idSelected = ''
let namaSelected = ''
var html5QrcodeScanner;

function openScan(idBarang, nama) {
    console.log(idBarang, nama);
    idSelected = idBarang;
    namaSelected = nama;
    modelScanElm.classList.add("d-flex");
    modelScanElm.classList.remove("d-none");

    html5QrcodeScanner = new Html5QrcodeScanner("reader", {
        fps: 10,
        qrbox: 250,
    });
    html5QrcodeScanner.render((decodedText, decodedResult) => {
        console.log(`Scan result: ${decodedText}`, decodedResult);
        html5QrcodeScanner.clear();
        if (decodedText = idSelected) {
            window.location.href = "/gudang/actionscan/" + decodedText + "/" + namaSelected.split("(")[1].slice(
                0, -1);
        } else {
            alertNoCorrectElm.classList.remove('d-none');
        }
    });
}

function closemodal() {
    html5QrcodeScanner.clear();
    modelScanElm.classList.remove("d-flex");
    modelScanElm.classList.add("d-none");
}
</script>

<?= $this->endSection(); ?>