(() => {
    'use strict';

    const en = (document.documentElement.lang || '').toLowerCase().startsWith('en');
    const copy = en ? {
        quick: 'Quick view', compare: 'Compare', comparison: 'Compare products', close: 'Close',
        details: 'View details', remove: 'Remove', clear: 'Clear comparison',
        emptyCart: 'Your bag is empty.', subtotal: 'Subtotal', viewCart: 'View bag', checkout: 'Checkout',
        filter: 'Filters', apply: 'Apply filters',
    } : {
        quick: 'Lihat cepat', compare: 'Bandingkan', comparison: 'Bandingkan produk', close: 'Tutup',
        details: 'Lihat detail', remove: 'Hapus', clear: 'Hapus pilihan',
        emptyCart: 'Keranjang masih kosong.', subtotal: 'Subtotal', viewCart: 'Lihat keranjang', checkout: 'Checkout',
        filter: 'Filter', apply: 'Terapkan filter',
    };

    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));
    const safeUrl = (value, sameOrigin = false) => {
        try {
            const url = new URL(String(value), window.location.href);
            if (!['http:', 'https:'].includes(url.protocol) || (sameOrigin && url.origin !== window.location.origin)) return '';
            return url.href;
        } catch (_) { return ''; }
    };

    const shell = document.createElement('div');
    shell.innerHTML = `
      <div class="ilena-extra-backdrop" data-extra-backdrop hidden></div>
      <aside class="ilena-mini-cart" data-mini-cart aria-hidden="true" aria-label="${esc(en ? 'Shopping bag' : 'Keranjang belanja')}">
        <header><h2>${esc(en ? 'Your bag' : 'Keranjang Anda')}</h2><button type="button" data-extra-close aria-label="${esc(copy.close)}">×</button></header>
        <div class="ilena-mini-cart__body" data-mini-cart-body aria-live="polite"></div>
        <footer><a class="ilena-extra-primary" data-mini-cart-link href="${en ? '/en/cart' : '/cart'}">${esc(copy.viewCart)}</a></footer>
      </aside>
      <section class="ilena-extra-modal" data-quick-modal role="dialog" aria-modal="true" aria-labelledby="ilenaQuickTitle" hidden>
        <button type="button" class="ilena-extra-dismiss" data-extra-close aria-label="${esc(copy.close)}">×</button>
        <img data-quick-image alt="" width="600" height="600"><div><p class="ilena-extra-eyebrow" data-quick-category></p>
        <h2 id="ilenaQuickTitle" data-quick-title></h2><p class="ilena-extra-price" data-quick-price></p>
        <a class="ilena-extra-primary" data-quick-link href="#">${esc(copy.details)}</a></div>
      </section>
      <aside class="ilena-compare-drawer" data-compare-drawer aria-hidden="true" aria-label="${esc(copy.comparison)}">
        <header><h2>${esc(copy.comparison)}</h2><button type="button" data-extra-close aria-label="${esc(copy.close)}">×</button></header>
        <div data-compare-content></div><button type="button" class="ilena-extra-clear" data-compare-clear>${esc(copy.clear)}</button>
      </aside>`;
    document.body.appendChild(shell);

    const backdrop = shell.querySelector('[data-extra-backdrop]');
    const miniCart = shell.querySelector('[data-mini-cart]');
    const miniBody = shell.querySelector('[data-mini-cart-body]');
    const quickModal = shell.querySelector('[data-quick-modal]');
    const compareDrawer = shell.querySelector('[data-compare-drawer]');
    const compareContent = shell.querySelector('[data-compare-content]');
    let active = null;
    let returnFocus = null;
    let closeFilterSheet = null;

    function closePanels() {
        if (active === 'filter' && closeFilterSheet) { closeFilterSheet(); return; }
        backdrop.hidden = true;
        [miniCart, compareDrawer].forEach((panel) => panel.setAttribute('aria-hidden', 'true'));
        quickModal.hidden = true;
        document.body.classList.remove('ilena-extra-open');
        if (active === 'cart') miniCart.classList.remove('is-open');
        if (active === 'compare') compareDrawer.classList.remove('is-open');
        active = null;
        returnFocus?.focus?.();
    }

    function openPanel(name, panel) {
        closePanels();
        returnFocus = document.activeElement;
        active = name;
        backdrop.hidden = false;
        document.body.classList.add('ilena-extra-open');
        panel.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => panel.classList.add('is-open'));
        panel.querySelector('button, a')?.focus();
    }

    async function loadMiniCart() {
        miniBody.innerHTML = `<p class="ilena-extra-loading">${esc(en ? 'Loading your bag…' : 'Memuat keranjang…')}</p>`;
        try {
            const response = await fetch(`${en ? '/en' : ''}/cart/summary`, {
                credentials: 'same-origin', headers: { Accept: 'application/json' }
            });
            if (!response.ok) throw new Error('cart-summary');
            const data = await response.json();
            if (!data.success || !Array.isArray(data.items)) throw new Error('cart-summary');
            if (!data.items.length) {
                miniBody.innerHTML = `<p class="ilena-extra-empty">${esc(copy.emptyCart)}</p>`;
                return;
            }
            const lines = data.items.map((item) => `<article class="ilena-mini-cart__item">
                <img src="${esc(item.image)}" alt="${esc(item.name)}" width="72" height="72" loading="lazy" decoding="async">
                <div><strong>${esc(item.name)}</strong><small>${esc(item.variant)} · ${esc(item.quantity)} × ${esc(item.unitPrice)}</small></div>
                <b>${esc(item.total)}</b></article>`).join('');
            miniBody.innerHTML = `${lines}<p class="ilena-mini-cart__subtotal"><span>${esc(copy.subtotal)}</span><strong>${esc(data.subtotal)}</strong></p>`;
        } catch (_) {
            miniBody.innerHTML = `<p class="ilena-extra-empty">${esc(en ? 'Bag details are temporarily unavailable. Open your bag to refresh.' : 'Ringkasan keranjang belum tersedia. Buka keranjang untuk memuat ulang.')}</p>`;
        }
    }

    document.addEventListener('click', (event) => {
        const close = event.target.closest('[data-extra-close]');
        if (close || event.target === backdrop) { closePanels(); return; }
        const cartLink = event.target.closest('[data-ilena-cart-link]');
        if (cartLink) {
            event.preventDefault();
            shell.querySelector('[data-mini-cart-link]').href = cartLink.href;
            openPanel('cart', miniCart);
            loadMiniCart();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && active) { closePanels(); return; }
        if (event.key !== 'Tab' || !active) return;
        const panel = active === 'cart' ? miniCart : active === 'compare' ? compareDrawer : active === 'quick' ? quickModal : document.querySelector('[data-filter-sheet]');
        if (!panel) return;
        const focusable = [...panel.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')].filter((node) => node.getClientRects().length);
        if (!focusable.length) { event.preventDefault(); panel.focus(); return; }
        const first = focusable[0], last = focusable[focusable.length - 1];
        if (event.shiftKey && (document.activeElement === first || !panel.contains(document.activeElement))) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });

    function cardData(card) {
        const image = card.querySelector('.gambar img.img-pic, .gambar img');
        const link = card.querySelector('.related-card-info, a.text-dark, .gambar');
        const title = card.querySelector('.related-card-name, h5');
        const category = card.querySelector('.related-card-category, a.text-dark p');
        const price = card.querySelector('.related-card-prices .harga, .harga');
        if (!image || !link || !title || !price) return null;
        return {
            id: card.dataset.productId || link.href,
            image: safeUrl(image.currentSrc || image.src),
            title: title.textContent.trim(), category: category?.textContent.trim() || '',
            price: price.textContent.trim().replace(/\s+/g, ' '), url: link.href,
            options: card.querySelectorAll('.container-varian input[type="radio"], .related-card-info input[type="radio"]').length,
        };
    }

    function addCardTools() {
        document.querySelectorAll('.card1[data-product-card]').forEach((card) => {
            if (card.querySelector('[data-quick-open]')) return;
            const data = cardData(card);
            if (!data) return;
            card.dataset.productId = data.id;
            const actions = card.querySelector('.card1-content-img-kanan');
            if (actions) {
                const quick = document.createElement('button');
                quick.type = 'button'; quick.className = 'card1-btn-img ilena-card-quick';
                quick.dataset.quickOpen = 'true'; quick.setAttribute('aria-label', `${copy.quick}: ${data.title}`);
                quick.title = copy.quick; quick.innerHTML = '<span class="material-icons" aria-hidden="true">visibility</span>';
                actions.appendChild(quick);
            }
            const compare = document.createElement('button');
            compare.type = 'button'; compare.className = 'ilena-card-compare'; compare.dataset.compareAdd = 'true';
            compare.setAttribute('aria-label', `${copy.compare}: ${data.title}`); compare.textContent = copy.compare;
            card.appendChild(compare);
        });
    }

    function openQuick(card) {
        const data = cardData(card);
        if (!data) return;
        shell.querySelector('[data-quick-image]').src = data.image;
        shell.querySelector('[data-quick-image]').alt = data.title;
        shell.querySelector('[data-quick-title]').textContent = data.title;
        shell.querySelector('[data-quick-category]').textContent = data.category;
        shell.querySelector('[data-quick-price]').textContent = data.price;
        shell.querySelector('[data-quick-link]').href = data.url;
        returnFocus = document.activeElement;
        active = 'quick'; quickModal.hidden = false; backdrop.hidden = false;
        document.body.classList.add('ilena-extra-open'); quickModal.querySelector('[data-extra-close]').focus();
    }

    const compareKey = 'ilena-product-compare-v1';
    let compareItems = [];
    try {
        const stored = JSON.parse(localStorage.getItem(compareKey) || '[]');
        compareItems = Array.isArray(stored) ? stored.slice(0, 3).filter((item) => item && typeof item.title === 'string')
            .map((item) => ({ id: String(item.id || ''), title: item.title.slice(0, 180), category: String(item.category || '').slice(0, 100), price: String(item.price || '').slice(0, 80), image: safeUrl(item.image), url: safeUrl(item.url, true), options: Math.max(0, Number(item.options) || 0) }))
            .filter((item) => item.id && item.image && item.url) : [];
    } catch (_) {}
    function drawCompare() {
        compareContent.replaceChildren();
        if (compareItems.length) {
            const table = document.createElement('div'); table.className = 'ilena-compare-table'; table.style.setProperty('--compare-count', String(compareItems.length)); table.setAttribute('role', 'table');
            const headers = document.createElement('div'); headers.className = 'ilena-compare-table__row ilena-compare-table__labels'; headers.setAttribute('role', 'row');
            const label = (value) => { const node = document.createElement('span'); node.textContent = value; node.setAttribute('role', 'columnheader'); return node; };
            headers.append(label(en ? 'Product' : 'Produk'));
            compareItems.forEach((item) => { const head = document.createElement('div'); head.className = 'ilena-compare-table__product'; head.setAttribute('role', 'columnheader'); const img = document.createElement('img'); img.src = item.image; img.alt = item.title; img.width = 76; img.height = 76; const title = document.createElement('a'); title.href = item.url; title.textContent = item.title; const remove = document.createElement('button'); remove.type = 'button'; remove.textContent = copy.remove; remove.addEventListener('click', () => { compareItems = compareItems.filter((x) => x.id !== item.id); persistCompare(); }); head.append(img, title, remove); headers.append(head); });
            table.append(headers);
            [[en ? 'Type' : 'Jenis', (item) => item.category || '—'], [en ? 'Price' : 'Harga', (item) => item.price], [en ? 'Options' : 'Pilihan warna', (item) => String(item.options || 0)]]
                .forEach(([title, get]) => { const row = document.createElement('div'); row.className = 'ilena-compare-table__row'; row.setAttribute('role', 'row'); const heading = document.createElement('strong'); heading.textContent = title; heading.setAttribute('role', 'rowheader'); row.append(heading); compareItems.forEach((item) => { const value = document.createElement('span'); value.textContent = get(item); value.setAttribute('role', 'cell'); row.append(value); }); table.append(row); });
            compareContent.append(table);
        }
        document.querySelectorAll('[data-compare-add]').forEach((button) => {
            const data = cardData(button.closest('.card1'));
            button.setAttribute('aria-pressed', String(Boolean(data && compareItems.some((item) => item.id === data.id))));
        });
    }
    function persistCompare() {
        try { localStorage.setItem(compareKey, JSON.stringify(compareItems)); } catch (_) {}
        drawCompare();
        if (compareItems.length) openPanel('compare', compareDrawer);
        else closePanels();
    }

    document.addEventListener('click', (event) => {
        const quick = event.target.closest('[data-quick-open]');
        if (quick) { event.preventDefault(); openQuick(quick.closest('.card1')); return; }
        const compare = event.target.closest('[data-compare-add]');
        if (compare) {
            event.preventDefault();
            const data = cardData(compare.closest('.card1'));
            if (!data) return;
            if (compareItems.some((item) => item.id === data.id)) compareItems = compareItems.filter((item) => item.id !== data.id);
            else if (compareItems.length < 3) compareItems.push(data);
            persistCompare();
        }
    });
    shell.querySelector('[data-compare-clear]').addEventListener('click', () => { compareItems = []; persistCompare(); });
    drawCompare(); addCardTools();

    function enableRecommendationCarousel() {
        const section = document.querySelector('.ilena-product-related');
        const track = section?.querySelector('.ilena-product-related-grid');
        const heading = section?.querySelector('.ilena-product-related-heading');
        if (!track || !heading || track.children.length < 2 || track.dataset.carouselReady) return;
        track.dataset.carouselReady = 'true';
        track.classList.add('is-carousel');
        track.setAttribute('role', 'region');
        track.setAttribute('aria-label', en ? 'Recommended products' : 'Rekomendasi produk');
        track.tabIndex = 0;
        const controls = document.createElement('div'); controls.className = 'ilena-recommendation-controls';
        const makeArrow = (direction, label, symbol) => {
            const button = document.createElement('button'); button.type = 'button'; button.className = 'ilena-recommendation-arrow';
            button.setAttribute('aria-label', label); button.textContent = symbol;
            button.addEventListener('click', () => track.scrollBy({ left: direction * Math.max(track.clientWidth * .8, 180), behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }));
            return button;
        };
        controls.append(makeArrow(-1, en ? 'Previous recommendations' : 'Rekomendasi sebelumnya', '‹'), makeArrow(1, en ? 'More recommendations' : 'Rekomendasi berikutnya', '›'));
        heading.append(controls);
        const update = () => {
            const overflowing = track.scrollWidth > track.clientWidth + 2;
            controls.hidden = !overflowing;
            controls.querySelectorAll('button')[0].disabled = track.scrollLeft <= 2;
            controls.querySelectorAll('button')[1].disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
        };
        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
        requestAnimationFrame(update);
    }

    function rememberProduct() {
        const detail = document.querySelector('.ilena-product-detail');
        if (!detail) return;
        const title = detail.querySelector('.ilena-product-title-row h1')?.textContent.trim();
        const image = detail.querySelector('.ilena-product-gallery__stage img.img-detail-prev');
        if (!title || !image) return;
        const current = { id: window.location.pathname, title: title.slice(0, 180), image: safeUrl(image.currentSrc || image.src), url: safeUrl(window.location.pathname, true), viewedAt: Date.now() };
        if (!current.image || !current.url) return;
        const key = 'ilena-recently-viewed-v1';
        let records = [];
        try { const parsed = JSON.parse(localStorage.getItem(key) || '[]'); if (Array.isArray(parsed)) records = parsed; } catch (_) {}
        records = [current, ...records.filter((item) => item && item.id !== current.id)].slice(0, 8);
        try { localStorage.setItem(key, JSON.stringify(records)); } catch (_) {}
        const previous = records.filter((item) => item.id !== current.id && typeof item.title === 'string' && safeUrl(item.url, true) && safeUrl(item.image)).slice(0, 4);
        if (!previous.length || document.querySelector('.ilena-recently-viewed')) return;
        const section = document.createElement('section'); section.className = 'ilena-recently-viewed';
        section.setAttribute('aria-labelledby', 'ilena-recently-viewed-title');
        const heading = document.createElement('h2'); heading.id = 'ilena-recently-viewed-title'; heading.textContent = en ? 'Recently viewed' : 'Terakhir dilihat';
        const rail = document.createElement('div'); rail.className = 'ilena-recently-viewed__rail';
        previous.forEach((item) => {
            const card = document.createElement('a'); card.className = 'ilena-recently-viewed__item'; card.href = safeUrl(item.url, true);
            const img = document.createElement('img'); img.src = safeUrl(item.image); img.alt = ''; img.width = 160; img.height = 160; img.loading = 'lazy'; img.decoding = 'async';
            const name = document.createElement('span'); name.textContent = item.title.slice(0, 180); card.append(img, name); rail.append(card);
        });
        section.append(heading, rail);
        document.querySelector('.ilena-product-related')?.after(section);
    }

    enableRecommendationCarousel();
    rememberProduct();

    let headerFrame = false;
    const updateHeaderDensity = () => {
        headerFrame = false;
        const compact = window.scrollY > 52;
        document.querySelectorAll('.ilena-topbar, .navbar-hp-atas').forEach((header) => header.classList.toggle('is-scroll-compact', compact));
    };
    window.addEventListener('scroll', () => {
        if (headerFrame) return;
        headerFrame = true;
        requestAnimationFrame(updateHeaderDensity);
    }, { passive: true });
    updateHeaderDensity();

    // Feedback is tied to deliberate actions and remains motion-safe.
    document.addEventListener('click', (event) => {
        const heart = event.target.closest('form[action*="wishlist/"] button, form[action*="wishlist"] button');
        if (heart) {
            const icon = heart.querySelector('.material-icons');
            if (icon) {
                icon.classList.remove('ilena-heart-pop');
                void icon.offsetWidth;
                icon.classList.add('ilena-heart-pop');
                icon.addEventListener('animationend', () => icon.classList.remove('ilena-heart-pop'), { once: true });
            }
        }
    }, true);

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.btn-default-merah, .ilena-extra-primary, .btn-lonjong');
        if (!button || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const bounds = button.getBoundingClientRect();
        const ripple = document.createElement('span'); ripple.className = 'ilena-button-ripple'; ripple.setAttribute('aria-hidden', 'true');
        const size = Math.max(bounds.width, bounds.height) * 1.25;
        ripple.style.width = `${size}px`; ripple.style.height = `${size}px`;
        ripple.style.left = `${event.clientX - bounds.left - size / 2}px`;
        ripple.style.top = `${event.clientY - bounds.top - size / 2}px`;
        button.classList.add('has-ilena-ripple'); button.append(ripple);
        ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
    }, true);

    document.addEventListener('change', (event) => {
        if (!(event.target instanceof HTMLInputElement) || !/^(?:gallery|varian|varian-mobile)$/.test(event.target.name)) return;
        const image = document.querySelector('.ilena-product-gallery__stage > img.img-detail-prev');
        if (!image) return;
        image.classList.add('is-crossfading');
        image.addEventListener('load', () => image.classList.remove('is-crossfading'), { once: true });
        window.setTimeout(() => image.classList.remove('is-crossfading'), 900);
    }, true);

    // Mobile filter panel uses the existing controls and existing apply behavior.
    const mobileFilter = document.querySelector('.container-filter');
    if (mobileFilter && !document.querySelector('[data-filter-sheet]')) {
        const originalParent = mobileFilter.parentNode;
        const marker = document.createComment('catalog-filter-sheet-origin');
        originalParent.insertBefore(marker, mobileFilter);
        const sheet = document.createElement('section');
        sheet.className = 'ilena-filter-sheet'; sheet.dataset.filterSheet = 'true';
        sheet.setAttribute('role', 'dialog'); sheet.setAttribute('aria-modal', 'true');
        sheet.setAttribute('aria-label', copy.filter); sheet.hidden = true;
        sheet.innerHTML = `<header><h2>${esc(copy.filter)}</h2><button type="button" data-filter-sheet-close aria-label="${esc(copy.close)}">×</button></header><label class="ilena-filter-sheet__sort">${esc(en ? 'Sort by' : 'Urutkan')}<select data-catalog-sort><option value="recommended">${esc(en ? 'Recommended' : 'Rekomendasi')}</option><option value="newest">${esc(en ? 'Newest' : 'Terbaru')}</option><option value="price-asc">${esc(en ? 'Price: low to high' : 'Harga terendah')}</option><option value="price-desc">${esc(en ? 'Price: high to low' : 'Harga tertinggi')}</option></select></label><div class="ilena-filter-sheet__content"></div><footer><button type="button" class="ilena-extra-primary" data-filter-sheet-apply>${esc(copy.apply)}</button></footer>`;
        document.body.appendChild(sheet);
        sheet.querySelector('.ilena-filter-sheet__content').appendChild(mobileFilter);
        const openSheet = () => { sheet.hidden = false; backdrop.hidden = false; document.body.classList.add('ilena-extra-open'); requestAnimationFrame(() => sheet.classList.add('is-open')); active = 'filter'; returnFocus = document.activeElement; sheet.querySelector('button')?.focus(); };
        const closeSheet = () => { sheet.classList.remove('is-open'); sheet.hidden = true; originalParent.insertBefore(mobileFilter, marker.nextSibling); backdrop.hidden = true; document.body.classList.remove('ilena-extra-open'); active = null; returnFocus?.focus?.(); };
        closeFilterSheet = closeSheet;
        const filterToggle = document.querySelector('label[for="container-filter"]');
        filterToggle?.addEventListener('click', (event) => { event.preventDefault(); openSheet(); });
        const sortSelect = sheet.querySelector('[data-catalog-sort]');
        if (sortSelect) sortSelect.value = new URLSearchParams(window.location.search).get('sort') || 'recommended';
        sheet.querySelector('[data-filter-sheet-close]').addEventListener('click', closeSheet);
        sheet.querySelector('[data-filter-sheet-apply]').addEventListener('click', () => mobileFilter.querySelector('.btn-filter')?.click());
        backdrop.addEventListener('click', () => { if (active === 'filter') closeSheet(); });
    }

    // The persistent product CTA mirrors the existing stock-aware purchase form.
    const sticky = document.querySelector('[data-sticky-purchase]');
    const purchaseForm = document.querySelector('#btn-keranjang');
    if (sticky && purchaseForm) {
        const stickyButton = sticky.querySelector('button');
        const productName = document.querySelector('.ilena-product-detail h1, .product-detail-soft h1');
        sticky.querySelector('[data-sticky-product-name]').textContent = productName?.textContent.trim() || '';
        const sync = () => {
            const sourceButton = purchaseForm.querySelector('button[type="submit"]');
            stickyButton.disabled = !sourceButton || sourceButton.disabled;
            stickyButton.textContent = sourceButton?.textContent.trim() || (en ? 'Unavailable' : 'Stok habis');
        };
        sync();
        document.querySelectorAll('input[name="varian"], input[name="varian-mobile"]').forEach((input) => input.addEventListener('change', sync));
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(([entry]) => { sticky.classList.toggle('is-visible', !entry.isIntersecting); });
            observer.observe(purchaseForm);
        } else sticky.classList.add('is-visible');
        stickyButton.addEventListener('click', () => purchaseForm.requestSubmit());
    }

    document.querySelectorAll('[data-commerce-variant]').forEach((field) => {
        const form = field.closest('form');
        const productInfo = document.querySelector('.ilena-product-info');
        const syncVariant = (event) => {
            const selected = event.target;
            if (selected?.matches('input[name="varian"], input[name="varian-mobile"]')) {
                field.value = selected.dataset.name || selected.value || '';
                const stockAlert = document.querySelector('[data-back-stock-alert]');
                if (stockAlert && selected.dataset.stok !== undefined) stockAlert.hidden = Number(selected.dataset.stok) > 0;
            }
        };
        productInfo?.addEventListener('change', syncVariant);
        if (form) form.addEventListener('submit', (event) => {
            if (!field.value.trim()) { event.preventDefault(); field.setCustomValidity(en ? 'Choose a product variant first.' : 'Pilih varian produk terlebih dahulu.'); field.reportValidity(); }
            else field.setCustomValidity('');
        });
    });
})();
