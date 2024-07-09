<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | I L E N A</title>
    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-aGWfdxs2btRH4xSd"></script>
    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>

<body>
    <script src="<?= base_url('js/bootstrap.bundle.min.js'); ?>">
    </script>
    <?= $this->include('layout/navbar'); ?>
    <?= $this->include('layout/navbarHp'); ?>
    <div style="flex: 1">
        <?= $this->renderSection('content'); ?>
    </div>
    <?= $this->include('layout/footer'); ?>
    <script>
        function copytext(teks) {
            navigator.clipboard.writeText(teks);
        }
    </script>
</body>

</html>