<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container d-flex justify-content-center align-items-center ilena-cart-page <?= empty($keranjang) ? 'is-empty' : ''; ?>">
    <?php if ($cartMessage = session()->getFlashdata('msg')): ?>
        <p class="pemberitahuan" role="alert"><?= esc($cartMessage); ?></p>
    <?php endif; ?>
    <div class="konten baris-ke-kolom">
        <div style="flex:1;">
            <div class="mb-4">
                <h1 class="teks-sedang">Keranjang</h1>
                <p style="color: grey;"><?= count($keranjang) <= 0 ? 'Tidak Ada' : count($keranjang) ?> Produk</p>
            </div>
            <?php if (empty($keranjang)): ?>
                <div class="ilena-cart-empty">
                    <p class="ilena-cart-empty__eyebrow">Belum ada pilihan</p>
                    <h2>Keranjang Anda masih kosong.</h2>
                    <p>Temukan furniture yang paling sesuai untuk ruang Anda.</p>
                    <a class="btn-default-merah" href="/product">Jelajahi koleksi</a>
                </div>
            <?php endif; ?>
            <div class="container-keranjang show-block-ke-hide">
                <?php foreach ($keranjang as $index => $k) { ?>
                <div class="item-keranjang">
                    <a href="/product/<?= str_replace(' ', '-', $k['detail']['nama']); ?>" style="display:block;">
                        <img src="<?= $k['src_gambar'] ?>" alt="Gambar Keranjang">
                    </a>
                    <div style="flex: 1;" class="d-flex flex-column">
                        <a href="/product/<?= str_replace(' ', '-', $k['detail']['nama']); ?>"
                            style="text-decoration:none; color: black;">
                            <p class="m-0"><?= ucfirst($k['detail']['kategori']) ?></p>
                            <h1 class="nama-barang"><?= ucwords($k['detail']['nama']) ?></h1>
                            <p class="my-2">Varian: <?= ucfirst($k['varian']) ?></p>
                        </a>
                        <div class="d-flex align-items-end flex-grow-1">
                            <div class="d-flex align-items-center gap-4">
                                <div class="number-control">
                                    <!-- <a style="text-decoration: none; color: black;" href="/reducecart/<?= $index ?>">
                                        <div class="number-left"></div>
                                    </a> -->
                                    <form action="/reducecart/<?= $index ?>" method="post">
                                        <?= csrf_field(); ?>
                                        <button type="submit">
                                            <div class="number-left"></div>
                                        </button>
                                    </form>
                                    <input type="number" name="number" class="number-quantity" disabled
                                        value="<?= $k['jumlah'] ?>">
                                    <form action="/addcart/<?= $k['id_barang'] ?>/<?= rawurlencode($k['varian']) ?>/1" method="post">
                                        <?= csrf_field(); ?>
                                        <button type="submit">
                                            <div class="number-right"></div>
                                        </button>
                                    </form>
                                </div>
                                <form action="/deletecart/<?= $index ?>" method="post" class="m-0">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn-teks-aja m-0">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div style="width: 150px;">
                        <p class="m-0 text-end">Harga Satuan</p>
                        <p style="font-weight:bold; font-size: 18px; letter-spacing: -1px; text-align: right;"
                            class="harga">Rp
                            <?= number_format($k['detail']['harga'] * (100 - $k['detail']['diskon']) / 100, 0, ',', '.'); ?>
                        </p>
                        <?php if ($k['detail']['diskon'] > 0) { ?>
                        <p class="harga-diskon text-end">
                            Rp <?= number_format($k['detail']['harga'], 0, ',', '.'); ?>
                        </p>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="hide-ke-show-block">
                <?php foreach ($keranjang as $index => $k) { ?>
                <div class="item-keranjang-hp">
                    <a href="/product/<?= str_replace(' ', '-', $k['detail']['nama']); ?>" style="display:block;">
                        <img src="<?= $k['src_gambar'] ?>" alt="Gambar Keranjang">
                    </a>
                    <div style="flex: 1;" class="d-flex flex-column justify-content-between">
                        <a href="/product/<?= str_replace(' ', '-', $k['detail']['nama']); ?>"
                            style="text-decoration:none; color: black;" class="d-flex flex-column h-100">
                            <div style="flex: 1">
                                <p class="m-0" style="letter-spacing: -1px;"><?= ucwords($k['detail']['kategori']); ?>
                                </p>
                                <h1 class="nama-barang"><?= ucwords($k['detail']['nama']) ?></h1>
                            </div>
                            <p class="harga-hp">
                                Rp
                                <?= number_format($k['detail']['harga'] * (100 - $k['detail']['diskon']) / 100, 0, ',', '.'); ?>
                            </p>
                            <p class="m-0" style="font-size: 10px;">Varian : <?= ucfirst($k['varian']) ?></p>
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-2 align-items-end justify-content-between">
                        <form action="/deletecart/<?= $index ?>" method="post">
                            <?= csrf_field(); ?>
                            <button type="submit" class="btn-teks-aja m-0">Hapus</button>
                        </form>
                        <div class="number-control-hp">
                            <form action="/reducecart/<?= $index ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit">
                                    <div class="number-left"></div>
                                </button>
                            </form>
                            <div class="number-quantity-hp">
                                <?= $k['jumlah'] ?>
                            </div>
                            <form action="/addcart/<?= $k['id_barang'] ?>/<?= rawurlencode($k['varian']) ?>/1" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit">
                                    <div class="number-right"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

        </div>
        <div class="tigapuluh-ke-seratus">
            <div class="card p-4 ilena-cart-summary">
                <h4 style="letter-spacing: -1px">Pesanan</h4>
                <div class="mt-2 d-flex justify-content-between py-1">
                    <p class="m-0">
                        harga
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                    </p>
                </div>
                <!-- <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        Biaya Admin
                    </p>
                    <p class="fw-bold m-0">
                        Rp 5,000
                    </p>
                </div> -->
                <span class="garis my-2"></span>
                <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        TOTAL
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                    </p>
                </div>
                <a <?= count($keranjang) > 0 ? 'href="/address"' : '' ?>
                    class="btn-default-merah <?= count($keranjang) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center">Checkout</a>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>
