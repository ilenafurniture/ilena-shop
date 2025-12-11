<!-- //views/admin/suratInvoice.php -->
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

    <!-- Icon & Font -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    :root {
        --merah: #b31217;
        --ink: #0f172a;
        --line: #e5e7eb;
        --line2: #f1f5f9;
        --lunas-shift-y: -12vh;
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
        font-size: 13.25px;
        line-height: 1.35;
    }

    h5,
    .h5 {
        font-size: 16px;
        font-weight: 600;
        letter-spacing: -.2px;
        margin: 0;
    }

    .tw-bold-italic {
        font-weight: 600;
        font-style: italic;
    }

    .nt {
        font-weight: 500;
        color: #111;
        font-style: italic;
        margin: 0;
    }

    .isint {
        font-weight: 500;
        font-style: italic;
        margin: 0;
    }

    .print-lunas {
        position: fixed !important;
        inset: 0 !important;
        display: grid !important;
        place-items: center !important;
        pointer-events: none !important;
        z-index: 2147483647 !important;
    }

    .print-lunas p {
        margin: 0;
        font-size: 110px;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--merah);
        opacity: .10;
        border: 6px solid var(--merah);
        padding: .12em .32em;
        border-radius: 12px;
        transform: translateY(var(--lunas-shift-y)) rotate(-15deg);
        user-select: none;
    }

    .table {
        border-color: var(--line);
    }

    .table thead th {
        background: #f8fafc !important;
        border-bottom: 1px solid var(--line);
        font-weight: 600;
        color: #0f172a;
        font-size: 11.5px;
        vertical-align: middle;
    }

    .table tbody td {
        border-color: var(--line2);
        vertical-align: middle;
        font-size: 11.5px;
    }

    .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-accent-bg: #fcfdff;
    }

    .num {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .nowrap {
        white-space: nowrap;
    }

    .kotak-pembayaran {
        border: 1px dashed #ef4444;
        padding: 10px 20px;
        margin: 20px 0;
        align-self: center;
        text-align: center;
        font-weight: 500;
        font-style: italic;
        border-radius: 10px;
        background: #fff;
    }

    .title h3 {
        letter-spacing: -.25px;
        font-weight: 600;
        font-size: 18px;
        margin: 0;
    }

    .container {
        margin-top: 28px !important;
        margin-bottom: 28px !important;
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
        img,
        .kotak-pembayaran {
            break-inside: avoid;
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-accent-bg: transparent;
        }

        .print-lunas {
            position: fixed !important;
            z-index: 2147483647 !important;
        }
    }
    </style>
</head>

<?php setlocale(LC_TIME, 'id_ID'); ?>

<body>
    <?php if ($pemesanan['status'] == "success" || $pemesanan['status'] == "DP paid") { ?>
    <div class="print-lunas">
        <p>L U N A S</p>
    </div>
    <?php } ?>

    <div class="container">
        <!-- Header perusahaan -->
        <div class="d-flex gap-4 justify-content-start mb-4">
            <div><img src="<?= base_url('img/logo/logo-invoice.jpg'); ?>" alt="Logo" width="70" height="40" /></div>
            <div class="d-flex flex-column justify-content-center gap-1">
                <h5 class="m-0">CV.CATUR BHAKTI MANDIRI</h5>
                <h6 class="m-0" style="font-size:12.5px; font-weight:500;">
                    Kawasan Industri BSB, A 3A, 5-6 Jatibarang, Mijen, Semarang
                </h6>
            </div>
        </div>

        <?php
        // ===== LOGIKA NOMOR & FLAG SUMMARY INVOICE INTERIOR =====
        $tanggalFix = $pemesanan['tanggal_inv'] ?? $pemesanan['tanggal'];

        $rawId      = (string)($pemesanan['id_pesanan'] ?? '');
        $jenisLower = strtolower(trim($pemesanan['jenis'] ?? ''));

        // ambil jenis_faktur dari project interior kalau ada (sale / nf)
        $jenisFakturProject = isset($project['jenis_faktur'])
            ? strtolower(trim($project['jenis_faktur']))
            : '';

        // prioritas: jenis_faktur project interior, kalau kosong pakai jenis dari pemesanan_offline
        $docType = $jenisFakturProject !== '' ? $jenisFakturProject : $jenisLower;

        // ambil 5 digit terakhir dari angka di id_pesanan
        $digits = '00001';
        if (preg_match('/(\d+)/', $rawId, $mDigits)) {
            $angka  = $mDigits[1];
            $digits = substr($angka, -5);
        }

        // tentukan prefix sesuai jenis dokumen utama:
        // - nf   => NF
        // - sale/sj => SJ
        // lainnya => tanpa prefix khusus
        $prefix = '';
        if ($docType === 'nf') {
            $prefix = 'NF';
        } elseif ($docType === 'sale' || $docType === 'sj') {
            $prefix = 'SJ';
        }

        if ($prefix !== '') {
            $nomorDisplayBase = $prefix . str_pad($digits, 5, '0', STR_PAD_LEFT);
        } else {
            // fallback: cuma angka
            $nomorDisplayBase = str_pad($digits, 5, '0', STR_PAD_LEFT);
        }

        // === NOMOR HEADER YANG DITAMPILKAN ===
        if (!empty($is_payment_invoice)) {
            // INVOICE PEMBAYARAN: pakai nomor khusus payment
            if (!empty($payment['no_invoice'])) {
                $nomorInvoiceHeader = $payment['no_invoice'];
            } elseif (!empty($payment['kode_invoice'])) {
                $nomorInvoiceHeader = $payment['kode_invoice'];
            } else {
                $idPay = (int)($payment['id'] ?? 0);
                $nomorInvoiceHeader = 'PMT-' . str_pad((string)$idPay, 4, '0', STR_PAD_LEFT);
            }
        } else {
            // INVOICE FINAL / LAMA: pakai nomor SJxxxx atau NFxxxx sesuai docType
            $nomorInvoiceHeader = $nomorDisplayBase;
        }

        // flag: ini FINAL INVOICE PROJECT INTERIOR (bukan invoice per pembayaran)
        $isFinalInteriorInvoice = !empty($is_project_interior) && empty($is_payment_invoice);
        ?>

        <!-- Nomor & tanggal -->
        <div class="d-flex">
            <div style="flex:1;"></div>
            <div class="d-flex gap-2 justify-content-end">
                <div class="d-flex flex-column align-items-end">
                    <?php if (!$isFinalInteriorInvoice): ?>
                    <p class="nt">Nomor :</p>
                    <?php endif; ?>
                    <p class="nt">Tanggal :</p>
                </div>

                <div class="d-flex flex-column align-items-start">
                    <?php if (!$isFinalInteriorInvoice): ?>
                    <p class="isint" style="font-weight:600;">
                        <?= $nomorInvoiceHeader; ?>/INV/CBM/<?= date('m', strtotime($tanggalFix)); ?>/<?= date('Y', strtotime($tanggalFix)); ?>
                    </p>
                    <?php endif; ?>
                    <p class="isint">
                        <?php
                        $bulan_indonesia = [
                            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                        ];
                        $tanggal = date('d', strtotime($tanggalFix));
                        $bulan   = date('n', strtotime($tanggalFix));
                        $tahun   = date('Y', strtotime($tanggalFix));
                        echo "$tanggal " . $bulan_indonesia[$bulan] . " $tahun";
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Judul -->
        <div class="my-1 title">
            <h3 class="text-center">
                <?php if (!empty($is_project_interior) && empty($is_payment_invoice)): ?>
                SUMMARY INVOICE
                <?php else: ?>
                INVOICE
                <?php endif; ?>
            </h3>
        </div>

        <!-- Tujuan -->
        <div class="d-flex justify-content-start mt-4 mb-4 flex-column">
            <p class="m-0 nt" style="max-width:260px; font-size:12px;">Kepada Yth.</p>
            <?php
                $namaNpwpCetak = trim((string)($pemesanan['nama_npwp'] ?? ''));

                if ($namaNpwpCetak === '' && !empty($project) && !empty($project['nama_npwp'])) {
                    $namaNpwpCetak = trim((string)$project['nama_npwp']);
                }

                if ($namaNpwpCetak === '' && !empty($project) && !empty($project['nama_customer'])) {
                    $namaNpwpCetak = trim((string)$project['nama_customer']);
                }
            ?>
            <p class="m-0 tw-bold-italic" style="max-width:260px; font-size:12px;">
                <?= esc($namaNpwpCetak); ?>
            </p>


            <p class="m-0" style="max-width:260px; font-size:12px;"><?= $pemesanan['alamat_tagihan']; ?></p>


            <?php
                    // Ambil NPWP dari pemesanan dulu
                    $npwpCetak = trim((string)($pemesanan['npwp'] ?? ''));

                    // Kalau interior dan npwp di pemesanan kosong, ambil dari project
                    if ($npwpCetak === '' && !empty($project) && !empty($project['npwp'])) {
                        $npwpCetak = trim((string)$project['npwp']);
                    }
                ?>
            <p style="font-size:12px;" class="isint">
                NPWP/NIK : <?= $npwpCetak !== '' ? esc($npwpCetak) : '-'; ?>
            </p>

        </div>

        <!-- Tabel Invoice -->
        <div class="table-responsive mt-2">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <?php
                        // Interior final: $is_project_interior
                        // Invoice per pembayaran interior: $is_payment_invoice + ada $project interior
                        $useInteriorHeader = !empty($is_project_interior)
                            || (!empty($is_payment_invoice) && !empty($project) && isset($project['kode_project']));
                        ?>

                        <?php if ($useInteriorHeader): ?>
                        <th class="text-center" style="width:10px;">NO</th>
                        <th class="text-center">KODE BARANG</th>
                        <th class="text-center">KETERANGAN</th>
                        <th class="text-center">KUANTITAS</th>
                        <th class="text-center">HARGA</th>
                        <th class="text-center">JUMLAH</th>
                        <?php else: ?>
                        <th class="text-center" style="width:10px;">NO</th>
                        <th class="text-center">KODE BARANG</th>
                        <th class="text-center">NAMA BARANG</th>
                        <th class="text-center">KUANTITAS</th>
                        <th class="text-center">HARGA SATUAN</th>
                        <th class="text-center">JUMLAH</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($is_payment_invoice)): ?>
                    <?php
                        // ===== MODE INVOICE PEMBAYARAN (DP / TERMIN / PELUNASAN) =====
                        $no = 1;

                        // Kode untuk kolom "KODE BARANG" di invoice pembayaran interior
                        if (!empty($project) && !empty($project['kode_barang'])) {
                            $kodeDisplay = strtoupper($project['kode_barang']);
                        } elseif (!empty($project) && !empty($project['kode_project'])) {
                            $kodeDisplay = strtoupper($project['kode_project']);
                        } else {
                            $kodeDisplay = strtoupper($pemesanan['id_pesanan'] ?? '');
                        }

                        $totalPaidBefore = 0;
                        if (!empty($history_before) && is_array($history_before)) {
                            foreach ($history_before as $h) {
                                $totalPaidBefore += (int)($h['nominal'] ?? 0);
                            }
                        }

                        $jenisNowRaw = strtolower($payment['jenis'] ?? '');
                        if ($jenisNowRaw === 'dp') {
                            $labelNow = 'UANG MUKA';
                        } elseif ($jenisNowRaw === 'termin') {
                            $labelNow = 'TERMIN';
                        } elseif ($jenisNowRaw === 'pelunasan') {
                            $labelNow = 'PELUNASAN';
                        } else {
                            $labelNow = 'PEMBAYARAN ' . strtoupper($payment['jenis'] ?? '-');
                        }

                        $nominalNow = (int)($payment['nominal'] ?? 0);
                        $catNow     = trim((string)($payment['catatan'] ?? ''));

                        // FLAG: ini invoice pembayaran interior atau bukan?
                        $isInteriorPayment = !empty($project) && isset($project['kode_project']);

                        // KETERANGAN / NAMA BARANG
                        if ($isInteriorPayment) {
                            $baseNama = 'Furniture Interior Lokal';
                            $detailTag = $catNow !== '' ? $catNow : $labelNow;
                            $namaBarangNow = $baseNama . ' (' . $detailTag . ')';
                        } else {
                            $namaBarangNow = $labelNow;
                            if (!empty($project['nama_project'])) {
                                $namaBarangNow .= ' (' . strtoupper($project['nama_project']) . ')';
                            }
                        }

                        $totalPaidUntilNow = $totalPaidBefore + $nominalNow;
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= $kodeDisplay; ?></td>
                        <td>
                            <p class="m-0" style="font-size:11.5px;"><?= $namaBarangNow; ?></p>
                            <?php if ($catNow !== '' && !$isInteriorPayment): ?>
                            <p class="m-0" style="font-size:11.5px;">
                                Catatan: <?= esc($catNow); ?>
                            </p>
                            <?php endif; ?>
                        </td>
                        <td class="text-center nowrap">1</td>
                        <td class="num nowrap"></td>
                        <td class="num nowrap">Rp <?= number_format($nominalNow, 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td colspan="5" class="fw-semibold">TOTAL INVOICE</td>
                        <td class="num fw-semibold">
                            Rp <?= number_format($nominalNow, 0, ',', '.'); ?>
                        </td>
                    </tr>

                    <?php else: ?>
                    <?php if (!empty($is_project_interior)): ?>
                    <?php
                        // ===== MODE INVOICE AKHIR PROJECT INTERIOR (SUMMARY INVOICE) =====
                        $no = 1;

                        $nilaiKontrakTotal = isset($nilai_kontrak)
                            ? (int)$nilai_kontrak
                            : (int)$pemesanan['total_akhir'];

                        if (isset($dpp) && isset($ppn_11)) {
                            $dppLocal = (int)$dpp;
                            $ppnLocal = (int)$ppn_11;
                        } else {
                            $dppLocal = $nilaiKontrakTotal > 0 ? (int)round($nilaiKontrakTotal / 1.11) : 0;
                            $ppnLocal = $nilaiKontrakTotal - $dppLocal;
                        }

                        // total pembayaran historis tetap dihitung, kalau mau dipakai bagian lain
                        $sumDpLocal        = (int)($dp_total ?? 0);
                        $sumTerminLocal    = (int)($termin_total ?? 0);
                        $sumPelunasanLocal = (int)($pelunasan_total ?? 0);

                        if (!empty($payments) && is_array($payments)) {
                            $sumDpLocal        = 0;
                            $sumTerminLocal    = 0;
                            $sumPelunasanLocal = 0;
                            $terminIndex       = 0;

                            foreach ($payments as $p) {
                                $jenisP = strtolower($p['jenis'] ?? '');
                                $nomP   = (int)($p['nominal'] ?? 0);

                                if ($jenisP === 'dp') {
                                    $sumDpLocal += $nomP;
                                } elseif ($jenisP === 'termin') {
                                    $terminIndex++;
                                    $sumTerminLocal += $nomP;
                                } elseif ($jenisP === 'pelunasan') {
                                    $sumPelunasanLocal += $nomP;
                                }
                            }
                        }

                        $totalBayarLocal  = $sumDpLocal + $sumTerminLocal + $sumPelunasanLocal;
                        $sisaTagihanLocal = isset($sisa_tagihan)
                            ? (int)$sisa_tagihan
                            : max(0, $nilaiKontrakTotal - $totalBayarLocal);

                        foreach ($items as $t):
                            $jumlahRow = (int)$t['jumlah'] * (int)$t['harga'];

                            // KODE BARANG: pakai field kode_barang dari project kalau ada
                            if (!empty($project['kode_barang'])) {
                                $kodeBarangCetak = strtoupper($project['kode_barang']);
                            } else {
                                $kodeBarangCetak = strtoupper($t['id_baru']);
                            }
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= $kodeBarangCetak; ?></td>
                        <td>
                            <p class="m-0" style="font-size:11.5px;">
                                <?= strtoupper($t['nama']); ?><?= $t['varian'] ? ' ('.strtoupper($t['varian']).')' : ''; ?>
                            </p>
                        </td>
                        <td class="text-center nowrap"><?= $t['jumlah']; ?></td>
                        <td class="num nowrap">Rp <?= number_format($t['harga'], 0, ',', '.'); ?></td>
                        <td class="num nowrap">Rp <?= number_format($jumlahRow, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="5" class="fw-semibold">DASAR PENGENAAN PAJAK</td>
                        <td class="num">Rp <?= number_format($dppLocal, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="fw-semibold">PPN</td>
                        <td class="num">Rp <?= number_format($ppnLocal, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="fw-semibold">JUMLAH KESELURUHAN</td>
                        <td class="num fw-semibold">Rp <?= number_format($nilaiKontrakTotal, 0, ',', '.'); ?></td>
                    </tr>

                    <!-- Tidak ada baris UANG MUKA / TERMIN / PELUNASAN / SISA di summary invoice -->

                    <?php else: ?>
                    <?php
                        // ===== MODE LAMA (OFFLINE BIASA) =====
                        $totalHargaBarang = 0;
                        foreach ($items as $t) {
                            $totalHargaBarang += $t['jumlah'] * $t['harga'];
                        }
                        $discountFactor = 1;
                        if ($pemesanan['total_akhir'] < $totalHargaBarang) {
                            $discountFactor = $pemesanan['total_akhir'] / $totalHargaBarang;
                        }
                    ?>

                    <?php if ($pemesanan['down_payment'] <= 0) { ?>
                    <?php
                            $no = 1;
                            foreach ($items as $t) {
                                $origPrice = $t['harga'];
                                $origTotal = $t['jumlah'] * $origPrice;
                                if ($discountFactor < 1) {
                                    $netPrice = round($origPrice * $discountFactor);
                                    $netTotal = $t['jumlah'] * $netPrice;
                                } else {
                                    $netPrice = $origPrice;
                                    $netTotal = $origTotal;
                                }
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= strtoupper($t['id_baru']); ?></td>
                        <td>
                            <p class="m-0" style="font-size:11.5px;">
                                <?= $t['special_price'] > 0 ? "[SPECIAL PRICE] " : ""; ?>
                                <?= strtoupper($t['nama']); ?> (<?= strtoupper($t['varian']); ?>)
                            </p>
                            <p class="m-0" style="font-size:11.5px;">
                                <?= "{$t['dimensi']['panjang']} x {$t['dimensi']['lebar']} x {$t['dimensi']['tinggi']}"; ?>
                            </p>
                        </td>
                        <td class="text-center nowrap"><?= $t['jumlah']; ?></td>
                        <td class="num nowrap">Rp <?= number_format($netPrice,0,',','.'); ?></td>
                        <td class="num nowrap">Rp <?= number_format($netTotal,0,',','.'); ?></td>
                    </tr>
                    <?php } ?>

                    <?php if ($pemesanan['down_payment'] < 0) { ?>
                    <tr>
                        <td colspan="5" class="fw-semibold">UANG MUKA YANG DITERIMA</td>
                        <td class="num">Rp <?= number_format(abs($pemesanan['down_payment']),0,',','.'); ?></td>
                    </tr>
                    <?php } ?>

                    <?php } else { ?>
                    <tr>
                        <td class="text-center">1</td>
                        <td class="text-center"></td>
                        <td>UANG MUKA</td>
                        <td class="text-center"></td>
                        <td class="num"></td>
                        <td class="num">Rp <?= number_format($pemesanan['down_payment'],0,',','.'); ?></td>
                    </tr>
                    <?php } ?>

                    <?php if (empty($is_project_interior)): ?>
                    <tr>
                        <td colspan="5" class="fw-semibold">TOTAL INVOICE</td>
                        <td class="num fw-semibold">
                            Rp
                            <?php
                                if ($pemesanan['down_payment'] > 0) {
                                    echo number_format($pemesanan['down_payment'],0,',','.');
                                } else {
                                    $net = $pemesanan['total_akhir'] - (
                                        $pemesanan['down_payment'] < 0
                                            ? abs($pemesanan['down_payment'])
                                            : 0
                                    );
                                    echo number_format($net,0,',','.');
                                }
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php endif; // end non-interior ?>
                    <?php endif; // end !is_payment_invoice ?>
                </tbody>
            </table>
        </div>

        <?php
        // === JATUH TEMPO ===
        $jtLabel = '-';
        if (empty($is_payment_invoice)) {
            if ($pemesanan['down_payment'] <= 0) {
                $sjDate = $pemesanan['tanggal'] ?? null;
                if ($sjDate) {
                    $dueTs = strtotime($sjDate . ' +14 days');
                    $tglJT = date('d', $dueTs);
                    $blnJT = (int)date('n', $dueTs);
                    $thJT  = date('Y', $dueTs);
                    $bulan_indonesia = [
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ];
                    $jtLabel = $tglJT . ' ' . $bulan_indonesia[$blnJT] . ' ' . $thJT;
                }
            }
        }

        // === FLAG TAMPILKAN SURAT JALAN UNTUK INVOICE PEMBAYARAN (TERMIN / PELUNASAN) ===
        $showSjOnPayment = false;
        if (!empty($is_payment_invoice)) {
            $jenisPayLower = strtolower($payment['jenis'] ?? '');
            $reqShow = isset($_GET['show_sj']) && $_GET['show_sj'] === '1';
            if ($reqShow && in_array($jenisPayLower, ['termin', 'pelunasan'], true)) {
                $showSjOnPayment = true;
            }
        }
        ?>

        <div>
            <?php
            // ==== TERBILANG ====
            function terbilang($angka){
                $angka=(int)$angka; $tingkat=['','Ribu','Juta','Miliar','Triliun','Kuadriliun','Kuintiliun'];
                if($angka==0) return "Nol Rupiah"; $kalimat=''; $i=0;
                while($angka>0){
                    $bagian=$angka%1000;
                    if($bagian!=0){ $kalimat=_terbilang_ratusan($bagian).' '.$tingkat[$i].' '.$kalimat; }
                    $angka=(int)($angka/1000); $i++;
                }
                return trim($kalimat).' Rupiah';
            }
            function _terbilang_ratusan($angka){
                $kalimat='';
                if($angka>=100){
                    if((int)($angka/100)==1) $kalimat.='Seratus ';
                    else{
                        $huruf=[1=>'Satu',2=>'Dua',3=>'Tiga',4=>'Empat',5=>'Lima',6=>'Enam',7=>'Tujuh',8=>'Delapan',9=>'Sembilan'];
                        $kalimat.=$huruf[(int)($angka/100)].' Ratus ';
                    }
                    $angka%=100;
                }
                if($angka>=10) $kalimat.=_terbilang_puluhan($angka);
                elseif($angka>0){
                    $huruf=[1=>'Satu',2=>'Dua',3=>'Tiga',4=>'Empat',5=>'Lima',6=>'Enam',7=>'Tujuh',8=>'Delapan',9=>'Sembilan',0=>'Nol'];
                    $kalimat.=$huruf[$angka];
                }
                return trim($kalimat);
            }
            function _terbilang_puluhan($angka){
                $huruf=[
                    10=>'Sepuluh',11=>'Sebelas',12=>'Dua Belas',13=>'Tiga Belas',14=>'Empat Belas',
                    15=>'Lima Belas',16=>'Enam Belas',17=>'Tujuh Belas',18=>'Delapan Belas',19=>'Sembilan Belas',
                    20=>'Dua Puluh',30=>'Tiga Puluh',40=>'Empat Puluh',50=>'Lima Puluh',
                    60=>'Enam Puluh',70=>'Tujuh Puluh',80=>'Delapan Puluh',90=>'Sembilan Puluh'
                ];
                if($angka<=9) return '';
                elseif($angka<=19) return $huruf[$angka];
                else{
                    $puluhan=(int)($angka/10)*10;
                    return $huruf[$puluhan].' '._terbilang_satuan($angka%10);
                }
            }
            function _terbilang_satuan($angka){
                $huruf=[1=>'Satu',2=>'Dua',3=>'Tiga',4=>'Empat',5=>'Lima',6=>'Enam',7=>'Tujuh',8=>'Delapan',9=>'Sembilan',0=>''];
                return $huruf[$angka];
            }

            if (!empty($is_payment_invoice)) {
                $angkaTerbilang = (int)($payment['nominal'] ?? 0);
            } elseif (!empty($is_project_interior)) {
                $nilaiKontrakTotalTb = isset($nilai_kontrak)
                    ? (int)$nilai_kontrak
                    : (int)$pemesanan['total_akhir'];
                $angkaTerbilang = $nilaiKontrakTotalTb;
            } else {
                if ($pemesanan['down_payment'] > 0) {
                    $angkaTerbilang = (int)$pemesanan['down_payment'];
                } else {
                    $angkaTerbilang = (int)(
                        $pemesanan['total_akhir']
                        - ($pemesanan['down_payment'] < 0 ? abs($pemesanan['down_payment']) : 0)
                    );
                }
            }
            ?>
            <table>
                <tbody>
                    <tr>
                        <td style="font-size:11.5px;" class="pe-3">Terbilang</td>
                        <td style="font-size:11.5px;">:
                            <i style="font-size:11.5px;">
                                <?= ucwords(strtolower(terbilang($angkaTerbilang))); ?>
                            </i>
                        </td>
                    </tr>
                    <tr>
                        <td class="pe-3" style="white-space:nowrap; font-size:11.5px;">PO</td>
                        <td style="white-space:nowrap; font-size:11.5px;">:
                            <?= $pemesanan['po'] ? $pemesanan['po'] : '-'; ?></td>
                    </tr>
                    <tr>
                        <td class="pe-3" style="white-space:nowrap; font-size:11.5px;">Surat Jalan</td>

                        <?php if (!empty($is_payment_invoice)): ?>
                        <?php if ($showSjOnPayment): ?>
                        <td style="white-space:nowrap; font-size:11.5px;">:
                            <?php
                                // Prioritas: kode_sj dari project interior kalau ada
                                if (!empty($project['kode_sj'])) {
                                    echo substr($project['kode_sj'],5);
                                } elseif (!empty($pemesanan['id_pesanan'])) {
                                    echo substr($pemesanan['id_pesanan'], 5);
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <?php else: ?>
                        <!-- Invoice pembayaran (DP / Termin / Pelunasan) tanpa show_sj -->
                        <td style="white-space:nowrap; font-size:11.5px;">: -</td>
                        <?php endif; ?>

                        <?php elseif ($pemesanan['down_payment']): ?>
                        <td style="white-space:nowrap; font-size:11.5px;">:
                            <?= isset($items[0]['id_return']) && $items[0]['id_return'] ? substr($items[0]['id_return'], 5) : '-' ; ?>
                        </td>
                        <?php else: ?>
                        <td style="white-space:nowrap; font-size:11.5px;">: <?= substr($pemesanan['id_pesanan'], 5); ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td class="pe-3" style="white-space:nowrap; font-size:11.5px;">Jatuh Tempo</td>
                        <td style="white-space:nowrap; font-size:11.5px;">: <?= $jtLabel; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="d-flex justify-content-between mt-5 mb-3">
            <div class="d-flex flex-column kotak-pembayaran">
                <p class="m-0" style="font-size:12px;">
                    Pembayaran mohon dapat ditransfer ke rekening: <br>
                    <b style="font-size:12px; color:#ef4444;">BCA 8715898787 a.n. CATUR BHAKTI MANDIRI</b>
                </p>
            </div>
            <div class="d-flex flex-column align-items-center" style="width:200px; font-size:12px;">
                Bagian Keuangan <br><br><br><br><br>
                <p class="tw-bold-italic" style="font-size:12px;">Puspita Aprilia Damayanti</p>
            </div>
        </div>
    </div>

    <script>
    const alamatLengkap =
        "<?= $pemesanan['alamat_tagihan'] ? $pemesanan['alamat_tagihan'] : $pemesanan['alamat_pengiriman']; ?>";
    const alamatParts = alamatLengkap.split(', ');
    const jalan = alamatParts[0],
        kelurahan = alamatParts[1],
        kecamatan = alamatParts[2],
        kota = alamatParts[3];
    const provinsiDanKodePos = (alamatParts[4] || '').split(' ');
    const provinsi = provinsiDanKodePos[0] || '';
    const kodePos = provinsiDanKodePos[1] || '';
    document.getElementById('jalan')?.innerText = jalan;
    document.getElementById('kelurahan')?.innerText = kelurahan;
    document.getElementById('kecamatan')?.innerText = kecamatan;
    document.getElementById('kota')?.innerText = kota;
    document.getElementById('provinsi')?.innerText = provinsi;
    document.getElementById('kodepos')?.innerText = kodePos;
    </script>

    <!--
    <script>
    window.print();
    window.onafterprint = function() {
        window.location.href = "<?= base_url('admin/order/offline/sale'); ?>";
    };
    </script>
    -->
</body>

</html>