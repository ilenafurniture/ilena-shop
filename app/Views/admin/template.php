<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token(); ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash(); ?>">
    <title><?= esc($title ?? 'Admin'); ?> | ILENA</title>

    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css?v=23-04-2025'); ?>">
    <link rel="icon" href="<?= base_url('img/logo/N.png?v=11-03-2025'); ?>" type="image/png">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Layout guard supaya konten & sticky element ga tabrakan -->
    <style>
    :root {
        --adminSidebarW: 264px;
        --adminPad: clamp(16px, 2vw, 28px);
        --adminSurface: #fffdfa;
        --adminBg: #f6f4f0;
        --adminBorder: #e9e3da;
        --adminInk: #28231f;
        --adminMuted: #756b62;
        --adminAccent: #8b5e3c;
        --adminAccentSoft: #f4ece4;
        --adminRadius: 12px;
        --adminShadow: 0 8px 24px rgba(47, 37, 27, .045);
    }

    html,
    body {
        height: 100%;
    }

    body {
        background: var(--adminBg);
        color: var(--adminInk);
        font-family: "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        -webkit-font-smoothing: antialiased;
        text-rendering: geometricPrecision;
    }

    body button,
    body input,
    body select,
    body textarea {
        font-family: inherit;
    }

    .btn-default-merah,
    .btn,
    button {
        border-radius: 12px;
        font-weight: 800;
        letter-spacing: -.01em;
    }

    /* Shell responsive: sidebar kiri + konten kanan */
    .admin-shell {
        display: flex;
        min-height: 100svh;
        width: 100%;
        background: linear-gradient(180deg, #faf8f4 0%, var(--adminBg) 100%);
    }

    .admin-sidebar {
        width: var(--adminSidebarW);
        min-width: var(--adminSidebarW);
        background: var(--adminSurface);
        border-right: 1px solid var(--adminBorder);
        overflow-y: auto;
        position: sticky;
        top: 0;
        height: 100svh;
        z-index: 20;
    }

    .admin-content {
        flex: 1;
        background: transparent;
        display: flex;
        flex-direction: column;
        /* ini scroller utama agar position:sticky di halaman (analytics) bekerja */
        overflow: auto;
    }

    .admin-content-inner {
        padding: var(--adminPad);
        position: relative;
        min-height: 100svh;
        width: 100%;
    }

    .admin-content-inner > * {
        max-width: 100%;
    }

    /* Shared admin baseline: consistent, restrained surfaces and accessible controls. */
    .admin-content-inner :is(.card, .admin-product-hero, .bulk-hero, .bulk-card) {
        border-color: var(--adminBorder);
        border-radius: var(--adminRadius);
        box-shadow: var(--adminShadow);
    }

    .admin-content-inner :is(input:not([type="checkbox"]):not([type="radio"]), select, textarea, .form-control, .form-select) {
        border-color: #ded7ce;
        border-radius: 9px;
        color: var(--adminInk);
    }

    .admin-content-inner :is(input, select, textarea, button, a):focus-visible {
        outline: 2px solid var(--adminAccent);
        outline-offset: 2px;
    }

    .admin-content-inner :is(.btn-default-merah, .btn-bulk-primary) {
        background: var(--adminAccent);
        border-radius: 9px;
        box-shadow: none;
    }

    .admin-content-inner :is(table, .table) > :not(caption) > * > * {
        border-color: var(--adminBorder);
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-shell *, .admin-shell *::before, .admin-shell *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            transition-duration: .01ms !important;
        }
    }

    .admin-content-inner .page-wrap,
    .admin-content-inner .container-table,
    .admin-content-inner .container {
        background-clip: padding-box;
    }

    /* Toast & notif tetap di atas konten */
    .toast {
        z-index: 1050;
    }

    #notif {
        z-index: 1040;
    }

    /* Kompat untuk helper kelas kamu */
    .show-block-ke-hide {
        display: block;
    }

    .hide-ke-show-block {
        display: none;
    }

    @media (max-width: 992px) {

        /* Di HP: sidebar jadi collapsible (pakai partial navbarHpAdmin) */
        .show-block-ke-hide {
            display: none;
        }

        .hide-ke-show-block {
            display: block;
        }

        .admin-shell {
            flex-direction: column;
        }

        .admin-sidebar {
            position: static;
            width: 100%;
            min-width: 0;
            height: auto;
        }

        .admin-content {
            width: 100%;
            flex: 1;
        }

        .admin-content-inner {
            padding: 14px 12px 96px;
            min-height: auto;
        }
    }
    </style>
</head>

<body>
    <div class="toast start-50 translate-middle">
        <div class="toast-body">
            <p>Hello, world! This is a toast message.</p>
            <div class="mt-2 pt-2 border-top d-flex gap-1">
                <form method="post">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Ok</button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm" onclick="hapusToast()">Batal</button>
            </div>
        </div>
    </div>

    <div id="notif" class="notif">Isi notif</div>

    <script src="<?= base_url('js/bootstrap.bundle.min.js'); ?>"></script>
    <script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <div class="admin-shell">
        <!-- Sidebar (Desktop) -->
        <aside class="admin-sidebar show-block-ke-hide">
            <?= $this->include('admin/navbar'); ?>
        </aside>

        <!-- Navbar HP (Mobile) -->
        <div class="hide-ke-show-block">
            <?= $this->include('admin/navbarHpAdmin'); ?>
        </div>

        <!-- Konten -->
        <main class="admin-content">
            <div class="admin-content-inner">
                <?= $this->renderSection('content'); ?>
            </div>
        </main>

        <!-- Spacer kecil agar bottom nav HP tidak ketutup (sesuai file lama) -->
        <div class="hide-ke-show-block" style="height:100px; width:10px;"></div>
    </div>

    <script>
    (() => {
        const tokenName = document.querySelector('meta[name="csrf-token-name"]')?.content;
        const tokenHash = document.querySelector('meta[name="csrf-token-hash"]')?.content;
        if (!tokenName || !tokenHash) return;

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || (form.method || 'get').toLowerCase() !== 'post') return;
            if (!form.querySelector(`input[name="${CSS.escape(tokenName)}"]`)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = tokenName;
                input.value = tokenHash;
                form.appendChild(input);
            }
        }, true);

        const nativeFetch = window.fetch.bind(window);
        window.fetch = (input, init = {}) => {
            const request = input instanceof Request ? input : null;
            const method = String(init.method || request?.method || 'GET').toUpperCase();
            const target = new URL(request?.url || input, window.location.href);
            if (target.origin === window.location.origin && !['GET', 'HEAD', 'OPTIONS'].includes(method)) {
                const headers = new Headers(request?.headers || undefined);
                new Headers(init.headers || {}).forEach((value, name) => headers.set(name, value));
                if (!headers.has('X-CSRF-TOKEN')) headers.set('X-CSRF-TOKEN', tokenHash);
                init = { ...init, headers };
            }
            return nativeFetch(input, init);
        };
    })();

    function copytext(teksCopy, teksNotif) {
        navigator.clipboard.writeText(teksCopy);
        callNotif(teksNotif);
    }

    function callNotif(teks) {
        const notifElm = document.getElementById('notif')
        notifElm.innerHTML = teks;
        notifElm.classList.add('show');
        setTimeout(() => notifElm.classList.remove('show'), 2000);
    }

    const toastElm = document.querySelector(".toast");
    const toastTeksElm = document.querySelector(".toast p");
    const toastOkElm = document.querySelector(`.toast form`);
    const toastCloseElm = document.querySelector(`.toast button[type="button"]`);

    function triggerToast(text, linkAction) {
        toastElm.classList.add("show");
        toastTeksElm.innerHTML = text;
        toastOkElm.action = linkAction || "";
        if (!linkAction) {
            toastOkElm.classList.add('d-none');
            toastCloseElm.innerHTML = 'Ok';
        } else {
            toastOkElm.classList.remove('d-none');
            toastCloseElm.innerHTML = 'Batal';
        }
    }

    function hapusToast() {
        toastElm.classList.remove("show");
    }
    </script>
</body>

</html>
