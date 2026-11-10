<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<style>
.menu-copy { --ink:#282521; --muted:#716a62; --line:#e8e3dc; color:var(--ink); display:grid; gap:20px; }
.menu-copy__head { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; padding:8px 0 18px; border-bottom:1px solid var(--line); }
.menu-copy__head h1 { margin:0 0 7px; font:500 clamp(24px,3vw,34px)/1.15 Georgia,serif; letter-spacing:-.025em; }
.menu-copy__head p { margin:0; max-width:680px; color:var(--muted); font-size:13px; line-height:1.65; }
.menu-copy__tools { display:flex; justify-content:space-between; align-items:center; gap:16px; }
.menu-copy__search { width:min(100%,380px); height:42px; padding:0 13px; border:1px solid #d9d2c9; border-radius:3px; color:var(--ink); background:#fff; font:inherit; }
.menu-copy__search:focus,.menu-copy textarea:focus { outline:2px solid #a62b2b; outline-offset:2px; }
.menu-copy__count { color:var(--muted); font-size:12px; white-space:nowrap; }
.menu-copy__group { border-bottom:1px solid var(--line); }
.menu-copy__group summary { list-style:none; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:16px; padding:15px 2px; font-size:14px; font-weight:650; }
.menu-copy__group summary::-webkit-details-marker { display:none; }
.menu-copy__group summary::after { content:'+'; color:#9b2828; font-size:20px; font-weight:400; }
.menu-copy__group[open] summary::after { content:'−'; }
.menu-copy__rows { padding:0 0 18px; }
.menu-copy__row { display:grid; grid-template-columns:minmax(155px,.48fr) minmax(0,1fr) minmax(0,1fr); gap:16px; padding:16px 0; border-top:1px solid #f0ede8; }
.menu-copy__label { padding-top:4px; }
.menu-copy__label strong { display:block; font-size:13px; font-weight:650; }
.menu-copy__label span { display:block; margin-top:4px; color:var(--muted); font-size:11px; }
.menu-copy__field label { display:block; margin-bottom:6px; font-size:11px; font-weight:650; letter-spacing:.045em; text-transform:uppercase; }
.menu-copy textarea { display:block; width:100%; min-height:82px; resize:vertical; padding:10px 11px; border:1px solid #ded8d0; border-radius:2px; background:#fff; color:var(--ink); font:inherit; font-size:13px; line-height:1.55; }
.menu-copy__hint { margin:5px 0 0; color:#827b72; font-size:10px; }
.menu-copy__actions { display:flex; justify-content:flex-end; padding:8px 0 18px; border-top:1px solid var(--line); }
.menu-copy__save { min-height:42px; padding:0 19px; border:0; border-radius:2px; background:#a4262a; color:#fff; font:inherit; font-size:12px; font-weight:600; letter-spacing:.025em; cursor:pointer; }
.menu-copy__save:hover { background:#861e22; }
.menu-copy__alert { padding:11px 14px; border-left:3px solid #a4262a; background:#fbf7f4; font-size:13px; }
.menu-copy__alert--error { border-color:#a4262a; color:#7d2020; }
.menu-copy__empty { display:none; padding:28px 0; color:var(--muted); text-align:center; font-size:13px; }
@media(max-width:820px) { .menu-copy__row { grid-template-columns:1fr 1fr; gap:12px; } .menu-copy__label { grid-column:1/-1; } }
@media(max-width:560px) { .menu-copy__head { align-items:flex-start; flex-direction:column; } .menu-copy__tools { align-items:flex-start; flex-direction:column; } .menu-copy__search { width:100%; } .menu-copy__row { grid-template-columns:1fr; gap:12px; } .menu-copy__label { grid-column:auto; } .menu-copy__actions,.menu-copy__save { width:100%; } }
</style>

<section class="menu-copy">
    <header class="menu-copy__head">
        <div>
            <h1>Deskripsi Menu</h1>
            <p>Atur ringkasan yang tampil saat pengunjung membuka menu koleksi. Perubahan ini hanya untuk navigasi, tidak mengubah deskripsi pada halaman detail produk. Kosongkan kedua bahasa untuk memakai deskripsi produk otomatis.</p>
        </div>
        <a class="btn btn-outline-secondary btn-sm" href="/" target="_blank" rel="noopener">Lihat website</a>
    </header>

    <?php if ($message = session()->getFlashdata('success')): ?><div class="menu-copy__alert" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if ($message = session()->getFlashdata('error')): ?><div class="menu-copy__alert menu-copy__alert--error" role="alert"><?= esc($message); ?></div><?php endif; ?>

    <div class="menu-copy__tools">
        <input class="menu-copy__search" type="search" placeholder="Cari jenis produk atau koleksi…" aria-label="Cari jenis produk atau koleksi" data-menu-search>
        <span class="menu-copy__count"><strong data-visible-count>0</strong> pasangan menu</span>
    </div>

    <form method="post" action="/admin/menu-descriptions">
        <?= csrf_field(); ?>
        <div>
            <?php $index = 0; foreach (($groups ?? []) as $type => $rows): ?>
            <details class="menu-copy__group" <?= $index === 0 ? 'open' : ''; ?> data-menu-group>
                <summary><span><?= esc(ucwords(str_replace('-', ' ', $type))); ?></span><span><?= count($rows); ?> koleksi</span></summary>
                <div class="menu-copy__rows">
                    <?php foreach ($rows as $row): ?>
                    <div class="menu-copy__row" data-menu-row data-search-text="<?= esc(function_exists('mb_strtolower') ? mb_strtolower($row['type'] . ' ' . $row['collection']) : strtolower($row['type'] . ' ' . $row['collection']), 'attr'); ?>">
                        <div class="menu-copy__label">
                            <strong><?= esc($row['collection']); ?></strong>
                            <span><?= esc($row['type']); ?></span>
                            <input type="hidden" name="descriptions[<?= $index; ?>][type]" value="<?= esc($row['type'], 'attr'); ?>">
                            <input type="hidden" name="descriptions[<?= $index; ?>][collection]" value="<?= esc($row['collection'], 'attr'); ?>">
                        </div>
                        <div class="menu-copy__field">
                            <label for="menu-id-<?= $index; ?>">Bahasa Indonesia</label>
                            <textarea id="menu-id-<?= $index; ?>" name="descriptions[<?= $index; ?>][id]" maxlength="320" placeholder="Pakai deskripsi produk otomatis"><?= esc($row['id']); ?></textarea>
                            <p class="menu-copy__hint">Maksimal 320 karakter</p>
                        </div>
                        <div class="menu-copy__field">
                            <label for="menu-en-<?= $index; ?>">English</label>
                            <textarea id="menu-en-<?= $index; ?>" name="descriptions[<?= $index; ?>][en]" maxlength="320" placeholder="Use the product description automatically"><?= esc($row['en']); ?></textarea>
                            <p class="menu-copy__hint">Up to 320 characters</p>
                        </div>
                    </div>
                    <?php $index++; endforeach; ?>
                </div>
            </details>
            <?php endforeach; ?>
            <p class="menu-copy__empty" data-menu-empty>Tidak ada jenis atau koleksi yang cocok dengan pencarian.</p>
        </div>
        <?php if ($index > 0): ?>
        <div class="menu-copy__actions"><button class="menu-copy__save" type="submit">Simpan deskripsi menu</button></div>
        <?php else: ?>
        <p class="menu-copy__empty" style="display:block">Belum ada pasangan jenis dan koleksi aktif.</p>
        <?php endif; ?>
    </form>
</section>

<script>
(() => {
    const input = document.querySelector('[data-menu-search]');
    const rows = [...document.querySelectorAll('[data-menu-row]')];
    const groups = [...document.querySelectorAll('[data-menu-group]')];
    const count = document.querySelector('[data-visible-count]');
    const empty = document.querySelector('[data-menu-empty]');
    if (!input) return;
    const filter = () => {
        const query = input.value.trim().toLocaleLowerCase();
        let visible = 0;
        rows.forEach((row) => {
            const match = !query || row.dataset.searchText.includes(query);
            row.hidden = !match;
            if (match) visible++;
        });
        groups.forEach((group) => {
            const hasRows = [...group.querySelectorAll('[data-menu-row]')].some((row) => !row.hidden);
            group.hidden = !hasRows;
            if (query && hasRows) group.open = true;
        });
        if (count) count.textContent = String(visible);
        if (empty) empty.style.display = visible ? 'none' : 'block';
    };
    input.addEventListener('input', filter);
    filter();
})();
</script>
<?= $this->endSection(); ?>
