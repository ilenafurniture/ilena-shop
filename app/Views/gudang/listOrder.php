<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1;">
            <h6 class="text-center mb-2">Pesanan belum diproses</h6>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Pesanan</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Nama dan varian</div>
                    <div style="flex: 3;">ID Barang</div>
                    <div style="flex: 1;">Jumlah</div>
                </div>
                <?php
                $no = 1;
                foreach ($pesanan as $p) {
                    if (!$p['packed']) {
                ?>
                        <div class="isi-table">
                            <div style="flex: 1;"><?= $no; ?></div>
                            <div style="flex: 2;"><?= $p['id_pesanan']; ?></div>
                            <div style="flex: 2;"><?= $p['tanggal']; ?></div>
                            <div style="flex: 3;"><?= $p['nama']; ?></div>
                            <div style="flex: 3;"><?= $p['id_barang']; ?></div>
                            <div style="flex: 1;"><?= $p['jumlah']; ?></div>
                        </div>
                <?php
                        $no++;
                    }
                } ?>
            </div>
        </div>

        <div style="flex:1">
            <h6 class="text-center mb-2">Pesanan Telah diproses</h6>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Pesanan</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Nama dan varian</div>
                    <div style="flex: 3;">ID Barang</div>
                    <div style="flex: 1;">Jumlah</div>
                </div>
                <?php
                $no = 1;
                foreach ($pesanan as $p) {
                    if ($p['packed']) {
                ?>
                        <div class="isi-table">
                            <div style="flex: 1;"><?= $no; ?></div>
                            <div style="flex: 2;"><?= $p['id_pesanan']; ?></div>
                            <div style="flex: 2;"><?= $p['tanggal']; ?></div>
                            <div style="flex: 3;"><?= $p['nama']; ?></div>
                            <div style="flex: 3;"><?= $p['id_barang']; ?></div>
                            <div style="flex: 1;"><?= $p['jumlah']; ?></div>
                        </div>
                <?php
                        $no++;
                    }
                } ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>