<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<form action="/actionpaycore" method="post">
    <div class="container konten baris-ke-kolom">
        <div style="flex:1;">
            <h5 style="letter-spacing: -1px; font-weight:100;" class="path"><a href="/address" class="me-3 text-secondary" style="text-decoration: none;">Alamat</a> >
                <a class="mx-3 text-dark fw-bold" style="text-decoration: none;">
                    Rincian Pembayaran</a>
            </h5>
            <div class="my-4">
                <div class="container-pembayaran mb-1">
                    <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample3" aria-expanded="true" aria-controls="collapseExample3">
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
                                <p class="m-0 fw-bold">: <?= $user['nama'] ?></p>
                                <p class="m-0 fw-bold">: <?= $user['no_hp'] ?></p>
                                <p class="m-0 fw-bold">: <?= $user['email'] ?></p>
                                <p class="m-0 fw-bold">: <?= $user['alamat'] ?></p>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>

                <div class="container-pembayaran mb-1">
                    <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample2" aria-expanded="true" aria-controls="collapseExample2">
                        Informasi Barang
                    </div>
                    <div class="collapse py-2 show" id="collapseExample2">
                        <hr>
                        <?php foreach ($keranjang as $index_k => $k) { ?>
                            <div class="d-flex gap-3 m-2">
                                <img src="<?= $k['src_gambar'] ?>" style="width:100px; height:100px; border-radius:8px;" alt=" gambar-produk">
                                <div class="d-flex gap-3">
                                    <div class="my-2">
                                        <p class="m-0 fw-normal">Nama</p>
                                        <p class="m-0 fw-normal">Varian</p>
                                        <p class="m-0 fw-normal">Jumlah</p>
                                        <p class="m-0 fw-normal">Harga Satuan</p>
                                    </div>
                                    <div class="my-2">
                                        <p class="m-0 fw-bold">: <?= $k['detail']['nama'] ?></p>
                                        <p class="m-0 fw-bold">: <?= $k['varian'] ?></p>
                                        <p class="m-0 fw-bold">: <?= $k['jumlah'] ?> Buah</p>
                                        <p class="m-0 fw-bold">: Rp
                                            <?= number_format((int)$k['detail']['harga'] * (100 - (float)$k['detail']['diskon']) / 100, 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <hr>
                    </div>
                </div>

                <!-- <?php if ($user['email'] == 'galih8.4.2001@gmail.com') { ?> -->
                <div class="container-pembayaran mb-1">
                    <div class="item-pembayaran" data-bs-toggle="collapse" href="#collapseExample4" aria-expanded="true" aria-controls="collapseExample4">
                        Metode Pembayaran
                    </div>
                    <div class="collapse py-2 show" id="collapseExample4">
                        <div class="alert d-none" role="alert" id="alert-cc"></div>
                        <div class="container-pembayaran mb-1">
                            <!-- <input type="radio" checked name="pembayaran" id="pembayaran1" value="bca">
                            <label for="pembayaran1" class="item-logo-pembayaran"><img src="/img/pembayaran/bca.webp" alt=""></label> -->
                            <input type="radio" name="pembayaran" checked id="pembayaran2" value="bni">
                            <label for="pembayaran2" class="item-logo-pembayaran"><img src="/img/pembayaran/bni.webp" alt=""></label>
                            <input type="radio" name="pembayaran" id="pembayaran3" value="bri">
                            <label for="pembayaran3" class="item-logo-pembayaran"><img src="/img/pembayaran/bri.webp" alt=""></label>
                            <input type="radio" name="pembayaran" id="pembayaran4" value="mandiri">
                            <label for="pembayaran4" class="item-logo-pembayaran"><img src="/img/pembayaran/mandiri.webp" alt=""></label>
                            <input type="radio" name="pembayaran" id="pembayaran5" value="permata">
                            <label for="pembayaran5" class="item-logo-pembayaran"><img src="/img/pembayaran/permatabank.webp" alt=""></label>
                            <input type="radio" name="pembayaran" id="pembayaran6" value="cimb">
                            <label for="pembayaran6" class="item-logo-pembayaran"><img src="/img/pembayaran/cimb.webp" alt=""></label>
                            <!-- <input type="radio" name="pembayaran" id="pembayaran7" value="qris">
                            <label for="pembayaran7" class="item-logo-pembayaran"><img src="/img/pembayaran/qris.webp" alt=""></label>
                            <input type="radio" name="pembayaran" id="pembayaran8" value="gopay">
                            <label for="pembayaran8" class="item-logo-pembayaran"><img src="/img/pembayaran/gopay.webp" alt=""></label> -->
                            <!-- <input type="radio" name="pembayaran" id="pembayaran9" value="shopeepay">
                            <label for="pembayaran9" class="item-logo-pembayaran"><img src="/img/pembayaran/shopeepay.webp" alt=""></label> -->
                            <input type="radio" name="pembayaran" id="pembayaran10" value="card">
                            <label for="pembayaran10" class="item-logo-pembayaran"><img src="/img/pembayaran/mastercard.webp" alt=""></label>
                            <script>
                                const alertCcElm = document.getElementById('alert-cc');
                                const radioPembayaranElm = document.querySelectorAll('input[name="pembayaran"]');
                                radioPembayaranElm.forEach(elm => {
                                    elm.addEventListener('change', (e) => {
                                        alertCcElm.classList.add('d-none')
                                        const pembayaranSelected = e.target.value
                                        const containerFormCCElm = document.getElementById('container-form-cc')
                                        if (pembayaranSelected == 'card') {
                                            containerFormCCElm.classList.remove('d-none')
                                        } else {
                                            containerFormCCElm.classList.add('d-none')
                                        }
                                    })
                                })
                            </script>
                        </div>
                        <div class="pt-3 border-top d-none" id="container-form-cc">
                            <h5 class="mb-2">Informasi Kredit Card</h5>
                            <div class="form-floating mb-1">
                                <input type="number" class="form-control" placeholder="ccNumber" name="ccNumber">
                                <label for="floatingInput">Card Number</label>
                            </div>
                            <div class="form-floating mb-1">
                                <input type="number" class="form-control" placeholder="ccCvv" name="ccCvv">
                                <label for="floatingInput">Card CVV</label>
                            </div>
                            <div class="d-flex gap-1 w-100">
                                <div class="form-floating mb-1">
                                    <select class="form-control" name="ccMonth">
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">Nopember</option>
                                        <option value="12">Desember</option>
                                    </select>
                                    <label for="floatingInput">Expire Month</label>
                                </div>
                                <div class="form-floating mb-1">
                                    <input type="number" class="form-control" placeholder="ccCvv" name="ccYear">
                                    <label for="floatingInput">Expire Year</label>
                                </div>
                            </div>
                            <input type="text" name="tokencc" class="d-none">
                            <button type="button" class="btn btn-primary1" onclick="verifKartu(event)">Verifikasi Kartu</button>
                        </div>
                        <script>
                            const tokenCCElm = document.querySelector('input[name="tokencc"]');
                            const ccNumber = document.querySelector('input[name="ccNumber"]');
                            const ccCvv = document.querySelector('input[name="ccCvv"]');
                            const ccMonth = document.querySelector('select[name="ccMonth"]');
                            const ccYear = document.querySelector('input[name="ccYear"]');

                            function verifKartu(e) {
                                var card = {
                                    card_number: ccNumber.value,
                                    card_cvv: ccCvv.value,
                                    card_exp_month: ccMonth.value,
                                    card_exp_year: ccYear.value,
                                }
                                var options = {
                                    onSuccess: function(response) {
                                        alertCcElm.classList.remove('d-none')
                                        alertCcElm.classList.add('alert-success')
                                        alertCcElm.classList.remove('alert-danger')
                                        alertCcElm.innerHTML = response.status_message

                                        e.target.innerHTML = 'Ubah'
                                        e.target.setAttribute('onclick', "reloadPage(event)")

                                        ccNumber.setAttribute('disabled', true);
                                        ccCvv.setAttribute('disabled', true);
                                        ccMonth.setAttribute('disabled', true);
                                        ccYear.setAttribute('disabled', true);
                                        tokenCCElm.value = response.token_id;
                                        console.log(response)
                                    },
                                    onFailure: function(response) {
                                        alertCcElm.classList.remove('d-none')
                                        alertCcElm.classList.remove('alert-success')
                                        alertCcElm.classList.add('alert-danger')
                                        alertCcElm.innerHTML = response.status_message

                                        e.target.innerHTML = 'Ubah'
                                        e.target.setAttribute('onclick', "reloadPage(event)")

                                        ccNumber.setAttribute('disabled', true);
                                        ccCvv.setAttribute('disabled', true);
                                        ccMonth.setAttribute('disabled', true);
                                        ccYear.setAttribute('disabled', true);
                                        console.log(response)
                                    }
                                }

                                MidtransNew3ds.getCardToken(card, options);
                            }

                            function reloadPage(e) {
                                alertCcElm.classList.add('d-none')

                                e.target.innerHTML = 'Verifikasi Kartu'
                                e.target.setAttribute('onclick', "verifKartu(event)")

                                ccNumber.removeAttribute('disabled');
                                ccCvv.removeAttribute('disabled');
                                ccMonth.removeAttribute('disabled');
                                ccYear.removeAttribute('disabled');
                                tokenCCElm.value = '';
                            }
                        </script>
                    </div>
                </div>
                <!-- <?php } ?> -->
            </div>
        </div>
        <div class="tigapuluh-ke-seratus">
            <?php if (count($voucher['list']) > 0) { ?>
                <div class="btn-voucher mb-2" onclick="openVoucher()">
                    <?php if ($voucher['selected']) { ?>
                        <p class="m-0" style="color: var(--merah)"><?= ucwords($voucher['selected']['nama']); ?></p>
                    <?php } else { ?>
                        <p class="m-0">Pilih voucher</p>
                    <?php } ?>
                    <i class="material-icons">chevron_right</i>
                </div>
                <div class="container-voucher">
                    <?php foreach ($voucher['list'] as $v) {
                        if ($voucher['selected']) {
                            if ($voucher['selected']['id'] == $v['id']) { ?>
                                <div class="item-voucher active">
                                    <div>
                                        <p class="m-0 fw-bold" style="color: var(--merah);">Active</p>
                                        <p class="m-0 fw-bold"><?= ucwords($v['nama']); ?></p>
                                        <p class="m-0">Potongan sebesar <?= $v['nominal']; ?> <?= $v['satuan']; ?></p>
                                    </div>
                                    <a href="/cancelvoucher/<?= $v['id'] . "-" . $indexAddress; ?>">Lepas</a>
                                </div>
                            <?php } else { ?>
                                <div class="item-voucher">
                                    <div>
                                        <p class="m-0 fw-bold"><?= ucwords($v['nama']); ?></p>
                                        <p class="m-0">Potongan sebesar <?= $v['nominal']; ?> <?= $v['satuan']; ?></p>
                                    </div>
                                    <a href="/usevoucher<?= $v['id'] . "-" . $indexAddress; ?>">Pakai</a>
                                </div>
                            <?php   }
                        } else { ?>
                            <div class="item-voucher">
                                <div>
                                    <p class="m-0 fw-bold"><?= ucwords($v['nama']); ?></p>
                                    <p class="m-0">Potongan sebesar <?= $v['nominal']; ?> <?= $v['satuan']; ?></p>
                                </div>
                                <a href="/usevoucher/<?= $v['id'] . "-" . $indexAddress; ?>">Pakai</a>
                            </div>
                    <?php }
                    } ?>
                    <hr>
                </div>
            <?php } ?>
            <div class="card p-4">
                <h4 style="letter-spacing: -1px">Pesanan</h4>
                <div class="mt-2 d-flex justify-content-between py-1">
                    <p class="m-0">
                        Harga
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format($hargaTotal, 0, ',', '.'); ?>
                    </p>
                </div>
                <?php if ($voucher['selected']) { ?>
                    <div class="d-flex justify-content-between py-1">
                        <p class="m-0">
                            Potongan Voucher
                        </p>
                        <p class="fw-bold m-0">
                            - Rp <?= number_format($voucher['selected']['rupiah'], 0, ',', '.'); ?>
                        </p>
                    </div>
                <?php } ?>
                <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        Biaya Admin
                    </p>
                    <p class="fw-bold m-0">
                        Rp 5,000
                    </p>
                </div>
                <span class="garis my-2"></span>
                <div class="d-flex justify-content-between py-1">
                    <p class="m-0">
                        TOTAL
                    </p>
                    <p class="fw-bold m-0">
                        Rp <?= number_format(($voucher['selected'] ? $hargaTotal + 5000 - $voucher['selected']['rupiah'] : $hargaTotal + 5000), 0, ',', '.'); ?>
                    </p>
                </div>
                <?php if (session()->get('role') == '4') { ?>
                    <a href="/admin/ordertoko/<?= $indexAddress; ?>" class="btn-default-merah">Pesankan</a>
                <?php } else { ?>
                    <button type="submit" class="btn-default-merah  w-100 mt-4 text-center">Bayar</button>
                <?php } ?>
            </div>
        </div>
    </div>
</form>

<script>
    function bayar(e) {
        // console.log(e.target);
        e.target.innerHTML = "Loading";
        async function getToken() {
            const res = await fetch('../actionpaysnap', {
                method: 'POST',
                headers: {
                    'content-type': 'application/json'
                },
                body: JSON.stringify({
                    content: '<?= $dataMidJson ?>'
                })
            })
            const snapToken = await res.json();
            console.log(snapToken);
            window.snap.pay(snapToken.token);
        }
        getToken();
    }

    let voucherDibuka = false;

    function openVoucher() {
        const containerVoucherElm = document.querySelector('.container-voucher');
        if (voucherDibuka) {
            containerVoucherElm.classList.remove('show')
            voucherDibuka = false
        } else {
            containerVoucherElm.classList.add('show')
            voucherDibuka = true
        }
    }
</script>

<?= $this->endSection(); ?>