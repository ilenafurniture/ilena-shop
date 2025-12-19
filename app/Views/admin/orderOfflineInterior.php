<!-- app/Views/admin/orderOfflineInterior.php -->
<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --merah: #b31217;
    --merah-600: #a50e12;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --slate-300: #d1d5db;
    --slate-700: #334155;
    --slate-800: #1f2937;
}

/* judul */
h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    letter-spacing: -.2px;
}

h1.teks-sedang::after {
    content: "";
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(179, 18, 23, .25), transparent);
    border-radius: 999px;
}

/* card */
.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
}

/* table */
.table-sm th,
.table-sm td {
    padding: .55rem .7rem;
    font-size: 13px;
    vertical-align: middle;
}

.table-sm thead th {
    background: #f9fafb;
    border-bottom: 1px solid var(--slate-100);
    position: sticky;
    top: 0;
    z-index: 1;
}

/* status badge */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
    letter-spacing: .02em;
}

.badge-status.draft {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #4b5563;
}

.badge-status.dp {
    background: #ecfeff;
    border-color: #a5f3fc;
    color: #0369a1;
}

.badge-status.termin {
    background: #fef9c3;
    border-color: #facc15;
    color: #854d0e;
}

.badge-status.lunas {
    background: #ecfdf3;
    border-color: #4ade80;
    color: #166534;
}

.badge-status.unknown {
    background: #fff7ed;
    border-color: #fed7aa;
    color: #9a3412;
}

/* buttons */
.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 700;
    letter-spacing: .05em;
    padding: .65em 1.1em;
    border-radius: 10px;
    box-shadow: 0 10px 26px rgba(179, 18, 23, .26);
    transition: transform .08s, filter .08s, box-shadow .18s, opacity .2s;
    font-size: 13px;
    text-transform: uppercase;
}

.btn-default-merah:hover {
    filter: brightness(.98);
}

.btn-default-merah:active {
    transform: translateY(1px);
    box-shadow: 0 7px 18px rgba(179, 18, 23, .22);
}

.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .55em .9em;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    color: #111827;
}

.btn-ghost:hover {
    background: #e5e7eb;
    color: #111827;
}

/* search & filter */
.filter-chip {
    font-size: 11.5px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    padding: 4px 10px;
    background: #f9fafb;
    cursor: pointer;
    user-select: none;
}

.filter-chip.active {
    background: #fee2e2;
    border-color: #fecaca;
    color: #b91c1c;
}

/* empty state */
.empty-state {
    padding: 2.5rem 1.5rem;
    text-align: center;
    color: #6b7280;
    font-size: 13px;
}

.empty-state i {
    font-size: 32px;
    color: #9ca3af;
    margin-bottom: 6px;
}

/* kecil-kecil tapi ngaruh */
.meta-top {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 12px;
    color: #64748b;
}

.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
}

.meta-pill b {
    color: #0f172a;
}

/* responsive */
@media (max-width: 768px) {
    .hide-md {
        display: none !important;
    }

    .btn-default-merah {
        padding: .6em .9em;
        font-size: 12px;
    }

    .btn-ghost {
        padding: .5em .75em;
        font-size: 12px;
    }
}
</style>

<?php
  // ekspektasi dari controller:
  // $projects = [...];
  // tiap item minimal:
  // kode_project, nama_project, nilai_kontrak, total_bayar, status, created_at

  $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
  $formatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'IDR');
  $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

  function rupiah_local($n, $formatter){
    return $formatter->formatCurrency((int)$n, 'IDR');
  }

  function status_norm($s){
    $s = strtolower(trim((string)$s));
    // normalisasi kalau dari DB pakai variasi
    if ($s === 'paid' || $s === 'lunas') return 'lunas';
    if ($s === 'downpayment' || $s === 'dp') return 'dp';
    if ($s === 'term' || $s === 'termin') return 'termin';
    if ($s === 'draft') return 'draft';
    return $s ?: 'unknown';
  }

  function status_icon($s){
    $s = status_norm($s);
    if ($s === 'lunas') return 'verified';
    if ($s === 'draft') return 'hourglass_empty';
    if ($s === 'termin') return 'schedule';
    if ($s === 'dp') return 'payments';
    return 'help';
  }

  // ringkasan cepat
  $countAll = is_array($projects) ? count($projects) : 0;
  $sumKontrak = 0; $sumBayar = 0;
  if (!empty($projects)){
    foreach ($projects as $p){
      $sumKontrak += (int)($p['nilai_kontrak'] ?? 0);
      $sumBayar   += (int)($p['total_bayar'] ?? 0);
    }
  }
  $sumSisa = max(0, $sumKontrak - $sumBayar);
?>

<div style="padding:2em;" class="h-100 d-flex flex-column">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column gap-1">
            <h1 class="teks-sedang mb-0">Pesanan Interior</h1>
            <div class="meta-top">
                <span class="meta-pill"><i class="material-icons" style="font-size:16px;">fact_check</i> Total Project:
                    <b><?= (int)$countAll; ?></b></span>
                <span class="meta-pill hide-md"><i class="material-icons" style="font-size:16px;">description</i> Nilai
                    Kontrak: <b><?= rupiah_local($sumKontrak, $formatter); ?></b></span>
                <span class="meta-pill hide-md"><i class="material-icons" style="font-size:16px;">payments</i> Total
                    Bayar: <b><?= rupiah_local($sumBayar, $formatter); ?></b></span>
                <span class="meta-pill"><i class="material-icons" style="font-size:16px;">pending_actions</i> Sisa:
                    <b><?= rupiah_local($sumSisa, $formatter); ?></b></span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/order/offline/sale'); ?>" class="btn-ghost">
                <i class="material-icons" style="font-size:16px;vertical-align:-3px;">store</i>
                Offline (Sale)
            </a>
            <a href="<?= site_url('admin/project-interior/add'); ?>" class="btn-default-merah">
                <i class="material-icons" style="font-size:16px;vertical-align:-3px;">add</i>
                Project Interior Baru
            </a>
        </div>
    </div>

    <div class="card-soft p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="search-input" class="form-control"
                    placeholder="Cari nama project / kode project...">
            </div>
            <div class="col-md-8 d-flex justify-content-end gap-2 flex-wrap">
                <span class="filter-chip active" data-status="all">Semua Status</span>
                <span class="filter-chip" data-status="draft">Draft</span>
                <span class="filter-chip" data-status="dp">DP</span>
                <span class="filter-chip" data-status="termin">Termin</span>
                <span class="filter-chip" data-status="lunas">Lunas</span>
            </div>
        </div>
    </div>

    <div class="card-soft p-0">
        <?php if (empty($projects)): ?>
        <div class="empty-state">
            <i class="material-icons">home</i>
            <div class="mb-1"><b>Belum ada project interior.</b></div>
            <div>Buat project baru untuk mulai mengelola pesanan interior.</div>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle" id="table-interior">
                <thead>
                    <tr>
                        <th style="width:90px;">Kode</th>
                        <th>Nama Project</th>
                        <th style="width:140px;" class="text-end">Nilai Kontrak</th>
                        <th style="width:140px;" class="text-end">Total Bayar</th>
                        <th style="width:140px;" class="text-end">Sisa</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:120px;" class="hide-md">Dibuat</th>
                        <th style="width:100px;" class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($projects as $p): ?>
                    <?php
                $nilai = (int)($p['nilai_kontrak'] ?? 0);
                $bayar = (int)($p['total_bayar'] ?? 0);
                $sisa  = max(0, $nilai - $bayar);

                $st = status_norm($p['status'] ?? '');
                $stClass = in_array($st, ['draft','dp','termin','lunas'], true) ? $st : 'unknown';

                $kodeProject = (string)($p['kode_project'] ?? '');
                $namaProject = (string)($p['nama_project'] ?? '');
                $searchKey = strtolower(trim($kodeProject . ' ' . $namaProject));

                $createdAt = $p['created_at'] ?? null;
                $createdText = '-';
                if (!empty($createdAt)){
                  $ts = strtotime((string)$createdAt);
                  if ($ts) $createdText = date('d/m/Y', $ts);
                }
              ?>

                    <tr data-status="<?= esc($st); ?>" data-search="<?= esc($searchKey); ?>">
                        <td><code><?= esc($kodeProject ?: '-'); ?></code></td>

                        <td>
                            <div style="font-size:13px;font-weight:700;letter-spacing:-.1px;">
                                <?= esc($namaProject ?: '-'); ?>
                            </div>
                        </td>

                        <td class="text-end" style="font-weight:700;">
                            <?= rupiah_local($nilai, $formatter); ?>
                        </td>

                        <td class="text-end" style="font-size:12.5px;">
                            <?= rupiah_local($bayar, $formatter); ?>
                        </td>

                        <td class="text-end"
                            style="font-size:12.5px;color:<?= $sisa>0 ? '#b91c1c' : '#16a34a'; ?>;font-weight:700;">
                            <?= rupiah_local($sisa, $formatter); ?>
                        </td>

                        <td>
                            <span class="badge-status <?= esc($stClass); ?>">
                                <i class="material-icons" style="font-size:14px;"><?= esc(status_icon($st)); ?></i>
                                <?= strtoupper($st ?: 'UNKNOWN'); ?>
                            </span>
                        </td>

                        <td class="hide-md" style="font-size:12px;color:#64748b;">
                            <?= esc($createdText); ?>
                        </td>

                        <td class="text-end">
                            <a href="<?= site_url('admin/project-interior/' . rawurlencode($kodeProject)); ?>"
                                class="btn-ghost" style="padding:.35em .6em;font-size:12px;"
                                title="Buka detail project">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- baris "tidak ada hasil" -->
                    <tr id="row-empty-filter" style="display:none;">
                        <td colspan="8" class="text-center" style="padding:18px;color:#6b7280;font-size:13px;">
                            <i class="material-icons"
                                style="font-size:18px;vertical-align:-4px;color:#9ca3af;">search_off</i>
                            Tidak ada data yang cocok dengan filter / pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
(function() {
    const chips = document.querySelectorAll('.filter-chip');
    const table = document.getElementById('table-interior');
    const searchInput = document.getElementById('search-input');

    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tbody tr')).filter(r => r.id !== 'row-empty-filter');
    const emptyRow = document.getElementById('row-empty-filter');

    let currentStatus = 'all';

    function applyFilter() {
        const q = (searchInput?.value || '').toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = (row.getAttribute('data-status') || '').toLowerCase().trim();
            const text = (row.getAttribute('data-search') || '').toLowerCase();

            const matchStatus = (currentStatus === 'all') || (rowStatus === currentStatus);
            const matchSearch = (!q) || text.includes(q);

            const show = matchStatus && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        if (emptyRow) {
            emptyRow.style.display = (visibleCount === 0) ? '' : 'none';
        }
    }

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentStatus = (chip.getAttribute('data-status') || 'all').toLowerCase().trim();
            applyFilter();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }

    // initial
    applyFilter();
})();
</script>

<?= $this->endSection(); ?>