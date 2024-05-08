<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="mb-4">
        <h1 class="teks-sedang">Pesanan Pelanggan</h1>
        <p style="color: grey;"><?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pesanan</p>
    </div>
    <div class="container-table">
        <div class="header-table border-buttom border-dark">
            <div>ID Pesanan</div>
            <div>Tanggal</div>
            <div>Penerima</div>
            <div>Harga</div>
            <div>Status</div>
        </div>
        <?php foreach ($pesanan as $ind_p => $p) { ?>
            <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)">
                <div><?= $p['id_midtrans']; ?></div>
                <?php
                $transactionTime = strtotime($p['data_mid']['transaction_time']);
                $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                ?>
                <div><?= $tgl; ?></div>
                <div><?= $p['nama']; ?></div>
                <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                <div><?= $p['status']; ?></div>
            </div>
        <?php } ?>
    </div>
</div>
<?= $this->endSection(); ?>