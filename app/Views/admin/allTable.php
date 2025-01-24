<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <a id="btnDownload">EXCEL</a>
    <table id="toExcel" class="uitable">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Description</th>
                <th>Availability</th>
                <th>Link</th>
                <th>Image Link</th>
                <th>Price</th>
                <th>Color</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produk as $p) { ?>
                <tr>
                    <td><?= $p['id']; ?></td>
                    <td><?= ucwords($p['nama']); ?></td>
                    <td><?= $p['deskripsi_nonhtml']; ?></td>
                    <td><?= $p['stok_total'] > 0 ? 'in_stock' : 'out_of_stock'; ?></td>
                    <td>https://ilenafurniture.com/product/<?= str_replace(' ', '-', $p['nama']); ?></td>
                    <td>https://ilenafurniture.com/viewpic/<?= $p['id']; ?></td>
                    <td><?= $p['harga']; ?> IDR</td>
                    <td><?= $p['warna']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script>
        var dataType = "application/vnd.ms-excel";
        var tableSelect = document.getElementById("toExcel");
        var btnDownload = document.getElementById("btnDownload");
        var tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");

        // Specify file name
        const filename = "Tabel Produk Ilena.xls";

        // if (navigator.msSaveOrOpenBlob) {
        //     var blob = new Blob(["\ufeff", tableHTML], {
        //         type: dataType,
        //     });
        //     navigator.msSaveOrOpenBlob(blob, filename);
        // } else {
        btnDownload.href = "data:" + dataType + ", " + tableHTML;
        btnDownload.download = filename;
        // }
    </script>
</body>

</html>