<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
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
            <img src="<?= $artikel['header']; ?>" alt="<?= $artikel['judul'] ?>" class="header">
            <div class="mb-4 hide-ke-show-block">
                <div class="p-4 mx-4"
                    style="background-color: white; box-shadow: 5px 5px 20px rgba(0,0,0,0.1); position: relative; margin-top: -15svh">
                    <div class="d-flex justify-content-between mb-1 align-items-center">
                        <div class="d-flex gap-1">
                            <?php foreach ($artikel['kategori'] as $k) { ?>
                                <h5 class="badge rounded-pill text-bg-secondary"><?= ucfirst($k); ?></h5>
                            <?php } ?>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-flex gap-1 align-items-center">
                                <a href="/addlikearticle/<?= $artikel['id'] ?>" class="btn-sm"><i
                                        class="material-icons text-secondary" style="font-size: 13px;">thumb_up</i></a>
                                <?php if ($artikel['suka'] > 0) { ?>
                                    <p class="m-0" style="font-size: 13px;"><?= $artikel['suka']; ?></p>
                                <?php } ?>
                            </div>
                            <div class="d-flex gap-1 align-items-center">
                                <a href="/addsharearticle/<?= $artikel['id'] ?>" class="btn-sm"><i
                                        class="material-icons text-secondary" style="font-size: 13px;">share</i></a>
                                <?php if ($artikel['bagikan'] > 0) { ?>
                                    <p class="m-0" style="font-size: 13px;"><?= $artikel['bagikan']; ?></p>
                                <?php } ?>
                            </div>
                            <?php if (session()->get('role') == 1) { ?>
                                <a href="/editarticle/<?= $artikel['id'] ?>" class="btn btn-default">Edit</a>
                            <?php } ?>
                        </div>
                    </div>
                    <h1 class="teks-besar mb-3"><?= $artikel['judul'] ?></h1>
                    <div class="d-flex justify-content-between">
                        <p class="m-0 text-secondary">Ditulis oleh <?= $artikel['penulis']; ?></p>
                        <p class="m-0 text-secondary"><?= $artikel['waktu']; ?></p>
                    </div>
                    <div style="height: 1px; background-color: #888;" class="my-3"></div>
                    <div class="isi-artikel">
                        <?= $artikel['isi']; ?>
                    </div>
                </div>
            </div>
            <div class="mb-5 show-block-ke-hide">
                <div class="p-5 mx-5"
                    style="background-color: white; box-shadow: 5px 5px 20px rgba(0,0,0,0.1); position: relative; margin-top: -10svh">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1 mb-3">
                            <?php foreach ($artikel['kategori'] as $k) { ?>
                                <h5 class="badge rounded-pill text-bg-secondary"><?= ucfirst($k); ?></h5>
                            <?php } ?>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="d-flex gap-1 align-items-center">
                                <a href="/addlikearticle/<?= $artikel['id'] ?>" class="btn"><i
                                        class="material-icons text-secondary">thumb_up</i></a>
                                <?php if ($artikel['suka'] > 0) { ?>
                                    <p class="m-0"><?= $artikel['suka']; ?></p>
                                <?php } ?>
                            </div>
                            <!-- <div class="d-flex gap-1 align-items-center">
                                <a href="/addsharearticle/<?= $artikel['id'] ?>" class="btn"><i
                                        class="material-icons text-secondary">share</i></a>
                                <?php if ($artikel['bagikan'] > 0) { ?>
                                <p class="m-0"><?= $artikel['bagikan']; ?></p>
                                <?php } ?>
                            </div> -->
                            <?php if (session()->get('role') == 1) { ?>
                                <a href="/admin/editarticle/<?= $artikel['id'] ?>" class="btn btn-default">Edit</a>
                                <button
                                    onclick="triggerToast('Artikel <?= $artikel['judul'] ?> akan dihapus?', '/admin/deletearticle/<?= $artikel['id'] ?>')"
                                    class="btn btn-default">Hapus</button>
                            <?php } ?>
                        </div>
                    </div>
                    <h1 class="teks-besar mb-4"><?= $artikel['judul'] ?></h1>
                    <div class="d-flex justify-content-between">
                        <p class="m-0 text-secondary">Ditulis oleh <?= $artikel['penulis']; ?></p>
                        <p class="m-0 text-secondary">Terakhir diubah pada <?= $artikel['waktu']; ?></p>
                    </div>
                    <div style="height: 1px; background-color: #888;" class="my-3"></div>
                    <div class="isi-artikel">
                        <?= $artikel['isi']; ?>
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
                    /* min-width: 425px; */
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
                <h5 class="jdl-section mb-3">Artikel Serupa</h5>
                <div class="container-artikel-list ubah-gap mb-5">
                    <?php foreach ($artikelTerkait as $ind_a => $a) { ?>
                        <a href="/article/<?= $a['path']; ?>" class="artikel-list" style="background-image: url(<?= $a['header']; ?>);">
                            <div>
                                <div style="flex: 1;" class="d-flex justify-content-end">
                                    <snap class="kategori"><?= ucwords($a['kategori'][0]); ?></snap>
                                </div>
                                <p class="tanggal"><?= $a['waktu']; ?></p>
                                <p class="judul"><?= $a['judul']; ?></p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
                <div class="container-artikel-list-hp gap-3 mb-5">
                    <?php foreach ($artikelTerkait as $ind_a => $a) { ?>
                        <a href="/article/<?= $a['path']; ?>" class="artikel-list gap-3">
                            <div class="image">
                                <img src="<?= $a['header']; ?>" alt="">
                            </div>
                            <div style="flex: 1;">
                                <div class="mb-1 tanggal">
                                    <span><?= ucwords($a['kategori'][0]); ?></span>
                                    <span class="text-secondary">•</span>
                                    <span class="text-secondary"><?= date("d", strtotime($a['waktu'])) . " " . $bulan[date("m", strtotime($a['waktu'])) - 1] . " " . date("Y", strtotime($a['waktu'])); ?></span>
                                </div>
                                <p class="m-0 fw-bold judul"><?= $a['judul']; ?></p>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php if (count($produkTerkait) > 0) { ?>
                <div class="mb-5">
                    <h5 class="jdl-section">Produk Terkait</h5>
                    <!-- <h1 class="mb-1">Mencari produk terkait artikel diatas</h1> -->
                    <div class="container-card1">
                        <?php foreach ($produkTerkait as $ind_p => $p) { ?>
                            <div class="card1">
                                <div style="position: relative;">
                                    <span class="card1-content-img-kiri"
                                        <?= $p['diskon'] > 0 ? '' : 'style="background-color: rgba(0,0,0,0);"'; ?>><?= $p['diskon'] > 0 ? $p['diskon'] . "%" : '' ?></span>
                                    <div class="d-flex flex-column gap-2 card1-content-img-kanan">
                                        <?= session()->get('role') == '1' ? '<a class="card1-btn-img" href="/admin/editproduct/' . $p['id'] . '"><i class="material-icons">edit</i></a>' : '' ?>
                                        <?= in_array($p['id'], $wishlist) ? '<a class="card1-btn-img" href="/delwishlist/' . $p['id'] . '"><i class="material-icons">bookmark</i></a>' : '<a class="card1-btn-img" href="/addwishlist/' . $p['id'] . '"><i class="material-icons">bookmark_border</i></a>' ?>
                                        <a id="card<?= $ind_p ?>" class="card1-btn-img"
                                            href="/addcart/<?= $p['id'] ?>/<?= json_decode($p['varian'], true)[0]['nama'] ?>/1"><i
                                                class="material-icons">shopping_cart</i></a>
                                    </div>
                                    <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="gambar">
                                        <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic" id="img<?= $ind_p ?>"
                                            src="/viewpic/<?= $p['id']; ?>" alt="">
                                        <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic-hover"
                                            id="img<?= $ind_p ?>" src="/viewpichover/<?= $p['id']; ?>" alt="">
                                    </a>
                                </div>
                                <div class="container-varian mb-1 d-flex">
                                    <?php foreach (json_decode($p['varian'], true) as $ind_v => $v) { ?>
                                        <input id="varian-<?= $ind_p ?>-<?= $ind_v ?>"
                                            value="<?= $v['urutan_gambar'] ?>-<?= $v['nama'] ?>" type="radio"
                                            name="varian<?= $ind_p ?>">
                                        <label for="varian-<?= $ind_p ?>-<?= $ind_v ?>"><span
                                                style="background-color: <?= $v['kode'] ?>"></span></label>
                                    <?php } ?>
                                    <script>
                                        const btnKeranjang<?= $ind_p ?>Elm = document.getElementById("card<?= $ind_p ?>");
                                        const varian<?= $ind_p ?>Elm = document.querySelectorAll(
                                            'input[name="varian<?= $ind_p ?>"]');
                                        varian<?= $ind_p ?>Elm.forEach(elm => {
                                            elm.addEventListener('change', (e) => {
                                                console.log(e.target.value)
                                                const img<?= $ind_p ?>Elm = document.getElementById(
                                                    "img<?= $ind_p ?>");
                                                img<?= $ind_p ?>Elm.src =
                                                    "/viewvar/<?= $p['id']; ?>/" + e.target.value.split("-")[0]
                                                    .split(
                                                        ",")[
                                                        0];

                                                btnKeranjang<?= $ind_p ?>Elm.href = "/addcart/<?= $p['id'] ?>/" + e
                                                    .target
                                                    .value.split("-")[1] + "/1";
                                            })
                                        });
                                    </script>
                                </div>
                                <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="text-dark">
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
                <a href="/product" class="btn mx-auto btn-default" style="width: fit-content;">Lihat Semua
                    Produk</a>
            </div>
        </div>
    </div>
</div>
<script>
    const ubahPaddingElm = document.querySelectorAll('.ubah-padding')
    const innerWidthClient = window.innerWidth;
    ubahPaddingElm.forEach((e) => {
        e.classList.add(innerWidthClient > 700 ? 'py-4' : 'py-3')
    })
    const ubahGapElm = document.querySelectorAll('.ubah-gap')
    ubahGapElm.forEach((e) => {
        e.classList.add(innerWidthClient > 700 ? 'gap-4' : 'gap-3')
    })
</script>
<?= $this->endSection(); ?>