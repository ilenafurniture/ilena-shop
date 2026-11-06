(() => {
    const root = document.querySelector('[data-product-shipping-estimator]');
    if (!root) return;

    const input = root.querySelector('#product-shipping-destination');
    const destinationField = root.querySelector('[data-shipping-destination]');
    const provinceField = root.querySelector('[data-shipping-province]');
    const list = root.querySelector('[role="listbox"]');
    const button = root.querySelector('[data-shipping-estimate-button]');
    const status = root.querySelector('[data-shipping-estimate-status]');
    const lang = root.dataset.locale === 'en' ? 'en' : 'id';
    const messages = {
        searching: lang === 'en' ? 'Searching destinations…' : 'Mencari tujuan…',
        noResults: lang === 'en' ? 'No matching destination found.' : 'Tujuan yang cocok tidak ditemukan.',
        select: lang === 'en' ? 'Choose a city or regency from the suggestions.' : 'Pilih kota atau kabupaten dari daftar.',
        loading: lang === 'en' ? 'Calculating the shipping estimate…' : 'Menghitung perkiraan ongkir…',
        unavailable: lang === 'en' ? 'A tariff is not available for this destination yet. Contact the Ilena team for confirmation.' : 'Tarif untuk tujuan ini belum dapat ditampilkan. Hubungi tim Ilena untuk konfirmasi.',
        invalid: lang === 'en' ? 'Please check the destination, variant, and product quantity.' : 'Periksa kembali pilihan tujuan, varian, dan jumlah produk.',
        stock: lang === 'en' ? 'The selected variant stock has changed. Update the quantity and try again.' : 'Stok varian berubah. Perbarui pilihan jumlah lalu coba lagi.',
        estimate: lang === 'en' ? 'Estimated APM shipping' : 'Perkiraan ongkir APM',
        delivery: lang === 'en' ? 'Estimated delivery' : 'Estimasi tiba',
        weight: lang === 'en' ? 'Billable weight' : 'Berat yang dihitung',
        days: lang === 'en' ? 'days' : 'hari',
    };

    let timer = 0;
    let activeController = null;
    let requestNumber = 0;

    const clearSelection = () => {
        destinationField.value = '';
        provinceField.value = '';
        status.textContent = '';
    };

    const closeList = () => {
        list.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        list.replaceChildren();
    };

    const renderSuggestions = (results) => {
        list.replaceChildren();
        if (!results.length) {
            status.textContent = messages.noResults;
            closeList();
            return;
        }
        results.forEach((item, index) => {
            const option = document.createElement('li');
            option.role = 'option';
            option.id = `product-shipping-option-${index}`;
            const choice = document.createElement('button');
            choice.type = 'button';
            choice.textContent = String(item.label || item.destination || '');
            choice.addEventListener('click', () => {
                input.value = String(item.label || item.destination || '');
                destinationField.value = String(item.destination || '');
                provinceField.value = String(item.province || '');
                status.textContent = '';
                closeList();
            });
            option.append(choice);
            list.append(option);
        });
        list.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        clearSelection();
        closeList();
        const query = input.value.trim();
        if (query.length < 2) return;
        const currentRequest = ++requestNumber;
        activeController?.abort();
        activeController = new AbortController();
        status.textContent = messages.searching;
        timer = window.setTimeout(async () => {
            try {
                const url = new URL(root.dataset.destinationsUrl, window.location.origin);
                url.searchParams.set('q', query);
                const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: activeController.signal });
                if (!response.ok) throw new Error('destination_lookup_failed');
                const data = await response.json();
                if (currentRequest !== requestNumber) return;
                renderSuggestions(Array.isArray(data.results) ? data.results : []);
            } catch (error) {
                if (error.name !== 'AbortError' && currentRequest === requestNumber) {
                    status.textContent = messages.unavailable;
                }
            }
        }, 180);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeList();
        if (event.key === 'Enter' && !list.hidden) {
            const first = list.querySelector('button');
            if (first) {
                event.preventDefault();
                first.click();
            }
        }
    });
    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) closeList();
    });

    const clearEstimate = () => {
        if (status.dataset.result === 'true') status.textContent = '';
        delete status.dataset.result;
    };
    root.querySelectorAll('input[name="varian"], input[name="varian-mobile"], .number-quantity').forEach((control) => {
        control.addEventListener('change', clearEstimate);
        control.addEventListener('input', clearEstimate);
    });

    button.addEventListener('click', async () => {
        if (!destinationField.value || !provinceField.value) {
            status.textContent = messages.select;
            input.focus();
            return;
        }
        const selectedVariant = document.querySelector('input[name="varian"]:checked, input[name="varian-mobile"]:checked');
        const quantity = Math.max(1, Number(document.querySelector('.number-quantity')?.value || 1));
        const variant = selectedVariant?.dataset.name || '';
        if (!variant || !Number.isInteger(quantity) || quantity > 100) {
            status.textContent = messages.invalid;
            return;
        }

        button.disabled = true;
        status.dataset.result = 'true';
        status.textContent = messages.loading;
        try {
            const url = new URL(root.dataset.estimateUrl, window.location.origin);
            url.search = new URLSearchParams({
                product_id: root.dataset.productId,
                variant,
                qty: String(quantity),
                destination: destinationField.value,
                province: provinceField.value,
            }).toString();
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            const data = await response.json();
            if (!response.ok || !data.ok) {
            status.textContent = data.code === 'stock_changed' ? messages.stock : (data.message || messages.unavailable);
                return;
            }
            const currency = new Intl.NumberFormat(lang === 'en' ? 'en-US' : 'id-ID', {
                style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
            }).format(Number(data.price));
            const result = document.createElement('strong');
            result.textContent = currency;
            const delivery = data.delivery_days ? `${messages.delivery}: ${data.delivery_days} ${messages.days}` : '';
            const weight = `${messages.weight}: ${Number(data.billable_weight_kg).toLocaleString(lang === 'en' ? 'en-US' : 'id-ID')} kg`;
            status.replaceChildren(document.createTextNode(`${messages.estimate} — `), result, document.createElement('br'));
            status.append(document.createTextNode([delivery, weight].filter(Boolean).join(' · ')));
            status.dataset.result = 'true';
        } catch (_) {
            status.textContent = messages.unavailable;
        } finally {
            button.disabled = false;
        }
    });
})();
