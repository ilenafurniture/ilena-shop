<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// Pastikan variabel aman
$wishlist = $wishlist ?? [];
$produk   = $produk   ?? [];
$wishlistCount = is_array($wishlist) ? count($wishlist) : 0;
?>

<div class="container">
    <div class="konten">
        <div class="d-flex justify-content-between mb-4">
            <div>
                <h1 class="teks-sedang">Produk tersimpan</h1>
                <p style="color: grey;">
                    <?= $wishlistCount <= 0 ? 'Tidak ada' : $wishlistCount ?> produk yang disimpan
                </p>
            </div>
            <!-- Tombol "Beli semua" DIHILANGKAN -->
            <div></div>
        </div>

        <?php if ($wishlistCount > 0): ?>
        <div class="container-card1">
            <?php foreach ($produk as $ind_p => $p): ?>
            <?php
            // Normalisasi/guard data produk
            $p_id        = $p['id']        ?? '';
            $p_nama      = $p['nama']      ?? 'Produk';
            $p_harga     = (int)($p['harga']  ?? 0);
            $p_diskon    = (int)($p['diskon'] ?? 0);
            $p_gbrHover  = !empty($p['gambar_hover']); // boolean
            $varianList  = json_decode($p['varian'] ?? '[]', true) ?: [];

            // Element IDs unik
            $imgMainId  = "img{$ind_p}";
          ?>

            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span <?= $p_diskon > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>>
                            <?= $p_diskon > 0 ? ($p_diskon . "%") : '' ?>
                        </span>
                        <!-- NOTE: tombol wishlist & cart DIHILANGKAN -->
                    </div>

                    <a href="/product/<?= str_replace(' ', '-', $p_nama); ?>" class="gambar">
                        <!-- Gambar utama -->
                        <img class="<?= $p_gbrHover ? '' : 'nonhover'; ?> img-pic" id="<?= $imgMainId ?>"
                            src="/img/barang/300/<?= $p_id; ?>.webp"
                            alt="<?= htmlspecialchars($p_nama, ENT_QUOTES); ?>">
                        <!-- Gambar hover: render hanya jika tersedia -->
                        <?php if ($p_gbrHover): ?>
                        <img class="img-pic-hover" src="/img/barang/hover/<?= $p_id; ?>.webp"
                            alt="<?= htmlspecialchars($p_nama . ' (hover)', ENT_QUOTES); ?>">
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Varian (hanya untuk ganti foto, tidak mempengaruhi cart) -->
                <div class="container-varian mb-1 d-flex">
                    <?php foreach ($varianList as $ind_v => $v):
                $kode   = $v['kode'] ?? '#ccc';
                $namaV  = $v['nama'] ?? 'default';
                $urut   = $v['urutan_gambar'] ?? '0';
                $inputId = "varian-{$ind_p}-{$ind_v}";
              ?>
                    <input id="<?= $inputId ?>"
                        value="<?= htmlspecialchars($urut, ENT_QUOTES); ?>-<?= htmlspecialchars($namaV, ENT_QUOTES); ?>"
                        type="radio" name="varian<?= $ind_p ?>" <?= $ind_v === 0 ? 'checked' : '' ?>>
                    <label for="<?= $inputId ?>">
                        <span style="background-color: <?= htmlspecialchars($kode, ENT_QUOTES); ?>"></span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Nama & Harga -->
                <h5><?= htmlspecialchars($p_nama, ENT_QUOTES); ?></h5>
                <div class="d-flex gap-2">
                    <p class="harga">
                        Rp <?= number_format( $p_harga * (100 - $p_diskon) / 100, 0, ',', '.'); ?>
                    </p>
                    <?php if ($p_diskon > 0): ?>
                    <p class="harga-diskon">Rp <?= number_format($p_harga, 0, ',', '.') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Script: varian hanya untuk ganti foto -->
                <script>
                (function() {
                    const imgMain = document.getElementById("<?= $imgMainId ?>");
                    const radios = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');

                    radios.forEach(elm => {
                        elm.addEventListener('change', (e) => {
                            const val = String(e.target.value || "");
                            const parts = val.split("-");
                            const urutGambar = (parts[0] || "0").split(",")[0];
                            imgMain.src = "/img/barang/1000/<?= $p_id; ?>-" + urutGambar + ".webp";
                        });
                    });
                })();
                </script>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="d-flex justify-content-center align-items-center">
            <img src="/img/sadface.webp" alt="Kosong" style="height: 100px; opacity: 0.5">
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>