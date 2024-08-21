<!DOCTYPE html>
<html lang="en" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | I L E N A</title>
    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php if ($title == 'Pembayaran') { ?>
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="SB-Mid-client-aGWfdxs2btRH4xSd"></script>
        <script id="midtrans-script" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js"
            data-environment="<?= $emailUji ? 'sandbox' : 'production'; ?>"
            data-client-key="<?= $emailUji ? 'SB-Mid-client-aGWfdxs2btRH4xSd' : ''; ?>" type="text/javascript"></script>
    <?php } ?>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
    <link rel="icon" href="<?= base_url('logo icon.png'); ?>" type="image/png">
</head>

<body>
    <script src="<?= base_url('js/bootstrap.bundle.min.js'); ?>">
    </script>
    <div
        class="container-melayang d-flex gap-2 align-items-end <?= isset($geser_container_melayang) ? 'geser' : ''; ?>">
        <div id="container-greeting-card" class="d-none">
            <div style="background-color: white; border-radius: 1em; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);"
                class="p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <p class="m-0">Hai!👋</p>
                    <p class="m-0" style="cursor: pointer;" onclick="closeGreetingCard()">x</p>
                </div>
                <p class="m-0">Kalau kamu butuh bantuan,<br>hubungi kami via WhatsApp ya!</p>
            </div>
            <div style="height: 30px;"></div>
        </div>
        <div class="d-flex flex-column gap-2">
            <!-- <a class="btn-circle" href="/form"><i class="material-icons">insert_comment</i></a> -->
            <a class="btn-circle hitam" id="btn-wa"
                href="https://api.whatsapp.com/send?phone=628112938158&text=Hi%2C%20CS%20Ilena%21%0D%0ASaya%20tertarik%20untuk%20beli%3A%0D%0ANama%20produk%3A%0D%0AVarian%20%3A%0D%0AJumlah%20%3A%0D%0ABantu%20saya%20untuk%20melakukan%20proses%20checkout%20nya%21">
                <i class="material-icons text-light">phone</i>
            </a>
        </div>
    </div>


    <?= $this->include('layout/navbar'); ?>
    <?= $this->include('layout/navbarHp'); ?>
    <div style="flex: 1;">
        <?= $this->renderSection('content'); ?>
    </div>
    <?= $this->include('layout/footer'); ?>
    <script>
        function copytext(teks) {
            navigator.clipboard.writeText(teks);
        }

        function pergiKeProduk(namaProduk) {
            window.location.href = '/product/' + namaProduk
        }
    </script>
</body>

</html>