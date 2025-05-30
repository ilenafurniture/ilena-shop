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

    <style>
        * {
            font-size: 14px;
        }
    </style>
</head>

<?php setlocale(LC_TIME, 'id_ID'); ?>

<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between my-5">
            <div style="flex: 1;" class="d-flex align-items-between gap-2">
                <div class="d-flex gap-4 justify-content-start mb-4">
                    <div>
                        <img src="<?= base_url('img/logo/logo-invoice.jpg'); ?>" alt="Logo" width="70" height="40" />
                    </div>
                    <div class="d-flex flex-column justify-content-center gap-1">
                        <h5 class="tw-bold m-0" style="font-size: large;">CV.CATUR BHAKTI MANDIRI</h5>
                        <h5 class="tw-bold m-0">Kawasan Industri BSB, A 3A, 5-6 Jatibarang,Mijen
                            Semarang</h5>
                    </div>
                </div>
                <div style="flex: 1;" class="d-flex justify-content-end align-items-center">
                    <img src="<?= base_url('img/LogoIlena.png'); ?>" alt="logo Ilena"
                        style="width:60mm; height:fit-content">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <div style="width: 300px;">
                <p class="m-0">Kepada Yth.</p>
                <p class="m-0"><?= $pemesanan['nama']; ?></p>
                <p class="m-0"><?= $pemesanan['alamat_pengiriman']; ?></p>
            </div>
        </div>
        <p class="m-0 fw-bold">KOREKSI SURAT PENGANTAR NO.
            <?= substr($pemesanan['id_pesanan'], 5); ?>/SK/<?= date('m', strtotime($pemesanan['tanggal'])); ?>/<?= date('Y', strtotime($pemesanan['tanggal'])); ?>
            </b>
        </p>
        <!-- INI Table Pengantar -->
        <div class="table-responsive mt-3">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 10px;">NO</th>
                        <th class="text-center">KODE BARANG</th>
                        <th class="text-center">NAMA BARANG</th>
                        <th class="text-center">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $t) { ?>
                        <tr>
                            <td class="text-center"><?= strtoupper($no++); ?></td>
                            <td class="text-center"><?= strtoupper($t['id_barang']); ?></td>
                            <td class="">
                                <p class="m-0"><?= strtoupper($t['nama']); ?> (<?= strtoupper($t['varian']); ?>)</p>
                                <p class="m-0"><?= $t['dimensi']['panjang'] ?> x <?= $t['dimensi']['lebar'] ?> x
                                    <?= $t['dimensi']['tinggi'] ?></p>
                            </td>
                            <td class="text-center"><?= $t['jumlah']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <p class="m-0"><b>Keterangan : </b><span
                    class="text-danger"><?= $pemesanan['keterangan'] ? '*' . $pemesanan['keterangan'] : '<i>Tidak ada keterangan</i>'; ?></span>
            </p>


            <div class="d-flex justify-content-end mt-5">
                <div style="width: 300px;">
                    <p>Kendal, <?= date('d F Y', strtotime($pemesanan['tanggal'])); ?></p>
                    <p class="m-0"><b>Dibuat Oleh :</b></p>
                    <br>
                    <br>
                    <br>
                    <p class="m-0"><b>Admin Ilena</b></p>
                    <br>
                    <!-- <p class="m-0"><b>____________________</b></p> -->
                </div>
            </div>
        </div>
    </div>
    <script>
        // window.print();
        // window.onafterprint = function() {
        //     window.close(
        //         window.location.href = "<?= base_url('/admin/order/offline/' . $pemesanan['jenis']); ?>"
        //     );
        // };
    </script>
</body>

</html>