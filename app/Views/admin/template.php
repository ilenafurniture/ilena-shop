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

    <div class="baris-ke-kolom gap-0 w-100" style="height: 100svh;">
        <div class="show-block-ke-hide bg-white" style="height: 100%; overflow-y: auto;">
            <?= $this->include('admin/navbar'); ?>
        </div>
        <div class="hide-ke-show-block">
            <?= $this->include('admin/navbarHpAdmin'); ?>
        </div>
        <div style="background-color: whitesmoke; overflow:scroll;" class="d-flex flex-column flex-grow-1">
            <?= $this->renderSection('content'); ?>
        </div>
        <div class="hide-ke-show-block" style="height:100px; width:10px; background-color: red; "></div>
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
            setTimeout(() => {
                notifElm.classList.remove('show');
            }, 2000);
        }

        const toastElm = document.querySelector(".toast")
        const toastTeksElm = document.querySelector(".toast p")
        const toastOkElm = document.querySelector(`.toast form`)
        const toastCloseElm = document.querySelector(`.toast button[type="button"]`)

        function triggerToast(text, linkAction) {
            toastElm.classList.add("show")
            toastTeksElm.innerHTML = text
            toastOkElm.action = linkAction;
            if (!linkAction) {
                toastOkElm.classList.add('d-none');
                toastCloseElm.innerHTML = 'Ok';
            } else {
                toastOkElm.classList.remove('d-none');
                toastCloseElm.innerHTML = 'Batal';
            }
        }

        function hapusToast() {
            toastElm.classList.remove("show")
        }
    </script>
</body>

</html>