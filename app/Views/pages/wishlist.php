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
                <p class="js-wishlist-counter" style="color: grey;">
                    <?= $wishlistCount <= 0 ? 'Tidak ada' : $wishlistCount ?> produk yang disimpan
                </p>
            </div>
            <div>
                <?= $wishlistCount <= 0 ? '' : '<a class="btn-lonjong" href="/wishlisttocart">Beli semua</a>' ?>
            </div>
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

            // Fallback varian pertama untuk Add to Cart
            $firstVarNama = $varianList[0]['nama'] ?? 'default';

            // Element IDs unik
            $imgMainId  = "img{$ind_p}";
            $cardBtnId  = "card{$ind_p}";
            ?>

            <div class="card1">
                <div style="position: relative;">
                    <div class="card1-content-img">
                        <span <?= $p_diskon > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>>
                            <?= $p_diskon > 0 ? ($p_diskon . "%") : '' ?>
                        </span>

                        <div class="d-flex flex-column gap-2">
                            <?= in_array($p_id, $wishlist, true)
                                ? '<a class="card1-btn-img js-wishlist-link" href="/delwishlist/' . $p_id . '"><i class="material-icons">bookmark</i></a>'
                                : '<a class="card1-btn-img js-wishlist-link" href="/addwishlist/' . $p_id . '"><i class="material-icons">bookmark_border</i></a>'
                            ?>
                            <a id="<?= $cardBtnId ?>" class="card1-btn-img"
                                href="/addcart/<?= $p_id ?>/<?= urlencode($firstVarNama) ?>/1">
                                <i class="material-icons">shopping_cart</i>
                            </a>
                        </div>
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

                <!-- Varian -->
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

                <!-- Script untuk switching varian per kartu -->
                <script>
                (function() {
                    const btnKeranjang = document.getElementById("<?= $cardBtnId ?>");
                    const imgMain = document.getElementById("<?= $imgMainId ?>");
                    const radios = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');

                    radios.forEach(elm => {
                        elm.addEventListener('change', (e) => {
                            const val = String(e.target.value || "");
                            const parts = val.split("-");
                            const urutGambar = (parts[0] || "0").split(",")[0];
                            const namaVar = parts.slice(1).join("-") || "default";

                            // Ganti foto utama sesuai urutan varian (gunakan folder 1000 agar detail)
                            imgMain.src = "/img/barang/1000/<?= $p_id; ?>-" + urutGambar + ".webp";

                            // Update link add to cart
                            btnKeranjang.href = "/addcart/<?= $p_id ?>/" + encodeURIComponent(
                                namaVar) + "/1";
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

<!-- ===== AJAX wishlist (non-intrusive). Jika gagal -> fallback redirect ===== -->
<script>
(function() {
    // Delegasi klik hanya untuk link add/del wishlist
    document.addEventListener('click', async function(e) {
        const a = e.target.closest(
            'a.js-wishlist-link[href^="/addwishlist/"], a.js-wishlist-link[href^="/delwishlist/"]');
        if (!a) return;

        // Coba fetch sebagai AJAX agar tidak dinavigasi oleh script SPA/prefetch
        e.preventDefault();
        const href = a.getAttribute('href');
        const icon = a.querySelector('.material-icons');
        const prevIcon = icon ? icon.textContent : null;

        if (icon) icon.textContent = 'hourglass_empty';

        try {
            const resp = await fetch(href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();

            // Update counter di header
            const counterP = document.querySelector('.js-wishlist-counter');
            if (counterP && typeof data.count === 'number') {
                counterP.textContent = (data.count <= 0 ? 'Tidak ada' : data.count) +
                    ' produk yang disimpan';
            }

            // Toggle UI ikon + href
            const isAdd = href.startsWith('/addwishlist/');
            const newHref = isAdd ? href.replace('/addwishlist/', '/delwishlist/') : href.replace(
                '/delwishlist/', '/addwishlist/');
            a.setAttribute('href', newHref);
            if (icon) icon.textContent = isAdd ? 'bookmark' : 'bookmark_border';

            // Kalau ini halaman wishlist dan action = del -> hapus kartu dari DOM
            if (data.action === 'del') {
                const card = a.closest('.card1');
                if (card) {
                    card.remove();
                    // Jika sudah tidak ada kartu lagi -> tampilkan empty state
                    const container = document.querySelector('.container-card1');
                    if (container && container.children.length === 0) {
                        container.outerHTML =
                            '<div class="d-flex justify-content-center align-items-center"><img src="/img/sadface.webp" alt="Kosong" style="height: 100px; opacity: 0.5"></div>';
                    }
                }
            }
        } catch (err) {
            // Gagal AJAX -> fallback ke navigasi biasa (perilaku lama)
            if (icon && prevIcon) icon.textContent = prevIcon;
            window.location.href = href;
        }
    }, true);
})();
</script>

<?= $this->endSection(); ?>