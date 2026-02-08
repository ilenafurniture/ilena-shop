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
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNxj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
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

    .badge-draft {
        display: inline-block;
        padding: .18rem .5rem;
        border: 1px solid var(--line);
        background: #fff7ed;
        color: #9a3412;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 600;
        margin-left: .4rem;
        vertical-align: middle;
    }

    /* ========================= */
    /* TANDA TANGAN (FIX SEJAJAR) */
    /* ========================= */
    .sig-row {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-start;
        margin-top: 1rem;
    }

    .sig-box {
        min-width: 180px;
        text-align: center;
    }

    /* area cap (tingginya disamakan) */
    .sig-body {
        height: 86px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        /* kasih "kertas" halus biar cap lebih natural */
        background: transparent;
    }

    /* tekstur halus (bukan kotak) */
    .sig-body::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 18% 35%, rgba(0, 0, 0, .025), transparent 58%),
            radial-gradient(circle at 72% 65%, rgba(0, 0, 0, .018), transparent 60%),
            radial-gradient(circle at 45% 82%, rgba(0, 0, 0, .012), transparent 55%);
        opacity: .55;
    }

    /*
      ✅ EFEK "BARU DI CAP" (tanpa miring, tanpa melebar)
      - base: lebih tegas + pressure
      - rough: bleed tipis + blur
      - edge: drop-shadow halus
    */
    .sig-stamp,
    .sig-stamp-rough {
        width: 112px;
        /* FIX: ukuran pasti biar gak melebar */
        height: auto;
        display: block;
        object-fit: contain;
        pointer-events: none;

        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%) scale(.98);
        /* tidak miring */
        background: transparent;

        mix-blend-mode: multiply;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .sig-stamp {
        z-index: 1;
        opacity: .62;
        filter:
            contrast(1.65) saturate(1.10) brightness(.90) blur(.06px) drop-shadow(0 .20px .25px rgba(0, 0, 0, .10)) drop-shadow(0 1.1px 1.4px rgba(0, 0, 0, .06));
    }

    .sig-stamp-rough {
        z-index: 2;
        opacity: .20;
        /* bleed + pressure random-ish */
        transform: translate(-50%, -50%) scale(1.01);
        filter:
            blur(.34px) contrast(1.20) brightness(.92);
    }


    .sig-footer {
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        line-height: 1;
        margin-top: 2px;
    }

    .sig-line {
        font-weight: 600;
        letter-spacing: .02em;
        margin: 0;
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

        .badge-draft {
            border: 1px solid #f59e0b;
            background: transparent;
            color: #9a3412;
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
                <!-- Optional right header -->
            </div>
        </div>

        <?php
        $jenisLower = strtolower(trim($pemesanan['jenis'] ?? ''));
        $idPesanan = strtoupper(trim($pemesanan['id_pesanan'] ?? ''));

        $tanggalFix = $sj['tanggal'] ?? ($pemesanan['tanggal'] ?? date('Y-m-d'));
        $tglTs      = strtotime($tanggalFix);

        // Determine document type based on order jenis OR id_pesanan prefix
        // SP orders can have jenis='display' but id_pesanan starts with 'SP'
        $isSP = ($jenisLower === 'sp') || (strpos($idPesanan, 'SP') === 0);
        
        if ($isSP) {
            $kodeDok    = 'SP';
            $labelSurat = 'PENGANTAR';
        } else {
            $kodeDok    = 'SJ';
            $labelSurat = 'JALAN';
        }
$noSjDb = '';
        if (isset($sj) && is_array($sj) && !empty($sj['no_sj'])) {
            $noSjDb = (string)$sj['no_sj'];
        }

        $isFinal = isset($sj['status']) && strtolower((string)$sj['status']) === 'final';
        $showDraftBadge = !$noSjDb;

        $bulan_id = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $tgl_indo = date('d', $tglTs) . ' ' . $bulan_id[(int)date('n',$tglTs)] . ' ' . date('Y', $tglTs);

        $nomorTampil = $noSjDb ?: 'DRAFT (belum final)';
        ?>

        <div class="d-flex justify-content-between align-items-end">
            <div style="flex:1">
                <p class="m-0 title-doc">
                    SURAT <?= esc($labelSurat); ?> NO.
                    <?= esc($nomorTampil); ?>
                    <?php if ($showDraftBadge): ?>
                    <span class="badge-draft">DRAFT</span>
                    <?php endif; ?>
                </p>
                <div class="divider"></div>

                <?php if ($isFinal && !$noSjDb): ?>
                <p class="m-0" style="color:#b91c1c; font-weight:600;">
                    *WARNING: Status FINAL tapi no_sj kosong. Cek data surat_jalan.no_sj.
                </p>
                <?php endif; ?>
            </div>

            <div style="flex:1" class="mb-5">
                <p class="m-0" style="font-weight:500;">Kepada Yth.</p>
                <p class="m-0 to-name"><?= esc($pemesanan['nama'] ?? ' '); ?></p>
                <p class="m-0"><?= esc($pemesanan['alamat_pengiriman'] ?? ' '); ?></p>
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
                                    $p  = $dim['panjang'] ?? '-';
                                    $l  = $dim['lebar'] ?? '-';
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
                    <?= $ketRaw !== '' ? '*'.esc($ketRaw) : '<i style="color:inherit;">Tidak ada keterangan</i>'; ?>
                </span>
            </p>

            <?php if (!$isSP): ?>
            <p class="m-0">
                <b style="font-weight:600;">Nama Penerima : </b>
                <span class="text-danger">
                    <?= esc($pemesanan['nama'] ?? ' '); ?>
                </span>
            </p>

            <p class="m-0">
                <b style="font-weight:600;">Nomor Penerima : </b>
                <span class="text-danger">
                    <?php if (!empty($pemesanan['nohp'])): ?>
                    <?= esc($pemesanan['nohp'] ?? ' '); ?>
                    <?php endif; ?>
                </span>
            </p>
            <?php endif; ?>


            <p class="mt-4" style="font-weight:500;">Kendal, <?= esc($tgl_indo); ?></p>

            <!-- Tanda Tangan -->
            <div class="sig-row">
                <!-- Dibuat -->
                <div class="sig-box">
                    <p class="m-0 sig-title">Dibuat Oleh :</p>

                    <div class="sig-body">
                        <img src="<?= base_url('img/logo/stampelfix.png'); ?>" alt="Stempel" class="sig-stamp" />
                        <img src="<?= base_url('img/logo/stampelfix.png'); ?>" alt="" class="sig-stamp-rough" />
                    </div>

                    <div class="sig-footer">Admin</div>
                    <p class="m-0 sig-name sig-line">____________________</p>
                </div>

                <!-- Dibawa -->
                <div class="sig-box">
                    <p class="m-0 sig-title">Dibawa Oleh :</p>

                    <div class="sig-body"></div>

                    <div class="sig-footer">&nbsp;</div>
                    <p class="m-0 sig-name sig-line">____________________</p>
                </div>

                <!-- Diterima -->
                <div class="sig-box">
                    <p class="m-0 sig-title">Diterima Oleh :</p>

                    <div class="sig-body"></div>

                    <div class="sig-footer">&nbsp;</div>
                    <p class="m-0 sig-name sig-line">____________________</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>