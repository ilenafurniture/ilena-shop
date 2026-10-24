(() => {
    const forms = document.querySelectorAll('[data-global-search]');
    forms.forEach((form) => {
        const input = form.querySelector('input[name="q"]');
        const list = form.querySelector('[role="listbox"]');
        if (!input || !list) return;
        let timer;
        let controller;
        let activeIndex = -1;
        const close = () => {
            list.hidden = true;
            list.replaceChildren();
            input.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
        };
        const choose = (item) => {
            input.value = item.name || '';
            close();
            form.requestSubmit();
        };
        const render = (items) => {
            list.replaceChildren();
            items.forEach((item, index) => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'ilena-search-suggestion';
                option.setAttribute('role', 'option');
                option.id = `${list.id}-option-${index}`;
                const title = document.createElement('span');
                title.className = 'ilena-search-suggestion-title';
                title.textContent = item.name || '';
                option.append(title);
                const detail = [item.category, item.collection].filter(Boolean).join(' · ');
                if (detail) {
                    const meta = document.createElement('small');
                    meta.textContent = detail;
                    option.append(meta);
                }
                option.addEventListener('mousedown', (event) => event.preventDefault());
                option.addEventListener('click', () => choose(item));
                list.append(option);
            });
            list.hidden = items.length === 0;
            input.setAttribute('aria-expanded', String(items.length > 0));
        };
        input.addEventListener('input', () => {
            clearTimeout(timer);
            if (controller) controller.abort();
            const query = input.value.trim();
            if (query.length < 2) return close();
            timer = setTimeout(async () => {
                controller = new AbortController();
                try {
                    const response = await fetch(`/search/suggest?q=${encodeURIComponent(query)}`, {
                        headers: { Accept: 'application/json' },
                        signal: controller.signal,
                    });
                    if (!response.ok) return close();
                    const data = await response.json();
                    render(Array.isArray(data.items) ? data.items : []);
                } catch (error) {
                    if (error.name !== 'AbortError') close();
                }
            }, 180);
        });
        input.addEventListener('keydown', (event) => {
            const options = [...list.querySelectorAll('[role="option"]')];
            if (event.key === 'Escape') return close();
            if (!options.length) return;
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                activeIndex = (activeIndex + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
                options.forEach((option, index) => {
                    option.setAttribute('aria-selected', String(index === activeIndex));
                });
                input.setAttribute('aria-activedescendant', options[activeIndex].id);
            } else if (event.key === 'Enter' && activeIndex >= 0) {
                event.preventDefault();
                choose({ name: options[activeIndex].querySelector('.ilena-search-suggestion-title')?.textContent || '' });
            }
        });
        input.addEventListener('blur', () => setTimeout(close, 120));
    });
})();
