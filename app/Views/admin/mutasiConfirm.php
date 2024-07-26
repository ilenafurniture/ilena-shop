<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">Konfirmasi Mutasi</h1>
        </div>
    </div>
    <?php if ($msg) { ?>
        <div class="pemberitahuan my-1" role="alert">
            <?= $msg; ?>
        </div>
    <?php } ?>
    <div class="container-table mt-4 show-block-ke-hide">
        <div class="header-table">
            <div style="flex: 1;">ID Produk</div>
            <div style="flex: 1;">Nama Produk</div>
            <div style="flex: 1;">Varian</div>
            <div style="flex: 1;">Stok</div>
            <div style="flex: 1;">Jumlah</div>
            <div style="flex: 2;">Alasan</div>
            <div style="flex: 1;">Action</div>
        </div>

        <?php foreach ($mutasi as $ind_m => $m) { ?>
            <div class="isi-table">
                <div style="flex: 1;"><?= $m['id_barang'] ?></div>
                <div style="flex: 1;"><?= $m['detail'] ? $m['detail']['nama'] : 'Barang tidak ditemukan' ?></div>
                <div style="flex: 1;"><?= $m['varian'] ?></div>
                <div style="flex: 1;"><?= $m['stok'] ?></div>
                <div style="flex: 1;"><?= $m['debit'] ? "+" . $m['debit'] : "-" . $m['kredit']; ?></div>
                <div style="flex: 2;"><?= $m['alasan'] ?></div>
                <div style="flex: 1;" class="gap-2">
                    <a class="btn-default" href="/admin/accmutasi/<?= $m['id']; ?>">Konfim</a>
                    <a class="btn-default-merah" href="/admin/denymutasi/<?= $m['id']; ?>">Tolak</a>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="container-table mt-4 hide-ke-show-block" style="width: 500px;">
        <div class="header-table">
            <div style="flex: 1;">ID Produk</div>
            <div style="flex: 1;">Nama Produk</div>
            <div style="flex: 1;">Varian</div>
            <div style="flex: 1;">Stok</div>
            <div style="flex: 1;">Jumlah</div>
            <div style="flex: 2;">Alasan</div>
            <div style="flex: 1;">Action</div>
        </div>

        <?php foreach ($mutasi as $ind_m => $m) { ?>
            <div class="isi-table">
                <div style="flex: 1;"><?= $m['id_barang'] ?></div>
                <div style="flex: 1;"><?= $m['detail'] ? $m['detail']['nama'] : 'Barang tidak ditemukan' ?></div>
                <div style="flex: 1;"><?= $m['varian'] ?></div>
                <div style="flex: 1;"><?= $m['stok'] ?></div>
                <div style="flex: 1;"><?= $m['debit'] ? "+" . $m['debit'] : "-" . $m['kredit']; ?></div>
                <div style="flex: 2;"><?= $m['alasan'] ?></div>
                <div style="flex: 1;" class="gap-2">
                    <a class="btn-default" href="/admin/accmutasi/<?= $m['id']; ?>">Konfim</a>
                    <a class="btn-default-merah" href="/admin/denymutasi/<?= $m['id']; ?>">Tolak</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?= $this->endSection(); ?>