<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">Produk Saya</h1>
            <p style="color: grey;"><?= count($produk); ?> Produk</p>
        </div>
        <div>
            <a href="/admin/addproduct" class="btn-default-merah">Tambah Produk</a>
        </div>
    </div>
    <div class="container-table show-block-ke-hide">
        <div class="header-table">
            <div style="flex: 1;">Gambar</div>
            <div style="flex: 2;">Nama dan ID</div>
            <div style="flex: 2;">Harga</div>
            <div style="flex: 2;">Status</div>
            <div style="flex: 1;">Action</div>
        </div>
        <?php foreach ($produk as $ind_p => $p) { ?>
            <div class="isi-table">
                <div style="flex: 1;" onclick="pergiKeProduct('<?= $p['id']; ?>')"><img style="width: 50px; height: 50px; object-fit:cover;" id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt=""></div>
                <div style="flex: 2;" class="d-flex flex-column align-items-start justify-content-center" onclick="pergiKeProduct('<?= $p['id']; ?>')">
                    <p class="fw-bold m-0" style="font-size: 20px;"><?= $p['nama']; ?></p>
                    <p class="m-0" style="color: grey; font-size: 13px;">#<?= $p['id']; ?></p>
                </div>
                <div style="flex: 2;">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                <div style="flex: 2;">
                    <div class="checkbox-apple">
                        <input onchange="ubahStatus('<?= $p['id']; ?>')" class="yep" id="check-apple<?= $ind_p ?>" type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                        <label for="check-apple<?= $ind_p ?>"></label>
                    </div>
                </div>
                <div style="flex: 1;">
                    <a class="btn" href="/admin/editproduct/<?= $p['id']; ?>"><i class="material-icons">edit</i></a>
                    <a class="btn" href="/admin/deleteproduct/<?= $p['id']; ?>"><i class="material-icons" style="color: var(--merah);">delete</i></a>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="hide-ke-show-block">
        <div class="container-table" style="width: 500px;">
            <div class="header-table">
                <div style="flex: 1;">Gambar</div>
                <div style="flex: 2;">Nama dan ID</div>
                <div style="flex: 2;">Harga</div>
                <div style="flex: 2;">Status</div>
                <div style="flex: 1;">Action</div>
            </div>
            <?php foreach ($produk as $ind_p => $p) { ?>
                <div class="isi-table">
                    <div style="flex: 1;" onclick="pergiKeProduct('<?= $p['id']; ?>')"><img style="width: 50px; height: 50px; object-fit:cover;" id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt=""></div>
                    <div style="flex: 2;" class="d-flex flex-column align-items-start justify-content-center" onclick="pergiKeProduct('<?= $p['id']; ?>')">
                        <p class="fw-bold m-0" style="font-size: 20px;"><?= $p['nama']; ?></p>
                        <p class="m-0" style="color: grey; font-size: 13px;">#<?= $p['id']; ?></p>
                    </div>
                    <div style="flex: 2;">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                    <div style="flex: 2;">
                        <div class="checkbox-apple">
                            <input onchange="ubahStatus('<?= $p['id']; ?>')" class="yep" id="check-apple<?= $ind_p ?>" type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                            <label for="check-apple<?= $ind_p ?>"></label>
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <a class="btn" href="/admin/editproduct/<?= $p['id']; ?>"><i class="material-icons">edit</i></a>
                        <a class="btn" href="/admin/deleteproduct/<?= $p['id']; ?>"><i class="material-icons" style="color: var(--merah);">delete</i></a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    function ubahStatus(id_produk) {
        console.log(id_produk)
        async function fetchUpdate() {
            const updateStatus = await fetch('/admin/activeproduct/' + id_produk);
        }
        fetchUpdate();
    }

    function pergiKeProduct(id_produk) {
        window.location.href = "/product/" + id_produk
    }
</script>
<?= $this->endSection(); ?>