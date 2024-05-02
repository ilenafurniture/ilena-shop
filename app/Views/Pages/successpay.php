<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h1 class="teks-besar text-center mb-3 mt-4">Pembayaran Berhasil</h1>
<div class="d-flex justify-content-center mb-3">
    <p style="width: calc(100% - 500px)" class="my-auto text-center">Pesanan akan segera kami proses. Simpan URL halaman ini untuk melihat status pesanan. Atau dapat login sebagai member kami untuk bisa melihat seluruh pesanan Anda.</p>
</div>
<div class="py-1 text-light w-100 text-center" style="background-color: var(--dark); letter-spacing: -1px;">ID
    Pesanan :
    <b><?= $pemesanan['id_midtrans']; ?></b>
</div>
<div class="konten mx-auto" style="width: calc(100% - 500px)">
    <div class="baris-ke-kolom justify-content-between W-100 mb-3 border-bottom pb-3">
        <div>
            <p class="m-0">Jumlah Tagihan</p>
            <div class="d-flex align-items-end gap-2">
                <h3 class="m-0" style="font-size: 40px; letter-spacing: -3px; font-weight:600;">Rp <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></h3>
                <button class="btn-teks-aja hitam mb-1" onclick="copytext('<?= (int)$dataMid['gross_amount']; ?>')"><i class="material-icons">content_copy</i></button>
            </div>
        </div>
        <div class="d-flex flex-column align-items-end">
            <p class="mb-1">Metode Pembayaran</p>
            <img class="mb-2" src="/img/pembayaran/<?= $bank; ?>.png" alt="">
        </div>
    </div>
    <div class="baris-ke-kolom justify-content-between W-100 mb-3 pb-3">
        <div style="flex: 1">
            <p class="mb-2">Ekspedisi</p>
            <div class="d-flex justify-content-between gap-2">
                <img src="/img/kurir/<?= $kurir['nama'] ?>.png" alt="" style="width: 100px; object-fit:contain">
                <div style="flex: 1;">
                    <p class="mb-0 fw-bold" style="letter-spacing: -1px; font-size: 20px;"><?= strtoupper($kurir['nama']) ?> <?= $kurir['deskripsi'] ?></p>
                    <?php if ($kurir['estimasi']) { ?>
                        <p class="m-0" style="letter-spacing: -1px;">Estimasi pengiriman <?= $kurir['estimasi'] ?> Hari</p>
                    <?php } ?>
                </div>
            </div>
            <p class="mb-0 mt-3">Status Pesanan</p>
            <!-- <p class="m-0 fw-bold">Proses</p> -->
            <span class="badge rounded-pill <?php
                                            switch ($pemesanan['status']) {
                                                case 'Menunggu Pembayaran':
                                                    echo "text-bg-primary";
                                                    break;
                                                case 'Proses':
                                                    echo "text-bg-warning";
                                                    break;
                                                case 'Dikirim':
                                                    echo "text-bg-info";
                                                    break;
                                                case 'Selesai':
                                                    echo "text-bg-success";
                                                    break;
                                                case 'Dibatalkan':
                                                    echo "text-bg-danger";
                                                    break;
                                                case 'Gagal':
                                                    echo "text-bg-danger";
                                                    break;
                                                default:
                                                    echo "text-bg-dark";
                                                    break;
                                            }
                                            ?>"><?= ucfirst($pemesanan['status']); ?></span>
        </div>
        <div style="flex: 1">
            <div id="item">
                <div class="d-flex mb-1 border-bottom pb-1">
                    <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Nama</p>
                    <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Jumlah</p>
                    <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;">Harga</p>
                </div>
                <?php foreach ($items as $i) { ?>
                    <div class="d-flex py-1">
                        <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;"><?= $i['name']; ?></p>
                        <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;"><?= $i['quantity']; ?></p>
                        <p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp <?= number_format($i['price'], 0, ',', '.'); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if (session()->get('isLogin')) { ?>
        <div class="w-100 d-flex justify-content-center mt-4">
            <a href="/order" class="btn-default-merah">Pesanan Saya</a>
        </div>
    <?php } ?>
</div>
<?= $this->endSection(); ?>