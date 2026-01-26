<?php // app/Views/admin/partials/printBase.php ?>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --ink: #0f172a;
    --muted: #4b5563;
    --line: #e5e7eb;
    --line2: #f3f4f6;
    --merah: #b31217;
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
    font-size: 12px;
    line-height: 1.35;
}

.table {
    border-color: var(--line);
}

.table thead th {
    background: #fbfbfd !important;
    border-bottom: 1px solid var(--line);
    font-weight: 600;
    color: var(--ink);
    font-size: 10.25px;
    vertical-align: middle;
    padding: .40rem .55rem;
    letter-spacing: .02em;
    text-transform: uppercase;
}

.table tbody td {
    border-color: var(--line2);
    vertical-align: middle;
    font-size: 10.9px;
    padding: .42rem .55rem;
}

@page {
    size: A4;
    margin: 14mm 14mm 16mm;
}

@media print {
    a[href]:after {
        content: "";
    }

    tr,
    img {
        break-inside: avoid;
    }
}
</style>