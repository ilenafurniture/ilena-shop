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

//nggabungin produk sejenis
$produkLama = $produk;
$produk = [];
$arrNamaProduk = [];
foreach ($produkLama as $p) {
    if (!in_array(strtolower($p['nama']), $arrNamaProduk)) {
        array_push($produk, $p);
        array_push($arrNamaProduk, strtolower($p['nama']));
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
        <div class="show-block-ke-hide container-filter-desktop"
            style="position: sticky; top: 50px; height:fit-content">
            <div class="item-filter" data-bs-toggle="collapse" href="#collapseExample" aria-expanded="false"
                aria-controls="collapseExample">
                Koleksi
            </div>
            <div class="collapse py-2" id="collapseExample">
                <?php foreach ($koleksiJenis['koleksi'] as $ind_k => $k) { ?>
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
                <?php foreach ($koleksiJenis['jenis'] as $ind_j => $j) { ?>
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
            <nav style="--bs-breadcrumb-divider: '/'; position: relative; height: <?= count($seluruhFilter) > 0 ? '80px' : '36px'; ?>"
                aria-label="breadcrumb">
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
                    <?php foreach ($koleksiJenis['koleksi'] as $ind_k => $k) { ?>
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
                    <?php foreach ($koleksiJenis['jenis'] as $ind_j => $j) { ?>
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
                                <?php if (session()->get('role') == '1') { ?>
                                    <form action="/admin/editproduct/<?= $p['id'] ?>" method="post">
                                        <button type="submit" class="card1-btn-img"><i class="material-icons">edit</i></button>
                                    </form>
                                <?php }
                                if (in_array($p['id'], $wishlist)) { ?>
                                    <form action="/delwishlist/<?= $p['id'] ?>" method="post">
                                        <button type="submit" class="card1-btn-img"><i
                                                class="material-icons">bookmark</i></button>
                                    </form>
                                <?php } else { ?>
                                    <form action="/addwishlist/<?= $p['id'] ?>" method="post">
                                        <button type="submit" class="card1-btn-img"><i
                                                class="material-icons">bookmark_border</i></button>
                                    </form>
                                <?php } ?>
                                <form method="post" id="card<?= $ind_p ?>"
                                    action="/addcart/<?= $p['id'] ?>/<?= json_decode($p['varian'], true)[0]['nama'] ?>/1"
                                    type="submit"><button class="card1-btn-img"><i class="material-icons">shopping_cart</i>
                                    </button>
                                </form>
                            </div>
                            <a href="/product/<?= str_replace(' ', '-', $p['nama']); ?>" class="gambar">
                                <img class="img-pic" id="img<?= $ind_p ?>"
                                    src="<?= base_url('img/barang/300/' . $p['id'] . '.webp') ?>" alt="">
                                <img class="img-pic-hover"
                                    id="img<?= $ind_p ?>" src="<?= base_url('img/barang/hover/' . $p['id'] . '.webp') ?>"
                                    alt="">
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
                                            "<?= base_url('img/barang/1000/' . $p['id'] . '-') ?>" + e.target
                                            .value.split("-")[0].split(
                                                ",")[
                                                0] + '.webp';

                                        btnKeranjang<?= $ind_p ?>Elm.action = "/addcart/<?= $p['id'] ?>/" + e
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
            <!-- <div class="container-pag">
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
            </div> -->
            <div class="container-pag">
                <?php
                $start = max(1, $pag - 2); // Mulai dari dua halaman sebelumnya
                $end = min($hitungPag, $pag + 2); // Selesai di dua halaman berikutnya

                // Menampilkan tombol 'Prev'
                if ($pag > 1) { ?>
                    <a class="item-pag"
                        href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $pag - 1; ?>"><i
                            class="material-icons">chevron_left</i></a>
                <?php } ?>

                <?php
                // Menampilkan angka pagination dari $start sampai $end
                for ($i = $start; $i <= $end; $i++) { ?>
                    <a class="item-pag <?= $pag == $i ? 'active' : ''; ?>"
                        href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $i; ?>"><?= $i; ?></a>
                <?php } ?>

                <?php
                // Menampilkan tombol 'Next'
                if ($pag < $hitungPag) { ?>
                    <a class="item-pag"
                        href="/product?koleksi=<?= isset($_GET['koleksi']) ? $_GET['koleksi'] : ''; ?>&jenis=<?= isset($_GET['jenis']) ? $_GET['jenis'] : ''; ?>&harga=<?= isset($_GET['harga']) ? $_GET['harga'] : ''; ?>&ruang=<?= isset($_GET['ruang']) ? $_GET['ruang'] : ''; ?>&pag=<?= $pag + 1; ?>"><i
                            class="material-icons">chevron_right</i></a>
                <?php } ?>
            </div>

            <!-- </div> -->
            <!-- Artikel kategori -->
            <?php if (isset($_GET['jenis'])) {
                switch ($_GET['jenis']) {
                    case 'bookshelf': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Simpan Buku Lebih Rapi di Bookshelf</h5>
                                <p class="text-justify">Bookshelf adalah salah satu furniture yang bisa membuat ruangan jadi lebih
                                    menarik dan membuat koleksi buku-buku tertata rapi dalam satu tempat pada sebuah bookcase.
                                    Umumnya, desain rak buku mirip dengan lemari, hanya saja biasanya tidak memiliki penutup atau
                                    kuncian pintu dan memiliki banyak sekat-sekat. Bentuknya yang terbuka inilah, yang bisa menjadi
                                    space menarik untuk memajang koleksi buku dengan lebih aman dan rapi agar tidak mudah rusak.
                                    Selain itu, desain ini juga akan memudahkan saat hendak mengambil atau menyimpan kembali buku di
                                    rak.</p>

                                <h5>Macam-Macam Model Bookshelves</h5>
                                <p class="text-justify">Sekarang ini, rak buku memiliki banyak model yang menarik dengan
                                    berbagai aksen unik yang menjadi daya tarik dari furniture satu ini. Beragam model ini juga
                                    dipengaruhi dengan kebutuhan konsumen yang beragam pula. Preferensi mulai dari model, bahan, dan
                                    ukuran ini membuat para pengrajin seperti Ilena Furniture sebagai produsen semangat untuk
                                    mengembangkan produk rak buku. salah satu produknya adalah bookshelf Ilena Cabana yang memiliki
                                    4 tingkat space rak buku plus sebuah laci di bagian bawahnya. Selain modelnya yang stylish,
                                    koleksi bookcase Ilena ini juga terbuat dari bahan-bahan berkualitas yang kokoh dan awet lama.
                                    Bahan kayu mahoni dengan dikombinasikan MDF grade A serta laminasi veneer jati yang terkenal
                                    dengan serat kayu khasnya yang unik. Pertimbangan inilah yang menjadikan furniture unggulan yang
                                    dapat digunakan sebagaimana fungsinya dalam jangka waktu yang lama.
                                <p>
                                <h5>Cara Perawatan Yang Mudah</h5>
                                <p class="text-justify mb-1">Beberapa tips & trik menjaga rak buku agar tetap bersih dan rapi ini
                                    bisa ditiru secara
                                    mandiri
                                    tanpa bantuan profesional:</p>
                                <ol>
                                    <li>Bersihkan secara rutin dan berkala</li>
                                    <p class="text-justify mb-1">Sama seperti pada furniture lain pada umumnya, bookshelves juga
                                        harus mendapat perhatian
                                        dengan cara dibersihkan secara rutin dan berkala. Hal ini dikarenakan tidak menutup
                                        kemungkinan jika bookcase kotor karena debu yang menutup sehingga bisa mengotori buku
                                        koleksi dan mengurangi estetika di bookcase itu sendiri. Jadwalkan secara rutin pembersihan,
                                        semisal seminggu sekali untuk hasil yang optimal.</p>
                                    <li>Kelompokkan Jenis Buku</li>
                                    <p class="text-justify mb-1">Jika sudah bisa memastikan rak buku bersih, kini saatnya untuk
                                        menata koleksi buku yang dimiliki. Susun buku dengan rapih dan berurutan sesuai dengan abjad
                                        dari penulis, judul, atau cara lainnya untuk mempermudah saat sedang mencari dan menaruh
                                        kembali buku pada tempatnya.</p>
                                    <li>Pakai Penyangga Buku</li>
                                    <p class="text-justify mb-1">Jika dirasa rak buku masih kurang membuat buku-buku terlihat rapi,
                                        maka cara mudahnya adalah
                                        dengan menambahkan penyangga buku untuk memisahkan koleksi buku dan memastikan kolekis buku
                                        tersimpan dengan tegak dan terlihat lebih rapi. Penyangga buku ini juga bisa dijadikan sekat
                                        tambahan dan space kosong sampingnya bisa dimanfaatkan sebagai tempat payangan foto, atau
                                        barang-barang kesayangan seperti akrilik atau lainnya.</p>
                                </ol>
                                <p class="text-justify">Itulah beberapa cara merawat rak buku yang sederhana dan dapat ditiru
                                    secara mandiri di rumah.
                                    Dengan ini, dapat memastikan bookcase tetap terjaga kebersihan dan kerapihannya sehingga dapat
                                    menambah estetika ruang.
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'dresser-3-drawer dresser-4-drawer dresser-5-drawer dresser-6-drawer dresser-tall-cabinet': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Kenapa Perlu Punya Drawer?</h5>
                                <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                                    berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                                    opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                                    bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                                    barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                                    memiliki drawer dalam hunian:</p>
                                <ol>
                                    <li>Tempat Praktis Menyimpan Barang</li>
                                    <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                                        menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                                        untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                                        Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                                        ada tempat khususnya sendiri.</p>
                                    <li>Hemat Ruang</li>
                                    <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                                        menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                                        utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                                    <li>Memaksimalkan Keindahan Ruang</li>
                                    <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                                        bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                                        ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                                        untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                                        kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                                    <li>Furniture Multifungsi</li>
                                    <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                                        membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                                        dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                                        dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                                        jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                                        nyamuk atau serangga lain di dalamnya.</p>
                                    <li>Mudah Diakses</li>
                                    <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                                        dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                                        Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                                        perlu banyak repot.</p>
                                </ol>
                                <h5>Temukan Berbagai Model Dresser Stylish</h5>
                                <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                                    hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                                    cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                                    material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                                    menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                                    memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                                    Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                                    dari member Kami!
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'dresser-3-drawer': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Kenapa Perlu Punya Drawer?</h5>
                                <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                                    berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                                    opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                                    bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                                    barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                                    memiliki drawer dalam hunian:</p>
                                <ol>
                                    <li>Tempat Praktis Menyimpan Barang</li>
                                    <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                                        menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                                        untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                                        Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                                        ada tempat khususnya sendiri.</p>
                                    <li>Hemat Ruang</li>
                                    <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                                        menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                                        utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                                    <li>Memaksimalkan Keindahan Ruang</li>
                                    <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                                        bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                                        ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                                        untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                                        kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                                    <li>Furniture Multifungsi</li>
                                    <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                                        membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                                        dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                                        dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                                        jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                                        nyamuk atau serangga lain di dalamnya.</p>
                                    <li>Mudah Diakses</li>
                                    <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                                        dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                                        Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                                        perlu banyak repot.</p>
                                </ol>
                                <h5>Temukan Berbagai Model Dresser Stylish</h5>
                                <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                                    hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                                    cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                                    material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                                    menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                                    memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                                    Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                                    dari member Kami!
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'dresser-4-drawer': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Kenapa Perlu Punya Drawer?</h5>
                                <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                                    berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                                    opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                                    bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                                    barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                                    memiliki drawer dalam hunian:</p>
                                <ol>
                                    <li>Tempat Praktis Menyimpan Barang</li>
                                    <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                                        menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                                        untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                                        Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                                        ada tempat khususnya sendiri.</p>
                                    <li>Hemat Ruang</li>
                                    <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                                        menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                                        utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                                    <li>Memaksimalkan Keindahan Ruang</li>
                                    <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                                        bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                                        ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                                        untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                                        kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                                    <li>Furniture Multifungsi</li>
                                    <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                                        membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                                        dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                                        dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                                        jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                                        nyamuk atau serangga lain di dalamnya.</p>
                                    <li>Mudah Diakses</li>
                                    <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                                        dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                                        Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                                        perlu banyak repot.</p>
                                </ol>
                                <h5>Temukan Berbagai Model Dresser Stylish</h5>
                                <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                                    hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                                    cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                                    material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                                    menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                                    memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                                    Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                                    dari member Kami!
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'dresser-5-drawer': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Kenapa Perlu Punya Drawer?</h5>
                                <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                                    berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                                    opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                                    bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                                    barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                                    memiliki drawer dalam hunian:</p>
                                <ol>
                                    <li>Tempat Praktis Menyimpan Barang</li>
                                    <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                                        menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                                        untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                                        Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                                        ada tempat khususnya sendiri.</p>
                                    <li>Hemat Ruang</li>
                                    <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                                        menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                                        utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                                    <li>Memaksimalkan Keindahan Ruang</li>
                                    <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                                        bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                                        ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                                        untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                                        kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                                    <li>Furniture Multifungsi</li>
                                    <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                                        membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                                        dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                                        dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                                        jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                                        nyamuk atau serangga lain di dalamnya.</p>
                                    <li>Mudah Diakses</li>
                                    <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                                        dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                                        Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                                        perlu banyak repot.</p>
                                </ol>
                                <h5>Temukan Berbagai Model Dresser Stylish</h5>
                                <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                                    hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                                    cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                                    material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                                    menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                                    memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                                    Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                                    dari member Kami!
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'dresser-6-drawer': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Kenapa Perlu Punya Drawer?</h5>
                                <p class="text-justify">Pada dasarnya lemari drawer atau lemari laci merupakan salah satu furniture
                                    berdesain sederhana yang lebih mementingkan fungsionalnya. Meskipun kerap dijadikan sebag ai
                                    opsi kesekian, tapi yang perlu dipahami adalah jenis furniture ini punya banyak manfaat yang
                                    bisa membuat jadi lebih rapi dan terorganisir untuk mempermudah saat sedang mencari atau menaruh
                                    barang. Agar lebih jelasnya, berikut ini adalah alasan kenapa perlu mempertimbangkan untuk
                                    memiliki drawer dalam hunian:</p>
                                <ol>
                                    <li>Tempat Praktis Menyimpan Barang</li>
                                    <p class="text-justify mb-1">Drawer didesain dengan model laci bertingkat untuk memudahkan dalam
                                        menyimpan barang sesuai dengan kebutuhan dan kategori. Di satu furniture ini, bisa digunakan
                                        untuk menyimpan barang-barang seperti aksesoris, pakaian, dokumen, dan lain sebagainya.
                                        Dengan begitu, tidak perlu lagi repot kesusahan mencari barang yang terselip karena sudah
                                        ada tempat khususnya sendiri.</p>
                                    <li>Hemat Ruang</li>
                                    <p class="text-justify mb-1">Dengan desainnya yang vertikal, furniture dresser akan lebih
                                        menghemat ruang. Sehingga cocok ditempatkan pada ruangan terbatas tanpa menghilangkan fungsi
                                        utamanya sebagai tempat penyimpanan dan juga tidak membuat ruangan terasa lebih sempit.</p>
                                    <li>Memaksimalkan Keindahan Ruang</li>
                                    <p class="text-justify mb-1">Ada banyak model lemari laci dengan berbagai pilihan warna dan
                                        bahan yang membuat tampilannya memiliki keunikan tersendiri masing-masing. Dengan begini,
                                        ada beragam opsi yang bisa dipilih sesuai dengan kebutuhan dan preferensi masing-masing
                                        untuk membeli lemari laci. Maka dari itu, ada baiknya jika memilih furniture berdasarkan
                                        kebutuhan dan tema ruangan agar selaras serta menambah estetika dalam ruang itu sendiri.</p>
                                    <li>Furniture Multifungsi</li>
                                    <p class="text-justify mb-1">salah satu keunggulan dari furniture ini adalah fleksibilitas yang
                                        membuatnya cocok ditempatkan pada berbagai ruangan mulai dari kamar tidur, ruang keluarga,
                                        dan lain sebagainya. Fungsinya pun beragam pula, mulai dari menyimpan pakaian, aksesoris,
                                        dokumen penting, mainan anak, dan masih banyak lagi. Tapi perlu diingat ya, akan lebih baik
                                        jika jenis penyimpanannya tertata dengan rapi dan terorganisir agar tidak menjadi sarang
                                        nyamuk atau serangga lain di dalamnya.</p>
                                    <li>Mudah Diakses</li>
                                    <p class="text-justify mb-1">Tak seperti lemari pada umumnya yang terkadang merepotkan saat
                                        dibuka karena jenis pintunya yang bisa bermasalah, tapi dengan drawer bisa lebih mudah.
                                        Cukup dengan menariknya, Anda bisa langsung mengakses dengan cepat dan kapan saja tanpa
                                        perlu banyak repot.</p>
                                </ol>
                                <h5>Temukan Berbagai Model Dresser Stylish</h5>
                                <p class="text-justify">Bingung cari lemari laci dengan model elegan yang stylish untuk mengisi
                                    hunian? Sekarang tidak lagi karena ada Ilena Furniture yang memberikan sentuhan modern yang
                                    cocok untuk dipadukan dengan tema scandinavian, minimalis, hingga industrial. Dibuat dengan
                                    material premium pilihan yang dapat digunakan secara maksimal hingga waktu lama. Tidak sekedar
                                    menjadi tempat penyimpanan saja untuk membuat kesan rapi dan terorganisir, tetapi juga
                                    memaksimalkan keindahan interior ruang sehingga membuat nyaman setiap penghuni di dalamnya.
                                    Temukan produk dresser populer di tahun ini dan dapatkan harga terbaiknya dengan menjadi bagian
                                    dari member Kami!
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'king-bed queen-bed single-bed': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Manfaat Tidur Pakai Divan</h5>
                                <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                                    kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                                <ol>
                                    <li>Memperlancar Sirkulasi udara</li>
                                    <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                                        baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                                        disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                                        menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                                    <li>Lebih Baik bagi Kesehatan</li>
                                    <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                                        jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                                        diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                                        kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                                    </p>
                                    <li>Sebagai Tempat Penyimpanan Tambahan</li>
                                    <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                                        Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                                        hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                                    <li>Memaksimalkan Estetika Kamar Tidur</li>
                                    <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                                        estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                                        selaras dengan tema dalam kamar tidur.</p>
                                </ol>
                                <h5>Rekomendasi Ukuran Bed</h5>
                                <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                                    salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                                </p>
                                <ol>
                                    <li>Single Size (90 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                                        kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                                        tempat.</p>
                                    <li>Double Size (120 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                                        bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                                        terbatas.
                                    </p>
                                    <li>Queen Size (160 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                                        mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                                        space untuk bergerak leluasa.</p>
                                    <li>King Size (180 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                                        dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                                        lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                                    <li>Super King Size (200 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                                        tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                                        luas paling besar diantara kamar tidur lain.</p>
                                </ol>
                                <h5>Belanja Ranjang Tidur Minimalis</h5>
                                <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                                    minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                                    mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                                    biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                                    ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                                    estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                                    di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                                    website ini.
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'king-bed': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Manfaat Tidur Pakai Divan</h5>
                                <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                                    kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                                <ol>
                                    <li>Memperlancar Sirkulasi udara</li>
                                    <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                                        baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                                        disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                                        menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                                    <li>Lebih Baik bagi Kesehatan</li>
                                    <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                                        jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                                        diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                                        kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                                    </p>
                                    <li>Sebagai Tempat Penyimpanan Tambahan</li>
                                    <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                                        Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                                        hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                                    <li>Memaksimalkan Estetika Kamar Tidur</li>
                                    <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                                        estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                                        selaras dengan tema dalam kamar tidur.</p>
                                </ol>
                                <h5>Rekomendasi Ukuran Bed</h5>
                                <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                                    salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                                </p>
                                <ol>
                                    <li>Single Size (90 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                                        kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                                        tempat.</p>
                                    <li>Double Size (120 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                                        bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                                        terbatas.
                                    </p>
                                    <li>Queen Size (160 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                                        mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                                        space untuk bergerak leluasa.</p>
                                    <li>King Size (180 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                                        dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                                        lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                                    <li>Super King Size (200 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                                        tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                                        luas paling besar diantara kamar tidur lain.</p>
                                </ol>
                                <h5>Belanja Ranjang Tidur Minimalis</h5>
                                <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                                    minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                                    mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                                    biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                                    ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                                    estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                                    di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                                    website ini.
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'queen-bed': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Manfaat Tidur Pakai Divan</h5>
                                <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                                    kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                                <ol>
                                    <li>Memperlancar Sirkulasi udara</li>
                                    <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                                        baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                                        disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                                        menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                                    <li>Lebih Baik bagi Kesehatan</li>
                                    <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                                        jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                                        diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                                        kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                                    </p>
                                    <li>Sebagai Tempat Penyimpanan Tambahan</li>
                                    <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                                        Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                                        hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                                    <li>Memaksimalkan Estetika Kamar Tidur</li>
                                    <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                                        estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                                        selaras dengan tema dalam kamar tidur.</p>
                                </ol>
                                <h5>Rekomendasi Ukuran Bed</h5>
                                <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                                    salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                                </p>
                                <ol>
                                    <li>Single Size (90 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                                        kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                                        tempat.</p>
                                    <li>Double Size (120 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                                        bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                                        terbatas.
                                    </p>
                                    <li>Queen Size (160 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                                        mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                                        space untuk bergerak leluasa.</p>
                                    <li>King Size (180 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                                        dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                                        lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                                    <li>Super King Size (200 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                                        tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                                        luas paling besar diantara kamar tidur lain.</p>
                                </ol>
                                <h5>Belanja Ranjang Tidur Minimalis</h5>
                                <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                                    minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                                    mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                                    biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                                    ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                                    estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                                    di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                                    website ini.
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'single-bed': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Manfaat Tidur Pakai Divan</h5>
                                <p class="text-justify">Dipan adalah sebuah rangka tempat tidur yang difungsikan untuk menopang
                                    kasur. penambahan divan sebagai penopan kasur memiliki manfaat yaitu sebagai berikut ini::</p>
                                <ol>
                                    <li>Memperlancar Sirkulasi udara</li>
                                    <p class="text-justify mb-1">Bagian bawah dan sekitar kasur akan memperoleh sirkulasi udara yang
                                        baik. Hal ini dimaksudkan untuk menghambat pertumbuhan jamur dan bakteri pada kasur yang
                                        disebabkan pada lembabnya suhu ruang. Selain itu juga agar kasu terhindar dari debu yang
                                        menumpuk jika langsung diletakkan di lantai tanpa adanya rangka ranjang.</p>
                                    <li>Lebih Baik bagi Kesehatan</li>
                                    <p class="text-justify mb-1">Sirkulasi udara yang baik memungkinkan untuk mengurangi tumbuhnya
                                        jamur dan kutu di kasur. Hal ini pastinya berdampak baik pada kesehatan. Apalagi jika sudah
                                        diketahui memiliki alergi atau permasalahan pada pernapasan. Selain itu, tidur dengan
                                        kondisi dipan yang dingin juga bisa mencegah penyakit gangguan paru-paru dan hipotermia.
                                    </p>
                                    <li>Sebagai Tempat Penyimpanan Tambahan</li>
                                    <p class="text-justify mb-1">Rangka ini juga bisa digunakan sebagai tempat penyimpanan tambahan.
                                        Berikan beberapa laci yang bisa digunakan untuk menyimpan beberapa barang dari ukuran besar
                                        hingga kecil di bawah tempat tidur dengan lebih rapi</p>
                                    <li>Memaksimalkan Estetika Kamar Tidur</li>
                                    <p class="text-justify mb-1">Penggunaan divan juga bisa menjadi opsi tepat untuk menambah
                                        estetika desain dalam kamar tidur. Terlebih jika ranjang ini memiliki model yang cantik dan
                                        selaras dengan tema dalam kamar tidur.</p>
                                </ol>
                                <h5>Rekomendasi Ukuran Bed</h5>
                                <p class="text-justify">Terdapat beberapa macam istilah umum untuk menyebut ukuran bed. Agar tidak
                                    salah beli, berikut ini beberapa ukurannya beserta istilahnya:
                                </p>
                                <ol>
                                    <li>Single Size (90 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran ini biasanya cocok digunakan untuk kamar tidur anak atau
                                        kamar tidur tamu. Size ini cocok untuk satu orang agar nyaman bergerak tanpa memakan banyak
                                        tempat.</p>
                                    <li>Double Size (120 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran double size memiliki ruang gerak yang lebih luas dari single
                                        bed. Cukup untuk 1 hingga 2 orang. Ukuran ini pas untuk pasangan yang memiliki space ruang
                                        terbatas.
                                    </p>
                                    <li>Queen Size (160 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran queen size merupakan pilihan populer untuk pasangan yang
                                        mempunyai space cukup luas. ukuran ini terbilang pas untuk dua orang, bahkan memungkinkan
                                        space untuk bergerak leluasa.</p>
                                    <li>King Size (180 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran king size ini biasanya cocok untuk menampilkan kesan elegan
                                        dan mewah di kamar tidur. Dengan ukuran yang luas ini, membuat leluasa tidur untuk 2 orang
                                        lebih nyenyak dan memiliki tidur yang berkualitas.</p>
                                    <li>Super King Size (200 x 200 cm)</li>
                                    <p class="text-justify mb-1">Ukuran paling besar adalah super king size yang memberikan ruang
                                        tidur sangat luas. Ukuran ini tentunya sangat ideal untuk kamar tidur utama yang memiliki
                                        luas paling besar diantara kamar tidur lain.</p>
                                </ol>
                                <h5>Belanja Ranjang Tidur Minimalis</h5>
                                <p class="text-justify">Ada banyak desain ranjang yang bisa dipilih dan dibeli. Mulai dari model
                                    minimalism modern, klasik, hingga industrial. Sesuaikan preferensi masing-masing untuk
                                    mendapatkan dipan yang terbaik. Perhatikan juga material bahan yang digunakan. Ranjang kayu
                                    biasanya lebih banyak diminati karena desain dan ketahannya. Terlebih jika pemilihan jatuh pada
                                    ranjang jati yang selalu banyak diincar karena sudah teruji ketahanan dan serat kayunya yang
                                    estetik. Nikmati suasana nyaman, tidur ternyaman dengan kualitas premium dengan membeli produk
                                    di Ilena Furniture. Dapatkan berbagai penawaran terbaik saat berbelanja online melalui situs
                                    website ini.
                                </p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'coffee-table': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Mengapa Perlu Miliki Coffee Table?</h5>
                                <p class="text-justify">Apabila menyebut kata “coffee table” maka yang pertama terbesit adalah meja
                                    untuk meletakkan cangkir kopi. Meskipun tidak salah, akan tetapi coffee table memiliki fungsi
                                    yang lebih daripada hanya sebagai tempat meletakkan kopi saja. Pada dasarnya, meja ini biasa
                                    ditempatkan di area ruang tamu sehingga bisa disebut juga sebagai meja tamu, meja sofa, meja
                                    tatami, meja lounge, living room table, tea table dan masih banyak lagi sebutan lainnya.</p>
                                <p class="text-justify">Center table pada umumnya digunakan untuk menambahkan rasa nyaman saat duduk
                                    di sofa untuk menunggu sesuatu atau bercengkrama dengan keluarga, sahabat, hingga kolega. Di
                                    meja lounge ini bisa juga untuk meletakkan barang selain kopi seperti camilan, buku, remote TV,
                                    vas bunga, dan aksesoris lainnya.</p>
                                <h5>Tips Memilih Meja Kopi yang Tetap dengan Kebutuhan</h5>
                                <p class="text-justify">Karena fungsinya yang mengedepankan kenyamanan, maka beberapa hal ini perlu
                                    dipertimbangkan agar mendapatkan ukuran dan desain meja lounge yang sesuai dengan kebutuhan.</p>
                                <ol>
                                    <li>Pilih Meja Tamu Sesuai dengan Fungsinya</li>
                                    <p class="text-justify mb-1">Meja kopi bukan hanya sebagai tempat meletakkan minuman atau
                                        makanan ringan, tetapi juga bisa berfungsi lebih dari itu. Jika kamu sering menerima tamu,
                                        pilih meja dengan permukaan yang cukup luas agar nyaman digunakan. Jika ingin yang lebih
                                        praktis, pilih living room table dengan rak tambahan di bawahnya untuk menyimpan majalah,
                                        buku, atau remote TV. Bagi yang memiliki ruang tamu minimalis, center table multifungsi
                                        dengan laci atau desain lipat bisa menjadi pilihan terbaik.</p>
                                    <li>Perhatikan Ukuran dan Dimensi Meja</li>
                                    <p class="text-justify mb-1">Jangan sampai center table yang dipilih malah membuat ruang tamu
                                        terasa sempit atau tidak proporsional. Pastikan ukurannya sesuai dengan luas ruangan dan
                                        tinggi sofa agar nyaman digunakan. Sebagai patokan umum, meja tatami idealnya memiliki
                                        tinggi sekitar 40-50 cm dan panjang sekitar setengah atau dua pertiga dari panjang sofa.
                                        Jika ruang tamu kecil, pilih living room table dengan desain minimalis dan ukuran yang lebih
                                        compact agar tidak mengganggu mobilitas.</p>
                                    <li>Selaraskan Bentuk dengan Luas Ruang Tamu</li>
                                    <p class="text-justify mb-1">Bentuk tea table juga harus disesuaikan dengan tata letak dan luas
                                        ruangan. Meja berbentuk persegi atau persegi panjang cocok untuk ruang tamu yang luas dan
                                        memiliki sofa panjang. Jika ruang tamu cukup kecil atau memiliki banyak perabot, meja
                                        berbentuk bundar atau oval bisa menjadi pilihan karena tidak memiliki sudut tajam dan lebih
                                        aman, terutama jika ada anak kecil di rumah.</p>
                                    <li>Pastikan Pilih yang Sesuai dengan Tema Ruangan</li>
                                    <p class="text-justify mb-1">Meja sofa yang selaras dengan tema ruangan akan membuat tampilan
                                        lebih harmonis dan estetis. Jika ruang tamu bergaya minimalis, pilih meja dengan desain
                                        simpel dan warna netral seperti putih, hitam, atau kayu alami. Untuk tema klasik atau
                                        vintage, meja tatami dengan material kayu solid dan ukiran detail bisa menjadi pilihan. Jika
                                        suka tampilan modern dan elegan, meja dengan kombinasi kaca dan logam bisa memberikan kesan
                                        mewah.</p>
                                </ol>
                                <h5>Beli Meja Kopi di Ilena Furniture</h5>
                                <p class="text-justify">Jika sedang berencana membeli furniture coffee table, Anda berada di tempat
                                    yang tepat! Karena di Ilena furniture Anda bisa menemukan berbagai model meja sofa dengan
                                    berbagai ukuran dan desain yang sesuai dengan selera. Mulai dari tea table dengan modern,
                                    minimalis, industrial, japandi, etnic, dan lain sebagainya.</p>
                                <p class="text-justify">Temukan berbagai produk pilihan dengan kualitas terbaik dengan harga yang
                                    terbaik pula. Makin hemat dengan gabung jadi member Ilena dan dapatkan penawaran khusus hanya
                                    untuk Anda!</p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'console-table': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <p class="text-justify">Console table atau entryway table merupakan meja kecil yang biasanya akan
                                    dijumpai pada area dekat dinding serta menjadi bagian dari dekorasi ruangan. Tidak hanya sekedar
                                    digunakan untuk tempat penyimpanan saja, akan tetapi meletakkan meja konsol di ruangan bisa
                                    menaikkan value ruang menjadi lebih dari sebelumnya. Karena pada dasarnya setiap ruang berharga
                                    dengan fungsinya masing-masing, hall table mempertegas dengan look yang lebih bergengsi dari
                                    ruangan tersebut. Mulai dari ruang tamu, lorong, atau bahkan tempat tidur. Furniture entryway
                                    table ini bisa melebur dengan indah menjadi bagian dari dekorasi ruangan selain dari fungsinya
                                    sebagai penambah area penyimpanan saja.</p>
                                <h5>Tips Memilih Console Table</h5>
                                <p class="text-justify">Memilih sofa table yang tepat memerlukan perhatian terhadap beberapa aspek
                                    penting. Berikut adalah tips untuk mendapatkan hall table yang sesuai dengan gaya dan kebutuhan
                                    hunian.</p>
                                <ol>
                                    <li>Sesuaikan dengan Gaya Interior</li>
                                    <p class="text-justify mb-1">Meja foyer harus selaras dengan konsep interior rumah. Jika rumah
                                        bergaya klasik, pilih meja dengan ukiran detail dan material kayu berkualitas tinggi seperti
                                        mahoni atau jati. Untuk gaya modern minimalis, meja dengan desain simpel dan material yang
                                        tepat seperti kayu. Sementara itu, bagi yang menyukai estetika kontemporer, perpaduan
                                        material marmer dan besi bisa memberikan kesan eksklusif.</p>
                                    <li>Perhatikan Ukuran dan Proporsi</li>
                                    <p class="text-justify mb-1">Ukuran sofa table harus seimbang dengan ruangan tempatnya
                                        ditempatkan. Meja yang terlalu besar dapat membuat ruangan terasa sempit, sedangkan meja
                                        yang terlalu kecil bisa tampak tenggelam dalam dekorasi. Pastikan tinggi meja tidak melebihi
                                        90 cm agar tetap proporsional dan nyaman digunakan.</p>
                                    <li>Pilih Material Berkualitas</li>
                                    <p class="text-justify mb-1">Kualitas material sangat menentukan daya tahan dan estetika accent
                                        table. Material seperti kayu solid ataupun engineering wood untuk memberikan kesan hangat
                                        dan klasik, sementara marmer memberikan tampilan mewah dan eksklusif. Jika menginginkan
                                        sentuhan industrial, accent table dengan kombinasi kayu dan besi bisa menjadi pilihan yang
                                        menarik.</p>
                                    <li>Fungsionalitas yang Optimal</li>
                                    <p class="text-justify mb-1">Selain sebagai elemen dekoratif, hall table juga bisa berfungsi
                                        sebagai tempat penyimpanan tambahan. Pilih meja dengan laci atau rak terbuka jika ingin
                                        menyimpan barang seperti kunci, buku, atau aksesoris dekoratif. Jika digunakan sebagai meja
                                        rias, pastikan ada ruang cukup untuk menempatkan cermin dan perlengkapan kecantikan.</p>
                                    <li>Warna dan Finishing yang Elegan</li>
                                    <p class="text-justify mb-1">Pemilihan warna dan finishing yang tepat akan memperkuat kesan
                                        mewah pada meja foyer. Warna netral seperti putih, hitam, atau abu-abu memberikan kesan
                                        modern dan elegan. Sementara itu, finishing glossy atau matte bisa dipilih sesuai preferensi
                                        pribadi untuk menyesuaikan dengan keseluruhan tampilan ruangan.</p>
                                    <li>Tambahan Dekorasi Yang Selaras</li>
                                    <p class="text-justify mb-1">Untuk mempercantik tampilan sofa table, tambahkan dekorasi yang
                                        sesuai seperti vas bunga, lampu meja, atau cermin dinding. Pastikan elemen dekoratif ini
                                        tetap proporsional dan tidak mengganggu fungsi utama meja foyer tersebut.</p>
                                </ol>
                                <h5>Beli Console Table Terpercaya dan Terjamin Kualitasnya</h5>
                                <p class="text-justify">Apabila sedang mencari referensi produk accent table dengan desain dan
                                    material yang berkualitas dan sudah teruji di pasar internasional, Ilena furniture adalah
                                    pilihan tepat! Hanya dibuat dari bahan berkualitas yang sudah teruji dan ramah lingkungan.
                                    Desain yang dibuat merupakan mahakarya yang diperuntukkan untuk menyempurnakan keindahan
                                    interior ruangan Anda.</p>
                                <p class="text-justify">Dapatkan untung lebih seru dengan join member Ilena dan klaim berbagai
                                    kesempatan berharga dalam berbelanja hanya dengan melakukan pembelian di website Ilena
                                    Furniture.</p>
                                <p class="text-justify">Lihat produk entryway table dan cari yang sesuai dengan tema serta
                                    preferensi masing-masing. Karena kami percaya, setiap ruang melambangkan suatu identitas dari
                                    pemilik ruangan tersebut.</p>
                                <p class="text-justify">Jadikan lebih indah bersama dengan Ilena Furniture.</p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
                    <?php break;
                    case 'side-table': ?>
                        <hr class="my-5">
                        <div class="container">
                            <div class="my-3 container-meta">
                                <div class="overlay-meta"></div>
                                <h5>Pengertian Meja Nakas</h5>
                                <p class="text-justify">Meja nakas atau yang dikenal juga sebagai side table adalah salah satu
                                    furniture yang biasanya diletakkan pada samping tempat tidur. Meja ini memiliki banyak fungsi
                                    seperti meletakkan barang-barang kecil yang diperlukan sebelum tidur, hingga aksesoris tambahan
                                    untuk percantik kamar. Beberapa hal yang harus diperhatikan saat memilih nakas minimalis:</p>
                                <ol>
                                    <li>Ukuran meja</li>
                                    <p class="text-justify mb-1">Side table biasanya tersedia dalam ukuran kecil dan sederhana
                                        supaya bisa hemat tempat</p>
                                    <li>Tinggi meja</li>
                                    <p class="text-justify mb-1">Pada umumnya tinggi end table berkisar dengan ketinggian kasur. Hal
                                        ini ditujukan agar saat menaruh dan menyimpan barang di nightstand table, dapat dengan mudah
                                        dalam jangkauan tangan saat sedang di atas tempat tidur.</p>
                                    <li>Bentuk</li>
                                    <p class="text-justify mb-1">Model meja ini sangatlah beragam walaupun ukurannya yang cukup
                                        ramping. Mulai dari persegi panjang, bulat, ataupun persegi.</p>
                                    <li>Material pembuatan</li>
                                    <p class="text-justify mb-1">Bahan yang umumnya digunakan untuk membuat nakas minimalis ini bisa
                                        berasal dari bahan kayu, metal, kaca, plastik, maupun kombinasi.</p>
                                </ol>
                                <h5>Dimana Meja Nakas dapat Difungsikan?</h5>
                                <p class="text-justify">Secara mudahnya, end table merupakan salah satu furniture simpel yang bisa
                                    dimanfaatkan di berbagai ruangan sesuai dengan kebutuhan. Berikut ini rekomendasi ruangan yang
                                    cocok dijadikan tempat menaruh furniture ini:</p>
                                <ol>
                                    <li>Kamar Tidur</li>
                                    <p class="text-justify mb-1">Bedside table dengan permukaan atau top table yang mudah dijangkau
                                        saat menyimpan barang menjadikan nightstand cocok diletakkan pada samping tempat tidur.
                                        Barang-barang seperti buku, kacamata, handphone, air minum, atau lainnya bisa dengan mudah
                                        diambil jika barang-barang tersebut berada di jangkauan tangan. Selain itu bedside table
                                        juga bisa dijadikan tempat menaruh lampu tidur untuk pencahayaan tambahan Tak hanya itu
                                        saja, aksesoris seperti frame foto,jam alarm, vas bunga atau tanaman kecil juga bisa
                                        diletakkan pada meja ini.</p>
                                    <li>Ruang Tamu</li>
                                    <p class="text-justify mb-1">Bedside table juga bisa jadi opsi terbaik untuk diletakkan pada
                                        ruang tamu sebagai tempat bersantai. Meja ini bisa digunakkan meletakkan minuman atau
                                        camilan supaya mudah dijangkau. Tak hanya itu, juga bisa untuk menaruh remot TV, remot AC,
                                        majalah, atau koleksi buku di side table. Selain menampilkan kesan rapi, nightstand ini juga
                                        bisa dijadikan space untuk menambah estetika dalam ruangan.</p>
                                    <li>Ruang Kerja</li>
                                    <p class="text-justify mb-1">Lamp table bisa juga dijadikan tempat untuk menyimpan banyak barang
                                        yang menunjang pekerjaan atau sekolah. Mulai dari alat tulis, catatan, alat tulis, atau
                                        koleksi buku yang sering digunakan saat bekerja. Penambahan lampu belajar di lamp table juga
                                        bisa jadi ide yang bagus untuk meningkatkan produktivitas kerja sehingga pekerjaan dapat
                                        selesai lebih cepat. </p>
                                    <li>Dapur</li>
                                    <p class="text-justify mb-1">Sekilas mungkin terdengar agak aneh, tapi jika berani untuk
                                        menambah nakas minimalis ini mencoba maka akan timbul suasana yang berbeda dan lebih segar
                                        di mata. Jadi tidak hanya menambah tempat penyimpanan barang saja, tapi lamp table ini bisa
                                        pula menambah kesan estetika pada ruang dapur.</p>
                                </ol>
                                <h5>Cari Meja Nakas di Ilena Furniture</h5>
                                <p class="text-justify">Apabila Anda sedang mencari referensi model end table dengan gaya modern dan
                                    minimalis terbaru. Terbuat dari bahan premium pilihan berupa engineering wood maupun metal yang
                                    awet lama sehingga cocok untuk penggunaan lama. Anda bisa memilih berbagai suasana mulai dari
                                    modern, minimalis, industrial, japandi, dan berbagai tema ruangan lainnya yang bisa dipadukan
                                    dengan furniture dari Ilena. Dibandrol dengan kisaran harga yang cukup terjangkau sehingga
                                    sangat worth it untuk pembelian dalam jumlah banyak.</p>
                                <p class="text-justify">Jadilah bagian dari teman Ilena dan dapatkan berbagai keuntungan membership
                                    yang tersedia terbatas hanya untuk Anda.</p>
                            </div>
                        </div>
                        <div class=" d-flex justify-content-center mb-5">
                            <button class="btn btn-lonjong " onclick="openMeta(event)">Lihat selengkapnya</button>
                        </div>
            <?php break;
                }
            } ?>
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
    let bukaMeta = false;

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

    function openMeta(e) {
        const containerMeta = document.querySelector('.container-meta');
        if (bukaMeta) {
            containerMeta.classList.remove('show')
            bukaMeta = false;
            e.target.innerHTML = 'Lihat selengkapnya'
        } else {
            containerMeta.classList.add('show')
            bukaMeta = true;
            e.target.innerHTML = 'Lebih sedikit'
        }
    }
</script>

<?= $this->endSection(); ?>