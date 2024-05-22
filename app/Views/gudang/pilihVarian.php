<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body class="d-block d-flex flex-column justify-content-center align-items-center" style="width: 100vw; height: 100svh;">
    <h1>Pilih Varian</h1>
    <?php foreach ($produk['varian'] as $ind_v => $v) { ?>
        <a href="/gudang/actionpilihvarian/<?= $produk['id']; ?>/<?= $ind_v; ?>" class="btn-default"><?= $v['nama']; ?></a>
    <?php } ?>
</body>

</html>