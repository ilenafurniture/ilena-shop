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
    .tw-bold-italic {
        font-weight: normal;
        font-style: italic;
    }

    .nt {
        font-weight: normal;
        color: black;
        font-style: italic;
        margin: 0;
    }

    .isint {
        font-weight: normal;
        font-style: italic;
        margin: 0;
    }

    .kotak-pembayaran {
        border: 1px solid red;
        padding: 10px 20px;
        margin-top: 20px;
        margin-bottom: 20px;
        align-self: center;
        text-align: center;
        font-weight: normal;
        font-style: italic;
    }

    * {
        font-size: 14px;
    }

    .print-lunas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 20px;
        /* background-color: aqua; */
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 20%;
    }

    .print-lunas p {
        font-size: 4em;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 900;
        margin: 0;
        /* initial-letter-align: center; */
        /* transform: rotate(-15deg) translateY(500px); */
        transform: translateY(500px) rotate(-15deg);

        border: 10px solid var(--merah);
        color: var(--merah);
        border-radius: 0.5em;
    }
    </style>

</head>

<?php setlocale(LC_TIME, 'id_ID'); ?>

<body>
    <?php if($pemesanan['down_payment'] <= 0) {?>
    <div class="print-lunas">
        <p>L U N A S</p>
    </div>
    <?php } ?>
    <div class="container mt-5 mb-5">
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
        <div class="d-flex">
            <div style="flex: 1;">

            </div>
            <div class="d-flex gap-2 justify-content-end">
                <div class="d-flex flex-column align-items-end">
                    <p class="nt">Nomor :</p>
                    <p class="nt">Tanggal :</p>
                </div>
                <?php $tanggalFix = $pemesanan['down_payment'] > 0 ? $pemesanan['tanggal'] : $pemesanan['tanggal_inv'] ?>
                <div class="d-flex flex-column align-items-start">
                    <p class="isint">
                        <?= substr($pemesanan['id_pesanan'], 5); ?>/INV/CBM/<?= date('m', strtotime($tanggalFix)); ?>/<?= date('Y', strtotime($tanggalFix)); ?>
                    </p>
                    <p class="isint"><?php
                                        $bulan_indonesia = [
                                            1 => 'Januari',
                                            2 => 'Februari',
                                            3 => 'Maret',
                                            4 => 'April',
                                            5 => 'Mei',
                                            6 => 'Juni',
                                            7 => 'Juli',
                                            8 => 'Agustus',
                                            9 => 'September',
                                            10 => 'Oktober',
                                            11 => 'November',
                                            12 => 'Desember'
                                        ];
                                        $tanggal = date('d', strtotime($tanggalFix));
                                        $bulan = date('n', strtotime($tanggalFix));
                                        $tahun = date('Y', strtotime($tanggalFix));
                                        echo "$tanggal " . $bulan_indonesia[$bulan] . " $tahun";
                                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="my-1">
            <h3 class="text-center">INVOICE</h3>
        </div>
        <div class="d-flex justify-content-start mt-4 mb-4 flex-column">
            <p class="m-0 nt">Kepada Yth.</p>
            <div class="d-flex flex-column">
                <p class="m-0 tw-bold-italic"><?= $pemesanan['nama']; ?></p>
                <div class="d-flex m-0 p-0">
                    <p class="m-0 tw-bold-italic" style="margin-right: 10px;"><span class="m-0" id="jalan"></span>,</p>
                    <p class="m-0 tw-bold-italic" style="margin-right: 10px;"><span class="m-0" id="kelurahan"></span>,
                    </p>
                </div>

                <div class="d-flex m-0 p-0">
                    <p class="m-0 tw-bold-italic" style="margin-right: 10px;"><span class="m-0" id="kecamatan"></span>,
                    </p>
                    <p class="m-0 tw-bold-italic" style="margin-right: 10px;"><span class="m-0" id="kota"></span>,</p>
                </div>

                <div class="d-flex m-0 p-0">
                    <p class="m-0 tw-bold-italic" style="margin-right: 10px;"><span id="provinsi"></span>,</p>
                    <p class="m-0 tw-bold-italic"><span id="kodepos"></span>,</p>
                </div>
                <p class="isint">NPWP : <?= $pemesanan['npwp'] ? $pemesanan['npwp'] : '-'; ?></p>
            </div>
        </div>

        <!-- Tabel Invoice -->
        <div class="table-responsive mt-2">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 10px;">NO</th>
                        <th class="text-center">KODE BARANG</th>
                        <th class="text-center">NAMA BARANG</th>
                        <th class="text-center">KUANTITAS</th>
                        <th class="text-center">HARGA</th>
                        <th class="text-center">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($pemesanan['down_payment'] <= 0) { ?>
                    <?php $no = 1; ?>
                    <?php $totalHargaBarang  = 0; ?>
                    <?php foreach ($items as $t) { ?>
                    <?php $totalHargaBarang += $t['jumlah'] * $t['harga']; ?>
                    <tr>
                        <td class="text-center"><?= strtoupper($no++); ?></td>
                        <td class="text-center"><?= strtoupper($t['id_barang']); ?></td>
                        <td class="">
                            <p class="m-0">
                                <?= $t['special_price'] > 0 ? "[SPECIAL PRICE] " : ""; ?><?= strtoupper($t['nama']); ?>
                                (<?= strtoupper($t['varian']); ?>)</p>
                            <p class="m-0"><?= $t['dimensi']['panjang']; ?> x <?= $t['dimensi']['lebar']; ?> x
                                <?= $t['dimensi']['tinggi']; ?></p>
                        </td>
                        <td class="text-center"><?= strtoupper($t['jumlah']); ?></td>
                        <td class="text-end" style="text-wrap: nowrap;">Rp
                            <?= strtoupper(number_format($t['harga'], 0, ',', '.')); ?></td>
                        <td class="text-end" style="text-wrap: nowrap;">Rp
                            <?= strtoupper(number_format($t['jumlah'] * $t['harga'], 0, ',', '.')); ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="5" class="fw-bold">JUMLAH</td>
                        <td class="text-end" style="text-wrap: nowrap;">Rp
                            <?= number_format($totalHargaBarang, 0, ',', '.'); ?></td>
                    </tr>
                    <?php $diskonPersen = round(100 - ($totalHargaBarang / $pemesanan['total_akhir'] * 100), 2); ?>
                    <tr>
                        <td colspan="5" class="text-start fw-bold italic" style="text-wrap: nowrap;">POTONGAN
                            <?= $diskonPersen > 0 ? "( $diskonPersen% )" : ''; ?></td>
                        <td class="text-end fw-bold italic" style="text-wrap: nowrap;">Rp
                            <?= strtoupper(number_format($pemesanan['total_akhir'] - $totalHargaBarang, 0, ',', '.')); ?>
                        </td>
                    </tr>
                    <?php if($pemesanan['down_payment'] < 0) { ?>
                    <tr>
                        <td colspan="5" class="fw-bold">UANG MUKA YANG DITERIMA</td>
                        <td class="text-end" style="text-wrap: nowrap;">Rp
                            <?= number_format(abs($pemesanan['down_payment']), 0, ',', '.'); ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr>
                        <td class="text-center">1</td>
                        <td class="text-center"></td>
                        <td class="">UANG MUKA</td>
                        <td class="text-center"></td>
                        <td class="text-end" style="text-wrap: nowrap;"></td>
                        <td class="text-end" style="text-wrap: nowrap;">Rp
                            <?= strtoupper(number_format($pemesanan['down_payment'], 0, ',', '.')); ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="5" class="fw-bold">TOTAL INVOICE</td>
                        <?php if($pemesanan['down_payment'] > 0) { ?>
                        <td class="text-end fw-bold" style="text-wrap: nowrap;">Rp
                            <?= strtoupper(number_format($pemesanan['down_payment'] , 0, ',', '.')); ?>
                        </td>
                        <?php } else { ?>
                        <td class="text-end fw-bold" style="text-wrap: nowrap;">Rp
                            <?= strtoupper(number_format($pemesanan['total_akhir'] - (($pemesanan['down_payment']) < 0 ? abs($pemesanan['down_payment']) : 0), 0, ',', '.')); ?>
                        </td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- end Tabel Invoice -->
        <div>
            <?php
            function terbilang($angka)
            {
                $angka = (int) $angka;
                $huruf = array(
                    1 => 'Satu',
                    2 => 'Dua',
                    3 => 'Tiga',
                    4 => 'Empat',
                    5 => 'Lima',
                    6 => 'Enam',
                    7 => 'Tujuh',
                    8 => 'Delapan',
                    9 => 'Sembilan',
                    0 => 'Nol'
                );
                $tingkat = array(
                    '',
                    'Ribu',
                    'Juta',
                    'Miliar',
                    'Triliun',
                    'Kuadriliun',
                    'Kuintiliun'
                );

                if ($angka == 0) {
                    return "Nol Rupiah";
                }

                $kalimat = '';
                $i = 0;
                while ($angka > 0) {
                    $bagian = $angka % 1000;
                    if ($bagian != 0) {
                        $kalimat = _terbilang_ratusan($bagian) . ' ' . $tingkat[$i] . ' ' . $kalimat;
                    }
                    $angka = (int) ($angka / 1000);
                    $i++;
                }

                return $kalimat . 'Rupiah';
            }

            function _terbilang_ratusan($angka)
            {
                $huruf = array(
                    1 => 'Satu',
                    2 => 'Dua',
                    3 => 'Tiga',
                    4 => 'Empat',
                    5 => 'Lima',
                    6 => 'Enam',
                    7 => 'Tujuh',
                    8 => 'Delapan',
                    9 => 'Sembilan',
                    0 => 'Nol'
                );

                $kalimat = '';
                if ($angka >= 100) {
                    $kalimat .= $huruf[(int)($angka / 100)] . ' Ratus ';
                    $angka %= 100;
                }
                if ($angka >= 10) {
                    $kalimat .= _terbilang_puluhan($angka);
                } else {
                    $kalimat .= $huruf[$angka];
                }

                return $kalimat;
            }

            function _terbilang_puluhan($angka)
            {
                $huruf = array(
                    10 => 'Sepuluh',
                    11 => 'Sebelas',
                    12 => 'Dua Belas',
                    13 => 'Tiga Belas',
                    14 => 'Empat Belas',
                    15 => 'Lima Belas',
                    16 => 'Enam Belas',
                    17 => 'Tujuh Belas',
                    18 => 'Delapan Belas',
                    19 => 'Sembilan Belas',
                    20 => 'Dua Puluh',
                    30 => 'Tiga Puluh',
                    40 => 'Empat Puluh',
                    50 => 'Lima Puluh',
                    60 => 'Enam Puluh',
                    70 => 'Tujuh Puluh',
                    80 => 'Delapan Puluh',
                    90 => 'Sembilan Puluh'
                );

                if ($angka <= 9) {
                    return '';
                } elseif ($angka <= 19) {
                    return $huruf[$angka];
                } else {
                    $puluhan = (int)($angka / 10) * 10;
                    return $huruf[$puluhan] . ' ';
                }
            }
            ?>
            <table>
                <tbody>
                    <tr>
                        <td class="pe-3">Terbilang <?= $pemesanan['down_payment'] > 0 ? 'DP' : '' ?></td>
                        <?php if($pemesanan['down_payment'] > 0) { ?>
                        <td>:
                            <i><?= ucwords(strtolower(terbilang($pemesanan['down_payment']))); ?></i>
                        </td>
                        <?php } else { ?>
                        <td>:
                            <i><?= ucwords(strtolower(terbilang($pemesanan['total_akhir'] - ($pemesanan['down_payment'] < 0 ? abs($pemesanan['down_payment']) : 0)))); ?></i>
                        </td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td class="pe-3">PO</td>
                        <td>: <?= $pemesanan['po'] ? $pemesanan['po'] : '-'; ?></td>
                    </tr>
                    <tr>
                        <td class="pe-3">Surat Jalan</td>
                        <?php
                        if($pemesanan['down_payment']) {?>
                        <td>: <?= substr($items[0]['id_return'], 5); ?></td>
                        <?php } else {?>
                        <td>: <?= substr($pemesanan['id_pesanan'], 5); ?></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>

        </div>

        <div class="d-flex justify-content-between mt-5 mb-3">
            <div class="d-flex flex-column kotak-pembayaran">
                <p class="m-0" style="font-size: 17px;"> Pembayaran mohon dapat ditransfer ke rekening: <br> <b
                        style="font-size: 17px; color: red;">BCA/C 8715898787 an CATUR BHAKTI MANDIRI</b></p>
            </div>
            <div class="d-flex flex-column align-items-center">
                Bagian Keuangan <br>
                <br>
                <br>
                <br>
                <br>
                <p class="tw-bold-italic">Puspita Aprilia Damayanti</p>
            </div>
        </div>

    </div>

    <script>
    const alamatLengkap =
        "<?= $pemesanan['alamat_tagihan'] ? $pemesanan['alamat_tagihan'] : $pemesanan['alamat_pengiriman']; ?>";
    const alamatParts = alamatLengkap.split(', ');
    const jalan = alamatParts[0];
    const kelurahan = alamatParts[1];
    const kecamatan = alamatParts[2];
    const kota = alamatParts[3];
    const provinsiDanKodePos = alamatParts[4].split(' ');
    const provinsi = provinsiDanKodePos[0];
    const kodePos = provinsiDanKodePos[1];
    document.getElementById('jalan').innerText = jalan;
    document.getElementById('kelurahan').innerText = kelurahan;
    document.getElementById('kecamatan').innerText = kecamatan;
    document.getElementById('kota').innerText = kota;
    document.getElementById('provinsi').innerText = provinsi;
    document.getElementById('kodepos').innerText = kodePos;
    </script>


</body>

</html>