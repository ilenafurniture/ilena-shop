<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <title><?= $title; ?> | Ilena Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .invoice-header {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .invoice-details {
        margin-bottom: 20px;
    }

    .invoice-details table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-details table,
    .invoice-details th,
    .invoice-details td {
        border: 1px solid black;
    }

    .invoice-details th,
    .invoice-details td {
        padding: 8px;
        text-align: left;
    }

    .total,
    .terbilang {
        margin-top: 20px;
        font-weight: bold;
    }

    .footer {
        margin-top: 40px;
        text-align: center;
    }
    </style>
</head>

<body>

    <div class="invoice-header">
        <p>INVOICE</p>
        <p>Nomor: 00001/INV/CBM/04/2025</p>
        <p>Tanggal: 26 April 2025</p>
    </div>

    <div class="invoice-details">
        <p>Kepada Yth.</p>
        <p>Nama: ...........................................</p>
        <p>Alamat: .............................................</p>
        <p>NPWP: ..............................................</p>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Barang</th>
                    <th>Kuantitas</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>King bed Socoplate 2140 x 1950 x 860 mm putih</td>
                    <td>1</td>
                    <td>Rp 2.846.250</td>
                    <td>Rp 2.846.250</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="total">
        <p>JUMLAH: Rp 2.846.250</p>
        <p>POTONGAN: ---- %</p>
        <p>UANG MUKA YANG DITERIMA: ----</p>
        <p>TOTAL INVOICE: Rp 2.846.250</p>
    </div>

    <div class="terbilang">
        <p>Terbilang: Dua juta delapan ratus empat puluh enam ribu dua ratus lima puluh rupiah</p>
    </div>

    <div class="footer">
        <p>PO: ..................</p>
        <p>Surat Jalan: ..................</p>
        <p>Bagian Keuangan: Puspita Aprilia Damayanti</p>
        <p>CV. CATUR BHAKTI MANDIRI</p>
        <p>Kawasan Industri BSB, A 3A, 5-6 Jatibarang, Mijen Semarang</p>
        <p>Pembayaran mohon dapat ditransfer ke rekening: BCA A/C 8715898787 an CATUR BHAKTI MANDIRI</p>
    </div>

</body>

</html>