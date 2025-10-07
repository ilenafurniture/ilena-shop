<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<?php
// ====== Guard & fallback supaya view aman ======
$voucher         = isset($voucher) && is_array($voucher) ? $voucher : [];
$selected        = $voucher['selected'] ?? null;
$list            = isset($voucher['list']) && is_array($voucher['list']) ? $voucher['list'] : [];
$user            = isset($user) && is_array($user) ? $user : ['nama'=>'', 'no_hp'=>'', 'email'=>'', 'alamat'=>''];
$keranjang       = isset($keranjang) && is_array($keranjang) ? $keranjang : [];
$listPaymentMethod = isset($listPaymentMethod) && is_array($listPaymentMethod) ? $listPaymentMethod : [];
$paymentMethod   = $paymentMethod ?? '';
$indexAddress    = (int)($indexAddress ?? 0);
$hargaTotal      = (float)($hargaTotal ?? 0);
$flashSale       = (float)($flashSale ?? 0);
$biayaAdmin      = (int)($biayaAdmin ?? 0);
$grossAmount     = (int)($grossAmount ?? 0);
$msg             = $msg ?? '';

// deteksi “voucher member baru” (target == 'baru' atau nama mengandung 'member baru')
$isMemberBaruSelected = false;
if (is_array($selected)) {
    $selTarget = strtolower($selected['target'] ?? '');
    $selNama   = strtolower($selected['nama'] ?? '');
    $isMemberBaruSelected = ($selTarget === 'baru') || str_contains($selNama, 'member baru');
}
?>

<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;" class="path">
            <a href="/address" class="me-3 text-secondary" style="text-decoration: none;">Alamat</a> >
            <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">Rincian Pembayaran</a>
        </h5>

        <?php if (!empty($msg)) : ?>
        <div class="pemberitahuan my-1 w-100" style="width: fit-content;" role="alert">
            <?= esc($msg); ?>
        </div>
        <?php endif; ?>

        <div class="my-4">
            <!-- Informasi Pembeli -->
            <div class="mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample3" aria-expanded="true"
                    aria-controls="collapseExample3">
                    Informasi Pembeli
                </div>
                <div class="collapse py-2 show" id="collapseExample3">
                    <hr>
                    <div class="d-flex">
                        <div style="flex:1" class="my-2">
                            <p class="m-0 fw-normal">Nama</p>
                            <p class="m-0 fw-normal">No Handphone</p>
                            <p class="m-0 fw-normal">Email</p>
                            <p class="m-0 fw-normal">Alamat</p>
                        </div>
                        <div style="flex:4" class="my-2">
                            <p class="m-0 fw-bold">: <?= esc($user['nama'] ?? ''); ?></p>
                            <p class="m-0 fw-bold">: <?= esc($user['no_hp'] ?? ''); ?></p>
                            <p class="m-0 fw-bold">: <?= esc($user['email'] ?? ''); ?></p>
                            <p class="m-0 fw-bold">: <?= esc($user['alamat'] ?? ''); ?></p>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>

            <!-- Informasi Barang -->
            <div class="mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="true"
                    aria-controls="collapseExample2">
                    Informasi Barang
                </div>
                <div class="collapse py-2 show" id="collapseExample2">
                    <hr>
                    <?php if (count($keranjang) > 0): ?>
                    <?php foreach ($keranjang as $index_k => $k) :
                            $nama    = $k['detail']['nama']   ?? '';
                            $varian  = $k['varian']            ?? '';
                            $jumlah  = (int)($k['jumlah']      ?? 0);
                            $harga   = (float)($k['detail']['harga']  ?? 0);
                            $diskon  = (float)($k['detail']['diskon'] ?? 0);
                            $src     = $k['src_gambar']        ?? '';
                            $hargaSatuan = (int)round($harga * (100 - $diskon) / 100);
                        ?>
                    <div class="d-flex gap-3 m-2">
                        <img src="<?= esc($src); ?>" style="width:100px; height:100px; border-radius:8px;"
                            alt="gambar-produk">
                        <div class="d-flex gap-3">
                            <div class="my-2">
                                <p class="m-0 fw-normal">Nama</p>
                                <p class="m-0 fw-normal">Varian</p>
                                <p class="m-0 fw-normal">Jumlah</p>
                                <p class="m-0 fw-normal">Harga Satuan</p>
                            </div>
                            <div class="my-2">
                                <p class="m-0 fw-bold">: <?= esc($nama); ?></p>
                                <p class="m-0 fw-bold">: <?= esc($varian); ?></p>
                                <p class="m-0 fw-bold">: <?= $jumlah; ?> Buah</p>
                                <p class="m-0 fw-bold">: Rp <?= number_format($hargaSatuan, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="m-2 text-muted">Keranjang kosong.</div>
                    <?php endif; ?>
                    <hr>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="container-pembayaran mb-1">
                <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample4" aria-expanded="true"
                    aria-controls="collapseExample4">
                    Metode Pembayaran
                </div>
                <div class="pemberitahuan my-1 d-none" id="alert-cc" role="alert">cek</div>
                <div class="collapse py-2 show" id="collapseExample4">
                    <div class="container-pembayaran">
                        <?php foreach ($listPaymentMethod as $l) : ?>
                        <a href="/payment/method/<?= esc($l);  ?>/<?= $indexAddress; ?>"
                            class="item-logo-pembayaran <?= ($l == $paymentMethod) ? 'active' : '' ?>">
                            <img src="/img/pembayaran/<?= esc($l); ?>.webp" alt="<?= esc($l); ?>">
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom kanan -->
    <div class="tigapuluh-ke-seratus">

        <?php if (count($list) > 0) : ?>
        <!-- Tombol Voucher (dengan badge Member Baru bila applicable) -->
        <div class="btn-voucher mb-2" onclick="openVoucher()" style="cursor:pointer;">
            <?php if (is_array($selected)) : ?>
            <div class="d-flex align-items-center justify-content-between w-100">
                <p class="m-0" style="color: var(--merah)">
                    <?= ucwords(esc($selected['nama'] ?? 'Voucher')); ?>
                </p>
                <?php if ($isMemberBaruSelected): ?>
                <span class="badge bg-success text-white ms-2"
                    style="font-size:.75rem;padding:4px 8px;border-radius:6px;">
                    🎁 Voucher Member Baru
                </span>
                <?php endif; ?>
            </div>
            <?php else : ?>
            <p class="m-0">Pilih voucher</p>
            <?php endif; ?>
            <i class="material-icons">chevron_right</i>
        </div>

        <!-- Daftar Voucher -->
        <div class="container-voucher">
            <?php foreach ($list as $v) :
                $vId     = (int)($v['id'] ?? 0);
                $vNama   = (string)($v['nama'] ?? '');
                $vNom    = (string)($v['nominal'] ?? '0');
                $vSat    = (string)($v['satuan'] ?? '');
                $vTarget = strtolower($v['target'] ?? '');
                $isMemberBaru = ($vTarget === 'baru') || str_contains(strtolower($vNama), 'member baru');
                $isActive     = (is_array($selected) && ((int)($selected['id'] ?? 0) === $vId));
            ?>
            <?php if ($isActive) : ?>
            <div class="item-voucher active">
                <div>
                    <p class="m-0 fw-bold" style="color: var(--merah);">Active</p>
                    <p class="m-0 fw-bold">
                        <?= ucwords(esc($vNama)); ?>
                        <?php if ($isMemberBaru): ?>
                        <span class="badge bg-success text-white ms-2" style="font-size:.7rem;">🎁 Member Baru</span>
                        <?php endif; ?>
                    </p>
                    <p class="m-0">Potongan sebesar <?= esc($vNom); ?> <?= esc($vSat); ?></p>
                </div>
                <a href="/cancelvoucher/<?= $vId . "-" . $indexAddress; ?>">Lepas</a>
            </div>
            <?php else : ?>
            <div class="item-voucher">
                <div>
                    <p class="m-0 fw-bold">
                        <?= ucwords(esc($vNama)); ?>
                        <?php if ($isMemberBaru): ?>
                        <span class="badge bg-success text-white ms-2" style="font-size:.7rem;">🎁 Member Baru</span>
                        <?php endif; ?>
                    </p>
                    <p class="m-0">Potongan sebesar <?= esc($vNom); ?> <?= esc($vSat); ?></p>
                </div>
                <!-- (fix) harus ada slash setelah /usevoucher -->
                <a href="/usevoucher/<?= $vId . "-" . $indexAddress; ?>">Pakai</a>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <hr>
        </div>
        <?php endif; ?>

        <!-- Ringkasan Pesanan -->
        <div class="card p-4">
            <h4 style="letter-spacing: -1px">Pesanan</h4>

            <!-- Notifikasi BESAR jika voucher member baru aktif -->
            <?php if ($isMemberBaruSelected): ?>
            <div class="alert alert-success py-2 px-3 mb-3"
                style="background-color:#eaffea;border:1px solid #b7f0b7;border-radius:6px;">
                🎉 Selamat! Kamu mendapatkan <b>Voucher Member Baru</b>
                sebesar
                <?php
                    // rupiah final (sudah dihitung controller) bila ada
                    $pot = (int)($selected['rupiah'] ?? 0);
                    if ($pot > 0) {
                        echo 'Rp ' . number_format($pot, 0, ',', '.');
                    } else {
                        // fallback: tampilkan deskripsi nominal+satuan
                        $nom = $selected['nominal'] ?? 0;
                        $sat = $selected['satuan']  ?? '';
                        echo esc($nom) . ' ' . esc($sat);
                    }
                ?>
                untuk transaksi ini.
            </div>
            <?php endif; ?>

            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">Harga</p>
                <p class="fw-bold m-0">Rp <?= number_format((int)$hargaTotal, 0, ',', '.'); ?></p>
            </div>

            <?php if (is_array($selected)) : ?>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">Potongan Voucher</p>
                <p class="fw-bold m-0">- Rp <?= number_format((int)($selected['rupiah'] ?? 0), 0, ',', '.'); ?></p>
            </div>
            <?php endif; ?>

            <?php if ($flashSale > 0) : ?>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">Potongan Flashsale</p>
                <p class="fw-bold m-0">- Rp <?= number_format((int)$flashSale, 0, ',', '.'); ?></p>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between py-1">
                <p class="m-0">Biaya Admin</p>
                <p class="fw-bold m-0">Rp <?= number_format((int)$biayaAdmin, 0, ',', '.'); ?></p>
            </div>

            <span class="garis my-2"></span>

            <div class="d-flex justify-content-between py-1">
                <p class="m-0">TOTAL</p>
                <p class="fw-bold m-0">Rp <?= number_format((int)$grossAmount, 0, ',', '.'); ?></p>
            </div>

            <?php if ((string)session()->get('role') === '4') : ?>
            <a href="/admin/ordertoko/<?= $indexAddress; ?>" class="btn-default-merah">Pesankan</a>
            <?php else : ?>
            <button onclick="bayar(event)" class="btn-default-merah w-100 mt-4 text-center">Bayar</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function bayar(e) {
    e.target.innerHTML = "Loading";
    const timeStamp = Math.floor(Date.now() / 1000);
    window.location.href = "/actionpaycore/" + btoa(timeStamp + ":" + "<?= $indexAddress; ?>");
}

let voucherDibuka = false;

function openVoucher() {
    const containerVoucherElm = document.querySelector('.container-voucher');
    if (!containerVoucherElm) return;
    if (voucherDibuka) {
        containerVoucherElm.classList.remove('show');
        voucherDibuka = false;
    } else {
        containerVoucherElm.classList.add('show');
        voucherDibuka = true;
    }
}
</script>

<?= $this->endSection(); ?>