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
<script src="https://cdn.tiny.cloud/1/<?= $tinyMCE; ?>/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<div id="modal-galeri" class="d-none justify-content-center align-items-center"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.3); z-index: 5;">
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
<div class="artikel p-5">
    <div class="d-flex gap-3">
        <form method="post" action="/admin/addarticle" style="flex: 1;">
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
                                    <div class="baris"><input type="text" class="form-control" name="header" required
                                            placeholder="Masukan URL">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Keywords</td>
                                <td>
                                    <div class="baris"><input type="text" class="form-control" name="keywords" required
                                            placeholder="Pisahkan dengan koma">
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
        <div style="background-color: gray; width: 1px;" class="me-2"></div>
        <div>
            <h2>SEO Check:</h2>
            <p class="mb-1"><strong>Panjang Title:</strong> <span class="text-danger seo-info">0 karakter (Kurang Optimal)</span></p>
            <p class="mb-1"><strong>Panjang Meta Description:</strong> <span class="text-danger seo-info">0 karakter (Kurang Optimal)</span></p>
            <p class="mb-1"><strong>Keyword Density:</strong> <span class="text-danger seo-info">(Kurang Optimal)</span></p>
            <div id="seo-container-keywords">
                <p class="mb-1"><strong>K:</strong><span class="text-danger">0% (Kurang Optimal)</span></p>
            </div>
            <p class="mb-1"><strong>Gambar dengan ALT:</strong> <span class="text-danger seo-info">0 dari 0 gambar (Kurang Optimal)</span></p>
            <p class="mb-1"><strong>Jumlah Link:</strong> <span class="text-danger seo-info">Internal: 0, External: 0 (Kurang Optimal)</span></p>
            <p class="mb-1"><strong>Struktur Heading:</strong> <span class="text-danger seo-info">(Kurang Optimal)</span></p>
            <p class="mb-1"><strong>Readability Score:</strong> <span class="text-danger seo-info">0 (Sulit Dibaca)</span></p>
        </div>
    </div>
</div>
<script>
    const inputJudulElm = document.querySelector('input[name="judul"]');
    const inputDeskripsiElm = document.querySelector('textarea[name="deskripsi"]');
    const inputKeywordsElm = document.querySelector('input[name="keywords"]');
    const modalGaleriElm = document.getElementById('modal-galeri');
    let contentArtikel = '';
    const [seoTitleElm, seoDescriptionElm, seoKeywordsElm, seoGambarElm, seoLinkElm, seoHeadingElm, seoReadibilityElm] = document.querySelectorAll('.seo-info');
    const seoContainerKeywordsElm = document.getElementById('seo-container-keywords');

    function checkTitleMeta(title, description) {
        const titleLength = title.length;
        const descLength = description.length;

        const data = {
            title_length: titleLength,
            description_length: descLength,
            title_status: (titleLength < 50) ? 'Kurang Optimal' : ((titleLength > 60) ? 'Berlebihan' : 'Optimal'),
            description_status: (descLength < 120) ? 'Kurang Optimal' : ((descLength > 160) ? 'Berlebihan' : 'Optimal'),
        }
        if (data.title_status == 'Optimal') {
            seoTitleElm.classList.remove('text-danger')
            seoTitleElm.classList.add('text-success')
        } else {
            seoTitleElm.classList.add('text-danger')
            seoTitleElm.classList.remove('text-success')
        }
        if (data.description_status == 'Optimal') {
            seoDescriptionElm.classList.remove('text-danger')
            seoDescriptionElm.classList.add('text-success')
        } else {
            seoDescriptionElm.classList.add('text-danger')
            seoDescriptionElm.classList.remove('text-success')
        }
        seoTitleElm.innerHTML = `${data.title_length} karakter (${data.title_status})`;
        seoDescriptionElm.innerHTML = `${data.description_length} karakter (${data.description_status})`
    }

    function checkKeywordDensity(content, keywords) {
        const textContent = content.replace(/<[^>]*>/g, ''); // Hapus HTML tags
        const totalWords = textContent.split(/\s+/).length;
        const keywordArray = keywords.toLowerCase().split(',').map(k => k.trim());

        let keywordCounts = {};
        let keywordPercentages = {};
        let totalKeywordCount = 0;

        keywordArray.forEach(keyword => {
            const regex = new RegExp(`\\b${keyword}\\b`, 'gi');
            const count = (textContent.match(regex) || []).length;
            keywordCounts[keyword] = count;
            totalKeywordCount += count;
        });

        // Hitung persentase per keyword
        Object.keys(keywordCounts).forEach(keyword => {
            keywordPercentages[keyword] = (totalWords > 0) ? ((keywordCounts[keyword] / totalWords) * 100).toFixed(2) : "0.00";
        });

        const totalDensity = (totalWords > 0) ? (totalKeywordCount / totalWords) * 100 : 0;

        const data = {
            total_words: totalWords,
            keyword_counts: keywordCounts,
            keyword_percentages: keywordPercentages,
            total_density: totalDensity.toFixed(2),
            status: (totalDensity < 1) ? 'Kurang Optimal' : ((totalDensity > 3) ? 'Berlebihan' : 'Optimal'),
        };
        seoContainerKeywordsElm.innerHTML = ''
        Object.entries(data.keyword_counts).forEach(([keyword, value]) => {
            seoContainerKeywordsElm.innerHTML += `
                <p class="mb-1">- <strong>${keyword}: </strong><span class="text-danger">${Number(data.keyword_percentages[keyword])}%</span></p>
            `
        })
        if (data.status == 'Optimal') {
            seoKeywordsElm.classList.remove('text-danger')
            seoKeywordsElm.classList.add('text-success')
        } else {
            seoKeywordsElm.classList.add('text-danger')
            seoKeywordsElm.classList.remove('text-success')
        }
        seoKeywordsElm.innerHTML = `${data.total_density}% (${data.status})`
    }


    function checkImageAltTags(content) {
        const imgTags = content.match(/<img[^>]*>/g) || [];
        const imagesWithAlt = content.match(/<img[^>]+alt=["'][^"']+["']/g) || [];

        const data = {
            total_images: imgTags.length,
            images_with_alt: imagesWithAlt.length,
            status: (imgTags.length === 0) ? 'Kurang Optimal' : ((imagesWithAlt.length === imgTags.length) ? 'Optimal' : 'Berlebihan'),
        };
        if (data.status == 'Optimal') {
            seoGambarElm.classList.remove('text-danger')
            seoGambarElm.classList.add('text-success')
        } else {
            seoGambarElm.classList.add('text-danger')
            seoGambarElm.classList.remove('text-success')
        }
        seoGambarElm.innerHTML = `${data.images_with_alt} dari ${data.total_images} gambar (${data.status})`
    }

    function checkInternalExternalLinks(content, baseUrl) {
        const links = content.match(/<a[^>]+href=["'](.*?)["']/g) || [];
        let internalLinks = 0;
        let externalLinks = 0;

        links.forEach(link => {
            const urlMatch = link.match(/href=["'](.*?)["']/);
            if (urlMatch) {
                const url = urlMatch[1];
                if (url.startsWith(baseUrl) || url.startsWith('/')) {
                    internalLinks++;
                } else {
                    externalLinks++;
                }
            }
        });

        const data = {
            total_links: links.length,
            internal_links: internalLinks,
            external_links: externalLinks,
            status: (links.length === 0) ? 'Kurang Optimal' : ((internalLinks > 0 && externalLinks > 0) ? 'Optimal' : 'Berlebihan'),
        };
        if (data.status == 'Optimal') {
            seoLinkElm.classList.remove('text-danger')
            seoLinkElm.classList.add('text-success')
        } else {
            seoLinkElm.classList.add('text-danger')
            seoLinkElm.classList.remove('text-success')
        }
        seoLinkElm.innerHTML = `Internal: ${data.internal_links}, External: ${data.external_links} (${data.status})`
    }

    function checkHeadingStructure(content) {
        const headingTags = content.match(/<h[1-6][^>]*>(.*?)<\/h[1-6]>/g) || [];
        let headingCounts = {};

        headingTags.forEach(tag => {
            const level = tag.match(/<h([1-6])/)[1];
            headingCounts[level] = (headingCounts[level] || 0) + 1;
        });

        const data = {
            headings: headingCounts,
            status: ((headingCounts['1'] || 0) === 0) ? 'Kurang Optimal' : (((headingCounts['1'] || 0) > 1) ? 'Berlebihan' : 'Optimal'),
        };
        if (data.status == 'Optimal') {
            seoHeadingElm.classList.remove('text-danger')
            seoHeadingElm.classList.add('text-success')
        } else {
            seoHeadingElm.classList.add('text-danger')
            seoHeadingElm.classList.remove('text-success')
        }
        seoHeadingElm.innerHTML = `(${data.status})`
    }

    function checkReadabilityScore(content) {
        const textContent = content.replace(/<[^>]*>/g, '');
        const sentences = textContent.split(/[.!?]/).filter(s => s.trim().length > 0);
        const words = textContent.split(/\s+/).length;
        const syllables = (textContent.match(/[aeiouy]{1,2}/gi) || []).length;

        const fleschScore = 206.835 - (1.015 * (words / Math.max(1, sentences.length))) - (84.6 * (syllables / Math.max(1, words)));

        const data = {
            score: fleschScore.toFixed(2),
            status: (fleschScore >= 60) ? 'Mudah Dibaca' : 'Sulit Dibaca',
        };
        if (data.status == 'Mudah Dibaca') {
            seoReadibilityElm.classList.remove('text-danger')
            seoReadibilityElm.classList.add('text-success')
        } else {
            seoReadibilityElm.classList.add('text-danger')
            seoReadibilityElm.classList.remove('text-success')
        }
        seoReadibilityElm.innerHTML = `${data.score} (${data.status})`
    }
</script>
<script>
    inputJudulElm.addEventListener('input', (e) => {
        checkTitleMeta(e.target.value, inputDeskripsiElm.value);
    })
    inputDeskripsiElm.addEventListener('input', (e) => {
        checkTitleMeta(inputJudulElm.value, e.target.value);
    })
    inputKeywordsElm.addEventListener('input', (e) => {
        checkKeywordDensity(contentArtikel, e.target.value);
    })

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
        setup: function(editor) {
            editor.on('input', function() {
                contentArtikel = editor.getContent()
                checkKeywordDensity(contentArtikel, inputKeywordsElm.value);
                checkImageAltTags(contentArtikel);
                checkInternalExternalLinks(contentArtikel, "<?= base_url(); ?>");
                checkHeadingStructure(contentArtikel);
                checkReadabilityScore(contentArtikel);
            });
        },
        plugins: [
            // "anchor",
            // "autolink",
            // "charmap",
            // "codesample",
            // "emoticons",
            "image",
            "link",
            "lists",
            "media",
            // "searchreplace",
            "table",
            // "visualblocks",
            // "wordcount",
            // "checklist",
            // "mediaembed",
            // "casechange",
            // "export",
            // "formatpainter",
            // "pageembed",
            // "a11ychecker",
            // "tinymcespellchecker",
            // "permanentpen",
            // "powerpaste",
            // "advtable",
            // "advcode",
            // "editimage",
            // "advtemplate",
            // "ai",
            // "mentions",
            // "tinycomments",
            // "tableofcontents",
            // "footnotes",
            // "mergetags",
            // "autocorrect",
            // "typography",
            // "inlinecss",
            // "markdown",
            // "importword",
            // "exportword",
            // "exportpdf",
        ],
        // toolbar: "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat",
        // tinycomments_mode: "embedded",
        // tinycomments_author: "Author name",
        // mergetags_list: [{
        //         value: "First.Name",
        //         title: "First Name"
        //     },
        //     {
        //         value: "Email",
        //         title: "Email"
        //     },
        // ],
        // ai_request: (request, respondWith) =>
        //     respondWith.string(() =>
        //         Promise.reject("See docs to implement AI Assistant")
        //     ),
    });
</script>
<?= $this->endSection(); ?>