<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php $isEnglish = service('request')->getLocale() === 'en'; ?>
<?php $safeArticleHtml = (new \App\Services\ArticleContentSanitizer())->sanitize((string) ($artikel['isi'] ?? '')); ?>
<?php
$articleStructuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => (string) ($artikel['judul'] ?? ''),
    'description' => (string) ($metaDeskripsi ?? ''),
    'image' => [(string) ($metaImage ?? base_url('img/logo/N.png'))],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => (string) ($metaUrl ?? current_url())],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Ilena Furniture',
        'logo' => ['@type' => 'ImageObject', 'url' => base_url('img/logo/N.png')],
    ],
    'inLanguage' => $isEnglish ? 'en' : 'id-ID',
];
if (trim((string) ($articleAuthor ?? '')) !== '') {
    $articleStructuredData['author'] = ['@type' => 'Person', 'name' => (string) $articleAuthor];
}
if (trim((string) ($articlePublishedIso ?? '')) !== '') {
    $articleStructuredData['datePublished'] = (string) $articlePublishedIso;
}
?>
<script type="application/ld+json"><?= json_encode($articleStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<style>
.isi-artikel h1 {
    font-weight: 500;
    letter-spacing: -4px;
}

.isi-artikel h3 {
    font-weight: 500;
    letter-spacing: -1px;
}

.isi-artikel a {
    color: var(--merah);
    text-decoration: none;
}

.isi-artikel a:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    .isi-artikel h1 {
        font-weight: 500;
        letter-spacing: -2px;
    }

    .isi-artikel h3 {
        font-weight: 500;
        letter-spacing: -1px;
    }
}
</style>

<div class="container artikel d-flex justify-content-center">
    <div class="konten">
        <div class="artikel">
            <img src="<?= esc($artikel['header'], 'attr'); ?>" alt="<?= esc($artikel['judul'], 'attr'); ?>" class="header" width="1600" height="900" decoding="async" fetchpriority="high">

            <div class="mb-4 hide-ke-show-block">
                <div class="p-4 mx-4"
                    style="background-color: white; box-shadow: 5px 5px 20px rgba(0,0,0,0.1); position: relative; margin-top: -15svh">
                    <div class="d-flex justify-content-between mb-1 align-items-center">
                        <div class="d-flex gap-1">
                            <?php foreach ($artikel['kategori'] as $k) { ?>
                            <h5 class="badge rounded-pill text-bg-secondary"><?= esc(ilena_article_category_label((string) $k)); ?></h5>
                            <?php } ?>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-flex gap-1 align-items-center">
                                <form action="<?= esc(ilena_store_path('/addlikearticle/' . $artikel['id']), 'attr'); ?>" method="post" class="m-0"><?= csrf_field(); ?><button type="submit" class="btn-sm border-0 bg-transparent" aria-label="<?= esc($isEnglish ? 'Like article' : 'Sukai artikel', 'attr'); ?>"><i class="material-icons text-secondary" style="font-size: 13px;">thumb_up</i></button></form>
                                <?php if ($artikel['suka'] > 0) { ?>
                                <p class="m-0" style="font-size: 13px;"><?= $artikel['suka']; ?></p>
                                <?php } ?>
                            </div>
                            <div class="d-flex gap-1 align-items-center">
                                <form action="<?= esc(ilena_store_path('/addsharearticle/' . $artikel['id']), 'attr'); ?>" method="post" class="m-0"><?= csrf_field(); ?><button type="submit" class="btn-sm border-0 bg-transparent" aria-label="<?= esc($isEnglish ? 'Share article' : 'Bagikan artikel', 'attr'); ?>"><i class="material-icons text-secondary" style="font-size: 13px;">share</i></button></form>
                                <?php if ($artikel['bagikan'] > 0) { ?>
                                <p class="m-0" style="font-size: 13px;"><?= $artikel['bagikan']; ?></p>
                                <?php } ?>
                            </div>
                            <?php if (session()->get('role') == 1) { ?>
                            <a href="/editarticle/<?= $artikel['id'] ?>" class="btn btn-default"><?= $isEnglish ? 'Edit' : 'Edit'; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <h1 class="teks-besar mb-3"><?= esc($artikel['judul'] ?? ''); ?></h1>
                    <div class="d-flex justify-content-between">
                        <p class="m-0 text-secondary"><?= $isEnglish ? 'Written by' : 'Ditulis oleh'; ?> <?= esc($artikel['penulis'] ?? ''); ?></p>
                        <p class="m-0 text-secondary"><?= $artikel['waktu']; ?></p>
                    </div>
                    <div style="height: 1px; background-color: #888;" class="my-3"></div>
                    <div class="isi-artikel">
                        <?= $safeArticleHtml; ?>
                    </div>
                </div>
            </div>

            <div class="mb-5 show-block-ke-hide">
                <div class="p-5 mx-5"
                    style="background-color: white; box-shadow: 5px 5px 20px rgba(0,0,0,0.1); position: relative; margin-top: -10svh">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1 mb-3">
                            <?php foreach ($artikel['kategori'] as $k) { ?>
                            <h5 class="badge rounded-pill text-bg-secondary"><?= esc(ilena_article_category_label((string) $k)); ?></h5>
                            <?php } ?>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-flex gap-1 align-items-center">
                                <form action="<?= esc(ilena_store_path('/addlikearticle/' . $artikel['id']), 'attr'); ?>" method="post" class="m-0"><?= csrf_field(); ?><button type="submit" class="btn border-0 bg-transparent" aria-label="<?= esc($isEnglish ? 'Like article' : 'Sukai artikel', 'attr'); ?>"><i class="material-icons text-secondary">thumb_up</i></button></form>
                                <?php if ($artikel['suka'] > 0) { ?>
                                <p class="m-0"><?= $artikel['suka']; ?></p>
                                <?php } ?>
                            </div>
                            <?php if (session()->get('role') == 1) { ?>
                            <a href="/admin/editarticle/<?= esc($artikel['id'] ?? '', 'attr'); ?>" class="btn btn-default">Edit</a>
                            <button
                                data-title="<?= esc($artikel['judul'] ?? '', 'attr'); ?>" data-url="/admin/deletearticle/<?= esc($artikel['id'] ?? '', 'attr'); ?>" onclick="triggerToast('Hapus artikel ' + this.dataset.title + '?', this.dataset.url)"
                                class="btn btn-default"><?= $isEnglish ? 'Delete' : 'Hapus'; ?></button>
                            <?php } ?>
                        </div>
                    </div>
                    <h2 class="teks-besar mb-4"><?= esc($artikel['judul'] ?? ''); ?></h2>
                    <div class="d-flex justify-content-between">
                        <p class="m-0 text-secondary"><?= $isEnglish ? 'Written by' : 'Ditulis oleh'; ?> <?= esc($artikel['penulis'] ?? ''); ?></p>
                        <p class="m-0 text-secondary"><?= $isEnglish ? 'Last updated' : 'Terakhir diubah pada'; ?> <?= $artikel['waktu']; ?></p>
                    </div>
                    <div style="height: 1px; background-color: #888;" class="my-3"></div>
                    <div class="isi-artikel">
                        <?= $safeArticleHtml; ?>
                    </div>
                </div>
            </div>

            <style>
            .container-artikel-list {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
            }

            @media (max-width: 995px) {
                .container-artikel-list {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .container-artikel-list .artikel-list {
                background-position: center;
                background-size: 100%;
                border-radius: 15px;
                cursor: pointer;
                transition: 0.5s;
                position: relative;
                aspect-ratio: 1 / 1;
                text-decoration: none;
            }

            .container-artikel-list .artikel-list:hover {
                background-size: 110%;
                transition: 0.5s;
            }

            .container-artikel-list .artikel-list>div {
                border-radius: 15px;
                height: 100%;
                width: 100%;
                display: flex;
                flex-direction: column;
                padding: 2em;
                background-image: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
                z-index: 3;
                position: relative;
            }

            .container-artikel-list .artikel-list::before {
                content: '';
                display: block;
                border-radius: 15px;
                height: 100%;
                width: 100%;
                background-color: rgba(0, 0, 0, 0);
                transition: 0.5s;
                position: absolute;
                z-index: 2;
            }

            .container-artikel-list .artikel-list:hover::before {
                background-color: rgba(0, 0, 0, 0.6);
                position: absolute;
                transition: 0.5s;
            }

            .container-artikel-list .artikel-list .judul {
                font-weight: 600;
                color: white;
                font-size: 20px;
                margin: 0;
            }

            .container-artikel-list .artikel-list .kategori {
                background-color: white;
                height: fit-content;
                display: block;
                padding: 0.3em 1em;
                font-weight: bold;
                border-radius: 2em;
                font-size: 14px;
                color: black;
            }

            .container-artikel-list .artikel-list .tanggal {
                background-color: rgba(255, 255, 255, 0.3);
                height: fit-content;
                display: block;
                padding: 0.3em 1em;
                font-weight: bold;
                border-radius: 2em;
                font-size: 14px;
                color: white;
                width: fit-content;
            }

            .container-artikel-list-hp {
                display: none;
                flex-direction: column;
            }

            .container-artikel-list-hp .artikel-list {
                display: flex;
                width: 100%;
                text-decoration: none;
                color: black;
            }

            .container-artikel-list-hp .artikel-list .image {
                width: 100px;
            }

            .container-artikel-list-hp .artikel-list img {
                width: 100%;
                aspect-ratio: 1 / 1;
                object-fit: cover;
                border-radius: 10px;
            }

            .container-artikel-list-hp .artikel-list .tanggal {
                display: flex;
                align-items: start;
                gap: 6px;
            }

            .container-artikel-list-hp .artikel-list .tanggal span {
                font-size: 10px;
            }

            .container-artikel-list-hp .artikel-list .judul {
                font-size: 12px;
            }

            @media (max-width: 700px) {
                .container-artikel-list {
                    display: none;
                }

                .container-artikel-list .artikel-list .judul {
                    font-size: 14px;
                }

                .container-artikel-list .artikel-list .kategori,
                .container-artikel-list .artikel-list .tanggal {
                    font-size: 10px;
                }

                .container-artikel-list-hp {
                    display: flex;
                }
            }
            </style>

            <div class="mb-5">
                <h5 class="jdl-section mb-3"><?= $isEnglish ? 'Related articles' : 'Artikel Serupa'; ?></h5>

                <!-- Desktop/Grid -->
                <div id="rel-container-desktop" class="container-artikel-list ubah-gap mb-3">
                    <?php foreach ($artikelTerkait as $ind_a => $a) { ?>
                    <a href="<?= esc(ilena_store_path('/article/' . $a['path']), 'attr'); ?>" class="artikel-list">
                        <img class="article-card-cover" src="<?= esc($a['header'], 'attr'); ?>" alt="" loading="lazy" decoding="async" width="900" height="700">
                        <div>
                            <div style="flex: 1;" class="d-flex justify-content-end">
                                <snap class="kategori"><?= esc(ilena_article_category_label((string) $a['kategori'][0])); ?></snap>
                            </div>
                            <p class="tanggal"><?= $a['waktu']; ?></p>
                            <p class="judul"><?= $a['judul']; ?></p>
                        </div>
                    </a>
                    <?php } ?>
                </div>

                <!-- Mobile/List -->
                <div id="rel-container-mobile" class="container-artikel-list-hp gap-3 mb-3">
                    <?php foreach ($artikelTerkait as $ind_a => $a) { ?>
                    <a href="<?= esc(ilena_store_path('/article/' . $a['path']), 'attr'); ?>" class="artikel-list gap-3">
                        <div class="image">
                            <img src="<?= esc($a['header'], 'attr'); ?>" alt="" loading="lazy" decoding="async" width="240" height="180">
                        </div>
                        <div style="flex: 1;">
                            <div class="mb-1 tanggal">
                                <span><?= esc(ilena_article_category_label((string) $a['kategori'][0])); ?></span>
                                <span class="text-secondary">•</span>
                                <span class="text-secondary">
                                    <?= date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu'])); ?>
                                </span>
                            </div>
                            <p class="m-0 fw-bold judul"><?= $a['judul']; ?></p>
                        </div>
                    </a>
                    <?php } ?>
                </div>

                <!-- Pagination SERVER-SIDE sebelumnya dihapus sesuai permintaan -->
            </div>

            <?php if (count($produkTerkait) > 0) { ?>
            <div class="mb-5">
                <h5 class="jdl-section"><?= $isEnglish ? 'Related products' : 'Produk Terkait'; ?></h5>
                <div class="container-card1">
                    <?php foreach ($produkTerkait as $ind_p => $p) { ?>
                    <div class="card1">
                        <div style="position: relative;">
                            <span class="card1-content-img-kiri"
                                <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>>
                                <?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?>
                            </span>
                            <div class="d-flex flex-column gap-2 card1-content-img-kanan">
                                <?= session()->get('role') == '1'
                                            ? '<a class="card1-btn-img" href="/admin/editproduct/' . $p['id'] . '"><i class="material-icons">edit</i></a>'
                                            : '' ?>
                                <?= in_array($p['id'], $wishlist)
                                            ? '<form method="post" action="' . esc(ilena_store_path('/delwishlist/' . $p['id']), 'attr') . '">' . csrf_field() . '<button class="card1-btn-img" type="submit"><i class="material-icons">favorite</i></button></form>'
                                            : '<form method="post" action="' . esc(ilena_store_path('/addwishlist/' . $p['id']), 'attr') . '">' . csrf_field() . '<button class="card1-btn-img" type="submit"><i class="material-icons">favorite_border</i></button></form>' ?>
                                <form method="post" id="card<?= $ind_p ?>" class="m-0"
                                    action="<?= esc(ilena_store_path('/addcart/' . $p['id'] . '/' . rawurlencode(json_decode($p['varian'], true)[0]['nama']) . '/1'), 'attr'); ?>">
                                    <?= csrf_field(); ?><button type="submit" class="card1-btn-img"><i class="material-icons">shopping_bag</i></button>
                                </form>
                            </div>
                            <a href="<?= esc(ilena_store_path('/product/' . str_replace(' ', '-', $p['nama'])), 'attr'); ?>" class="gambar">
                                <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic" id="img<?= $ind_p ?>"
                                    src="/viewpic/<?= esc($p['id'], 'attr'); ?>" alt="<?= esc($p['nama'], 'attr'); ?>" loading="lazy" decoding="async">
                                <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic-hover"
                                    id="img<?= $ind_p ?>" src="/viewpichover/<?= esc($p['id'], 'attr'); ?>" alt="" loading="lazy" decoding="async">
                            </a>
                        </div>
                        <div class="container-varian mb-1 d-flex">
                            <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                            <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>"
                                value="<?= $v['urutan_gambar'] ?>-<?= $v['nama'] ?>" type="radio"
                                name="varian<?= $ind_p ?>">
                            <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>">
                                <span style="background-color: <?= $v['kode'] ?>"></span>
                            </label>
                            <?php } ?>
                            <script>
                            const btnKeranjang<?= $ind_p ?>Elm = document.getElementById("card<?= $ind_p ?>");
                            const varian<?= $ind_p ?>Elm = document.querySelectorAll(
                                'input[name="varian<?= $ind_p ?>"]');
                            varian<?= $ind_p ?>Elm.forEach(elm => {
                                elm.addEventListener('change', (e) => {
                                    const img<?= $ind_p ?>Elm = document.getElementById(
                                        "img<?= $ind_p ?>");
                                    img<?= $ind_p ?>Elm.src = "<?= esc(ilena_store_path('/viewvar/' . $p['id']), 'js'); ?>/" + e.target
                                        .value.split("-")[0].split(",")[0];
                                    btnKeranjang<?= $ind_p ?>Elm.action = "<?= esc(ilena_store_path('/addcart/' . $p['id']), 'js'); ?>/" + encodeURIComponent(e.target.value.split("-")[1]) + "/1";
                                })
                            });
                            </script>
                        </div>
                        <a href="<?= esc(ilena_store_path('/product/' . str_replace(' ', '-', $p['nama'])), 'attr'); ?>" class="text-dark">
                            <p class="text-secondary text-sm-start m-0"><?= strtolower($p['kategori']); ?></p>
                            <h5 style="font-size:18px;"><?= str_replace('Tv', 'TV', ucwords($p['nama'])); ?></h5>
                            <div class="d-flex gap-2">
                                <p class="harga">Rp
                                    <?= number_format($p['harga'] * (100 - $p['diskon']) / 100, 0, ',', '.'); ?></p>
                                <?php if ($p['diskon'] > 0) { ?>
                                <p class="harga-diskon">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                                <?php } ?>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <div class="mx-auto mt-2" style="width: fit-content;">
                <a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>" class="btn mx-auto btn-default" style="width: fit-content;"><?= $isEnglish ? 'View all products' : 'Lihat Semua Produk'; ?></a>
            </div>
        </div>
    </div>
</div>

<script>
const ubahPaddingElm = document.querySelectorAll('.ubah-padding');
const innerWidthClient = window.innerWidth;
ubahPaddingElm.forEach((e) => {
    e.classList.add(innerWidthClient > 700 ? 'py-4' : 'py-3')
});
const ubahGapElm = document.querySelectorAll('.ubah-gap');
ubahGapElm.forEach((e) => {
    e.classList.add(innerWidthClient > 700 ? 'gap-4' : 'gap-3')
});
</script>

<?= $this->endSection(); ?>
