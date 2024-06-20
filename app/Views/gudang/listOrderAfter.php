<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div id="form-ajukan" style="background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left:0; width: 100vw; height: 100svh; z-index: 3;" class="d-none justify-content-center align-items-center">
    <div class="bg-light p-4" style="border-radius: 1em;">
        <form action="ajukanprint" method="post">
            <h1 class="teks-sedangmb-3">Ajuan Print Ulang</h1>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="kendala" required>
                <label for="floatingInput">Kendala</label>
            </div>
            <input type="text" class="d-none" name="id_midtrans">
            <div class="d-flex gap-1">
                <button class="btn-default" type="submit">Ajukan</button>
                <button class="btn-default-merah" type="button" onclick="closeAjukan()">Batal</button>
            </div>
        </form>
    </div>
</div>
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
                    <?php if ($p['status_print'] == 'siap') { ?>
                        <a class="btn-default" href="/gudang/suratjalan/<?= $p['id_midtrans']; ?>">Print</a>
                    <?php } else if ($p['status_print'] == 'sudah print') { ?>
                        <button class="btn-default" onclick="openAjukan('<?= $p['id_midtrans']; ?>')">Ajukan print</button>
                    <?php } else if ($p['status_print'] == 'ajukan') { ?>
                        <p class="m-0 text-secondary">Proses pengajuaan</p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="container-offcanvas" style="z-index: 2;">
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
    const formAjukanElm = document.getElementById('form-ajukan')

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
        });
        containerOffcanvasElm.classList.add("show")
    }

    function closeDetail() {
        containerOffcanvasElm.classList.remove("show")
    }

    function printSuratJalan(idMid) {
        console.log(idMid);
        var printWindow = window.open('https://ilenafurniture.com/gudang/suratjalan/'.idMid, '_blank');
        printWindow.addEventListener('load', function() {
            printWindow.print();
            printWindow.onafterprint = function() {
                printWindow.close();
            };
        });
    }

    function openAjukan(idMid) {
        console.log(idMid)
        document.querySelector('input[name="id_midtrans"]').value = idMid
        formAjukanElm.classList.remove('d-none');
        formAjukanElm.classList.add('d-flex');
    }

    function closeAjukan() {
        formAjukanElm.classList.add('d-none');
        formAjukanElm.classList.remove('d-flex');
    }
</script>

<?= $this->endSection(); ?>