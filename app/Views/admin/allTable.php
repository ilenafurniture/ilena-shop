<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tabel Produk Ilena</title>
    <style>
    :root {
        --bg: #f7fafc;
        --panel: #ffffff;
        --ring: #e5e7eb;
        --ring-2: #dbe1e8;
        --txt: #0f172a;
        --muted: #64748b;
        --brand: #2e7d32;
        --brand2: #22c55e;
        --accent: #e11d48;
    }

    * {
        box-sizing: border-box
    }

    body {
        margin: 0;
        padding: 22px;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", Arial;
        color: var(--txt);
        background: var(--bg)
    }

    .wrap {
        max-width: 1200px;
        margin: 0 auto
    }

    /* Toolbar */
    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: linear-gradient(180deg, #fff, #fbfdff);
        border: 1px solid var(--ring);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 14px;
        box-shadow: 0 10px 26px rgba(2, 6, 23, .06);
    }

    .ttl {
        display: flex;
        align-items: center;
        gap: 10px
    }

    .ttl h1 {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -.2px
    }

    .ttl .chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: .3rem .6rem;
        border-radius: 999px;
        border: 1px solid var(--ring);
        background: #f8fafc;
        color: #111827;
        font-size: 12px
    }

    .tools {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap
    }

    .search {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--ring);
        border-radius: 10px;
        padding: .55rem .7rem;
        min-width: 220px;
    }

    .search input {
        border: 0;
        outline: 0;
        width: 100%;
        background: transparent;
        font-size: 14px
    }

    .count {
        color: var(--muted);
        font-size: 13px
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        background: linear-gradient(180deg, #fff, #f8fafc);
        color: #111827;
        border: 1px solid var(--ring);
        padding: .6rem .85rem;
        border-radius: 10px;
        font-weight: 700;
        box-shadow: 0 8px 24px rgba(2, 6, 23, .06);
    }

    .btn:hover {
        transform: translateY(-1px)
    }

    .btn-excel .dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--brand2);
        box-shadow: 0 0 0 3px rgba(34, 197, 94, .2)
    }

    /* Table panel */
    .table-card {
        background: #fff;
        border: 1px solid var(--ring);
        border-radius: 14px;
        overflow: auto;
        box-shadow: 0 10px 26px rgba(2, 6, 23, .06);
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0
    }

    thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8fafc;
        color: #111827;
        text-align: left;
        font-weight: 800;
        letter-spacing: -.2px;
        padding: 12px;
        border-bottom: 1px solid var(--ring);
        white-space: nowrap;
    }

    tbody td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        font-size: 14px
    }

    tbody tr:hover {
        background: #fafafa
    }

    .mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Roboto Mono", "Courier New", monospace
    }

    .right {
        text-align: right
    }

    .nowrap {
        white-space: nowrap
    }

    /* Badge stok (dari JS data-attr) */
    td[data-stock] {
        font-weight: 700
    }

    td[data-stock="in"] {
        color: #166534
    }

    td[data-stock="out"] {
        color: #991b1b
    }

    /* Link sel */
    .linkcell a {
        color: #2563eb;
        text-decoration: none
    }

    .linkcell a:hover {
        text-decoration: underline
    }

    /* Mobile stacked table */
    @media (max-width:760px) {
        thead {
            display: none
        }

        table,
        tbody,
        tr,
        td {
            display: block;
            width: 100%
        }

        tbody tr {
            background: #fff;
            border: 1px solid var(--ring-2);
            border-radius: 12px;
            margin: 10px 10px 14px
        }

        tbody td {
            border-bottom: 1px dashed #eef2f7;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        tbody td:last-child {
            border-bottom: 0
        }

        tbody td::before {
            content: attr(data-th);
            font-weight: 700;
            color: #0f172a;
            min-width: 120px;
            flex: 0 0 120px;
        }

        .right {
            text-align: left
        }
    }
    </style>
</head>

<body>
    <div class="wrap">
        <!-- Toolbar -->
        <div class="toolbar">
            <div class="ttl">
                <h1>Feed Produk</h1>
                <span class="chip"><span
                        style="width:8px;height:8px;border-radius:999px;background:var(--accent)"></span> Export &
                    Filter</span>
            </div>
            <div class="tools">
                <div class="search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#64748b" viewBox="0 0 24 24">
                        <path d="M10 18a8 8 0 1 1 5.293-14.001A8 8 0 0 1 10 18Zm12 3-6.104-6.104" />
                    </svg>
                    <input id="q" type="search" placeholder="Cari produk / warna / deskripsi…">
                </div>
                <span class="count"><span id="rowCount">0</span> baris</span>
                <!-- Tombol download: id dipertahankan -->
                <a id="btnDownload" class="btn btn-excel" href="#"><span class="dot"></span> EXCEL</a>
            </div>
        </div>

        <!-- Tabel (struktur & id dipertahankan) -->
        <div class="table-card">
            <table id="toExcel" class="uitable">
                <thead>
                    <tr>
                        <th class="nowrap">Id</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th class="nowrap">Availability</th>
                        <th>Link</th>
                        <th>Image Link</th>
                        <th class="nowrap">Price</th>
                        <th>Color</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produk as $p) { ?>
                    <tr>
                        <td class="mono"><?= $p['id']; ?></td>
                        <td><?= ucwords($p['nama']); ?></td>
                        <td><?= $p['deskripsi_nonhtml']; ?></td>
                        <td class="mono"><?= $p['stok_total'] > 0 ? 'in_stock' : 'out_of_stock'; ?></td>
                        <td class="mono">https://ilenafurniture.com/product/<?= str_replace(' ', '-', $p['nama']); ?>
                        </td>
                        <td class="mono">https://ilenafurniture.com/viewpic/<?= $p['id']; ?></td>
                        <td class="mono right"><?= $p['harga']; ?> IDR</td>
                        <td><?= $p['warna']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    /* ===== UTIL ===== */
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    /* ===== Row counter + badge stok + mobile labels + linkable URLs ===== */
    (function enhanceTable() {
        const table = $('#toExcel');
        if (!table) return;

        const headers = $$('#toExcel thead th').map(th => th.textContent.trim());

        // Set data-th for mobile stacked layout & decorate cells
        $$('#toExcel tbody tr').forEach(tr => {
            const tds = $$('td', tr);
            tds.forEach((td, i) => td.setAttribute('data-th', headers[i] || ''));

            // Availability badge (col 4 index 3)
            const avail = (tds[3]?.textContent || '').trim().toLowerCase();
            if (avail === 'in_stock') tds[3].setAttribute('data-stock', 'in');
            else if (avail === 'out_of_stock') tds[3].setAttribute('data-stock', 'out');

            // Make Link & Image Link clickable (col 5 & 6)
            [4, 5].forEach(idx => {
                const cell = tds[idx];
                if (!cell) return;
                const url = cell.textContent.trim();
                if (/^https?:\/\//i.test(url)) {
                    cell.classList.add('linkcell');
                    cell.innerHTML = `<a href="${url}" target="_blank" rel="noopener">${url}</a>`;
                }
            });

            // Align price (col 7 index 6)
            tds[6]?.classList.add('right');
        });

        // Row count
        const rowCount = $$('#toExcel tbody tr').length;
        $('#rowCount').textContent = rowCount;
    })();

    /* ===== Filter/Search ===== */
    (function searchFeature() {
        const q = $('#q');
        const tbody = $('#toExcel tbody');
        if (!q || !tbody) return;

        const rows = $$('#toExcel tbody tr');

        function normalize(s) {
            return (s || '').toLowerCase().trim();
        }

        function applyFilter() {
            const term = normalize(q.value);
            let visible = 0;
            rows.forEach(tr => {
                const text = normalize(tr.innerText);
                const show = !term || text.includes(term);
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            $('#rowCount').textContent = visible;
        }

        q.addEventListener('input', applyFilter);
    })();

    /* ===== Export Excel via Blob (id btnDownload dipertahankan) ===== */
    function makeFilename(prefix) {
        const d = new Date(),
            pad = n => String(n).padStart(2, '0');
        return `${prefix}_${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}.xls`;
    }

    function buildExcelHTML(table) {
        const htmlTable = table.outerHTML;
        return `<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
<x:Name>TabelProduk</x:Name>
<x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions>
</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
</head>
<body>${htmlTable}</body></html>`;
    }

    function triggerDownload(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }
    (function excelButton() {
        const btn = document.getElementById('btnDownload');
        const table = document.getElementById('toExcel');
        if (!btn || !table) return;
        const filename = makeFilename('Tabel_Produk_Ilena');

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const origLabel = btn.innerHTML;
            btn.innerHTML = '<span class="dot" style="background:#999"></span> Menyiapkan…';
            btn.style.pointerEvents = 'none';

            try {
                const html = buildExcelHTML(table);
                const blob = new Blob(['\ufeff', html], {
                    type: 'application/vnd.ms-excel;charset=utf-8;'
                });
                triggerDownload(blob, filename);
            } finally {
                setTimeout(() => {
                    btn.innerHTML = '<span class="dot"></span> EXCEL';
                    btn.style.pointerEvents = '';
                }, 500);
            }
        });

        // Optional: inisialisasi href agar bisa "Save link as"
        try {
            const initBlob = new Blob(['\ufeff', buildExcelHTML(table)], {
                type: 'application/vnd.ms-excel;charset=utf-8;'
            });
            btn.href = URL.createObjectURL(initBlob);
            btn.download = filename;
        } catch (_) {}
    })();
    </script>
</body>

</html>