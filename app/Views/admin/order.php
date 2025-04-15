<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
    [data-bs-toggle="tooltip"] {
        color: gray;
    }
</style>
<div id="edit-resi" class="d-none justify-content-center align-items-center" style="position: fixed; top: 0; left: 0; height: 100svh; width: 100vw; background-color: rgba(0,0,0,0.8); z-index: 101;">
    <div class="p-4" style="background-color: white; border-radius: 1em">
        <form action="/admin/actioneditresi" method="post">
            <h5 class="teks-sedang mb-2">Edit resi</h5>
            <div class="mb-1">
                <label for="">Nama Ekspedisi</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-1">
                <label for="">Layanan Ekspedisi</label>
                <input type="text" name="deskripsi" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="">Resi</label>
                <input type="text" name="resi" class="form-control" required>
            </div>
            <div class="d-flex gap-1 justify-content-end">
                <button type="submit" class="btn-default">Simpan</button>
                <button type="button" class="btn-teks-aja" onclick="closeEditResi()">Batal</button>
            </div>
            <input type="text" name="idMid" class="d-none">
        </form>
    </div>
</div>
<div style="padding: 2em;">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-2">
        <div>
            <h1 class="teks-sedang">Pesanan Pelanggan Online</h1>
            <p style="color: grey;"><?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pesanan</p>
        </div>
        <a class="btn-default-merah" href="/admin/order/add">Tambah</a>
    </div>
    <div class="container-table show-block-ke-hide">
        <div class="header-table border-buttom border-dark">
            <div style="flex: 2;">ID Pesanan</div>
            <div style="flex: 2;">Tanggal</div>
            <div style="flex: 2;">Penerima</div>
            <div style="flex: 2;">Harga</div>
            <div style="flex: 1;">Status</div>
            <!-- <div style="flex: 2;" class="d-flex justify-content-center">Action</div> -->
        </div>
        <div class="d-flex flex-column gap-1">
            <?php foreach ($pesanan as $ind_p => $p) { ?>
                <div style="box-shadow: 0 8px 6px -6px rgba(0,0,0,0.2);" class="px-3 py-2">
                    <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>, event)">
                        <div style="flex: 2;"><?= $p['id_midtrans']; ?></div>
                        <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y", $transactionTime);
                        ?>
                        <div style="flex: 2;"><?= $tgl; ?></div>
                        <div style="flex: 2;"><?= $p['nama']; ?></div>
                        <div style="flex: 2;">Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                        <div style="flex: 1;">
                            <span class="badge rounded-pill <?php
                                                            switch ($p['status']) {
                                                                case 'Menunggu Pembayaran':
                                                                    echo "text-bg-primary";
                                                                    break;
                                                                case 'Proses':
                                                                    echo "text-bg-warning";
                                                                    break;
                                                                case 'Dikirim':
                                                                    echo "text-bg-info";
                                                                    break;
                                                                case 'Selesai':
                                                                    echo "text-bg-success";
                                                                    break;
                                                                case 'Dibatalkan':
                                                                    echo "text-bg-danger";
                                                                    break;
                                                                case 'Gagal':
                                                                    echo "text-bg-danger";
                                                                    break;
                                                                default:
                                                                    echo "text-bg-dark";
                                                                    break;
                                                            }
                                                            ?>"><?= ucfirst($p['status']); ?></span>
                        </div>
                    </div>
                    <div style="flex: 2;" class="d-flex justify-content-center border-top">
                        <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Label barang" class="btn" href="/admin/labelbarang/<?= $p['id_midtrans']; ?>" target="_blank"><i class="material-icons">label_outline</i></a>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice" class="btn" href="/invoice/<?= $p['id_midtrans']; ?>"><i class="material-icons">description</i></a>
                        <?php if ($p['status'] == 'Proses' || $p['status'] == 'Dikirim') { ?>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit resi" class="btn" onclick="openEditResi('<?= $p['id_midtrans']; ?>')"><i class="material-icons">edit</i></a>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Surat jalan (<?= $p['status_print'] ? $p['status_print'] : 'No status' ?>)" class="btn" <?= $p['status_print'] == 'sudah print' ? 'style="color: var(--merah);"' : '' ?> href="/admin/suratjalan/<?= $p['id_midtrans']; ?>" target="_blank"><i class="material-icons">local_shipping</i></a>
                        <?php } ?>
                        <?php if ($p['status'] == 'Menunggu Pembayaran') { ?>
                            <a class="btn" href="/cancelorder/<?= $p['id_midtrans']; ?>"><i class="material-icons" style="color: var(--merah);">cancel</i></a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="hide-ke-show-block">
        <div class="container-table" style="width: 700px; overflow-x: auto;">
            <div class="header-table border-buttom border-dark">
                <div style="flex: 1;">ID Pesanan</div>
                <div style="flex: 2;">Tanggal</div>
                <div style="flex: 2;">Penerima</div>
                <div style="flex: 2;">Harga</div>
                <div style="flex: 1;">Status</div>
                <div style="flex: 2;" class="d-flex justify-content-center">Action</div>
            </div>
            <?php foreach ($pesanan as $ind_p => $p) { ?>
                <div>
                    <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>, event)">
                        <div style="flex: 1;"><?= $p['id_midtrans']; ?></div>
                        <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y", $transactionTime);
                        ?>
                        <div style="flex: 2;"><?= $tgl; ?></div>
                        <div style="flex: 2;"><?= $p['nama']; ?></div>
                        <div style="flex: 2;">Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                        <div style="flex: 1;">
                            <span class="badge rounded-pill <?php
                                                            switch ($p['status']) {
                                                                case 'Menunggu Pembayaran':
                                                                    echo "text-bg-primary";
                                                                    break;
                                                                case 'Proses':
                                                                    echo "text-bg-warning";
                                                                    break;
                                                                case 'Dikirim':
                                                                    echo "text-bg-info";
                                                                    break;
                                                                case 'Selesai':
                                                                    echo "text-bg-success";
                                                                    break;
                                                                case 'Dibatalkan':
                                                                    echo "text-bg-danger";
                                                                    break;
                                                                case 'Gagal':
                                                                    echo "text-bg-danger";
                                                                    break;
                                                                default:
                                                                    echo "text-bg-dark";
                                                                    break;
                                                            }
                                                            ?>"><?= ucfirst($p['status']); ?></span>
                        </div>
                        <div style="flex: 2;" class="d-flex justify-content-center">
                            <a class="btn" href="/admin/labelbarang/<?= $p['id_midtrans']; ?>" target="_blank"><i class="material-icons">label_outline</i></a>
                            <a class="btn" href="/invoice/<?= $p['id_midtrans']; ?>"><i class="material-icons">description</i></a>
                            <?php if ($p['status'] == 'Proses' || $p['status'] == 'Dikirim') { ?>
                                <a class="btn" href="/editresi/<?= $p['id_midtrans']; ?>"><i class="material-icons">edit</i></a>
                            <?php } ?>
                            <?php if ($p['status'] == 'Menunggu Pembayaran' || $p['status'] == 'Proses' || $p['status'] == 'Dikirim') { ?>
                                <a class="btn" href="/cancelorder/<?= $p['id_midtrans']; ?>"><i class="material-icons" style="color: var(--merah);">cancel</i></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
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
            <!-- <div class="d-flex py-1">
            <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Lemari (Sonoma)</p>
            <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">2</p>
            <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp 2000.000</p>
        </div> -->
        </div>
    </div>

    <div class="d-flex gap-1 mt-3 justify-content-center">
        <a href="" class="btn-default btnBawah">Invoice</a>
        <a href="" class="btn-default btnBawah">Edit resi</a>
        <a href="" class="btn-default-merah btnBawah">Cancel Pesanan</a>
        <a href="" class="btn-default btnBawah">Detail</a>
    </div>
</div>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
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
    const btnBawahElm = document.querySelectorAll(".btnBawah");
    const editResiElm = document.getElementById('edit-resi');

    function openEditResi(idMid) {
        editResiElm.classList.remove('d-none')
        editResiElm.classList.add('d-flex')
        document.querySelector('input[name="idMid"]').value = idMid
    }

    function closeEditResi() {
        editResiElm.classList.add('d-none')
        editResiElm.classList.remove('d-flex')
    }

    function openDetail(indP, event) {
        const pesananSelected = pesanan[indP];
        console.log(pesananSelected)
        idElm.innerHTML = pesananSelected.id_midtrans
        namaElm.innerHTML = pesananSelected.nama
        nohpElm.innerHTML = pesananSelected.nohp
        alamatElm.innerHTML = pesananSelected.alamat.alamat_lengkap
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
        btnBawahElm[0].href = "/invoice/" + pesananSelected.id_midtrans
        btnBawahElm[1].href = "/editresi/" + pesananSelected.id_midtrans
        btnBawahElm[2].href = "/cancelorder/" + pesananSelected.id_midtrans
        btnBawahElm[3].href = "/order/" + pesananSelected.id_midtrans
        containerOffcanvasElm.classList.add("show")
    }

    function closeDetail() {
        containerOffcanvasElm.classList.remove("show")
    }
</script>

<?= $this->endSection(); ?>