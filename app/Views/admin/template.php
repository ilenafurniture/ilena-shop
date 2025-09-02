<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | I L E N A</title>

    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css?v=23-04-2025'); ?>">
    <link rel="icon" href="<?= base_url('img/logo/N.png?v=11-03-2025'); ?>" type="image/png">

    <!-- Layout guard supaya konten & sticky element ga tabrakan -->
    <style>
    :root {
        --adminSidebarW: 300px;
        /* lebar sidebar admin */
        --adminPad: 16px;
        /* spacing dalam konten */
    }

    html,
    body {
        height: 100%;
    }

    body {
        background: #f5f6f8;
    }

    /* Shell responsive: sidebar kiri + konten kanan */
    .admin-shell {
        display: flex;
        min-height: 100svh;
        width: 100%;
    }

    .admin-sidebar {
        width: var(--adminSidebarW);
        background: #fff;
        border-right: 1px solid #eee;
        overflow-y: auto;
    }

    .admin-content {
        flex: 1;
        background: whitesmoke;
        display: flex;
        flex-direction: column;
        /* ini scroller utama agar position:sticky di halaman (analytics) bekerja */
        overflow: auto;
    }

    .admin-content-inner {
        padding: var(--adminPad);
        position: relative;
        min-height: 500px;
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

        .admin-content {
            width: 100%;
            flex: 1;
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