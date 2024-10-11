<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<?php
$koleksiterpilih = '';
if (isset($_GET['koleksi'])) {
    if ($_GET['koleksi'] != '') {
        $koleksiselect = $_GET['koleksi'];
        $produkLama = $produk;
        $produk = [];
        foreach ($produkLama as $p) {
            if (strtolower(str_replace("-", " ", $koleksiselect)) == strtolower($p['kategori'])) {
                array_push($produk, $p);
            }
        }
        $koleksiterpilih = $koleksiselect;
    }
}

$hitungPag = ceil(count($produk) / 10);
$pag = 1;
if (isset($_GET['pag'])) $pag = (int)$_GET['pag'];
$produkLama = array_slice($produk, ($pag - 1) * 10);
$produk = [];
for ($i = 0; $i < 10; $i++) {
    if (isset($produkLama[$i]))
        array_push($produk, $produkLama[$i]);
}
?>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">Produk Saya</h1>
            <p style="color: black;"><?= count($produk); ?> Produk</p>
        </div>
        <div>
            <a href="/admin/addproduct" class="btn-default-merah">Tambah Produk</a>
        </div>
    </div>
    <div>
        <!-- Selected adalah atribud yang altif -->
        <select name="" class="form-select w-50" onchange="gantikoleksi(event)">
            <option value="semua" <?= $koleksiterpilih == '' ? 'selected' : '' ?>>Semua</option>
            <?php foreach ($koleksi as $k) { ?>
            <option value="<?= str_replace(' ', '-', $k['nama']) ?>"
                <?= $koleksiterpilih == str_replace(' ', '-', $k['nama']) ? 'selected' : '' ?>>
                <?= $k['nama'] ?></option>
            <?php } ?>
        </select>
    </div>
    <hr>
    <div class="container-table show-block-ke-hide">
        <div class="header-table">
            <div style="flex: 0.7; color:black;"><strong>Gambar</strong></div>
            <div style="flex: 2; color:black;"><strong>Nama dan ID</strong></div>
            <div style="flex: 1; color:black;"><strong>Harga</strong></div>
            <div style="flex: 1.5; color:black;"><strong>Stok</strong></div>
            <div style="flex: 1; color:black;"><strong>Status</strong></div>
            <div style="flex: 1; color:black;"><strong>Action</strong></div>
        </div>
        <?php foreach ($produk as $ind_p => $p) { ?> <div class="isi-table">
            <div style="flex: 0.7;" onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')"><img
                    style="width: 70px; height: 70px; object-fit:cover; border-radius:8px;" id="img<?= $ind_p ?>"
                    src="/viewpic/<?= $p['id']; ?>" alt=""></div>
            <div style="flex: 2;" class="d-flex flex-column align-items-start justify-content-center"
                onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                <p class="m-0"><?= ucfirst($p['kategori']); ?></p>
                <p class="fw-bold m-0" style="font-size: 16px;"><?= strtoupper($p['nama']); ?></p>
                <p class="m-0" style="color: black; font-size: 13px;">#<?= $p['id']; ?></p>
            </div>
            <div style="flex: 1;">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
            <div style="flex: 1.5; font-size: 14px;"><?= strtolower($p['allstok']) ?></div>
            <div style="flex: 1;">
                <div class="checkbox-apple">
                    <input onchange="ubahStatus('<?= $p['id']; ?>')" class="yep" id="check-apple<?= $ind_p ?>"
                        type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                    <label for="check-apple<?= $ind_p ?>"></label>
                </div>
            </div>
            <div style="flex: 1;">
                <a class="btn" href="/admin/editproduct/<?= $p['id']; ?>"><i class="material-icons">edit</i></a>
                <form action="/admin/deleteproduct/<?= $p['id']; ?>" method="post">
                    <button class="btn-default-merah d-flex justify-content-center align-items-center">
                        <p class="m-0"><i class="material-icons">delete</i></p>
                    </button>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="hide-ke-show-block" style="overflow:auto;">
        <div class="container-table" style="width: 500px;">
            <div class="header-table">
                <div style="flex: 1; color:black;"><strong>Gambar</strong></div>
                <div style="flex: 1; color:black;"><strong>Nama dan ID</strong></div>
                <div style="flex: 1; color:black;"><strong>Harga</strong></div>
                <div style="flex: 1; color:black;"><strong>Stok</strong></div>
                <div style="flex: 1; color:black;"><strong>Status</strong></div>
                <div style="flex: 1; color:black;"><strong>Action</strong></div>
            </div>
            <?php foreach ($produk as $ind_p => $p) { ?>
            <div class="isi-table">
                <div style="flex: 01; border-radius:8px;"
                    onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')"><img
                        style="width: 50px; height: 50px; object-fit:cover;" id="img<?= $ind_p ?>"
                        src="/viewpic/<?= $p['id']; ?>" alt=""></div>
                <div style="flex: 1;" class="d-flex flex-column align-items-start justify-content-center"
                    onclick="pergiKeProduct('<?= str_replace(' ', '-', $p['nama']); ?>')">
                    <p class="m-0"><?= ucfirst($p['kategori']); ?></p>
                    <p class="fw-bold m-0" style="font-size:12px;"><?= ucwords($p['nama']); ?></p>
                    <p class="m-0" style="color: grey; font-size:12px;">#<?= $p['id']; ?></p>
                </div>
                <div style="flex: 1;">
                    <p class="m-0" style=" font-size:12px;">Rp
                        <?= number_format(strtolower($p['harga']), 0, ',', '.'); ?></p>
                </div>
                <div style="flex: 1;" class="m-0" style="color: grey; font-size:12px;"><?= strtolower($p['allstok']) ?>
                </div>
                <div style="flex: 1;">
                    <div class="checkbox-apple">
                        <input onchange="ubahStatus('<?= $p['id']; ?>')" class="yep" id="check-apple<?= $ind_p ?>"
                            type="checkbox" <?= $p['active'] ? 'checked' : ''; ?>>
                        <label for="check-apple<?= $ind_p ?>"></label>
                    </div>
                </div>
                <div style="flex: 1;">
                    <a class="btn" href="/admin/editproduct/<?= $p['id']; ?>"><i class="material-icons">edit</i></a>
                    <a class="btn" href="/admin/deleteproduct/<?= $p['id']; ?>"><i class="material-icons"
                            style="color: var(--merah);">delete</i></a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="container-pag">
        <?php if ($pag > 1) { ?>
        <a class="item-pag"
            href="/admin/product?pag=<?= $pag - 1; ?><?= isset($_GET['koleksi']) ? '&koleksi=' . $_GET['koleksi'] : ''; ?>"><i
                class="material-icons">chevron_left</i></a>
        <?php } ?>
        <?php for ($i = 0; $i < $hitungPag; $i++) { ?>
        <a class="item-pag <?= $pag == ($i + 1) ? 'active' : ''; ?>"
            href="/admin/product?pag=<?= $i + 1; ?><?= isset($_GET['koleksi']) ? '&koleksi=' . $_GET['koleksi'] : ''; ?>"><?= $i + 1; ?></a>
        <?php } ?>
        <?php if ($pag < $hitungPag) { ?>
        <a class="item-pag"
            href="/admin/product?pag=<?= $pag + 1; ?><?= isset($_GET['koleksi']) ? '&koleksi=' . $_GET['koleksi'] : ''; ?>"><i
                class="material-icons">chevron_right</i></a>
        <?php } ?>
    </div>
</div>
<script>
function gantikoleksi(e) {
    console.log(e.target.value);
    if (e.target.value == 'semua') {
        window.location.href = window.location.pathname;
    } else {
        window.location.href = window.location.pathname + '?koleksi=' + e.target.value;
    }
}

function ubahStatus(id_produk) {
    console.log(id_produk)
    async function fetchUpdate() {
        const updateStatus = await fetch('/admin/activeproduct/' + id_produk);
    }
    fetchUpdate();
}

function pergiKeProduct(nama_produk) {
    window.location.href = "/product/" + nama_produk
}
</script>
<?= $this->endSection(); ?>