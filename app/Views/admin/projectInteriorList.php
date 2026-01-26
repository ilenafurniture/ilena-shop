<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --merah: #b31217;
    --merah-600: #a50e12;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --slate-700: #334155;
    --slate-800: #1f2937;
    --ring: rgba(255, 180, 180, .35);
}

/* ===== Judul ===== */
h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    letter-spacing: -.2px
}

h1.teks-sedang::after {
    content: "";
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(179, 18, 23, .25), transparent);
    border-radius: 999px;
}

/* ===== Card ===== */
.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
    box-shadow: 0 10px 28px rgba(2, 6, 23, .06);
}

/* ===== Input ===== */
.form-control {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    font-size: 13px;
    font-weight: 500;
}

.form-control:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none;
}

/* ===== Table ===== */
.table-sm th,
.table-sm td {
    padding: .5rem .6rem;
    font-size: 13px;
    vertical-align: middle
}

.table-sm thead th {
    background: #f9fafb;
    border-bottom: 1px solid var(--slate-100)
}

.table-sm tbody tr:hover {
    background: #fafafa
}

/* ===== Badge Status ===== */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 999px;
}

.badge-status.draft {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    color: #4b5563
}

.badge-status.dp {
    background: #ecfeff;
    border: 1px solid #a5f3fc;
    color: #0369a1
}

.badge-status.termin {
    background: #fef9c3;
    border: 1px solid #facc15;
    color: #854d0e
}

.badge-status.lunas {
    background: #ecfdf3;
    border: 1px solid #4ade80;
    color: #166534
}

.badge-status.unknown {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412
}

/* ===== Buttons ===== */
.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 800;
    font-size: 13px;
    padding: .65em 1.1em;
    border-radius: 10px;
}

.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .55em .9em;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
}

/* ===== Filter Chip ===== */
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
    color: #b91c1c
}

/* ===== Doc Pill ===== */
.doc-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    font-size: 11.5px;
}
</style>

<?php
$formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);
$formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

function rupiah($n,$f){ return $f->formatCurrency((int)$n,'IDR'); }
function st($s){ return strtolower(trim((string)$s)); }
function icon($s){ return $s==='lunas'?'verified':($s==='draft'?'hourglass_empty':'payments'); }
function docLabel($k){
  $k=strtoupper($k);
  if(!$k) return '';
  if(str_starts_with($k,'SJ')) return 'SJ';
  if(str_starts_with($k,'NF')) return 'NF';
  return 'DOC';
}

$countAll = is_array($projects??null) ? count($projects) : 0;
?>

<div style="padding:2em">

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h1 class="teks-sedang mb-0">Project Interior</h1>
            <small class="text-muted">Total: <b id="count-visible"><?= $countAll ?></b> / <?= $countAll ?></small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/order/offline/sale') ?>" class="btn-ghost">Offline</a>
            <a href="<?= site_url('admin/project-interior/add') ?>" class="btn-default-merah">+ Project</a>
        </div>
    </div>

    <div class="card-soft p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input id="search-input" class="form-control" placeholder="Cari kode / nama / SJ / NF (Ctrl+K)">
            </div>
            <div class="col-md-8 d-flex justify-content-end gap-2 flex-wrap">
                <span class="filter-chip active" data-status="all">Semua</span>
                <span class="filter-chip" data-status="draft">Draft</span>
                <span class="filter-chip" data-status="dp">DP</span>
                <span class="filter-chip" data-status="termin">Termin</span>
                <span class="filter-chip" data-status="lunas">Lunas</span>
            </div>
        </div>
    </div>

    <div class="card-soft p-0">
        <?php if(empty($projects)): ?>
        <div class="p-4 text-center text-muted">Belum ada project interior</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm mb-0" id="table-interior">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Dokumen</th>
                        <th class="text-end">Kontrak</th>
                        <th class="text-end">Bayar</th>
                        <th class="text-end">Sisa</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($projects as $p):
          $nilai=(int)($p['nilai_kontrak']??0);
          $bayar=(int)($p['total_bayar']??0);
          $sisa=max(0,$nilai-$bayar);
          $status=st($p['status']??'draft');
          $kode=$p['kode_project']??'';
          $dok=$p['kode_sj']??'';
          $search=strtolower($kode.' '.$p['nama_project'].' '.$dok);
        ?>
                    <tr data-status="<?= esc($status) ?>" data-search="<?= esc($search) ?>">
                        <td><code><?= esc($kode) ?></code></td>
                        <td><b><?= esc($p['nama_project']) ?></b></td>
                        <td>
                            <?php if($dok): ?>
                            <span class="doc-pill"><?= docLabel($dok) ?> <code><?= esc($dok) ?></code></span>
                            <?php else: ?><span class="text-muted">-</span><?php endif ?>
                        </td>
                        <td class="text-end"><?= rupiah($nilai,$formatter) ?></td>
                        <td class="text-end"><?= rupiah($bayar,$formatter) ?></td>
                        <td class="text-end" style="color:<?= $sisa? '#b91c1c':'#16a34a' ?>">
                            <?= rupiah($sisa,$formatter) ?>
                        </td>
                        <td>
                            <span class="badge-status <?= esc($status) ?>">
                                <i class="material-icons" style="font-size:13px"><?= icon($status) ?></i>
                                <?= strtoupper($status) ?>
                            </span>
                        </td>
                        <td><?= !empty($p['created_at']) ? date('d/m/Y',strtotime($p['created_at'])):'-' ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/project-interior/'.$kode) ?>"
                                class="btn-ghost btn-sm">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php endif ?>
    </div>
</div>

<script>
(() => {
    const rows = [...document.querySelectorAll('#table-interior tbody tr')];
    const chips = [...document.querySelectorAll('.filter-chip')];
    const search = document.getElementById('search-input');
    const count = document.getElementById('count-visible');
    let status = 'all';

    function apply() {
        const q = (search.value || '').toLowerCase();
        let v = 0;
        rows.forEach(r => {
            const okS = status === 'all' || r.dataset.status === status;
            const okQ = r.dataset.search.includes(q);
            r.style.display = (okS && okQ) ? '' : 'none';
            if (okS && okQ) v++;
        });
        count.textContent = v;
    }

    chips.forEach(c => c.onclick = () => {
        chips.forEach(x => x.classList.remove('active'));
        c.classList.add('active');
        status = c.dataset.status;
        apply();
    });
    search.oninput = apply;

    window.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            search.focus();
        }
    });
})();
</script>

<?= $this->endSection(); ?>