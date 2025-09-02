<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<?php
$fmt = fn($n)=>number_format((int)$n);
$delta = function($now,$prev){ $d=(int)$now-(int)$prev; $sign=$d>0?'+':($d<0?'-':'±'); return [$d,$sign]; };
$percent = function($now,$prev){ if((int)$prev===0) return '—'; $p=(($now-$prev)/max(1,$prev))*100; return ($p>0?'+':'').number_format($p,1).' %'; };
?>

<style>
:root {
    --brand: var(--merah, #e84a49);
    --stroke: #e5e7eb;
    --muted: #6b7280;
    --bg-soft: #f5f6f8;
}

.ana {
    color: #111;
}

.container-page {
    display: grid;
    gap: 18px;
    margin: 8px auto 24px;
    padding: 0 12px;
    width: 100%;
    max-width: 1580px;
}

.header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.page-title {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -.01em;
    margin: 0;
}

.sub {
    color: var(--muted);
}

.card {
    background: #fff;
    border: 1px solid var(--stroke);
    border-radius: 12px;
    padding: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
}

.toolbar {
    position: sticky;
    top: 10px;
    z-index: 5;
    background: #fff;
    border: 1px solid var(--stroke);
    border-radius: 12px;
    padding: 14px;
}

.grid {
    display: grid;
    gap: 12px;
}

.grid-12 {
    grid-template-columns: repeat(12, 1fr);
}

.col-6 {
    grid-column: span 6;
}

@media (max-width:980px) {
    .col-6 {
        grid-column: span 12;
    }
}

label {
    font-size: 12px;
    color: var(--muted);
    display: block;
    margin-bottom: 6px;
}

input,
button,
.btn {
    height: 38px;
    border: 1px solid var(--stroke);
    border-radius: 10px;
    background: #fff;
    color: #111;
    padding: 0 12px;
    font-size: 14px;
}

input:focus {
    outline: none;
    border-color: #9ca3af;
    box-shadow: 0 0 0 3px rgba(156, 163, 175, .25);
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: .15s;
}

.btn.primary {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
}

.btn.secondary {
    background: #fff;
    border-color: var(--stroke);
}

.btn:hover {
    filter: brightness(.97);
}

/* Quick range */
.quick-range {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
}

.pill {
    background: #fff;
    border: 1px solid var(--stroke);
    height: 32px;
    padding: 0 10px;
    border-radius: 999px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}

.pill.active {
    border-color: var(--brand);
    color: var(--brand);
    font-weight: 600;
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border: 1px solid var(--stroke);
    border-radius: 999px;
    font-size: 12px;
    background: #fafafa;
    color: #374151;
}

.badge.green {
    background: #f0fdf4;
    border-color: #c7f0d5;
    color: #0a7a2e;
}

.badge.red {
    background: #fff1f1;
    border-color: #ffd2d2;
    color: #b91c1c;
}

.badge.amber {
    background: #fff7ed;
    border-color: #ffdfbd;
    color: #92400e;
}

/* KPI (RENAMED to avoid collision) */
.ana .kpi {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

@media (max-width:980px) {
    .ana .kpi {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.ana .kpi-item {
    background: #fff;
    border: 1px solid var(--stroke);
    border-radius: 10px;
    padding: 12px;
}

.ana .kpi-label {
    display: block;
    font-size: 12px;
    color: var(--muted) !important;
    margin-bottom: 6px;
    background: none;
    border: none;
    padding: 0;
}

.ana .kpi-value {
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -.02em;
    display: block;
}

.ana .kpi-trend {
    margin-top: 6px;
}

/* Tables */
.table-wrap {
    border: 1px solid var(--stroke);
    border-radius: 12px;
    overflow: auto;
    max-height: 52vh;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    min-width: 420px;
}

thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    text-align: left;
    padding: 10px;
    background: #f9fafb;
    color: var(--muted);
    border-bottom: 1px solid #ececec;
}

tbody td {
    padding: 10px;
    border-bottom: 1px solid #f3f4f6;
}

tbody tr:hover {
    background: #fafafa;
}

.right {
    text-align: right;
}

/* Chart */
.chart-card canvas {
    max-height: 340px;
}

/* Titles */
.h3 {
    margin: 0 0 8px;
    font-size: 16px;
    font-weight: 700;
}

/* Two columns */
.two-col {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (max-width:980px) {
    .two-col {
        grid-template-columns: 1fr;
    }
}

.header-meta {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}
</style>

<div class="container-page ana">
    <div class="header-row">
        <h1 class="page-title">Insights Analytics</h1>
        <div class="header-meta sub">
            <span>Range aktif: <b><?= esc($opt['start']) ?> → <?= esc($opt['end']) ?></b></span>
            <span>• Min durasi <span class="badge"><?= (int)$opt['min_duration'] ?>s+</span></span>
            <?php if($opt['exclude_low_avg_duration']): ?>
            <span>• Avg dur <span class="badge amber">&lt; <?= (int)$opt['exclude_low_avg_duration'] ?>s</span></span>
            <?php endif; ?>
            <?php if($opt['exclude_high_hits_per_day']): ?>
            <span>• Hit/har ≥ <span class="badge amber"><?= (int)$opt['exclude_high_hits_per_day'] ?></span></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- FILTERS -->
    <form method="get" class="toolbar" id="filterForm">
        <div class="grid grid-12">
            <div class="col-6">
                <label>Dari tanggal</label>
                <input type="date" name="start" value="<?= esc($opt['start']) ?>">
            </div>
            <div class="col-6">
                <label>Sampai tanggal</label>
                <input type="date" name="end" value="<?= esc($opt['end']) ?>">
            </div>
            <div class="col-6">
                <label>Min durasi (detik)</label>
                <input type="number" name="min_duration" min="0" value="<?= (int)$opt['min_duration'] ?>">
            </div>
            <div class="col-6">
                <label>Exclude IP avg durasi &lt; (0=off)</label>
                <input type="number" name="exclude_low_avg_duration" min="0"
                    value="<?= (int)$opt['exclude_low_avg_duration'] ?>">
            </div>
            <div class="col-6">
                <label>Exclude IP hit/har ≥ (0=off)</label>
                <input type="number" name="exclude_high_hits_per_day" min="0"
                    value="<?= (int)$opt['exclude_high_hits_per_day'] ?>">
            </div>
            <div class="col-6">
                <label>Exclude IP kustom (pisahkan koma, kosong=pakai blacklist table)</label>
                <input type="text" name="exclude_ips" placeholder="1.2.3.4,5.6.7.8"
                    value="<?= esc(implode(',', $opt['exclude_ips'] ?? [])) ?>">
            </div>
        </div>

        <div class="actions" style="margin-top:10px">
            <button type="submit" class="btn primary">Terapkan Filter</button>
            <a class="btn secondary" href="<?= site_url('analytics/exportCsv?'.http_build_query($opt)) ?>">Export
                CSV</a>
            <a class="btn secondary" href="<?= site_url('analytics/exportPdf?'.http_build_query($opt)) ?>">Export
                PDF</a>
            <button type="button" id="btnRefresh" class="btn secondary">Refresh Sekarang</button>
            <span id="lastRefresh" class="sub" style="margin-left:6px"></span>
        </div>

        <div class="quick-range" style="margin-top:8px">
            <span class="sub" style="margin-right:2px">Quick Range:</span>
            <button type="button" class="pill" data-days="7">7 hari</button>
            <button type="button" class="pill" data-days="14">14 hari</button>
            <button type="button" class="pill" data-days="21">21 hari</button>
            <button type="button" class="pill" data-days="30">30 hari</button>
        </div>
    </form>

    <!-- KPI -->
    <?php
    [$dTrack,$sTrack] = $delta($summaryNow['total_tracking'],$summaryPrev['total_tracking']);
    [$dIP,$sIP]       = $delta($summaryNow['total_ip_unik'],$summaryPrev['total_ip_unik']);
    [$dPath,$sPath]   = $delta($summaryNow['total_path_unik'],$summaryPrev['total_path_unik']);
    [$dDur,$sDur]     = $delta($summaryNow['total_durasi'],$summaryPrev['total_durasi']);
    $badgeClass = fn($d)=> $d>0?'green':($d<0?'red':'amber');
  ?>
    <div class="card">
        <div class="kpi">


            <div class="kpi-item">
                <span class="kpi-label">Online Sekarang</span>
                <span class="kpi-value"><?= number_format($onlineNow) ?></span>
                <div class="kpi-trend"><span id="kpiOnlineTrend" class="badge amber">
                        (diperbarui setiap 30 detik)
                    </span></div>
            </div>
            <!-- <div class="kpi-item">
                <span class="kpi-label">Tracking (range aktif)</span>
                <span class="kpi-value" id="kpiTracking"><?= $fmt($summaryNow['total_tracking']) ?></span>
                <div class="kpi-trend"><span id="kpiTrackingTrend" class="badge <?= $badgeClass($dTrack) ?>">
                        <?= $sTrack==='±'?'±':$sTrack ?><?= $fmt(abs($dTrack)) ?>
                        (<?= $percent($summaryNow['total_tracking'],$summaryPrev['total_tracking']) ?>) vs prev
                    </span></div>
            </div> -->
            <div class="kpi-item">
                <span class="kpi-label">IP Unik</span>
                <span class="kpi-value" id="kpiIp"><?= $fmt($summaryNow['total_ip_unik']) ?></span>
                <div class="kpi-trend"><span id="kpiIpTrend" class="badge <?= $badgeClass($dIP) ?>">
                        <?= $sIP==='±'?'±':$sIP ?><?= $fmt(abs($dIP)) ?>
                        (<?= $percent($summaryNow['total_ip_unik'],$summaryPrev['total_ip_unik']) ?>)
                    </span></div>
            </div>
            <div class="kpi-item">
                <span class="kpi-label">Path Unik</span>
                <span class="kpi-value" id="kpiPath"><?= $fmt($summaryNow['total_path_unik']) ?></span>
                <div class="kpi-trend"><span id="kpiPathTrend" class="badge <?= $badgeClass($dPath) ?>">
                        <?= $sPath==='±'?'±':$sPath ?><?= $fmt(abs($dPath)) ?>
                        (<?= $percent($summaryNow['total_path_unik'],$summaryPrev['total_path_unik']) ?>)
                    </span></div>
            </div>
            <div class="kpi-item">
                <span class="kpi-label">Total Durasi (detik)</span>
                <span class="kpi-value" id="kpiDur"><?= $fmt($summaryNow['total_durasi']) ?></span>
                <div class="kpi-trend"><span id="kpiDurTrend" class="badge <?= $badgeClass($dDur) ?>">
                        <?= $sDur==='±'?'±':$sDur ?><?= $fmt(abs($dDur)) ?>
                        (<?= $percent($summaryNow['total_durasi'],$summaryPrev['total_durasi']) ?>)
                    </span></div>
            </div>
        </div>
    </div>

    <!-- CHART -->
    <div class="card chart-card">
        <div class="h3">Trend Harian</div>
        <canvas id="chartDaily"></canvas>
    </div>

    <!-- TOP PATHS -->
    <div class="two-col">
        <div class="card">
            <div class="h3">Top 10 Path — Range Aktif</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:70%">Path</th>
                            <th class="right">Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topNow as $r): ?>
                        <tr>
                            <td><?= esc($r['path'] ?: '/') ?></td>
                            <td class="right"><?= $fmt($r['jumlah']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="h3">Top 10 Path — Range Sebelumnya</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:70%">Path</th>
                            <th class="right">Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPrev as $r): ?>
                        <tr>
                            <td><?= esc($r['path'] ?: '/') ?></td>
                            <td class="right"><?= $fmt($r['jumlah']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BLACKLIST -->
    <div class="card">
        <div class="h3">IP Blacklist</div>

        <?php if (session()->getFlashdata('ok')): ?>
        <div class="badge green" style="margin-bottom:8px"><?= esc(session()->getFlashdata('ok')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('err')): ?>
        <div class="badge red" style="margin-bottom:8px"><?= esc(session()->getFlashdata('err')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('analytics/blacklist/add') ?>"
            style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px;margin-bottom:10px">
            <?= csrf_field() ?>
            <input type="text" name="ip" placeholder="IP" required>
            <input type="text" name="alasan" placeholder="Alasan (opsional)" style="min-width:260px;flex:1">
            <button type="submit" class="btn secondary">Tambah</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:30%">IP</th>
                        <th>Alasan</th>
                        <th style="width:15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blacklist as $b): ?>
                    <tr>
                        <td><?= esc($b['ip']) ?></td>
                        <td><?= esc($b['alasan'] ?? '') ?></td>
                        <td><a class="btn secondary"
                                href="<?= site_url('analytics/blacklist/del/'.urlencode($b['ip'])) ?>">Hapus</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    const qs = (s, r = document) => r.querySelector(s);
    const fmtNum = (n) => (Number(n) || 0).toLocaleString('id-ID');
    const pct = (now, prev) => {
        prev = Number(prev) || 0;
        now = Number(now) || 0;
        if (prev === 0) return '—';
        const p = ((now - prev) / Math.max(1, prev)) * 100;
        return (p > 0 ? '+' : '') + p.toFixed(1) + ' %';
    };
    const badgeClass = (d) => d > 0 ? 'green' : (d < 0 ? 'red' : 'amber');

    // Chart
    const css = getComputedStyle(document.documentElement);
    const brand = (css.getPropertyValue('--merah') || '').trim() || '#e84a49';
    const grey = '#94a3b8';
    const ctx = document.getElementById('chartDaily').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 280);
    grad.addColorStop(0, brand + '33');
    grad.addColorStop(1, brand + '05');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                    label: 'Range Sebelumnya',
                    data: [],
                    tension: .35,
                    borderColor: grey,
                    backgroundColor: 'transparent',
                    pointRadius: 0,
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Range Aktif',
                    data: [],
                    tension: .35,
                    borderColor: brand,
                    backgroundColor: grad,
                    pointRadius: 0,
                    borderWidth: 2,
                    fill: true
                },
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    grid: {
                        color: '#eef2f7'
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#eef2f7'
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                }
            }
        }
    });

    let resizeTO = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTO);
        resizeTO = setTimeout(() => chart.resize(), 120);
    });

    function getFilters() {
        const f = qs('#filterForm');
        const g = n => (f.querySelector(`[name="${n}"]`)?.value ?? '').trim();
        return {
            start: g('start'),
            end: g('end'),
            min_duration: g('min_duration') || 5,
            exclude_low_avg_duration: g('exclude_low_avg_duration') || 0,
            exclude_high_hits_per_day: g('exclude_high_hits_per_day') || 0,
            exclude_ips: g('exclude_ips') || ''
        };
    }
    const toQuery = (o) => Object.entries(o).filter(([, v]) => v !== '' && v != null).map(([k, v]) =>
        `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join('&');
    const markLastRefresh = (ts) => {
        const el = qs('#lastRefresh');
        if (el) el.textContent = `Terakhir refresh: ${ts}`;
    };

    async function fetchLiveAndRender() {
        try {
            const q = toQuery({
                ...getFilters(),
                start: getFilters().start || '<?= esc($opt['start']) ?>'
            });
            const res = await fetch(`<?= site_url('analytics/live'); ?>?${q}`, {
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            const now = data.summaryNow || {},
                prev = data.summaryPrev || {};
            const applyTrend = (sel, nowVal, prevVal) => {
                const el = qs(sel);
                if (!el) return;
                const d = (Number(nowVal) || 0) - (Number(prevVal) || 0);
                el.classList.remove('green', 'red', 'amber');
                el.classList.add(badgeClass(d));
                el.textContent =
                    `${d>0?'+':(d<0?'-':'±')}${fmtNum(Math.abs(d))} (${pct(nowVal,prevVal)}) vs prev`;
            };
            qs('#kpiTracking').textContent = fmtNum(now.total_tracking || 0);
            qs('#kpiIp').textContent = fmtNum(now.total_ip_unik || 0);
            qs('#kpiPath').textContent = fmtNum(now.total_path_unik || 0);
            qs('#kpiDur').textContent = fmtNum(now.total_durasi || 0);
            applyTrend('#kpiTrackingTrend', now.total_tracking, prev.total_tracking);
            applyTrend('#kpiIpTrend', now.total_ip_unik, prev.total_ip_unik);
            applyTrend('#kpiPathTrend', now.total_path_unik, prev.total_path_unik);
            applyTrend('#kpiDurTrend', now.total_durasi, prev.total_durasi);

            const dailyNow = data.dailyNow || [];
            const dailyPrev = data.dailyPrev || [];
            const labels = [...new Set([...dailyPrev.map(d => d.tanggal), ...dailyNow.map(d => d.tanggal)])]
                .sort();
            const mapVal = (arr) => labels.map(d => (arr.find(x => x.tanggal === d)?.hits ?? 0));
            chart.data.labels = labels;
            chart.data.datasets[0].data = mapVal(dailyPrev);
            chart.data.datasets[1].data = mapVal(dailyNow);
            chart.update('none');

            const renderTop = (root, rows) => {
                const tbody = root.querySelector('tbody');
                if (!tbody) return;
                tbody.innerHTML = (rows || []).map(r =>
                        `<tr><td>${(r.path||'/')
          .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</td><td class="right">${fmtNum(r.jumlah||0)}</td></tr>`
                    ).join('') ||
                    '<tr><td colspan="2" class="right">—</td></tr>';
            };
            const wraps = document.querySelectorAll('.two-col .card .table-wrap');
            if (wraps[0]) renderTop(wraps[0], data.topNow || []);
            if (wraps[1]) renderTop(wraps[1], data.topPrev || []);

            markLastRefresh(data.server_time || new Date().toISOString().slice(0, 19).replace('T', ' '));
        } catch (e) {
            console.error(e);
            markLastRefresh('gagal memuat');
        }
    }

    function setDateRangeByDays(days) {
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - (days - 1));
        const toYMD = d => d.toISOString().slice(0, 10);
        const f = qs('#filterForm');
        f.querySelector('[name="start"]').value = toYMD(start);
        f.querySelector('[name="end"]').value = toYMD(end);
    }

    function updatePillActive(days) {
        document.querySelectorAll('.pill').forEach(p => p.classList.toggle('active', String(p.dataset.days) ===
            String(days)));
    }
    document.querySelectorAll('.pill').forEach(p => p.addEventListener('click', () => {
        const d = Number(p.dataset.days || 0);
        setDateRangeByDays(d);
        updatePillActive(d);
        fetchLiveAndRender();
    }));
    const btn = qs('#btnRefresh');
    if (btn) btn.addEventListener('click', fetchLiveAndRender);

    fetchLiveAndRender();
    setInterval(fetchLiveAndRender, 60000);
})();
</script>

<?= $this->endSection(); ?>