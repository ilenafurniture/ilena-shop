<div class="header-table border-buttom border-dark">
    <div style="flex: 1;">No</div>
    <div style="flex: 2;">ID Pesanan</div>
    <div style="flex: 2;">Tanggal</div>
    <div style="flex: 4;">Nama dan varian</div>
    <div style="flex: 2;">ID Barang</div>
    <div style="flex: 1;">Stok Barang</div>
    <div style="flex: 3;">Target Selesai</div>
    <div style="flex: 2;">Action</div>
</div>

<?php
$no = 1;
if (count($pesanan) > 0) {
    foreach ($pesanan as $p) {
        if (!$p['packed']) {
?>
            <div class="isi-table">
                <div style="flex: 1;"><?= $no; ?></div>
                <div style="flex: 2;"><?= $p['id_pesanan']; ?></div>
                <div style="flex: 2;"><?= $p['tanggal']; ?></div>
                <div style="flex: 4;"><?= strtoupper($p['detail_barang']['kategori']); ?> <?= $p['nama']; ?></div>
                <div style="flex: 2;"><?= $p['id_barang']; ?></div>
                <div style="flex: 1;"><?= $p['stok']; ?></div>
                <div style="flex: 3;"><?= $p['target_selesai']; ?></div>
                <div style="flex: 2;">
                    <button class="btn-default" onclick="openScan('<?= $p['id_barang'] ?>','<?= $p['nama'] ?>')">Scan</button>
                </div>
            </div>
    <?php
            $no++;
        }
    }
} else { ?>
    <div class="isi-table">
        <div style="flex: 1;">Tidak ada pesanan</div>
    </div>
<?php } ?>