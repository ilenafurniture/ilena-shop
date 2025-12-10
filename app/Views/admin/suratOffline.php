<!-- // app/Views/admin/suratOffline.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title><?= $title; ?> | Ilena Furniture</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <!-- Font (ringan & aman untuk print) -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
    :root {
        --ink: #0f172a;
        --muted: #4b5563;
        --line: #e5e7eb;
        --line2: #f3f4f6;
    }

    html,
    body {
        font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        background: #fff;
    }

    /* tipografi global: kecil & rapi */
    * {
        font-size: 12px;
        line-height: 1.35;
    }

    /* Headline perusahaan */
    h5 {
        font-size: 14.25px;
        font-weight: 600;
        margin: 0;
        letter-spacing: -.15px;
    }

    .subhead {
        font-size: 11px;
        color: var(--muted);
        font-weight: 500;
        margin: 0;
    }

    /* Judul baris nomor dokumen */
    .title-doc {
        font-size: 13.25px;
        font-weight: 600;
        letter-spacing: .02em;
    }

    .divider {
        height: 1px;
        background: var(--line);
        margin: .4rem 0 .8rem;
    }

    /* Tabel (rapi & hemat tinta) */
    .table {
        border-color: var(--line);
    }

    .table thead th {
        background: #fbfbfd !important;
        border-bottom: 1px solid var(--line);
        font-weight: 600;
        color: #0f172a;
        font-size: 10.25px;
        vertical-align: middle;
        padding: .40rem .55rem;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .table tbody td {
        border-color: var(--line2);
        vertical-align: middle;
        font-size: 10.9px;
        padding: .42rem .55rem;
    }

    .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-accent-bg: #fcfdff;
    }

    .nowrap {
        white-space: nowrap;
    }

    /* Area penerima/telepon lebih kalem */
    .to-name {
        font-weight: 600;
    }

    .to-phone {
        color: var(--muted);
    }

    /* Spasi luar dokumen */
    .container {
        margin-top: 20px !important;
        margin-bottom: 20px !important;
    }

    /* Signature blocks */
    .sig-title {
        font-weight: 600;
    }

    .sig-name {
        font-weight: 600;
    }

    /* Print setup */
    @page {
        size: A4;
        margin: 14mm 14mm 16mm;
    }

    @media print {
        .container {
            width: auto !important;
        }

        a[href]:after {
            content: "";
        }

        tr,
        img {
            break-inside: avoid;
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: transparent;
        }
    }
    </style>
</head>

<?php setlocale(LC_TIME, 'id_ID'); ?>

<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="d-flex justify-content-between my-4">
            <div style="flex:1;" class="d-flex align-items-between gap-2">
                <div class="d-flex gap-4 justify-content-start">
                    <div>
                        <img src="<?= base_url('img/logo/logo-invoice.jpg'); ?>" alt="Logo" width="70" height="40" />
                    </div>
                    <div class="d-flex flex-column justify-content-center gap-1">
                        <h5>CV.CATUR BHAKTI MANDIRI</h5>
                        <p class="subhead">Kawasan Industri BSB, A 3A, 5-6 Jatibarang, Mijen, Semarang</p>
                    </div>
                </div>
            </div>
            <div style="flex:1;" class="d-flex justify-content-end align-items-center">
                <img src="<?= base_url('img/LogoIlena.png'); ?>" alt="logo Ilena" style="width:56mm; height:auto">
            </div>
        </div>

        <?php
        // ===== Nomor Dokumen (NF menampilkan 5 digit setelah 'NF') =====
        $jenisLower = strtolower(trim($pemesanan['jenis'] ?? ''));
        $rawId = (string)($pemesanan['id_pesanan'] ?? '');
        $noBase = '';

        if (preg_match('/^NF(\d+)$/i', $rawId, $m)) {
            $digits = $m[1];
            $last5  = substr($digits, -5);
            $noBase = 'NF' . str_pad($last5, 5, '0', STR_PAD_LEFT);
        } else if ($jenisLower === 'nf') {
            $nomorBase = substr($rawId, 5);
            if (preg_match('/(\d+)/', $nomorBase, $m2)) {
                $last5  = substr($m2[1], -5);
                $noBase = 'NF' . str_pad($last5, 5, '0', STR_PAD_LEFT);
            } else {
                $noBase = 'NF' . $nomorBase;
            }
        } else {
            $noBase = substr($rawId, 5);
        }

        // ===== Kode dokumen & label (support sale, sp, nf) =====
        $kodeDok    = 'SP';
        $labelSurat = 'PENGANTAR';

        // 'sale' atau 'nf' → Surat Jalan (SJ)
        if ($jenisLower === 'sale' || $jenisLower === 'nf') {
            $kodeDok    = 'SJ';
            $labelSurat = 'JALAN';
        } elseif ($jenisLower === 'sp') {
            $kodeDok    = 'SP';
            $labelSurat = 'PENGANTAR';
        }

        $tgl      = strtotime($pemesanan['tanggal']);
        $bulan_id = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $tgl_indo = date('d', $tgl) . ' ' . $bulan_id[(int)date('n',$tgl)] . ' ' . date('Y', $tgl);
        ?>

        <div class="d-flex justify-content-between align-items-end">
            <div style="flex:1">
                <p class="m-0 title-doc">
                    SURAT <?= $labelSurat; ?> NO.
                    <?= $noBase; ?>/<?= $kodeDok; ?>/<?= date('m', $tgl); ?>/<?= date('Y', $tgl); ?>
                </p>
                <div class="divider"></div>
            </div>
            <div style="flex:1" class="mb-4">
                <p class="m-0" style="font-weight:500;">Kepada Yth.</p>
                <p class="m-0 to-name"><?= $pemesanan['nama']; ?></p>
                <?php if (!empty($pemesanan['nohp'])): ?>
                <p class="m-0 to-phone"><?= $pemesanan['nohp']; ?></p>
                <?php endif; ?>
                <p class="m-0"><?= $pemesanan['alamat_pengiriman']; ?></p>
            </div>
        </div>

        <!-- Tabel Pengantar -->
        <div class="table-responsive mt-2">
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
                    <?php $no = 1; foreach ($items as $t): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <!-- PAKAI id_baru -->
                        <td class="text-center"><?= strtoupper($t['id_baru']); ?></td>
                        <td>
                            <p class="m-0" style="font-size:10.9px; font-weight:500;">
                                <?= strtoupper($t['nama']); ?> (<?= strtoupper($t['varian']); ?>)
                            </p>
                            <p class="m-0" style="font-size:10.6px; color:var(--muted);">
                                <?= $t['dimensi']['panjang']; ?> x <?= $t['dimensi']['lebar']; ?> x
                                <?= $t['dimensi']['tinggi']; ?>
                            </p>
                        </td>
                        <td class="text-center"><?= $t['jumlah']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php
                // Deteksi kalau ini surat hasil relasi Project Interior
                $isInterior = (!empty($is_project_interior))
                    || (!empty($project) && isset($project['kode_project']));

                $ketRaw = trim((string)($pemesanan['keterangan'] ?? ''));

                // KHUSUS INTERIOR:
                // walaupun kolom keterangan terisi auto (misal: CI0011... - Furniture Interior Lokal ...),
                // JANGAN dipakai; anggap kosong saja supaya output "Tidak ada keterangan"
                if ($isInterior) {
                    $ketRaw = '';
                }
            ?>
            <p class="m-0">
                <b style="font-weight:600;">Keterangan : </b>
                <span class="text-danger">
                    <?= $ketRaw !== '' 
                        ? '*'.esc($ketRaw) 
                        : '<i style="color:inherit;">Tidak ada keterangan</i>'; ?>
                </span>
            </p>


            <p class="mt-4" style="font-weight:500;">Kendal, <?= $tgl_indo; ?></p>

            <!-- Tanda Tangan -->
            <div class="d-flex justify-content-between mt-4">
                <div class="text-center" style="min-width:180px;">
                    <p class="m-0 sig-title">Dibuat Oleh :</p>
                    <br><br><br>
                    <p class="m-0 sig-name">Admin Ilena</p>
                    <p class="m-0 sig-name">____________________</p>
                </div>
                <div class="text-center" style="min-width:180px;">
                    <p class="m-0 sig-title">Dibawa Oleh :</p>
                    <br><br><br><br>
                    <p class="m-0 sig-name">____________________</p>
                </div>
                <div class="text-center" style="min-width:180px;">
                    <p class="m-0 sig-title">Diterima Oleh :</p>
                    <br><br><br><br>
                    <p class="m-0 sig-name">____________________</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // window.print();
    // window.onafterprint = function() {
    //     window.location.href = "<?= base_url('admin/order/offline/sale'); ?>";
    // };
    </script>
</body>

</html>