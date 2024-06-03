<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1">
            <div>
                <h6 class="text-center mb-2">Kartu Stok</h6>
            </div>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Keterangan</div>
                    <div style="flex: 2;">Debit</div>
                    <div style="flex: 2;">Kredit</div>
                    <div style="flex: 2;">Saldo</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 1;">1</div>
                    <div style="flex: 2;">31 Mei 2024</div>
                    <div style="flex: 3;">ISI_NYA ID PESANAN</div>
                    <div style="flex: 2;">6</div>
                    <div style="flex: 2;">4</div>
                    <div style="flex: 2;">6</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 1;">2</div>
                    <div style="flex: 2;">3 Juni 2024</div>
                    <div style="flex: 3;">ISI_NYA ID PESANAN</div>
                    <div style="flex: 2;">9</div>
                    <div style="flex: 2;">5</div>
                    <div style="flex: 2;">4</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>