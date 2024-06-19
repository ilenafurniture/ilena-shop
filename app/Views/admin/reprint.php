<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">List Konfirmasi Ulang</h1>
        </div>
    </div>
    <div class="container-table mt-4">
        <div class="header-table">
            <div style="flex: 1;">No</div>
            <div style="flex: 2;">tanggal</div>
            <div style="flex: 2;">Atas nama</div>
            <div style="flex: 2;">ID Pesanan</div>
            <div style="flex: 2;">Kendala</div>
            <div style="flex: 2;">Action</div>
        </div>
        <?php foreach ($ajukan as $ind_a => $a) { ?>
            <div class="isi-table">
                <div style="flex: 1;"><?= $ind_a + 1; ?></div>
                <div style="flex: 2;"><?= $a['tanggal']; ?></div>
                <div style="flex: 2;"><?= $a['atas_nama']; ?></div>
                <div style="flex: 2;"><?= $a['id_midtrans']; ?></div>
                <div style="flex: 2;"><?= $a['kendala']; ?></div>
                <div style="flex: 2;" class="d-flex align-items-center gap-1">
                    <a class="btn-default" href="/admin/accreprint/<?= $a['id_midtrans']; ?>"><i class=" material-icons">check</i></a>
                    <a class="btn-default-merah" href="/admin/denyreprint/<?= $a['id_midtrans']; ?>"><i class=" material-icons">close</i></a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?= $this->endSection(); ?>