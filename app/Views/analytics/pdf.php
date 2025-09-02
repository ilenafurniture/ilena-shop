<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Analytics Report <?= esc($opt['start']) ?> - <?= esc($opt['end']) ?></title>
    <style>
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        color: #222;
        line-height: 1.5;
        margin: 20px;
    }

    h1,
    h2,
    h3 {
        margin: 0 0 8px;
        font-weight: bold;
        color: #111;
    }

    h1 {
        font-size: 20px;
        margin-bottom: 16px;
        text-align: center;
        border-bottom: 2px solid #444;
        padding-bottom: 6px;
    }

    .muted {
        color: #555;
        font-size: 11px;
        margin-bottom: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #aaa;
        padding: 6px 8px;
    }

    th {
        text-align: left;
        background: #f2f2f2;
        font-weight: bold;
    }

    .section {
        margin-bottom: 22px;
    }

    .summary-table td:first-child {
        font-weight: bold;
        width: 40%;
    }

    .footer {
        text-align: center;
        font-size: 10px;
        color: #777;
        margin-top: 30px;
        border-top: 1px solid #ccc;
        padding-top: 8px;
    }
    </style>
</head>

<body>
    <h1>Website Analytics Report</h1>
    <div class="muted">
        Periode: <?= esc($opt['start']) ?> → <?= esc($opt['end']) ?><br>
        Min durasi: <?= (int)$opt['min_duration'] ?> detik
        <?php if($opt['exclude_low_avg_duration']): ?> • Exclude avg dur <
            <?= (int)$opt['exclude_low_avg_duration'] ?>s<?php endif; ?> <?php if($opt['exclude_high_hits_per_day']): ?>
            • Exclude IP hit/har ≥ <?= (int)$opt['exclude_high_hits_per_day'] ?><?php endif; ?> </div>

            <div class="section">
                <h2>Ringkasan</h2>
                <table class="summary-table">
                    <tbody>
                        <tr>
                            <td>Total Tracking</td>
                            <td><?= number_format($summary['total_tracking']) ?></td>
                        </tr>
                        <tr>
                            <td>IP Unik</td>
                            <td><?= number_format($summary['total_ip_unik']) ?></td>
                        </tr>
                        <tr>
                            <td>Path Unik</td>
                            <td><?= number_format($summary['total_path_unik']) ?></td>
                        </tr>
                        <tr>
                            <td>Total Durasi (detik)</td>
                            <td><?= number_format($summary['total_durasi']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Top Paths</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Path</th>
                            <th style="width:20%;text-align:right">Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($top as $r): ?>
                        <tr>
                            <td><?= esc($r['path'] ?: '/') ?></td>
                            <td style="text-align:right"><?= number_format($r['jumlah']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>Daily Series</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width:40%">Tanggal</th>
                            <th style="text-align:right">Hits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($daily as $d): ?>
                        <tr>
                            <td><?= esc($d['tanggal']) ?></td>
                            <td style="text-align:right"><?= number_format($d['hits']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="footer">
                Laporan otomatis dihasilkan oleh sistem Analytics • <?= date('d M Y H:i') ?>
            </div>
</body>

</html>