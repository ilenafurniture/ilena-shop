<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<?php
$activePartners = is_array($partners ?? null) ? $partners : [];
$partnerQuery = (string) ($partnerQuery ?? '');
$partnerTotal = (int) ($partnerTotal ?? count($activePartners));
$partnerNetworkTotal = (int) ($partnerNetworkTotal ?? $partnerTotal);
$partnerPage = (int) ($partnerPage ?? 1);
$partnerPages = (int) ($partnerPages ?? 1);
$partnerPerPage = (int) ($partnerPerPage ?? 10);
$partnerFallbackImage = base_url('img/ilena-image-placeholder.png');
$partnerFirst = $partnerTotal === 0 ? 0 : (($partnerPage - 1) * $partnerPerPage) + 1;
$partnerLast = min($partnerPage * $partnerPerPage, $partnerTotal);
?>
<main class="ilena-editorial ilena-partners" aria-labelledby="partners-title">
    <nav class="ilena-editorial__breadcrumb" aria-label="Breadcrumb">
        <a href="/">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Mitra kami</span>
    </nav>

    <header class="ilena-editorial__hero ilena-partners__hero">
        <div class="ilena-partners__hero-copy">
            <p class="ilena-editorial__eyebrow">Jaringan resmi Ilena</p>
            <h1 id="partners-title">Lebih dekat<br>dengan Ilena.</h1>
            <p class="ilena-editorial__intro">Temukan partner dan toko yang menyediakan produk Ilena. Cari berdasarkan nama, kota, atau alamat.</p>
            <a class="ilena-editorial__text-link" href="#partner-directory">Jelajahi partner <span aria-hidden="true">↓</span></a>
        </div>
        <aside class="ilena-partners__hero-note" aria-label="Ringkasan jaringan partner">
            <span class="ilena-partners__hero-mark" aria-hidden="true">I</span>
            <p class="ilena-editorial__eyebrow">Ruang untuk menemukan</p>
            <p class="ilena-partners__hero-stat"><strong><?= number_format($partnerNetworkTotal, 0, ',', '.'); ?></strong><span>partner terdaftar</span></p>
            <a href="#ilena-partner-map">Lihat jangkauan <span aria-hidden="true">↗</span></a>
        </aside>
    </header>

    <section class="ilena-partners__map-section" aria-labelledby="partner-map-title">
        <div class="ilena-editorial__section-heading">
            <div>
                <p class="ilena-editorial__eyebrow">Jangkauan toko</p>
                <h2 id="partner-map-title">Temukan Ilena di sekitar Anda</h2>
            </div>
            <p class="ilena-partners__count"><strong><?= count($mapPartners ?? []); ?></strong> lokasi bertitik di peta</p>
        </div>
        <div class="ilena-partners__map-tools" aria-label="Kontrol peta">
            <p>Jelajahi lokasi dengan scroll, geser, atau cubit untuk zoom.</p>
            <div class="ilena-partners__map-buttons">
                <button type="button" data-map-action="zoom-in" aria-label="Perbesar peta" disabled>＋</button>
                <button type="button" data-map-action="zoom-out" aria-label="Perkecil peta" disabled>−</button>
                <button type="button" data-map-action="fit" aria-label="Tampilkan semua partner" disabled>
                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M3 7V3h4M13 3h4v4M17 13v4h-4M7 17H3v-4M3 3l5 5m9-5-5 5m5 9-5-5m-9 5 5-5"/></svg>
                    <span>Semua lokasi</span>
                </button>
                <button type="button" data-map-action="locate" aria-label="Cari partner terdekat dari lokasi saya" disabled>
                    <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="6.5"/><circle cx="10" cy="10" r="2"/><path d="M10 1v3m0 12v3M1 10h3m12 0h3"/></svg>
                    <span>Di dekat saya</span>
                </button>
            </div>
        </div>
        <div id="ilena-partner-map" class="ilena-partners__map" role="region" aria-label="Peta lokasi partner resmi Ilena" data-directory-url="<?= esc(site_url('partner'), 'attr'); ?>" data-partners="<?= esc(json_encode($mapPartners ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'attr'); ?>">
            <p class="ilena-partners__map-status">Peta lokasi partner akan dimuat saat bagian ini terlihat.</p>
        </div>
        <p class="ilena-partners__map-note" id="partner-map-feedback" aria-live="polite">Pilih penanda untuk melihat partner dan petunjuk arah. Peta dapat dijelajahi dengan mouse, keyboard, atau sentuhan.</p>
    </section>

    <section id="partner-directory" class="ilena-partners__directory" aria-labelledby="partner-directory-title">
        <div class="ilena-editorial__section-heading">
            <div>
                <p class="ilena-editorial__eyebrow">Temukan lokasi</p>
                <h2 id="partner-directory-title">Partner Ilena</h2>
            </div>
            <p class="ilena-partners__count">Menampilkan <strong><?= $partnerFirst; ?>–<?= $partnerLast; ?></strong> dari <strong><?= $partnerTotal; ?></strong> partner</p>
        </div>

        <form class="ilena-partners__search" action="/partner" method="get" role="search" data-search-index="<?= esc(json_encode($partnerSearchIndex ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), 'attr'); ?>">
            <div class="ilena-partners__search-main">
                <label class="sr-only" for="partner-search">Cari partner berdasarkan nama, kota, atau alamat</label>
                <span aria-hidden="true">⌕</span>
                <input id="partner-search" name="q" type="search" value="<?= esc($partnerQuery, 'attr'); ?>" placeholder="Coba: toko mebel di Surabaya" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="partner-search-suggestions">
                <div class="ilena-partners__suggestions" id="partner-search-suggestions" role="listbox" aria-label="Saran partner" hidden></div>
            </div>
            <label class="ilena-partners__city-filter">
                <span class="sr-only">Filter berdasarkan kota</span>
                <select name="city" aria-label="Filter berdasarkan kota">
                    <option value="">Semua kota</option>
                    <?php foreach (($partnerCities ?? []) as $city): ?>
                        <option value="<?= esc($city, 'attr'); ?>" <?= ($partnerCityFilter ?? '') === $city ? 'selected' : ''; ?>><?= esc($city); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Cari</button>
        </form>
        <p class="ilena-partners__search-hint">Cari dengan nama toko, kota, alamat, atau kalimat seperti “toko mebel dekat Solo”. Kami juga memahami beberapa variasi ejaan.</p>

        <?php if ($activePartners !== []): ?>
            <ul class="ilena-partners__list" id="partner-list">
                <?php foreach ($activePartners as $partner): ?>
                    <?php
                    $partnerName = trim((string) ($partner['name'] ?? ''));
                    $partnerCity = trim((string) ($partner['city'] ?? ''));
                    $partnerAddress = trim((string) ($partner['address'] ?? ''));
                    $partnerImage = trim((string) ($partner['img'] ?? ''));
                    $partnerHref = trim((string) ($partner['href'] ?? ''));
                    $partnerMapQuery = trim(implode(', ', array_filter([$partnerAddress, $partnerCity, $partnerName], static fn ($value) => $value !== '')));
                    $partnerMapEmbed = 'https://www.google.com/maps?q=' . rawurlencode($partnerMapQuery) . '&output=embed';
                    ?>
                    <li class="ilena-partners__item" data-partner-id="<?= (int) ($partner['id'] ?? 0); ?>">
                        <img class="ilena-partners__image<?= $partnerImage === '' ? ' is-placeholder' : ''; ?>"
                            src="<?= esc($partnerImage !== '' ? $partnerImage : $partnerFallbackImage, 'attr'); ?>"
                            data-fallback-src="<?= esc($partnerFallbackImage, 'attr'); ?>" alt="" width="112" height="84"
                            loading="lazy" decoding="async">
                        <div class="ilena-partners__details">
                            <?php if ($partnerCity !== ''): ?><p class="ilena-editorial__eyebrow"><?= esc($partnerCity); ?></p><?php endif; ?>
                            <h3><?= esc($partnerName !== '' ? $partnerName : 'Partner Ilena'); ?></h3>
                            <?php if ($partnerAddress !== ''): ?><address><?= esc($partnerAddress); ?></address><?php endif; ?>
                        </div>
                        <?php if ($partnerHref !== '' || $partnerMapQuery !== ''): ?>
                            <div class="ilena-partners__actions">
                                <?php if ($partnerHref !== ''): ?>
                                    <a class="ilena-partners__map-link" href="<?= esc($partnerHref, 'attr'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Buka <?= esc($partnerName, 'attr'); ?> di Google Maps">Buka di Maps <span aria-hidden="true">↗</span></a>
                                <?php endif; ?>
                                <?php if (is_array($partner['coords'] ?? null) && count($partner['coords']) === 2): ?>
                                    <a class="ilena-partners__map-link" href="#ilena-partner-map" data-map-focus="<?= (int) ($partner['id'] ?? 0); ?>">Lihat di peta</a>
                                    <a class="ilena-partners__map-link" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= esc($partner['coords'][0] . ',' . $partner['coords'][1], 'attr'); ?>" target="_blank" rel="noopener noreferrer" aria-label="Petunjuk arah ke <?= esc($partnerName, 'attr'); ?>">Petunjuk arah <span aria-hidden="true">↗</span></a>
                                <?php endif; ?>
                                <?php if ($partnerMapQuery !== ''): ?>
                                    <details class="ilena-partners__map-preview">
                                        <summary aria-label="Tampilkan peta <?= esc($partnerName, 'attr'); ?>">Pratinjau peta <span aria-hidden="true">＋</span></summary>
                                        <div class="ilena-partners__map-frame">
                                            <iframe title="Peta lokasi <?= esc($partnerName, 'attr'); ?>" data-map-src="<?= esc($partnerMapEmbed, 'attr'); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                                        </div>
                                    </details>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($partnerPages > 1): ?>
                <nav class="ilena-partners__pagination" aria-label="Halaman daftar partner">
                    <?php if ($partnerPage > 1): ?><a href="<?= site_url('partner') . '?' . http_build_query(array_filter(['q' => $partnerQuery, 'city' => $partnerCityFilter ?? '', 'page' => $partnerPage - 1], static fn ($value) => $value !== '')); ?>" rel="prev">← Sebelumnya</a><?php endif; ?>
                    <span>Halaman <?= $partnerPage; ?> dari <?= $partnerPages; ?></span>
                    <?php if ($partnerPage < $partnerPages): ?><a href="<?= site_url('partner') . '?' . http_build_query(array_filter(['q' => $partnerQuery, 'city' => $partnerCityFilter ?? '', 'page' => $partnerPage + 1], static fn ($value) => $value !== '')); ?>" rel="next">Berikutnya →</a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="ilena-partners__empty">
                <h3><?= $partnerQuery !== '' || ($partnerCityFilter ?? '') !== '' ? 'Belum menemukan partner yang cocok' : 'Informasi partner sedang diperbarui'; ?></h3>
                <p>Coba ejaan lain, hapus filter kota, atau cari dengan nama kota/alamat yang lebih singkat.</p>
                <?php if (($partnerSuggestions ?? []) !== []): ?>
                    <p class="ilena-partners__empty-label">Mungkin lokasi ini yang Anda cari:</p>
                    <div class="ilena-partners__alternatives">
                        <?php foreach ($partnerSuggestions as $suggestion): ?>
                            <?php $suggestionName = trim((string) ($suggestion['name'] ?? '')); $suggestionCity = trim((string) ($suggestion['city'] ?? '')); ?>
                            <a href="<?= site_url('partner') . '?' . http_build_query(['q' => $suggestionName]); ?>"><strong><?= esc($suggestionName); ?></strong><span><?= esc($suggestionCity); ?></span></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a class="ilena-partners__reset" href="<?= site_url('partner'); ?>">Lihat semua partner</a>
                <p>Butuh bantuan? <a href="mailto:cs@ilenafurniture.com">Hubungi Customer Service Ilena</a>.</p>
            </div>
        <?php endif; ?>
    </section>
</main>
<script>
document.querySelectorAll('.ilena-partners__image').forEach((image) => {
    image.addEventListener('error', () => {
        image.classList.add('is-placeholder');
        image.src = image.dataset.fallbackSrc;
    }, { once: true });
});
</script>
<script>
(() => {
    const form = document.querySelector('.ilena-partners__search');
    const input = document.getElementById('partner-search');
    const list = document.getElementById('partner-search-suggestions');
    if (!form || !input || !list) return;

    const partners = JSON.parse(form.dataset.searchIndex || '[]');
    const synonymGroups = [['toko', 'gerai', 'outlet', 'store', 'mitra', 'partner', 'showroom', 'dealer', 'reseller'], ['mebel', 'meubel', 'furniture', 'furnitur', 'perabot'], ['dekat', 'sekitar', 'near', 'around', 'lokasi']];
    const stopWords = new Set(['di', 'ke', 'dari', 'dan', 'yang', 'ada', 'mana', 'dimana', 'cari', 'carikan', 'tolong', 'untuk', 'kota', 'wilayah', 'in', 'where', 'is', 'the', 'please', 'find']);
    const genericWords = new Set([...synonymGroups[0], ...synonymGroups[2]]);
    const normalize = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, ' ').trim();
    const searchEntries = partners.map((partner) => ({
        partner,
        fields: [partner.name || '', partner.city || '', partner.address || ''].map((value) => {
            const normalized = normalize(value);
            return { value: normalized, words: normalized.split(/\s+/).filter(Boolean) };
        })
    }));
    let activeOption = -1;
    let suggestionTimer = 0;
    const editDistance = (a, b) => {
        const row = Array.from({ length: b.length + 1 }, (_, i) => i);
        for (let i = 1; i <= a.length; i++) {
            let diagonal = row[0];
            row[0] = i;
            for (let j = 1; j <= b.length; j++) {
                const above = row[j];
                row[j] = Math.min(row[j] + 1, row[j - 1] + 1, diagonal + (a[i - 1] === b[j - 1] ? 0 : 1));
                diagonal = above;
            }
        }
        return row[b.length];
    };
    const renderSuggestions = () => {
        const words = normalize(input.value).split(/\s+/).filter(word => word.length > 1 && !stopWords.has(word) && !genericWords.has(word));
        list.replaceChildren();
        input.removeAttribute('aria-activedescendant');
        if (words.length === 0) {
            list.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            return;
        }
        const ranked = searchEntries.map(({ partner, fields }) => {
            let score = 0;
            words.forEach((word) => {
                let variants = [word];
                synonymGroups.forEach(group => { if (group.includes(word)) variants = variants.concat(group); });
                let best = 0;
                variants.forEach(variant => fields.forEach((field, index) => {
                    const weight = index === 0 ? 8 : index === 1 ? 7 : 3;
                    if (field.value.includes(variant)) best = Math.max(best, weight + 2);
                    field.words.forEach(candidate => {
                        const distance = editDistance(variant, candidate);
                        if (distance <= (variant.length >= 8 ? 2 : variant.length >= 5 ? 1 : 0)) best = Math.max(best, weight - distance);
                    });
                }));
                score += best;
            });
            return { partner, score };
        }).filter(item => item.score > 0).sort((a, b) => b.score - a.score).slice(0, 6);

        activeOption = -1;
        ranked.forEach(({ partner }, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.role = 'option';
            button.id = `partner-suggestion-${index}`;
            button.setAttribute('aria-selected', 'false');
            const name = document.createElement('strong');
            name.textContent = partner.name || 'Partner Ilena';
            const city = document.createElement('span');
            city.textContent = [partner.city, partner.address].filter(Boolean).join(' · ');
            button.append(name, city);
            button.addEventListener('click', () => {
                input.value = partner.name || '';
                const citySelect = form.querySelector('select[name="city"]');
                if (citySelect) citySelect.value = Array.from(citySelect.options).some(option => option.value === partner.city) ? partner.city : '';
                list.hidden = true;
                input.setAttribute('aria-expanded', 'false');
                input.removeAttribute('aria-activedescendant');
                form.requestSubmit();
            });
            list.appendChild(button);
        });
        list.hidden = ranked.length === 0;
        input.setAttribute('aria-expanded', String(ranked.length > 0));
    };

    const scheduleSuggestions = () => {
        window.clearTimeout(suggestionTimer);
        suggestionTimer = window.setTimeout(renderSuggestions, 120);
    };
    input.addEventListener('input', scheduleSuggestions);
    input.addEventListener('focus', renderSuggestions);
    form.querySelector('select[name="city"]')?.addEventListener('change', () => form.requestSubmit());
    input.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            list.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            input.removeAttribute('aria-activedescendant');
        }
        const options = Array.from(list.querySelectorAll('[role="option"]'));
        if (!list.hidden && options.length && ['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
            event.preventDefault();
            if (event.key === 'Home') activeOption = 0;
            else if (event.key === 'End') activeOption = options.length - 1;
            else if (activeOption < 0) activeOption = event.key === 'ArrowDown' ? 0 : options.length - 1;
            else activeOption = (activeOption + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
            options.forEach((option, index) => {
                option.setAttribute('aria-selected', String(index === activeOption));
                if (index === activeOption) {
                    input.setAttribute('aria-activedescendant', option.id);
                    option.scrollIntoView({ block: 'nearest' });
                }
            });
        }
        if (event.key === 'Enter' && !list.hidden && options.length) {
            event.preventDefault();
            options[Math.max(0, activeOption)].click();
        }
    });
    document.addEventListener('click', event => {
        if (!form.contains(event.target)) {
            list.hidden = true;
            input.setAttribute('aria-expanded', 'false');
            input.removeAttribute('aria-activedescendant');
        }
    });
})();
</script>
<script>
(() => {
    const mapElement = document.getElementById('ilena-partner-map');
    if (!mapElement) return;

    const status = mapElement.querySelector('.ilena-partners__map-status');
    const feedback = document.getElementById('partner-map-feedback');
    const mapButtons = document.querySelectorAll('.ilena-partners__map-buttons button');
    const directoryUrl = mapElement.dataset.directoryUrl || '/partner';
    let initialized = false;
    let pendingFocusId = '';
    let map;
    let markers = [];
    const selectDirectoryPartner = (id) => {
        const item = Array.from(document.querySelectorAll('.ilena-partners__item[data-partner-id]'))
            .find((candidate) => candidate.dataset.partnerId === String(id));
        document.querySelectorAll('.ilena-partners__item.is-map-selected').forEach((selected) => {
            selected.classList.remove('is-map-selected');
            selected.removeAttribute('aria-current');
        });
        if (!item) return false;
        item.classList.add('is-map-selected');
        item.setAttribute('aria-current', 'location');
        item.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return true;
    };
    const focusPartner = (id) => {
        const entry = markers.find((candidate) => String(candidate.location.id) === String(id));
        if (!entry || !map) {
            if (feedback) feedback.textContent = 'Koordinat lokasi ini belum tersedia di peta.';
            return;
        }
        map.setView(entry.point, Math.max(map.getZoom(), 12), { animate: true });
        entry.marker.openPopup();
        selectDirectoryPartner(id);
        if (feedback) feedback.textContent = `Peta menyorot ${entry.location.name || 'partner Ilena'}${entry.location.city ? `, ${entry.location.city}` : ''}.`;
    };
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-map-focus]');
        if (!trigger) return;
        event.preventDefault();
        pendingFocusId = trigger.dataset.mapFocus || '';
        mapElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        initializeMap();
        if (map) focusPartner(pendingFocusId);
    });
    const initializeMap = () => {
        if (initialized) return;
        initialized = true;
        const locations = JSON.parse(mapElement.dataset.partners || '[]');
        if (!locations.length) {
            if (status) status.textContent = 'Belum ada data koordinat partner untuk ditampilkan.';
            return;
        }

        const loadStyles = () => {
            if (document.querySelector('link[data-ilena-leaflet]')) return;
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            link.dataset.ilenaLeaflet = 'true';
            document.head.appendChild(link);
        };
        const start = () => {
            if (status) status.remove();
            map = L.map(mapElement, { scrollWheelZoom: true, zoomControl: false, keyboard: true, zoomSnap: 0.5 });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const bounds = [];
            markers = [];
            let userMarker = null;
            locations.forEach((location) => {
                const point = [Number(location.lat), Number(location.lng)];
                if (!Number.isFinite(point[0]) || !Number.isFinite(point[1])) return;
                bounds.push(point);
                const marker = L.circleMarker(point, {
                    radius: 7,
                    color: '#fff',
                    weight: 2,
                    fillColor: '#b3262d',
                    fillOpacity: 1
                }).addTo(map);
                markers.push({ marker, point, location });

                const popup = document.createElement('div');
                popup.className = 'ilena-partners__map-popup';
                const name = document.createElement('strong');
                name.textContent = location.name || 'Partner Ilena';
                popup.appendChild(name);
                if (location.city) {
                    const city = document.createElement('span');
                    city.textContent = location.city;
                    popup.appendChild(city);
                }
                const direction = document.createElement('a');
                direction.href = `https://www.google.com/maps/dir/?api=1&destination=${point[0]},${point[1]}`;
                direction.target = '_blank';
                direction.rel = 'noopener noreferrer';
                direction.textContent = 'Petunjuk arah ↗';
                popup.appendChild(direction);
                const listUrl = new URL(directoryUrl, window.location.origin);
                if (location.name) listUrl.searchParams.set('q', location.name);
                if (location.city) listUrl.searchParams.set('city', location.city);
                const directoryLink = document.createElement('a');
                directoryLink.href = `${listUrl.pathname}${listUrl.search}#partner-directory`;
                directoryLink.textContent = 'Lihat di daftar';
                popup.appendChild(directoryLink);
                marker.bindPopup(popup);
                marker.on('click', () => {
                    if (!selectDirectoryPartner(location.id) && feedback) {
                        feedback.textContent = `${location.name || 'Partner Ilena'}${location.city ? `, ${location.city}` : ''} dipilih di peta. Buka “Lihat di daftar” pada penanda untuk menampilkannya di hasil pencarian.`;
                    }
                });
            });

            if (bounds.length === 1) map.setView(bounds[0], 12);
            else if (bounds.length > 1) map.fitBounds(bounds, { padding: [28, 28], maxZoom: 7 });
            else map.setView([-2.5, 118], 4);
            mapButtons.forEach((button) => { button.disabled = false; });
            mapElement.classList.add('is-ready');
            map.invalidateSize();
            if (pendingFocusId) focusPartner(pendingFocusId);

            mapButtons.forEach((button) => button.addEventListener('click', () => {
                const action = button.dataset.mapAction;
                if (action === 'zoom-in') map.zoomIn();
                if (action === 'zoom-out') map.zoomOut();
                if (action === 'fit') {
                    if (bounds.length === 1) map.setView(bounds[0], 12);
                    else if (bounds.length > 1) map.fitBounds(bounds, { padding: [28, 28], maxZoom: 7 });
                    if (feedback) feedback.textContent = 'Peta menampilkan kembali semua lokasi partner.';
                }
                if (action === 'locate') {
                    if (!navigator.geolocation) {
                        if (feedback) feedback.textContent = 'Browser ini belum mendukung pencarian lokasi.';
                        return;
                    }
                    button.disabled = true;
                    if (feedback) feedback.textContent = 'Mencari lokasi Anda…';
                    navigator.geolocation.getCurrentPosition((position) => {
                        const point = [position.coords.latitude, position.coords.longitude];
                        const distanceKm = (a, b) => {
                            const radians = (value) => value * Math.PI / 180;
                            const latitudeDelta = radians(b[0] - a[0]);
                            const longitudeDelta = radians(b[1] - a[1]);
                            const arc = Math.sin(latitudeDelta / 2) ** 2 + Math.cos(radians(a[0])) * Math.cos(radians(b[0])) * Math.sin(longitudeDelta / 2) ** 2;
                            return 6371 * 2 * Math.atan2(Math.sqrt(arc), Math.sqrt(1 - arc));
                        };
                        const nearest = markers.reduce((current, candidate) => !current || distanceKm(point, candidate.point) < distanceKm(point, current.point) ? candidate : current, null);
                        map.setView(nearest ? nearest.point : point, nearest ? 12 : 11, { animate: true });
                        if (userMarker) userMarker.remove();
                        userMarker = L.circleMarker(point, { radius: 8, color: '#fff', weight: 3, fillColor: '#292622', fillOpacity: 1 }).addTo(map);
                        userMarker.bindPopup('Lokasi Anda · hanya tersimpan di perangkat ini');
                        if (nearest) {
                            nearest.marker.openPopup();
                            selectDirectoryPartner(nearest.location.id);
                        }
                        const distance = nearest ? distanceKm(point, nearest.point) : null;
                        if (feedback) feedback.textContent = nearest
                            ? `Partner terdekat: ${nearest.location.name || 'Partner Ilena'}${nearest.location.city ? `, ${nearest.location.city}` : ''} · sekitar ${distance < 1 ? `${Math.round(distance * 1000)} m` : `${distance.toFixed(1)} km`}. Lokasi hanya digunakan di perangkat ini.`
                            : 'Lokasi Anda ditemukan, tetapi belum ada partner dengan koordinat yang tersedia.';
                        button.disabled = false;
                    }, () => {
                        if (feedback) feedback.textContent = 'Lokasi tidak tersedia. Izinkan akses lokasi atau jelajahi peta secara manual.';
                        button.disabled = false;
                    }, { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 });
                }
            }));
        };

        loadStyles();
        if (window.L) {
            start();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.async = true;
        script.onload = start;
        script.onerror = () => {
            initialized = false;
            if (status) status.textContent = 'Peta gagal dimuat. Silakan coba muat ulang halaman.';
            if (feedback) feedback.textContent = 'Peta gagal dimuat. Anda tetap dapat mencari partner melalui daftar di bawah.';
        };
        document.head.appendChild(script);
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            if (!entries.some((entry) => entry.isIntersecting)) return;
            observer.disconnect();
            initializeMap();
        }, { rootMargin: '240px' });
        observer.observe(mapElement);
    } else {
        initializeMap();
    }
})();
</script>
<?= $this->endSection(); ?>
