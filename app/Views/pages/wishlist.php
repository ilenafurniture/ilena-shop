<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// Pastikan variabel aman
$wishlist = $wishlist ?? [];
$produk   = $produk   ?? [];
$wishlistCount = is_array($wishlist) ? count($wishlist) : 0;

// Helper: cek apakah id ada di wishlist
$isInWishlist = function($id) use ($wishlist) {
  return in_array((string)$id, array_map('strval', $wishlist), true);
};
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
          $p_gbrHover  = !empty($p['gambar_hover']);
          $varianList  = json_decode($p['varian'] ?? '[]', true) ?: [];

          $imgMainId  = "img{$ind_p}";
          ?>

            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span <?= $p_diskon > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>>
                            <?= $p_diskon > 0 ? ($p_diskon . "%") : '' ?>
                        </span>
                    </div>

                    <a href="/product/<?= str_replace(' ', '-', $p_nama); ?>" class="gambar">
                        <img class="<?= $p_gbrHover ? '' : 'nonhover'; ?> img-pic" id="<?= $imgMainId ?>"
                            src="/img/barang/300/<?= $p_id; ?>.webp"
                            alt="<?= htmlspecialchars($p_nama, ENT_QUOTES); ?>">
                        <?php if ($p_gbrHover): ?>
                        <img class="img-pic-hover" src="/img/barang/hover/<?= $p_id; ?>.webp"
                            alt="<?= htmlspecialchars($p_nama . ' (hover)', ENT_QUOTES); ?>">
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Varian -->
                <div class="container-varian mb-1 d-flex">
                    <?php foreach ($varianList as $ind_v => $v):
                $kode   = $v['kode'] ?? '#ccc';
                $namaV  = $v['nama'] ?? 'default';
                $urut   = $v['urutan_gambar'] ?? '0';
                $stok   = $v['stok'] ?? 0;
                $inputId = "varian-{$ind_p}-{$ind_v}";
              ?>
                    <input id="<?= $inputId ?>"
                        value="<?= htmlspecialchars($urut, ENT_QUOTES); ?>-<?= htmlspecialchars($namaV, ENT_QUOTES); ?>"
                        type="radio" name="varian<?= $ind_p ?>" data-stok="<?= (int)$stok ?>"
                        <?= $ind_v === 0 ? 'checked' : '' ?>>
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

                <!-- Tombol wishlist + keranjang -->
                <div class="mt-2" style="margin: 6px 12px 12px; display:flex; gap:10px; flex-wrap:wrap;">
                    <?php if ($isInWishlist($p_id)): ?>
                    <form action="/delwishlist/<?= $p_id ?>" method="post" style="display:inline;">
                        <?= function_exists('csrf_field') ? csrf_field() : '' ?>
                        <button type="submit" class="btn-teks-aja my-3"
                            style="display:inline-flex;align-items:center;gap:6px;">
                            <i class="material-icons" style="font-size:18px;">bookmark</i>
                            Hapus
                        </button>
                    </form>
                    <?php endif; ?>

                    <!-- Tombol tambah ke keranjang -->
                    <?php
                $stokAwal = $varianList[0]['stok'] ?? 0;
                $namaVarAwal = $varianList[0]['nama'] ?? 'default';
              ?>
                    <form id="keranjang-<?= $ind_p ?>" method="post"
                        action="<?= $stokAwal > 0 ? '/addcart/' . $p_id . '/' . urlencode($namaVarAwal) . '/1' : ''; ?>">
                        <?= function_exists('csrf_field') ? csrf_field() : '' ?>
                        <button id="btn-keranjang-<?= $ind_p ?>"
                            class="btn-default-merah <?= $stokAwal > 0 ? '' : 'disabled'; ?>"
                            <?= $stokAwal > 0 ? '' : 'disabled'; ?> type="submit">
                            <?= $stokAwal > 0 ? 'Keranjang' : 'Stok habis'; ?>
                        </button>
                    </form>
                </div>

                <!-- Script varian → gambar & tombol -->
                <script>
                (function() {
                    const imgMain = document.getElementById("<?= $imgMainId ?>");
                    const radios = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');
                    const btn = document.getElementById("btn-keranjang-<?= $ind_p ?>");
                    const form = document.getElementById("keranjang-<?= $ind_p ?>");

                    radios.forEach(elm => {
                        elm.addEventListener('change', (e) => {
                            const val = String(e.target.value || "");
                            const parts = val.split("-");
                            const urutGambar = (parts[0] || "0").split(",")[0];
                            const namaVar = parts.slice(1).join("-") || "default";
                            const stok = parseInt(e.target.dataset.stok || "0");

                            // update gambar
                            imgMain.src = "/img/barang/1000/<?= $p_id; ?>-" + urutGambar + ".webp";

                            // update tombol & form
                            form.action = stok > 0 ?
                                "/addcart/<?= $p_id; ?>/" + encodeURIComponent(namaVar) + "/1" :
                                "";
                            btn.textContent = stok > 0 ? "Keranjang" : "Stok habis";
                            btn.classList.toggle("disabled", stok <= 0);
                            btn.disabled = stok <= 0;
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