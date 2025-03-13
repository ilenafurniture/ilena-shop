<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<style>
    .container-galeri {
        width: 90%;
        height: 90%;
        background-color: white;
        padding: 2em;
        border-radius: 1em;
        display: flex;
        flex-direction: column;
    }

    .galeri {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
    }

    @media (max-width: 1600px) {
        .galeri {
            grid-template-columns: repeat(5, 1fr);
        }
    }

    @media (max-width: 1300px) {
        .galeri {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .galeri .item-galeri {
        aspect-ratio: 1 /1;
        border-radius: 1em;
        overflow: hidden;
        border: 3px solid gray;
        cursor: pointer;
    }

    .galeri .item-galeri:hover {
        border: 3px solid var(--merah);
    }

    .galeri .item-galeri img {
        width: 100%;
        aspect-ratio: 1 /1;
        object-fit: cover;
        display: block;
    }
</style>
<script
    src="https://cdn.tiny.cloud/1/<?= $tinyMCE; ?>/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
<div id="modal-galeri" class="d-none justify-content-center align-items-center" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.3); z-index: 5;">
    <div class="container-galeri">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin-bottom: -5px;">Galeri</h2>
                <p class="m-0 text-secondary">Klik gambar untuk menyalin URL gambar</p>
            </div>
            <button onclick="closeModalGaleri()" class="btn btn-outline-dark">Tutup</button>
        </div>
        <hr>
        <div class="d-flex gap-1">
            <input type="file" class="form-control" id="input-add-galeri">
            <button onclick="submitAddGaleri()" class="btn-default-merah">+</button>
        </div>
        <hr>
        <div style="flex: 1; overflow-y:scroll;" class="pe-1">
            <div class="galeri gap-1">
                <?php foreach ($galeri as $g) { ?>
                    <div class="item-galeri" onclick="copytext('<?= base_url($g['url']); ?>', 'Url telah di salin')">
                        <img src="<?= base_url($g['url']); ?>" alt="">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
        function submitAddGaleri() {
            const galeriElm = document.querySelector('.galeri');
            const inputFile = document.getElementById('input-add-galeri');
            if (inputFile.files.length === 0) {
                alert('Pilih file terlebih dahulu!');
                return;
            }
            const formData = new FormData();
            formData.append('file', inputFile.files[0]);
            fetch('/admin/addgaleriarticle', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const base_url = window.location.origin;
                    galeriElm.innerHTML += `
                        <div class="item-galeri" onclick="copytext('${base_url}/${data.url}', 'Url telah di salin')">
                            <img src="${base_url}/${data.url}" alt="">
                        </div>`;
                    inputFile.value = "";
                    console.log(data)
                    callNotif('Galeri ditambahkan')
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan, coba lagi nanti!');
                });
        }
    </script>
</div>
<div class="container artikel">
    <div class="konten">
        <form method="post" action="/admin/addarticle">
            <div class="container">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h1 class="mb-3">Tambah Artikel</h1>
                    <button type="button" class="btn-default-merah" onclick="openModalGaleri()">Galeri</button>
                </div>
                <?= csrf_field(); ?>
                <div>
                    <table class="table-input w-100">
                        <tbody>
                            <tr>
                                <td>Judul</td>
                                <td>
                                    <div class="baris"><input type="text" class="form-control" name="judul" required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Penulis</td>
                                <td>
                                    <div class="baris"><input type="text" class="form-control" name="penulis" required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Kategori</td>
                                <td>
                                    <div class="baris"><input type="text" class="form-control" name="kategori"
                                            placeholder="pisahkan dengan koma" required>
                                    </div>
                                </td>
                            <tr>
                                <td>Tanggal Ubah</td>
                                <td>
                                    <div class="baris"><input type="datetime-local" class="form-control" name="waktu"
                                            required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Gambar Header</td>
                                <td>
                                    <div class="baris"><input type="text" class="form-control" name="header" required placeholder="Masukan URL">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Keywords</td>
                                <td>
                                    <div class="baris"><input type="text" class="form-control" name="keywords" required placeholder="Pisahkan dengan koma">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Deskripsi</td>
                                <td>
                                    <div class="baris">
                                        <textarea name="deskripsi" class="form-control" required></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="container mt-3">
                <h5>Isi Artikel</h5>
                <textarea name="isi" id="content"></textarea>
            </div>
            <button type="submit" class="btn-default mt-2 w-100">Buat</button>
        </form>
    </div>
</div>
<script>
    const modalGaleriElm = document.getElementById('modal-galeri');

    function openModalGaleri() {
        modalGaleriElm.classList.remove('d-none')
        modalGaleriElm.classList.add('d-flex')
    }

    function closeModalGaleri() {
        modalGaleriElm.classList.add('d-none')
        modalGaleriElm.classList.remove('d-flex')
    }

    tinymce.init({
        selector: "#content",
        plugins: [
            // Core editing features
            "anchor",
            "autolink",
            "charmap",
            "codesample",
            "emoticons",
            "image",
            "link",
            "lists",
            "media",
            "searchreplace",
            "table",
            "visualblocks",
            "wordcount",
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Mar 26, 2025:
            "checklist",
            "mediaembed",
            "casechange",
            "export",
            "formatpainter",
            "pageembed",
            "a11ychecker",
            "tinymcespellchecker",
            "permanentpen",
            "powerpaste",
            "advtable",
            "advcode",
            "editimage",
            "advtemplate",
            "ai",
            "mentions",
            "tinycomments",
            "tableofcontents",
            "footnotes",
            "mergetags",
            "autocorrect",
            "typography",
            "inlinecss",
            "markdown",
            "importword",
            "exportword",
            "exportpdf",
        ],
        toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
        tinycomments_mode: "embedded",
        tinycomments_author: "Author name",
        mergetags_list: [{
                value: "First.Name",
                title: "First Name"
            },
            {
                value: "Email",
                title: "Email"
            },
        ],
        ai_request: (request, respondWith) =>
            respondWith.string(() =>
                Promise.reject("See docs to implement AI Assistant")
            ),
    });
</script>
<?= $this->endSection(); ?>