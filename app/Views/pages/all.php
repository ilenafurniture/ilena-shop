<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php
if (isset($kategori)) $_GET['koleksi'] = $kategori;
if (isset($_GET['koleksi'])) {
    if ($_GET['koleksi'] != '') {
        $koleksi = explode(" ", $_GET['koleksi']);
        $produkLama = $produk;
        $produk = [];
        foreach ($koleksi as $k) {
            foreach ($produkLama as $p) {
                if (strtolower(str_replace("-", " ", $k)) == strtolower($p['kategori'])) {
                    array_push($produk, $p);
                }
            }
        }
    }
}
if (isset($_GET['jenis'])) {
    if ($_GET['jenis'] != '') {
        $jenis = explode(" ", $_GET['jenis']);
        $produkLama = $produk;
        $produk = [];
        foreach ($jenis as $j) {
            foreach ($produkLama as $p) {
                if (strtolower(str_replace("-", " ", $j)) == strtolower($p['subkategori'])) {
                    array_push($produk, $p);
                }
            }
        }
    }
}
if (isset($_GET['varian'])) {
    if ($_GET['varian'] != '') {
        $varian = explode(" ", $_GET['varian']);
        $produkLama = $produk;
        $produk = [];
        foreach ($varian as $v) {
            foreach ($produkLama as $p) {
                $varianProdukSelected = json_decode($p['varian'], true);
                foreach ($varianProdukSelected as $vp) {
                    if (strtolower(str_replace("-", " ", $v)) == strtolower($vp['nama'])) {
                        if (!in_array($p, $produk))
                            array_push($produk, $p);
                    }
                }
            }
        }
    }
}
if (isset($_GET['harga'])) {
    if ($_GET['harga'] != '') {
        $produkLama = $produk;
        $produk = [];
        foreach ($produkLama as $p) {
            $hargaDiskon = (int)$p['harga'] * (100 - $p['diskon']) / 100;
            switch ($_GET['harga']) {
                case '0':
                    if ($hargaDiskon < 5000000)
                        array_push($produk, $p);
                    break;
                case '1':
                    if (5000000 <= $hargaDiskon && $hargaDiskon < 10000000) {
                        array_push($produk, $p);
                    }
                    break;
                case '2':
                    if (10000000 <= $hargaDiskon && $hargaDiskon < 15000000)
                        array_push($produk, $p);
                    break;
                case '3':
                    if (15000000 <= $hargaDiskon && $hargaDiskon < 20000000)
                        array_push($produk, $p);
                    break;
                default:
                    if (20000000 <= $hargaDiskon)
                        array_push($produk, $p);
                    break;
            }
        }
    }
}
if (isset($_GET['ruang'])) {
    if ($_GET['ruang'] != '') {
        $ruang = explode(" ", $_GET['ruang']);
        $produkLama = $produk;
        $produk = [];
        foreach ($ruang as $r) {
            foreach ($produkLama as $p) {
                if ($p['ruang_' . $r]) {
                    array_push($produk, $p);
                }
            }
        }
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

$seluruhFilter = [];
if (isset($_GET['koleksi'])) {
    $koleksiArr = explode(' ', $_GET['koleksi']);
    foreach ($koleksiArr as $k) {
        array_push($seluruhFilter, $k);
    }
}
if (isset($_GET['jenis'])) {
    $koleksiArr = explode(' ', $_GET['jenis']);
    foreach ($koleksiArr as $k) {
        array_push($seluruhFilter, $k);
    }
}
if (isset($_GET['ruang'])) {
    $koleksiArr = explode(' ', $_GET['ruang']);
    foreach ($koleksiArr as $k) {
        array_push($seluruhFilter, $k);
    }
}

?>
<style>
    .container-filter-desktop {
        width: 0px;
        overflow-x: hidden;
        transition: 0.4s;
    }

    .container-filter-desktop.show {
        width: 200px;
        transition: 0.4s;
    }

    .ganti-gap {
        gap: 0;
        transition: 0.4s;
    }

    .ganti-gap.geser {
        gap: 4em;
        transition: 0.4s;
    }
</style>
<div class="container d-flex justify-content-center">
    <div class="konten baris-ke-kolom ganti-gap">
        <div class="show-block-ke-hide container-filter-desktop" style="position: sticky; top: 50px; height:fit-content">
            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false"
                aria-controls="collapseExample">
                Koleksi
            </div>
            <div class="collapse py-2" id="collapseExample">
                <?php foreach ($navbar['koleksi'] as $ind_k => $k) { ?>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-1-<?= $ind_k ?>" class="inp-cbx filter" name="koleksi"
                            value="<?= str_replace(" ", "-", strtolower($k['nama'])); ?>"
                            <?= isset($_GET['koleksi']) ? (in_array(str_replace(" ", "-", strtolower($k['nama'])), explode(" ", $_GET['koleksi'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-1-<?= $ind_k ?>" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p><?= ucfirst($k['nama']); ?></p>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="false"
                aria-controls="collapseExample1">
                Jenis
            </div>
            <div class="collapse py-2" id="collapseExample1">
                <?php foreach ($navbar['jenis'] as $ind_j => $j) { ?>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-2-<?= $ind_j ?>" class="inp-cbx filter" name="jenis"
                            value="<?= str_replace(" ", "-", strtolower($j['nama'])); ?>"
                            <?= isset($_GET['jenis']) ? (in_array(str_replace(" ", "-", strtolower($j['nama'])), explode(" ", $_GET['jenis'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-2-<?= $ind_j ?>" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p><?= ucfirst($j['nama']); ?></p>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample4" aria-expanded="false"
                aria-controls="collapseExample4">
                Room Set
            </div>
            <div class="collapse py-2" id="collapseExample4">
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-10" class="inp-cbx filter" name="ruang" value="tamu"
                        <?= isset($_GET['ruang']) ? (in_array('tamu', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-10" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Ruang Tamu</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-11" class="inp-cbx filter" name="ruang" value="keluarga"
                        <?= isset($_GET['ruang']) ? (in_array('keluarga', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-11" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Ruang Keluarga</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="checkbox" id="checkbox-filter-12" class="inp-cbx filter" name="ruang" value="tidur"
                        <?= isset($_GET['ruang']) ? (in_array('tidur', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-12" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Ruang Tidur</p>
                    </label>
                </div>
            </div>

            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="false"
                aria-controls="collapseExample2">
                Harga
            </div>
            <div class="collapse py-2" id="collapseExample2">
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-5" class="inp-cbx filter" value="0"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("0", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-5" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                            </svg></span>
                        <p>
                            < Rp 5.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-6" class="inp-cbx filter" value="1"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("1", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-6" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 5.000.000 - Rp 10.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-7" class="inp-cbx filter" value="2"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("2", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-7" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 10.000.000 - Rp 15.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-8" class="inp-cbx filter" value="3"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("3", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-8" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>Rp 15.000.000 - 20.000.000</p>
                    </label>
                </div>
                <div class="checkbox-wrapper-46">
                    <input type="radio" name="harga" id="checkbox-filter-9" class="inp-cbx filter" value="4"
                        style="display: none;"
                        <?= isset($_GET['harga']) ? (in_array("4", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                    <label for="checkbox-filter-9" class="cbx"><span>
                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span>
                        <p>> 20.000.000</p>
                    </label>
                </div>
            </div>
            <div class="d-flex gap-3 mt-2">
                <a class="btn-filter btn-lonjong">Terapkan</a>
                <?php if (!empty($_GET['koleksi']) || !empty($_GET['jenis']) || !empty($_GET['harga'])) { ?>
                    <a class="btn-teks-aja" href="/product">Hapus Filter</a>
                <?php } ?>
            </div>
        </div>
        <div class="hide-ke-show-block">
            <nav style="--bs-breadcrumb-divider: '/'; position: relative; height: <?= count($seluruhFilter) > 0 ? '80px' : '36px'; ?>" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="/product">Produk</a></li>
                </ol>
                <div class="container-badge-filter mb-2" style="position: absolute;">
                    <?php foreach ($seluruhFilter as $ind_f => $f) {
                        if ($f) { ?>
                            <div class="item-badge-filter">
                                <p><?= ucwords(str_replace('-', ' ', $f)); ?></p>
                            </div>
                    <?php }
                    } ?>
                </div>
            </nav>

            <input type="checkbox" id="container-filter" class="d-none">
            <label for="container-filter" class="w-100">
                <div class="d-flex gap-2 align-items-center w-100 justify-content-center btn-lonjong">
                    <i class="material-icons">filter_list</i>
                    <p class="m-0">Filter</p>
                </div>
            </label>
            <div class="container-filter mt-2">
                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false"
                    aria-controls="collapseExample">
                    Koleksi
                </div>
                <div class="collapse py-2" id="collapseExample">
                    <?php foreach ($navbar['koleksi'] as $ind_k => $k) { ?>
                        <div class="checkbox-wrapper-46">
                            <input type="checkbox" id="checkbox-filter-1-<?= $ind_k ?>-hp" class="inp-cbx filter"
                                name="koleksi1" value="<?= str_replace(" ", "-", strtolower($k['nama'])); ?>"
                                <?= isset($_GET['koleksi']) ? (in_array(str_replace(" ", "-", strtolower($k['nama'])), explode(" ", $_GET['koleksi'])) ? 'checked' : '') : ''; ?> />
                            <label for="checkbox-filter-1-<?= $ind_k ?>-hp" class="cbx"><span>
                                    <svg viewBox="0 0 12 10" height="10px" width="12px">
                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                    </svg></span>
                                <p><?= ucfirst($k['nama']); ?></p>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample1" aria-expanded="false"
                    aria-controls="collapseExample1">
                    Jenis
                </div>
                <div class="collapse py-2" id="collapseExample1">
                    <?php foreach ($navbar['jenis'] as $ind_j => $j) { ?>
                        <div class="checkbox-wrapper-46">
                            <input type="checkbox" id="checkbox-filter-2-<?= $ind_j ?>-hp" class="inp-cbx filter"
                                name="jenis1" value="<?= str_replace(" ", "-", strtolower($j['nama'])); ?>"
                                <?= isset($_GET['jenis']) ? (in_array(str_replace(" ", "-", strtolower($j['nama'])), explode(" ", $_GET['jenis'])) ? 'checked' : '') : ''; ?> />
                            <label for="checkbox-filter-2-<?= $ind_j ?>-hp" class="cbx"><span>
                                    <svg viewBox="0 0 12 10" height="10px" width="12px">
                                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                    </svg></span>
                                <p><?= ucfirst($j['nama']); ?></p>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample4" aria-expanded="false"
                    aria-controls="collapseExample4">
                    Room Set
                </div>
                <div class="collapse py-2" id="collapseExample4">
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-10-hp" class="inp-cbx filter" name="ruang1"
                            value="tamu"
                            <?= isset($_GET['ruang']) ? (in_array('tamu', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-10-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Ruang Tamu</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-11-hp" class="inp-cbx filter" name="ruang1"
                            value="keluarga"
                            <?= isset($_GET['ruang']) ? (in_array('keluarga', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-11-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Ruang Keluarga</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="checkbox" id="checkbox-filter-12-hp" class="inp-cbx filter" name="ruang1"
                            value="tidur"
                            <?= isset($_GET['ruang']) ? (in_array('tidur', explode(" ", $_GET['ruang'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-12-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Ruang Tidur</p>
                        </label>
                    </div>
                </div>

                <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="false"
                    aria-controls="collapseExample2">
                    Harga
                </div>
                <div class="collapse py-2" id="collapseExample2">
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-5-hp" class="inp-cbx filter" value="0"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("0", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-5-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="3.5 6 4.5 9 10.5 3"></polyline>
                                </svg></span>
                            <p>
                                < Rp 5.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-6-hp" class="inp-cbx filter" value="1"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("1", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-6-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 5.000.000 - Rp 10.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-7-hp" class="inp-cbx filter" value="2"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("2", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-7-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 10.000.000 - Rp 15.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-8-hp" class="inp-cbx filter" value="3"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("3", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-8-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>Rp 15.000.000 - 20.000.000</p>
                        </label>
                    </div>
                    <div class="checkbox-wrapper-46">
                        <input type="radio" name="harga1" id="checkbox-filter-9-hp" class="inp-cbx filter" value="4"
                            style="display: none;"
                            <?= isset($_GET['harga']) ? (in_array("4", explode("-", $_GET['harga'])) ? 'checked' : '') : ''; ?> />
                        <label for="checkbox-filter-9-hp" class="cbx"><span>
                                <svg viewBox="0 0 12 10" height="10px" width="12px">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span>
                            <p>> 20.000.000</p>
                        </label>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-2">
                    <a class="btn-filter btn-lonjong">Terapkan</a>
                    <?php if (!empty($_GET['koleksi']) || !empty($_GET['jenis']) || !empty($_GET['harga'])) { ?>
                        <a class="btn-teks-aja" href="/product">Hapus Filter</a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div style="flex: 1;">
            <!-- <div style="position: absolute;" class="w-100"> -->
            <?php if (session()->get('role') == '1') { ?>
                <div class="d-flex justify-content-between">
                    <h1 class="teks-sedang">List Product</h1>
                    <a href="/admin/addproduct" class="btn-default-merah">Tambah Produk</a>
                </div>
            <?php } ?>
            <div class="d-flex gap-4 align-items-stretch mb-3">
                <div style="width: fit-content;" class="show-flex-ke-hide justify-content-center align-items-center">
                    <button class="btn btn-sm btn-outline-dark d-flex align-items-center" onclick="showFilterDesktop()">
                        <i class="material-icons">chevron_left</i>
                        <p class="m-0" style="line-height: 7px;">Filter</p>
                    </button>
                </div>
                <div style="width: 1px; background-color:darkgrey;" class="show-block-ke-hide"></div>
                <div class="d-flex align-items-center">
                    <nav style="--bs-breadcrumb-divider: '/'; position: relative; <?= (isset($_GET['koleksi']) || isset($_GET['jenis']) || isset($_GET['ruang'])) ? 'height: 60px' : ''; ?>"
                        aria-label="breadcrumb" class="show-block-ke-hide">
                        <ol class="breadcrumb <?= count($seluruhFilter) > 0 ? 'mb-2' : 'm-0'; ?>">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/product">Produk</a></li>
                            <!-- <li class="breadcrumb-item">Meja TV</li> -->
                        </ol>
                        <div class="container-badge-filter" style="position: absolute;">
                            <?php foreach ($seluruhFilter as $ind_f => $f) {
                                if ($f) { ?>
                                    <div class="item-badge-filter">
                                        <p><?= ucwords(str_replace('-', ' ', $f)); ?></p>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                    </nav>
                </div>
            </div>
            <?php if (isset($find)) { ?>
                <p>Anda mencari "<?= $find ?>"</p>
            <?php } ?>
            <div class="container-card1">
                <?php foreach ($produk as $ind_p => $p) { ?>
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
                                <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic" id="img<?= $ind_p ?>" src="/viewpic/<?= $p['id']; ?>" alt="">
                                <img class="<?= $p['gambar_hover'] ? '' : 'nonhover'; ?> img-pic-hover" id="img<?= $ind_p ?>" src="/viewpichover/<?= $p['id']; ?>" alt="">
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
                                const varian<?= $ind_p ?>Elm = document.querySelectorAll('input[name="varian<?= $ind_p ?>"]');
                                varian<?= $ind_p ?>Elm.forEach(elm => {
                                    elm.addEventListener('change', (e) => {
                                        console.log(e.target.value)
                                        const img<?= $ind_p ?>Elm = document.getElementById("img<?= $ind_p ?>");
                                        img<?= $ind_p ?>Elm.src =
                                            "/viewvar/<?= $p['id']; ?>/" + e.target.value.split("-")[0].split(
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
            <div class="container-pag">
                <?php if ($pag > 1) { ?>
                    <a class="item-pag"
                        href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $pag - 1; ?>"><i
                            class="material-icons">chevron_left</i></a>
                <?php } ?>
                <?php for ($i = 0; $i < $hitungPag; $i++) { ?>
                    <a class="item-pag <?= $pag == ($i + 1) ? 'active' : ''; ?>"
                        href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $i + 1; ?>"><?= $i + 1; ?></a>
                <?php } ?>
                <?php if ($pag < $hitungPag) { ?>
                    <a class="item-pag"
                        href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $pag + 1; ?>"><i
                            class="material-icons">chevron_right</i></a>
                <?php } ?>
            </div>
            <!-- </div> -->
        </div>
    </div>
</div>
<script>
    const btnFilterElm = document.querySelectorAll('.btn-filter');
    const filterKoleksiElm = document.querySelectorAll('input[name="koleksi"]')
    const filterJenisElm = document.querySelectorAll('input[name="jenis"]')
    const filterHargaElm = document.querySelectorAll('input[name="harga"]')
    const filterRuangElm = document.querySelectorAll('input[name="ruang"]')
    const filterKoleksi1Elm = document.querySelectorAll('input[name="koleksi1"]')
    const filterJenis1Elm = document.querySelectorAll('input[name="jenis1"]')
    const filterHarga1Elm = document.querySelectorAll('input[name="harga1"]')
    const filterRuang1Elm = document.querySelectorAll('input[name="ruang1"]')
    let urlCur = '/product';
    console.log(urlCur)
    let koleksiParam = ''
    let jenisParam = ''
    let hargaParam = ''
    let ruangParam = ''

    let showFilter = false;

    function showFilterDesktop() {
        const containerFilterDesktopElm = document.querySelector('.container-filter-desktop');
        const gantiGapElm = document.querySelector('.ganti-gap')
        if (showFilter) {
            containerFilterDesktopElm.classList.remove('show')
            gantiGapElm.classList.remove('geser')
        } else {
            containerFilterDesktopElm.classList.add('show')
            gantiGapElm.classList.add('geser')
        }
        showFilter = !showFilter;
    }

    btnFilterElm.forEach(elm => {
        elm.addEventListener('click', () => {
            koleksiParam = ''
            jenisParam = ''
            hargaParam = ''
            ruangParam = ''
            console.log(window.innerWidth)
            if (window.innerWidth > 600) {
                filterKoleksiElm.forEach((koleksiElm) => {
                    if (koleksiElm.checked) {
                        if (koleksiParam == '')
                            koleksiParam += koleksiElm.value
                        else
                            koleksiParam += '+' + koleksiElm.value
                    }
                });
                filterJenisElm.forEach((jenisElm) => {
                    if (jenisElm.checked) {
                        if (jenisParam == '')
                            jenisParam += jenisElm.value
                        else
                            jenisParam += '+' + jenisElm.value
                    }
                });
                filterHargaElm.forEach((hargaElm) => {
                    if (hargaElm.checked) {
                        hargaParam = hargaElm.value
                    }
                });
                filterRuangElm.forEach((ruangElm) => {
                    if (ruangElm.checked) {
                        if (ruangParam == '')
                            ruangParam += ruangElm.value
                        else
                            ruangParam += '+' + ruangElm.value
                    }
                });
            } else {
                filterKoleksi1Elm.forEach((koleksiElm) => {
                    if (koleksiElm.checked) {
                        if (koleksiParam == '')
                            koleksiParam += koleksiElm.value
                        else
                            koleksiParam += '+' + koleksiElm.value
                    }
                });
                filterJenis1Elm.forEach((jenisElm) => {
                    if (jenisElm.checked) {
                        if (jenisParam == '')
                            jenisParam += jenisElm.value
                        else
                            jenisParam += '+' + jenisElm.value
                    }
                });
                filterHarga1Elm.forEach((hargaElm) => {
                    if (hargaElm.checked) {
                        hargaParam = hargaElm.value
                    }
                });
                filterRuang1Elm.forEach((ruangElm) => {
                    if (ruangElm.checked) {
                        if (ruangParam == '')
                            ruangParam += ruangElm.value
                        else
                            ruangParam += '+' + ruangElm.value
                    }
                });
            }

            // console.log(urlCur + '?koleksi=' + koleksiParam + '&jenis=' + jenisParam + '&harga=' + hargaParam)
            let paramArr = [];
            if (koleksiParam != '') paramArr.push('koleksi=' + koleksiParam)
            if (jenisParam != '') paramArr.push('jenis=' + jenisParam)
            if (hargaParam != '') paramArr.push('harga=' + hargaParam)
            if (ruangParam != '') paramArr.push('ruang=' + ruangParam)
            window.location.href = urlCur + '?' + paramArr.join('&')
        })
    })
    // btnFilterElm.forEach(element => {
    //     element.addEventListener('click', () => {
    //         const filterInputElm = document.querySelectorAll(".filter:checked");
    //         let arrFilter = [];
    //         let valueFilter = [];
    //         filterInputElm.forEach(filterinp => {
    //             // console.log(filterinp.value);
    //             if (!arrFilter.includes(filterinp.value.split("-")[0])) {
    //                 arrFilter.push(filterinp.value.split("-")[0]);
    //                 valueFilter.push(filterinp.value.split("-")[1]);
    //             } else {
    //                 valueFilter[valueFilter.length - 1] += '-' + filterinp.value.split("-")[1]
    //             }
    //         });
    //         console.log(arrFilter)
    //         console.log(valueFilter)
    //         const pathUrl = window.location.pathname
    //         let strUrl = pathUrl + '?'
    //         arrFilter.forEach((fil, ind_fil) => {
    //             strUrl += "&" + fil + "=" + valueFilter[ind_fil]
    //         })
    //         console.log(strUrl)
    //         window.location.href = strUrl
    //     })
    // });

    function pergiKeProduct(id_produk) {
        window.location.href = "/product/" + id_produk
    }
</script>

<?= $this->endSection(); ?>