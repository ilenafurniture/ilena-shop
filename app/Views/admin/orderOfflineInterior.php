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
    padding: .5rem .6rem;
    font-size: 13px;
    vertical-align: middle;
}

.table-sm thead th {
    background: #f9fafb;
    border-bottom: 1px solid var(--slate-100);
}

/* status badge */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid transparent;
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
}

.btn-ghost:hover {
    background: #e5e7eb;
}

/* search & filter */
.filter-chip {
    font-size: 11.5px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    padding: 4px 10px;
    background: #f9fafb;
    cursor: pointer;
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
</style>

<?php
    // ekspektasi dari controller:
    // $title = 'Pesanan';
    // $projects = [...]; // array project interior
    // tiap item minimal: kode_project, nama_project, nilai_kontrak, total_bayar, status, created_at
    $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
    $formatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'IDR');
    $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

    function rupiah_local($n, $formatter) {
        return $formatter->formatCurrency((int)$n, 'IDR');
    }
?>

<div style="padding: 2em;" class="h-100 d-flex flex-column">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="teks-sedang mb-0">
            Pesanan Interior
        </h1>

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
                        <th style="width: 80px;">Kode</th>
                        <th>Nama Project</th>
                        <th style="width: 130px;" class="text-end">Nilai Kontrak</th>
                        <th style="width: 130px;" class="text-end">Total Bayar</th>
                        <th style="width: 130px;" class="text-end">Sisa</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 120px;">Dibuat</th>
                        <th style="width: 90px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                    <?php
                                $sisa = max(0, (int)$p['nilai_kontrak'] - (int)$p['total_bayar']);
                            ?>
                    <tr data-status="<?= esc(strtolower($p['status'])); ?>"
                        data-search="<?= strtolower(trim($p['kode_project'].' '.$p['nama_project'])); ?>">
                        <td>
                            <code><?= esc($p['kode_project']); ?></code>
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:600;"><?= esc($p['nama_project']); ?></div>
                        </td>
                        <td class="text-end" style="font-weight:600;">
                            <?= rupiah_local($p['nilai_kontrak'], $formatter); ?>
                        </td>
                        <td class="text-end" style="font-size:12.5px;">
                            <?= rupiah_local($p['total_bayar'], $formatter); ?>
                        </td>
                        <td class="text-end" style="font-size:12.5px;color:<?= $sisa>0?'#b91c1c':'#16a34a'; ?>;">
                            <?= rupiah_local($sisa, $formatter); ?>
                        </td>
                        <td>
                            <span class="badge-status <?= esc(strtolower($p['status'])); ?>">
                                <i class="material-icons" style="font-size:14px;">
                                    <?php if ($p['status'] === 'lunas'): ?>
                                    verified
                                    <?php elseif ($p['status'] === 'draft'): ?>
                                    hourglass_empty
                                    <?php else: ?>
                                    payments
                                    <?php endif; ?>
                                </i>
                                <?= strtoupper($p['status']); ?>
                            </span>
                        </td>
                        <td style="font-size:12px;">
                            <?= esc(date('d/m/Y', strtotime($p['created_at'] ?? 'now'))); ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/project-interior/' . $p['kode_project']); ?>" class="btn-ghost"
                                style="padding:.35em .6em;font-size:12px;">
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
// filter status
const chips = document.querySelectorAll('.filter-chip');
const rows = document.querySelectorAll('#table-interior tbody tr');
const searchInput = document.getElementById('search-input');

let currentStatus = 'all';

chips.forEach(chip => {
    chip.addEventListener('click', () => {
        chips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        currentStatus = chip.getAttribute('data-status') || 'all';
        applyFilter();
    });
});

if (searchInput) {
    searchInput.addEventListener('input', () => {
        applyFilter();
    });
}

function applyFilter() {
    const q = (searchInput?.value || '').toLowerCase().trim();

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status') || '';
        const text = row.getAttribute('data-search') || '';

        const matchStatus = (currentStatus === 'all') || (rowStatus === currentStatus);
        const matchSearch = !q || text.includes(q);

        row.style.display = matchStatus && matchSearch ? '' : 'none';
    });
}
</script>

<?= $this->endSection(); ?>