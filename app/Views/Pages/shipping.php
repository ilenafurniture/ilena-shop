<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container konten baris-ke-kolom">
    <div style="flex:1;">
        <h5 style="letter-spacing: -1px; font-weight:100;" class="path">
            <a href="/address" class="me-3 text-secondary" style="text-decoration: none;">Alamat</a> >
            <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">Kurir</a> >
            <a class="mx-3 text-secondary" style="text-decoration: none;">Rincian Pembayaran</a>
        </h5>
        <div class="container-kurir my-4">
            <?php foreach ($kurir as $index_k => $k) { ?>
            <input type="radio" name="kurir" id="kurir<?= $index_k ?>" value="<?= $k['harga']?>"
                <?= $index_k <= 0 ? 'checked' : '' ?>>
            <label for="kurir<?= $index_k ?>" class="item-kurir">
                <div style="flex: 1;">
                    <p class="mb-1 nama"><?= strtoupper($k['nama']) ?> <?= $k['deskripsi'] ?></p>
                    <?php if($k['estimasi']) { ?>
                    <p class="mb-1">Estimasi pengiriman <?= $k['estimasi'] ?> Hari</p>
                    <?php } ?>
                    <p class="mb-1" style="font-weight: 600;">Rp <?= number_format($k['harga'], 0, ',', '.'); ?></p>
                </div>
                <div style="width:fit-content" class="show-block-ke-hide">
                    <img src="/img/kurir/<?= $k['nama'] ?>.png" alt="">
                </div>
            </label>
            <?php } ?>
        </div>
    </div>
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4">
            <h4 style="letter-spacing: -1px">Pesanan</h4>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">
                    harga
                </p>
                <p class="fw-bold m-0">
                    Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Biaya Admin
                </p>
                <p class="fw-bold m-0">
                    Rp 5,000
                </p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    Biaya Ongkir
                </p>
                <p id="harga-ongkir" class="fw-bold m-0">
                    Rp <?= number_format($kurir[0]['harga'], 0, ',', '.'); ?>
                </p>
            </div>
            <span class="garis my-2"></span>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0">
                    TOTAL
                </p>
                <p class="fw-bold m-0" id="harga-keseluruhan">
                    Rp <?= number_format($hargaKeseluruhan, 0, ',', '.'); ?>
                </p>
            </div>
            <a id="btn-payment" <?= count($alamat) > 0 ? 'href="/payment/0"' : '' ?>
                class="btn-default-merah <?= count($alamat) > 0 ? '' : 'disabled' ?> w-100 mt-4 text-center">Rincian
                Pembayaran</a>
        </div>
    </div>
</div>
<script>
const radioKurirElm = document.querySelectorAll('input[name="kurir"]');
const hargaOngkirElm = document.getElementById('harga-ongkir');
const hargaKeseluruhan = document.getElementById('harga-keseluruhan');
const btnPaymentElm = document.getElementById('btn-payment');

radioKurirElm.forEach((element, ind) => {
    element.addEventListener('change', (e) => {
        hargaOngkirElm.innerHTML = 'Rp ' + e.target.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,
            ".");
        hargaKeseluruhan.innerHTML = 'Rp ' + (Number(e.target.value) + <?= $hargaTotal + 5000 ?>)
            .toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                ".");
        btnPaymentElm.href = '/payment/' + ind;
    })
});
</script>


<?= $this->endSection(); ?>