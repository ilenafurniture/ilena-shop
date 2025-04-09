<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<div class="container d-flex justify-content-center artikel">
    <div class="konten">
        <form action="/actionsearcharticle" method="post">
            <div class="d-flex mb-2 align-items-center">
                <div class="container-search-artikel show">
                    <input type="text" placeholder="Cari artikel" class="form-control" name="cari"
                        value="<?= isset($find) ? $find : ''; ?>">
                    <button type="submit" class="btn btn-light"><i class="material-icons">search</i></button>
                </div>
            </div>
        </form>
        <div class="mb-4">
            <div class="p-5 header show-flex-ke-hide"
                style="position: absolute; flex-direction: column; justify-content: end; width: 50%;">
                <h1 class="teks-besar text-light">Welcome to Ilena's Article</h1>
                <p class="text-light mb-3">Perbarui informasi & referensi Anda seputar furniture dengan desain ala
                    masyarakat urban</p>
                <div class="d-flex gap-2">
                    <a href="/product" class="btn-default-hitam">Pergi ke Toko</a>
                </div>
            </div>
            <img class="d-block rounded header"
                src="https://img.ilenafurniture.com/image/1742445475511.webp/?apikey=<?= $apikey_img_ilena ?>" alt="">
        </div>
        <?php
        $indexAwal = -1;
        if (count($artikel) > 6) { ?>
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-5 mt-5 align-items-center">
                    <h5 class="teks-besar text-center">Artikel Baru</h5>
                    <?php if (session()->get('role') == '1') { ?>
                        <a href="/admin/addarticle" class="btn btn-default-putih">Buat Artikel Baru</a>
                    <?php } ?>
                </div>
                <div class="gap-4 show-flex-ke-hide container-card-artikel">
                    <div class="flex-grow-1">
                        <div class="card-artikel-besar" onclick="pergiKeArtikel(`<?= $artikel[0]['path']; ?>`)">
                            <img class="rounded" src="<?= $artikel[0]['header']; ?>" alt="<?= $artikel[0]['judul']; ?>">
                            <p class="m-0 judul"><?= $artikel[0]['judul']; ?></p>
                            <div class="flex-grow-1">
                                <p class="m-0 isi"><?= $artikel[0]['deskripsi']; ?></p>
                            </div>
                            <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[0]['penulis']; ?></p>
                            <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[0]['waktu']; ?></p>
                        </div>
                    </div>
                    <div class="d-flex flex-grow-1 flex-column gap-4">
                        <div class="card-artikel-kecil" onclick="pergiKeArtikel(`<?= $artikel[1]['path']; ?>`)">
                            <div class="img">
                                <img class="rounded" src="<?= $artikel[1]['header']; ?>" alt="<?= $artikel[1]['judul']; ?>">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <p class="m-0 judul"><?= $artikel[1]['judul']; ?></p>
                                <div class="flex-grow-1">
                                    <p class="m-0 isi"><?= $artikel[1]['deskripsi']; ?></p>
                                </div>
                                <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[1]['penulis']; ?></p>
                                <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[1]['waktu']; ?></p>
                            </div>
                        </div>
                        <div class="card-artikel-kecil" onclick="pergiKeArtikel(`<?= $artikel[2]['path']; ?>`)">
                            <div class="img">
                                <img class="rounded" src="<?= $artikel[2]['header']; ?>" alt="<?= $artikel[2]['judul']; ?>">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <p class="m-0 judul"><?= $artikel[2]['judul']; ?></p>
                                <div class="flex-grow-1">
                                    <p class="m-0 isi"><?= $artikel[2]['deskripsi']; ?></p>
                                </div>
                                <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[2]['penulis']; ?></p>
                                <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[2]['waktu']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-grow-1 flex-column gap-4">
                        <div class="card-artikel-kecil" onclick="pergiKeArtikel(`<?= $artikel[3]['path']; ?>`)">
                            <div class="img">
                                <img class="rounded" src="<?= $artikel[3]['header']; ?>" alt="<?= $artikel[3]['judul']; ?>">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <p class="m-0 judul"><?= $artikel[3]['judul']; ?></p>
                                <div class="flex-grow-1">
                                    <p class="m-0 isi"><?= $artikel[3]['deskripsi']; ?></p>
                                </div>
                                <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[3]['penulis']; ?></p>
                                <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[3]['waktu']; ?></p>
                            </div>
                        </div>
                        <div class="card-artikel-kecil" onclick="pergiKeArtikel(`<?= $artikel[4]['path']; ?>`)">
                            <div class="img">
                                <img class="rounded" src="<?= $artikel[4]['header']; ?>" alt="<?= $artikel[4]['judul']; ?>">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <p class="m-0 judul"><?= $artikel[4]['judul']; ?></p>
                                <div class="flex-grow-1">
                                    <p class="m-0 isi"><?= $artikel[4]['deskripsi']; ?></p>
                                </div>
                                <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[4]['penulis']; ?></p>
                                <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[4]['waktu']; ?></p>
                            </div>
                        </div>
                        <div class="card-artikel-kecil" onclick="pergiKeArtikel(`<?= $artikel[5]['path']; ?>`)">
                            <div class="img">
                                <img class="rounded" src="<?= $artikel[5]['header']; ?>" alt="<?= $artikel[5]['judul']; ?>">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <p class="m-0 judul"><?= $artikel[5]['judul']; ?></p>
                                <div class="flex-grow-1">
                                    <p class="m-0 isi"><?= $artikel[5]['deskripsi']; ?></p>
                                </div>
                                <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[5]['penulis']; ?></p>
                                <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[5]['waktu']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            $indexAwal = 5;
        }
        ?>
        <div class="show-flex-ke-hide flex-column gap-2">
            <?php foreach ($artikel as $ind_a => $a) {
                if ($ind_a > $indexAwal) {
                    if (fmod($ind_a, 3) == 0) { ?>
                        <div class="gap-4 d-flex container-card-artikel">
                            <?php for ($i = $ind_a; $i < $ind_a + 3; $i++) {
                                if (isset($artikel[$i])) { ?>
                                    <div class="flex-grow-1" onclick="pergiKeArtikel(`<?= $artikel[$i]['path']; ?>`)">
                                        <div class="card-artikel-besar" .>
                                            <img class="rounded" src="<?= $artikel[$i]['header']; ?>" alt="<?= $artikel[$i]['judul']; ?>">
                                            <p class="m-0 judul"><?= $artikel[$i]['judul']; ?></p>
                                            <div class="flex-grow-1">
                                                <p class="m-0 isi"><?= $artikel[$i]['deskripsi']; ?></p>
                                            </div>
                                            <p class="m-0 fw-bold" style="font-size: smaller;"><?= $artikel[$i]['penulis']; ?></p>
                                            <p class="m-0" style="font-size: smaller; color: #888;"><?= $artikel[$i]['waktu']; ?></p>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
            <?php }
                }
            } ?>
        </div>
        <div class="hide-ke-show-flex flex-column gap-2">
            <?php foreach ($artikel as $ind_a => $a) { ?>
                <div class="gap-4 d-flex container-card-artikel" style="height: 100px;">
                    <div class="d-flex flex-grow-1 flex-column gap-4">
                        <div class="card-artikel-kecil" onclick="pergiKeArtikel(`<?= $a['path']; ?>`)">
                            <div class="img">
                                <img class="rounded" src="<?= $a['header']; ?>" alt="<?= $a['judul']; ?>">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column">
                                <p class="m-0 judul" style="line-height: 16px;"><?= $a['judul']; ?></p>
                                <div class="flex-grow-1">
                                    <p class="m-0 isi"><?= $a['deskripsi']; ?></p>
                                </div>
                                <p class="m-0 fw-bold" style="font-size: smaller;"><?= $a['penulis']; ?></p>
                                <p class="m-0" style="font-size: smaller; color: #888;"><?= $a['waktu']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    function pergiKeArtikel(judulArtikel) {
        console.log(judulArtikel)
        window.location.href = '/article/' + judulArtikel
    }
</script>
<?= $this->endSection(); ?>