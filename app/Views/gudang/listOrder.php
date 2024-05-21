<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1; border-right:2px solid black; ">
            <h6 class="text-center">Pesanan belum diproses</h6>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">ID Pesanan</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Nama dan varian</div>
                    <div style="flex: 3;">Kode</div>
                    <div style="flex: 2;">Jumlah</div>
                </div>
                <?php
                $no = 1;
                foreach ($pesanan as $p) {
                    foreach ($p['items'] as $p_item) {
                        if ($p_item['name'] != "Biaya Ongkir" && $p_item['name'] != "Biaya Admin") {
                ?>
                            <div class="isi-table">
                                <div style="flex: 1;"><?= $no; ?></div>
                                <div style="flex: 2;"><?= $p['id_midtrans']; ?></div>
                                <div style="flex: 2;"><?= $p['data_mid']['transaction_time']; ?></div>
                                <div style="flex: 3;"><?= $p_item['name']; ?></div>
                                <div style="flex: 3;"><?= $p_item['id']; ?></div>
                                <div style="flex: 2;"><?= $p_item['quantity']; ?></div>
                            </div>
                <?php
                            $no++;
                        }
                    }
                } ?>
            </div>
        </div>

        <div style="flex:1">
            <h6 class="text-center">Pesanan Telah diproses</h6>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Nama dan varian</div>
                    <div style="flex: 3;">Kode</div>
                    <div style="flex: 2;">Jumlah</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 1;">1</div>
                    <div style="flex: 2;">19/05/2024</div>
                    <div style="flex: 3;">Lemari V - Macha</div>
                    <div style="flex: 3;">D647</div>
                    <div style="flex: 2;">1</div>
                </div>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>