<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-500: #64748b;
    --slate-700: #334155;
    --emerald-500: #10b981;
}

/* Page padding */
.page-wrap {
    padding: 2rem;
}

/* Header */
.page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.page-title {
    margin: 0;
    line-height: 1.2
}

.subtle {
    color: #6b7280;
    font-size: 13px
}

/* Toolbar */
.toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.searchbox {
    position: relative;
    min-width: 260px;
    max-width: 420px;
    flex: 1;
}

.searchbox input {
    width: 100%;
    padding: 10px 12px 10px 38px;
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    outline: none;
    background: #fff;
}

.searchbox input:focus {
    border-color: var(--slate-300);
    box-shadow: 0 0 0 3px rgba(100, 116, 139, .12)
}

.searchbox .material-icons {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: var(--slate-500);
}

/* Card container for table */
.table-card {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(0, 0, 0, .04);
}

.table-scroll {
    width: 100%;
    overflow: auto
}

.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: linear-gradient(#fff, #fff), linear-gradient(to bottom, #fff 0%, var(--slate-50) 100%);
    color: #90a0b5;
    font-size: 12px;
    letter-spacing: .3px;
    text-transform: uppercase;
    font-weight: 700;
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-200);
    white-space: nowrap;
}

.table tbody td {
    padding: 14px;
    border-bottom: 1px solid var(--slate-100);
    vertical-align: middle;
    color: var(--slate-700);
}

.table tbody tr:hover {
    background: #fcfcfd;
}

.table tbody tr:last-child td {
    border-bottom: 0;
}

/* Col widths + truncation */
.col-no {
    width: 56px;
    text-align: center;
    color: #6b7280
}

.col-judul {
    min-width: 260px;
    max-width: 560px
}

.truncate-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.col-tanggal {
    white-space: nowrap;
    width: 170px;
    color: #475569
}

.col-penulis {
    white-space: nowrap;
    width: 160px
}

.col-kategori {
    white-space: nowrap;
    width: 160px
}

.col-action {
    width: 120px;
    white-space: nowrap;
    text-align: right
}

/* Kategori badge */
.badge-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    font-size: 12px;
    color: #0f172a;
}

/* Action buttons (ikon teks) */
.action-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid var(--slate-200);
    background: #fff;
    color: #111827;
    transition: .15s ease-in-out;
    cursor: pointer;
    text-decoration: none;
}

.btn-icon:hover {
    background: var(--slate-50);
}

.btn-icon.danger {
    color: #b42318;
    border-color: #ffd3cf;
    background: #fff5f5;
}

.btn-icon.danger:hover {
    background: #ffecec;
}

/* Alert */
.alert-wrap {
    margin: 8px 0 12px
}

.pemberitahuan {
    border-radius: 10px
}

/* Responsive tweaks */
@media (max-width: 960px) {

    .col-penulis,
    .col-kategori {
        display: none;
    }
}

@media (max-width: 720px) {
    .col-tanggal {
        display: none;
    }

    .col-judul {
        max-width: 420px;
    }
}
</style>

<div class="page-wrap">
    <div class="page-head">
        <div>
            <h1 class="teks-sedang page-title">Artikel Ilena</h1>
            <div class="subtle"><?= count($artikel) ?> artikel</div>
        </div>
        <a href="/admin/addarticle" class="btn-default-merah">Tambah</a>
    </div>

    <?php if ($msg) { ?>
    <div class="alert-wrap">
        <div class="pemberitahuan w-100" role="alert"><?= $msg; ?></div>
    </div>
    <?php } ?>

    <!-- Toolbar: search client-side (tidak mengubah sistem) -->
    <div class="toolbar">
        <div class="searchbox">
            <i class="material-icons">search</i>
            <input id="filterInput" type="text" placeholder="Cari judul, penulis, atau kategori…">
        </div>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table class="table" id="artikelTable">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-judul">Judul</th>
                        <th class="col-tanggal">Tanggal</th>
                        <th class="col-penulis">Penulis</th>
                        <th class="col-kategori">Kategori</th>
                        <th class="col-action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($artikel as $ind_a => $a) { ?>
                    <tr>
                        <td class="col-no"><?= $ind_a + 1; ?></td>
                        <td class="col-judul">
                            <div class="truncate-1" title="<?= htmlspecialchars($a['judul'], ENT_QUOTES); ?>">
                                <?= $a['judul']; ?>
                            </div>
                        </td>
                        <td class="col-tanggal"><?= date('d/m/Y H:i:s', strtotime($a['waktu'])); ?></td>
                        <td class="col-penulis"><?= $a['penulis']; ?></td>
                        <td class="col-kategori">
                            <span class="badge-chip" title="Kategori">
                                <i class="material-icons" style="font-size:14px">label</i><?= $a['kategori']; ?>
                            </span>
                        </td>
                        <td class="col-action">
                            <div class="action-group">
                                <a class="btn-icon" href="/article/<?= $a['path']; ?>" title="Lihat artikel"
                                    aria-label="Lihat artikel">
                                    <i class="material-icons">open_in_new</i>
                                </a>
                                <a class="btn-icon" href="/admin/editarticle/<?= $a['id']; ?>" title="Edit artikel"
                                    aria-label="Edit artikel">
                                    <i class="material-icons">edit</i>
                                </a>
                                <button class="btn-icon danger" type="button" title="Hapus artikel"
                                    aria-label="Hapus artikel"
                                    onclick="triggerToast('Hapus artikel <?= htmlspecialchars($a['judul'], ENT_QUOTES); ?>?', '/admin/deletearticle/<?= $a['id']; ?>')">
                                    <i class="material-icons">delete</i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Filter tabel di sisi klien (tidak mengubah sistem)
(function() {
    const input = document.getElementById('filterInput');
    const tbody = document.querySelector('#artikelTable tbody');
    if (!input || !tbody) return;

    input.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        [...tbody.rows].forEach(tr => {
            const tds = [...tr.cells];
            const teks = tds.map(td => td.innerText.toLowerCase()).join(' ');
            tr.style.display = q ? (teks.includes(q) ? '' : 'none') : '';
        });
    });
})();
</script>

<?= $this->endSection(); ?>