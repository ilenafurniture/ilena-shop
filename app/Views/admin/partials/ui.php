<?php // app/Views/admin/partials/ui.php ?>
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
    --ok: #16a34a;
    --warn: #b91c1c;
    --info: #2563eb;
}

* {
    box-sizing: border-box;
}

body {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

html,
body {
    scroll-behavior: smooth;
}

*::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

*::-webkit-scrollbar-thumb {
    background: #d9d9d9;
    border-radius: 20px;
}

*::-webkit-scrollbar-thumb:hover {
    background: #c7c7c7;
}

*::-webkit-scrollbar-track {
    background: transparent;
}

/* ===== Judul ===== */
h1.teks-sedang {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    letter-spacing: -.2px;
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
    box-shadow: 0 10px 26px rgba(2, 8, 23, .06);
}

.card-head {
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    letter-spacing: -.2px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 14px;
}

/* ===== Form ===== */
.form-control,
.form-select,
input {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    background: #fff;
    font-weight: 600;
    font-size: 13px;
    transition: border-color .15s, box-shadow .15s, background .15s;
}

textarea.form-control {
    height: auto;
    min-height: 88px;
    resize: vertical;
}

.form-control:focus,
.form-select:focus,
input:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none;
}

/* ===== Helper ===== */
.helper {
    position: relative;
    display: block;
    margin: 8px 0 0;
    padding: 8px 12px 8px 36px;
    font-size: 12px;
    line-height: 1.35;
    letter-spacing: -.2px;
    background: #e8f4ff;
    color: #1e4e79;
    border: 1px solid #cfe7ff;
    border-radius: 10px;
}

.helper:before {
    content: "info";
    font-family: "Material Icons";
    font-size: 18px;
    line-height: 1;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: .9;
}

.helper.warn {
    background: #ffe8e8;
    border-color: #ffc9c9;
    color: #8b2a2b;
}

.helper.warn:before {
    content: "priority_high";
}

.helper.ok {
    background: #ecfdf3;
    border-color: #4ade80;
    color: #166534;
}

.helper.ok:before {
    content: "check_circle";
}

/* ===== Badge ===== */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #111827;
}

.badge.ok {
    color: #065f46;
    background: #e7f8ef;
    border-color: #c9f0dc;
}

.badge.warn {
    color: #b42318;
    background: #feeaea;
    border-color: #ffd3cf;
}

.badge.red {
    color: #991b1b;
    background: #fee2e2;
    border-color: #fecaca;
}

/* ===== Status badge (list) ===== */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
    letter-spacing: .02em;
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

.badge-status.unknown {
    background: #fff7ed;
    border-color: #fed7aa;
    color: #9a3412;
}

/* ===== Buttons ===== */
.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .7em 1em;
    border-radius: 10px;
    font-weight: 900;
}

.btn-ghost:hover {
    background: #e5e7eb;
}

.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 900;
    letter-spacing: .1px;
    padding: .9em 1.1em;
    border-radius: 12px;
    box-shadow: 0 14px 36px rgba(179, 18, 23, .28);
    transition: transform .08s, filter .08s, box-shadow .18s, opacity .2s;
}

.btn-default-merah:hover {
    filter: brightness(.98);
}

.btn-default-merah:active {
    transform: translateY(1px);
    box-shadow: 0 10px 22px rgba(179, 18, 23, .24);
}

.btn-default-merah.disabled,
[disabled].btn-default-merah {
    opacity: .55;
    pointer-events: none;
}

.muted {
    color: #64748b;
    font-size: 12px;
}

.mono {
    font-variant-numeric: tabular-nums;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

@media(max-width:992px) {
    .layout-grid {
        flex-direction: column;
    }
}
</style>