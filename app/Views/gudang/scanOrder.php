<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;" class="d-flex justify-content-center align-items-center">
    <div>
        <h6 class="text-center">Scan barang yang telah di packing disini:</h6>
        <div class="bg-secondary" style="width: 1000px; height: 300px;">
            <video id="camera-web" style="width: 1000px; height: 300px;" autoplay loop muted>
                Browser tidak mendukung camera
            </video>
        </div>
        <form action="/gudang/actionscan" method="post">
            <input type="text" name="id_produk">
            <button type="submit">submit</button>
        </form>
    </div>
</div>
<script>
    // const Quagga = require('quagga').default;
    // const Quagga = window.Quagga;
    // const cameraElm = document.getElementById("camera-web");
    // navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;

    // console.log(navigator)
    // if (navigator.mediaDevices) {
    //     console.log('ada')
    //     navigator.mediaDevices.getUserMedia({
    //         video: true
    //     }).then((stream) => {
    //         console.log(stream)
    //         console.log('berhasil')
    //         cameraElm.srcObject = stream;
    //         cameraElm.play();
    //     })
    // }


    // function videoEror(e) {
    //     alert('nggk bisa nyetel video');
    // }
</script>


<?= $this->endSection(); ?>