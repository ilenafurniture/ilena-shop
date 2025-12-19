<!-- //views/admin/suratOffline.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title><?= esc($title ?? 'Surat'); ?> | Ilena Furniture</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

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

    * {
        font-size: 12px;
        line-height: 1.35;
    }

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

    .to-name {
        font-weight: 600;
    }

    .to-phone {
        color: var(--muted);
    }

    .container {
        margin-top: 20px !important;
        margin-bottom: 20px !important;
    }

    .sig-title,
    .sig-name {
        font-weight: 600;
    }

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
        // ==========================================================
        // KONSEP (konsisten dengan controller):
        // - jenis sp      => SP (Surat Pengantar)
        // - jenis sale/nf => SJ (Surat Jalan)
        // - NF tetap SJ, bedanya nomor pakai prefix "NF"
        // - Nomor dasar ambil 4 digit terakhir dari angka id_pesanan
        // - Pecah SJ pakai sj_ke dari tabel surat_jalan (controller kirim $sj)
        // ==========================================================

        $jenisLower = strtolower(trim($pemesanan['jenis'] ?? ''));

        // tanggal dokumen: gunakan tanggal pemesanan (kalau ada), fallback sekarang
        $tanggalFix = $pemesanan['tanggal'] ?? date('Y-m-d');
        $tglTs = strtotime($tanggalFix);

        // ambil 4 digit terakhir dari id_pesanan (angka mana pun yang muncul)
        $rawId  = (string)($pemesanan['id_pesanan'] ?? '');
        $digits = '0001';
        if (preg_match('/(\d+)/', $rawId, $mDigits)) {
            $angka  = $mDigits[1];
            $digits = substr($angka, -4);
        }
        $base4 = str_pad($digits, 4, '0', STR_PAD_LEFT);

        // nomor dasar tampil: NFxxxx atau xxxx
        $noBase = ($jenisLower === 'nf') ? ('NF' . $base4) : $base4;

        // dokumen type
        $kodeDok    = 'SP';
        $labelSurat = 'PENGANTAR';
        if (in_array($jenisLower, ['sale','nf'], true)) {
            $kodeDok    = 'SJ';
            $labelSurat = 'JALAN';
        }

        // sequence SJ:
        // utamakan dari controller: $sj['sj_ke']
        // fallback: ?seq=2 (kalau memang manual)
        $sjSequence = 1;
        if (isset($sj) && is_array($sj) && !empty($sj['sj_ke']) && (int)$sj['sj_ke'] > 0) {
            $sjSequence = (int)$sj['sj_ke'];
        } elseif (isset($_GET['seq']) && (int)$_GET['seq'] > 0) {
            $sjSequence = (int)$_GET['seq'];
        }

        // nomor dokumen:
        // SJ => NF0012-2  / 0012-1
        // SP => NF0012 atau 0012 (umumnya SP bukan NF, tapi aman saja)
        $noDoc = ($kodeDok === 'SJ') ? ($noBase . '-' . $sjSequence) : $noBase;

        $bulan_id = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $tgl_indo = date('d', $tglTs) . ' ' . $bulan_id[(int)date('n',$tglTs)] . ' ' . date('Y', $tglTs);
        ?>

        <div class="d-flex justify-content-between align-items-end">
            <div style="flex:1">
                <p class="m-0 title-doc">
                    SURAT <?= esc($labelSurat); ?> NO.
                    <?= esc($noDoc); ?>/<?= esc($kodeDok); ?>/<?= date('m', $tglTs); ?>/<?= date('Y', $tglTs); ?>
                </p>
                <div class="divider"></div>
            </div>

            <div style="flex:1" class="mb-4">
                <p class="m-0" style="font-weight:500;">Kepada Yth.</p>
                <p class="m-0 to-name"><?= esc($pemesanan['nama'] ?? '-'); ?></p>
                <?php if (!empty($pemesanan['nohp'])): ?>
                <p class="m-0 to-phone"><?= esc($pemesanan['nohp']); ?></p>
                <?php endif; ?>
                <p class="m-0"><?= esc($pemesanan['alamat_pengiriman'] ?? '-'); ?></p>
            </div>
        </div>

        <!-- Tabel -->
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
                    <?php if (!empty($items) && is_array($items)): ?>
                    <?php $no = 1; foreach ($items as $t): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">
                            <?= esc(strtoupper((string)($t['id_baru'] ?? ''))); ?>
                        </td>
                        <td>
                            <p class="m-0" style="font-size:10.9px; font-weight:500;">
                                <?= esc(strtoupper((string)($t['nama'] ?? ''))); ?>
                                <?php if (!empty($t['varian'])): ?>
                                (<?= esc(strtoupper((string)$t['varian'])); ?>)
                                <?php endif; ?>
                            </p>
                            <p class="m-0" style="font-size:10.6px; color:var(--muted);">
                                <?php
                                            $dim = $t['dimensi'] ?? [];
                                            $p = $dim['panjang'] ?? '-';
                                            $l = $dim['lebar'] ?? '-';
                                            $tg = $dim['tinggi'] ?? '-';
                                        ?>
                                <?= esc($p . ' x ' . $l . ' x ' . $tg); ?>
                            </p>
                        </td>
                        <td class="text-center"><?= (int)($t['jumlah'] ?? 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="color:var(--muted);">
                            Tidak ada item.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php $ketRaw = trim((string)($pemesanan['keterangan'] ?? '')); ?>
            <p class="m-0">
                <b style="font-weight:600;">Keterangan : </b>
                <span class="text-danger">
                    <?= $ketRaw !== ''
                        ? '*'.esc($ketRaw)
                        : '<i style="color:inherit;">Tidak ada keterangan</i>'; ?>
                </span>
            </p>

            <p class="mt-4" style="font-weight:500;">Kendal, <?= esc($tgl_indo); ?></p>

            <!-- Tanda Tangan -->
            <div class="d-flex justify-content-between mt-4">
                <div class="text-center" style="min-width:180px;">
                    <p class="m-0 sig-title">Dibuat Oleh :</p>
                    <br><br><br>
                    <p class="m-0 sig-name">Admin</p>
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
</body>

</html>