<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;" class="d-flex justify-content-center align-items-center">
    <div>
        <h6 class="text-center">Scan barang yang telah di packing disini:</h6>
        <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1" role="alert">
                <?= $val['msg']; ?>
            </div>
        <?php } ?>
        <div class="d-flex flex-column justify-content-center align-items-center">
            <div style="width: 500px" id="reader"></div>
        </div>
    </div>
</div>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`, decodedResult);
        html5QrcodeScanner.clear();
        window.location.href = "/gudang/actionscan/" + decodedText;
    }

    var html5QrcodeScanner = new Html5QrcodeScanner("reader", {
        fps: 10,
        qrbox: 250,
    });
    html5QrcodeScanner.render(onScanSuccess);
</script>


<?= $this->endSection(); ?>