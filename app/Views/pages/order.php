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
        <form action="/editsandi/order" method="post">
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
            <a href="/account" class="mb-1 btn-default-abu" style="width: 100%; text-align:center;">Profile</a>
            <a href="/order" class="mb-1 btn-default-abu" style="width: 100%; text-align:center;">Pesanan</a>
            <a href="/logout" class="btn-default-merah" style="width: 100%; text-align:center;">Keluar</a>
        </div>
    </div>
    <div style="flex:1;">
        <div class="mb-4">
            <h1 class="teks-sedang">Pesanan</h1>
            <p style="color: grey;"><?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pemesanan</p>
        </div>
        <div style="overflow-x: auto;" class="show-block-ke-hide">
            <div class="container-table">
                <div class="header-table">
                    <div>ID Pesanan</div>
                    <div>Tanggal</div>
                    <div>Penerima</div>
                    <div>Harga</div>
                    <div>Status</div>
                </div>
                <?php foreach ($pesanan as $ind_p => $p) { ?>
                    <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)">
                        <div><?= $p['id_midtrans']; ?></div>
                        <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                        ?>
                        <div><?= $tgl; ?></div>
                        <div><?= $p['nama']; ?></div>
                        <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                        <div><?= $p['status']; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div style="overflow-x: auto;" class="hide-ke-show-block">
            <div class="container-table" style="width: 600px;">
                <div class="header-table">
                    <div>ID Pesanan</div>
                    <div>Tanggal</div>
                    <div>Penerima</div>
                    <div>Harga</div>
                    <div>Status</div>
                </div>
                <?php foreach ($pesanan as $ind_p => $p) { ?>
                    <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)">
                        <div><?= $p['id_midtrans']; ?></div>
                        <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                        ?>
                        <div><?= $tgl; ?></div>
                        <div><?= $p['nama']; ?></div>
                        <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                        <div><?= $p['status']; ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="container-offcanvas" style="z-index: 100;">
    <div class="d-flex justify-content-end"><button class="btn btn-light" onclick="closeDetail()"><i class="material-icons">close</i></button></div>
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
    <h3 style="letter-spacing: -1px;" class="my-3">Produk</h3>
    <div id="item">
        <div class="d-flex mb-1 border-bottom pb-1">
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p>
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p>
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p>
        </div>
        <!-- <div class="d-flex py-1">
            <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Lemari (Sonoma)</p>
            <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">2</p>
            <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp 2000.000</p>
        </div> -->
    </div>
    <span class="garis mb-5"></span>
    <div class="d-flex justify-content-center gap-1">
        <a href="" class="btn-default" id="btn-invoice">Invoice</a>
        <a href="" class="btn-default" id="btn-detail">Detail Pemesanan</a>
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
    const btnInvoiceElm = document.getElementById('btn-invoice')
    const btnDetailElm = document.getElementById('btn-detail')
    const sandiElm = document.querySelector('input[name="sandi"]');
    const editSandiElm = document.getElementById('edit-sandi')

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

    function openDetail(indP) {
        const pesananSelected = pesanan[indP];
        console.log(pesananSelected)
        idElm.innerHTML = pesananSelected.id_midtrans
        namaElm.innerHTML = pesananSelected.nama
        nohpElm.innerHTML = pesananSelected.nohp
        alamatElm.innerHTML = pesananSelected.alamat
        emailElm.innerHTML = pesananSelected.email
        itemElm.innerHTML = '<div class="d-flex mb-1 border-bottom pb-1"><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p><p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p></div>';
        pesananSelected.items.forEach(element => {
            if (element.name != 'Biaya Ongkir' && element.name != 'Biaya Admin') {
                itemElm.innerHTML += '<div class="d-flex py-1"><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + element.name + '</p><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + element.quantity + '</p><p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp ' + element.price + '</p></div>'
            }
        });
        btnInvoiceElm.href = '/invoice/' + pesananSelected.id_midtrans
        btnDetailElm.href = '/order/' + pesananSelected.id_midtrans
        containerOffcanvasElm.classList.add("show")
    }

    function closeDetail() {
        containerOffcanvasElm.classList.remove("show")
    }
</script>

<?= $this->endSection(); ?>