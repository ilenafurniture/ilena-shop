(() => {
    'use strict';

    const badgeNodes = () => document.querySelectorAll('[data-ilena-cart-badge]');
    const cartLinks = () => document.querySelectorAll('[data-ilena-cart-link]');
    const feedback = document.querySelector('[data-ilena-cart-feedback]');
    const locale = document.documentElement.lang?.toLowerCase().startsWith('en') ? 'en' : 'id';
    const defaultMessages = {
        id: {
            added: 'Produk berhasil ditambahkan ke keranjang.',
            adjusted: 'Jumlah disesuaikan dengan stok yang tersedia.',
            failed: 'Produk atau varian tidak tersedia. Keranjang belum berubah.'
        },
        en: {
            added: 'Added to your shopping bag.',
            adjusted: 'Quantity adjusted to the available stock.',
            failed: 'This product or variant is unavailable. Your bag was not changed.'
        }
    };
    const messages = defaultMessages[locale];
    let feedbackTimer;
    let channel = null;

    try {
        if ('BroadcastChannel' in window) channel = new BroadcastChannel('ilena-cart-state');
    } catch (_) {
        channel = null;
    }

    function updateQuantity(rawQuantity, broadcast = true) {
        const quantity = Math.max(0, Number.parseInt(rawQuantity, 10) || 0);
        badgeNodes().forEach((badge) => {
            badge.dataset.count = String(quantity);
            badge.textContent = quantity > 99 ? '99+' : String(quantity);
            badge.hidden = quantity < 1;
        });
        cartLinks().forEach((link) => {
            const label = link.dataset.cartLabel || (locale === 'en' ? 'Shopping bag' : 'Keranjang');
            link.setAttribute('aria-label', `${label} (${quantity})`);
        });
        if (broadcast && channel) channel.postMessage({ quantity });
        return quantity;
    }

    function announce(message, isError = false) {
        if (!feedback) return;
        window.clearTimeout(feedbackTimer);
        feedback.textContent = message;
        feedback.classList.toggle('is-error', isError);
        feedback.hidden = false;
        requestAnimationFrame(() => feedback.classList.add('is-visible'));
        feedbackTimer = window.setTimeout(() => {
            feedback.classList.remove('is-visible');
            window.setTimeout(() => { feedback.hidden = true; }, 220);
        }, 3200);
    }

    function visibleCartTarget() {
        return Array.from(cartLinks()).find((link) => link.getClientRects().length > 0) || null;
    }

    function productImage(form) {
        const card = form.closest('.card1, [data-product-card], [data-wishlist-product]');
        const cardImage = card?.querySelector('.gambar img.img-pic, .gambar img, img.img-pic');
        if (cardImage?.currentSrc || cardImage?.src) return cardImage;
        return document.querySelector('.ilena-product-detail img.img-detail-prev, .product-detail-soft img.img-detail-prev');
    }

    function flyProductToCart(form) {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const target = visibleCartTarget();
        const image = productImage(form);
        if (!target || !image || !image.complete || image.naturalWidth < 1 || typeof Element.prototype.animate !== 'function') return;

        const from = image.getBoundingClientRect();
        const to = target.querySelector('.material-icons')?.getBoundingClientRect() || target.getBoundingClientRect();
        if (from.width < 2 || from.height < 2 || to.width < 2 || to.height < 2) return;

        const flyer = image.cloneNode(false);
        flyer.removeAttribute('id');
        flyer.removeAttribute('srcset');
        flyer.removeAttribute('sizes');
        flyer.src = image.currentSrc || image.src;
        flyer.setAttribute('aria-hidden', 'true');
        Object.assign(flyer.style, {
            position: 'fixed',
            zIndex: '3000',
            left: `${from.left}px`,
            top: `${from.top}px`,
            width: `${Math.min(from.width, 180)}px`,
            height: `${Math.min(from.height, 180)}px`,
            objectFit: 'cover',
            borderRadius: '8px',
            boxShadow: '0 10px 28px rgba(25, 21, 17, .24)',
            pointerEvents: 'none',
            margin: '0'
        });
        document.body.appendChild(flyer);

        const startX = from.left + from.width / 2;
        const startY = from.top + from.height / 2;
        const endX = to.left + to.width / 2;
        const endY = to.top + to.height / 2;
        const offsetX = endX - startX;
        const offsetY = endY - startY;
        const shrinkX = Math.min(.22, Math.max(.08, to.width / Math.max(from.width, 1)));
        const shrinkY = Math.min(.22, Math.max(.08, to.height / Math.max(from.height, 1)));

        const animation = flyer.animate([
            { transform: 'translate3d(0,0,0) scale(1)', opacity: 1 },
            { transform: `translate3d(${offsetX}px,${offsetY}px,0) scale(${shrinkX},${shrinkY})`, opacity: .18 }
        ], { duration: 620, easing: 'cubic-bezier(.2,.72,.22,1)', fill: 'forwards' });
        animation.onfinish = () => flyer.remove();
        animation.oncancel = () => flyer.remove();
    }

    function celebrateCart() {
        const target = visibleCartTarget();
        if (!target) return;
        target.classList.remove('is-bouncing');
        void target.offsetWidth;
        target.classList.add('is-bouncing');
        target.addEventListener('animationend', () => target.classList.remove('is-bouncing'), { once: true });
        const badge = target.querySelector('[data-ilena-cart-badge]');
        if (badge) {
            badge.classList.remove('is-pulsing');
            void badge.offsetWidth;
            badge.classList.add('is-pulsing');
            badge.addEventListener('animationend', () => badge.classList.remove('is-pulsing'), { once: true });
        }
    }

    if (channel) channel.onmessage = (event) => {
        if (Number.isFinite(Number(event.data?.quantity))) updateQuantity(event.data.quantity, false);
    };

    // The server-rendered value is authoritative on every page load.
    const initialBadge = document.querySelector('[data-ilena-cart-badge]');
    if (initialBadge) updateQuantity(initialBadge.dataset.count, true);

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.cartPending === 'true') return;
        const action = new URL(form.action, window.location.href);
        if (!action.pathname.includes('/addcart/') || form.method.toLowerCase() !== 'post') return;

        event.preventDefault();
        form.dataset.cartPending = 'true';
        const submitter = event.submitter || form.querySelector('[type="submit"]');
        if (submitter) submitter.disabled = true;

        try {
            const response = await fetch(action.href, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form)
            });
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                announce(feedback?.dataset.networkMessage || messages.failed, true);
                return;
            }
            const result = await response.json();
            if (result.csrfName && result.csrfHash) {
                document.querySelectorAll('input[type="hidden"]').forEach((input) => {
                    if (input.name === result.csrfName) input.value = result.csrfHash;
                });
            }
            if (!response.ok || !result.success) {
                if (Number.isFinite(Number(result.quantity))) updateQuantity(result.quantity);
                announce(result.message || messages.failed, true);
                return;
            }

            updateQuantity(result.quantity);
            flyProductToCart(form);
            celebrateCart();
            announce(result.message || messages.added);
        } catch (_) {
            announce(feedback?.dataset.networkMessage || messages.failed, true);
        } finally {
            delete form.dataset.cartPending;
            if (submitter) submitter.disabled = false;
        }
    });
})();
