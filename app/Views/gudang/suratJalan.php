<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title><?= $title; ?> | Ilena Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="/css/style_pdf.css" />
</head>

<body>
    <div class="print">
        <div class="d-flex justify-content-between">
            <div style="flex: 1;">
                <div class="d-flex gap-2 align-items-center">
                    <img src="<?= base_url('img/Logo CBM.png'); ?>" alt="Logo CBM"
                        style="width:20mm; height:fit-content">
                    <p class="m-0 fw-bold">CV. CATUR BHAKTI MANDIRI</p>
                </div>
            </div>
            <div style="flex: 1;">
                <img src="<?= base_url('img/LogoIlena.png'); ?>" alt="logo Ilena"
                    style="width:80mm; height:fit-content">
                <p class="m-0">Kepada Yth.</p>
                <p class="m-0">Nama: <?= $pemesanan['nama']; ?></p>
                <p class="m-0"><?= $pemesanan['alamat']; ?></p>
            </div>
        </div>
        <?php $tglNoSrtJln = date("/m/Y", strtotime($pemesanan['data_mid']['transaction_time'])); ?>
        <p class="m-0">SURAT JALAN NO. <?= $pemesanan['id_midtrans']; ?><?= $tglNoSrtJln; ?></p>
        <div class="w-100 border-bottom pb-2 d-flex">
            <div style="flex: 8;">
                <div class="w-100 d-flex border-bottom border-top border-dark py-2">
                    <div style="flex:0.5;">
                        <p class="m-0">No</p>
                    </div>
                    <div style="flex:2;">
                        <p class="m-0">Kode barang</p>
                    </div>
                    <div style="flex:4;">
                        <p class="m-0">Nama barang</p>
                    </div>
                    <div style="flex:1;">
                        <p class="m-0">Jumlah</p>
                    </div>
                </div>
                <?php foreach ($pemesanan['items'] as $ind_i => $i) {
                    if ($i['name'] != 'Biaya Admin' && $i['name'] != 'Voucher') { ?>
                <div class="w-100 d-flex">
                    <div style="flex:0.5;">
                        <p class="m-0"><?= $ind_i + 1; ?></p>
                    </div>
                    <div style="flex:2;">
                        <p class="m-0"><?= $i['id']; ?></p>
                    </div>
                    <div style="flex:4;">
                        <p class="m-0"><?= $i['name']; ?></p>
                    </div>
                    <div style="flex:1;">
                        <p class="m-0"><?= $i['quantity']; ?> Unit</p>
                    </div>
                </div>
                <?php }
                } ?>
            </div>
            <div style="flex: 2;">
                <div class="w-100 border-bottom border-top border-dark py-2">
                    <p class="m-0">Keterangan</p>
                </div>
                <div class="w-100">
                    <p class="m-0"><?= $pemesanan['keterangan_suratjalan']; ?></p>
                </div>
            </div>
        </div>
        <p class="mb-3">Keterangan: permintaan harus selesai di pukul 17:00 Wib</p>
        <p class="m-0">Kendal, <?= $tanggal; ?></p>
        <div class="d-flex mb-5 justify-content-between">
            <div style="flex:1;">
                <p class="m-0">Dibuat Oleh</p>
            </div>
            <!-- <div style="flex:1;" class="d-flex flex-column align-items-center">
                <p class="m-0">Angkutan</p>
                <p class="m-0">No. Pol</p>
            </div> -->
            <div style="flex:1;" class="d-flex flex-column align-items-end">
                <p class="m-0">Penerima</p>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div style="flex:1;">
                <p class="m-0"><?= session()->get('nama'); ?></p>
                <p class="m-0">(‎)</p>
            </div>
            <div style="flex:1;" class="d-flex flex-column align-items-end">
                <p class="m-0">‎ </p>
                <p class="m-0">(‎)</p>
            </div>
            <!-- <div style="flex:1;" class="d-flex flex-column align-items-end">
                <p class="m-0"><?= $pemesanan['nama']; ?></p>
                <p class="m-0">Nama Terang</p>
            </div> -->
        </div>
    </div>
    <script>
    window.print();
    </script>
</body>

</html>