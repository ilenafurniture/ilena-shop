<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> | I L E N A</title>
    <link href="<?= base_url('css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="<?= base_url('css/style.css'); ?>">
</head>

<body>
    <script src="<?= base_url('js/bootstrap.bundle.min.js'); ?>">
    </script>
    <div class="d-flex w-100" style="height: 100svh;">
        <?= $this->include('market/layout/navbar'); ?>
        <div style="background-color: whitesmoke; overflow:scroll;" class="d-flex flex-column flex-grow-1">
            <?= $this->renderSection('content'); ?>
        </div>
    </div>
</body>

</html>