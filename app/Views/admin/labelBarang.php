<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title><?= $title; ?> | Ilena Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <!-- <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" /> -->
    <!-- icon google -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link rel="stylesheet" href="/css/style_pdf.css" />
</head>

<body>
    <div class="print">
        <h1 class="mb-3">PENGIRIM</h1>
        <h3>ILENA FURNITURE</h3>
        <h3>08112938158</h3>
        <h3 class="mb-5">Jalan Lingkar Taman Industri, Jatibarang, Mijen, Kota Semarang, Jawa Tengah</h3>
        <h1 class="mb-3">PENERIMA</h1>
        <h3><?= $pemesanan['nama']; ?></h3>
        <?php foreach ($pemesanan['items'] as $item) {
            if ($item['id'] != 'Biaya Admin' && $item['id'] != 'Biaya Ongkir' && $item['id'] != 'Voucher') { ?>
                <h3><?= $item['name']; ?></h3>
        <?php }
        } ?>
        <h3><?= $pemesanan['nohp']; ?></h3>
        <h3><?= $pemesanan['alamat']; ?></h3>
    </div>
    <script>
        window.print()
    </script>
</body>

</html>