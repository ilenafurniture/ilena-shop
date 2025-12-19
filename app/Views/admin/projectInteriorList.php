<!-- app/Views/admin/projectInteriorList.php -->
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
    --ring: rgba(255, 180, 180, .35);
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
    box-shadow: 0 10px 28px rgba(2, 6, 23, .06);
}

/* search input (biar nyatu style) */
.form-control {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    transition: border-color .15s, box-shadow .15s, background .15s;
    background: #fff;
    font-weight: 500;
    font-size: 13px;
}

.form-control:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none;
}

/* table */
.table-sm th,
.table-sm td {
    padding: .5rem .6rem;
    font-size: 13px;
    vertical-align: middle;
}

.table-sm thead th {
    background: #f9fafb;
    border-bottom: 1px solid var(--slate-100);
}

.table-sm tbody tr:hover {
    background: #fafafa;
}

/* badge status */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid transparent;
    white-space: nowrap;
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

/* buttons */
.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 800;
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
    font-weight: 700;
    font-size: 13px;
}

.btn-ghost:hover {
    background: #e5e7eb;
}

/* filter chip */
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

/* little counter */
.small-counter {
    font-size: 12px;
    color: #64748b;
}

/* doc badge kecil */
.doc-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    font-size: 11.5px;
    color: #334155;
    white-space: nowrap;
}

.doc-pill code {
    font-size: 11.5px;
}
</style>

<?php
  // Ekspektasi dari controller:
  // $projects = [ ['kode_project','nama_project','kode_sj','nilai_kontrak','total_bayar','status','created_at'], ... ]
  // $msg = optional

  $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
  $formatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'IDR');
  $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

  function rupiah_local($n, $formatter){
    return $formatter->formatCurrency((int)$n, 'IDR');
  }

  function status_norm($s){
    return strtolower(trim((string)$s));
  }

  function status_icon($s){
    $s = status_norm($s);
    if ($s === 'lunas') return 'verified';
    if ($s === 'draft') return 'hourglass_empty';
    return 'payments'; // dp / termin / lainnya
  }

  function doc_label($kodeDok){
    $k = strtoupper(trim((string)$kodeDok));
    if ($k === '') return 'Dokumen Utama';
    if (preg_match('/^NF/', $k)) return 'NF';
    if (preg_match('/^SJ/', $k)) return 'SJ';
    return 'Dokumen Utama';
  }

  // hitung ringkas
  $countAll = is_array($projects ?? null) ? count($projects) : 0;
?>

<div style="padding: 2em;" class="h-100 d-flex flex-column">

    <?php if (!empty($msg)): ?>
    <div class="alert alert-info py-2 mb-3" style="font-size:13px;">
        <?= esc($msg); ?>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column">
            <h1 class="teks-sedang mb-0">Project Interior</h1>
            <span class="small-counter mt-1">
                Total: <b id="count-visible"><?= (int)$countAll; ?></b> / <?= (int)$countAll; ?> project
            </span>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/order/offline/sale'); ?>" class="btn-ghost">
                <i class="material-icons" style="font-size:16px;vertical-align:-3px;">store</i>
                Offline (Sale)
            </a>
            <a href="<?= site_url('admin/project-interior/add'); ?>" class="btn-default-merah">
                <i class="material-icons" style="font-size:16px;vertical-align:-3px;">add</i>
                Project Baru
            </a>
        </div>
    </div>

    <div class="card-soft p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="search-input" class="form-control"
                    placeholder="Cari nama / kode / dokumen... (Ctrl+K juga boleh 😄)">
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
                        <th style="width:80px;">Kode</th>
                        <th>Nama Project</th>
                        <th style="width:160px;">Dokumen Utama (SJ/NF)</th>
                        <th style="width:140px;" class="text-end">Nilai Kontrak</th>
                        <th style="width:140px;" class="text-end">Total Bayar</th>
                        <th style="width:130px;" class="text-end">Sisa</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:120px;">Dibuat</th>
                        <th style="width:90px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                    <?php
                        $nilai   = (int)($p['nilai_kontrak'] ?? 0);
                        $bayar   = (int)($p['total_bayar'] ?? 0);
                        $sisa    = max(0, $nilai - $bayar);

                        $status  = status_norm($p['status'] ?? 'draft');
                        $created = $p['created_at'] ?? null;

                        $kode = (string)($p['kode_project'] ?? '');
                        $nama = (string)($p['nama_project'] ?? '');
                        $kodeDok = (string)($p['kode_sj'] ?? ''); // dokumen utama (SJ/NF)
                        $dokLabel = doc_label($kodeDok);

                        $searchKey = strtolower(trim(preg_replace('/\s+/', ' ', $kode.' '.$nama.' '.$kodeDok)));
                    ?>
                    <tr data-status="<?= esc($status); ?>" data-search="<?= esc($searchKey); ?>">
                        <td><code><?= esc($kode ?: '-'); ?></code></td>

                        <td>
                            <div style="font-size:13px;font-weight:700;"><?= esc($nama ?: '-'); ?></div>
                        </td>

                        <td style="font-size:12px;">
                            <?php if (!empty($kodeDok)): ?>
                            <span class="doc-pill">
                                <i class="material-icons" style="font-size:14px;color:#64748b;">description</i>
                                <span style="font-weight:800;"><?= esc($dokLabel); ?></span>
                                <code><?= esc($kodeDok); ?></code>
                            </span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-end" style="font-weight:700;"><?= rupiah_local($nilai, $formatter); ?></td>
                        <td class="text-end" style="font-size:12.5px;"><?= rupiah_local($bayar, $formatter); ?></td>

                        <td class="text-end" style="font-size:12.5px;color:<?= $sisa>0?'#b91c1c':'#16a34a'; ?>;">
                            <?= rupiah_local($sisa, $formatter); ?>
                        </td>

                        <td>
                            <span class="badge-status <?= esc($status); ?>">
                                <i class="material-icons" style="font-size:14px;"><?= esc(status_icon($status)); ?></i>
                                <?= strtoupper($status ?: 'UNKNOWN'); ?>
                            </span>
                        </td>

                        <td style="font-size:12px;">
                            <?= $created ? esc(date('d/m/Y', strtotime($created))) : '-'; ?>
                        </td>

                        <td class="text-end">
                            <a href="<?= site_url('admin/project-interior/' . rawurlencode($kode)); ?>"
                                class="btn-ghost" style="padding:.35em .6em;font-size:12px;">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
(() => {
    const chips = document.querySelectorAll('.filter-chip');
    const table = document.getElementById('table-interior');
    const rows = table ? Array.from(table.querySelectorAll('tbody tr')) : [];
    const searchInput = document.getElementById('search-input');
    const countVisibleEl = document.getElementById('count-visible');

    // persist filter (biar balik lagi tetap ingat)
    const LS_STATUS = 'pi_filter_status';
    const LS_QUERY = 'pi_filter_query';

    let currentStatus = localStorage.getItem(LS_STATUS) || 'all';

    function setActiveChip(status) {
        chips.forEach(c => c.classList.toggle('active', (c.getAttribute('data-status') || 'all') === status));
    }

    function applyFilter() {
        const q = (searchInput?.value || '').toLowerCase().trim();
        let visible = 0;

        rows.forEach(row => {
            const rowStatus = (row.getAttribute('data-status') || '').toLowerCase().trim();
            const text = (row.getAttribute('data-search') || '').toLowerCase();

            const matchStatus = (currentStatus === 'all') || (rowStatus === currentStatus);
            const matchSearch = !q || text.includes(q);

            const show = matchStatus && matchSearch;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (countVisibleEl) countVisibleEl.textContent = String(visible);

        localStorage.setItem(LS_STATUS, currentStatus);
        localStorage.setItem(LS_QUERY, q);
    }

    // init saved query
    if (searchInput) {
        const savedQ = localStorage.getItem(LS_QUERY) || '';
        if (savedQ) searchInput.value = savedQ;
    }

    setActiveChip(currentStatus);
    applyFilter();

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            currentStatus = (chip.getAttribute('data-status') || 'all').toLowerCase().trim();
            setActiveChip(currentStatus);
            applyFilter();
        });
    });

    if (searchInput) {
        // Ctrl+K fokus search (biar berasa dashboard pro 😄)
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });

        searchInput.addEventListener('input', applyFilter);
    }
})();
</script>

<?= $this->endSection(); ?>