(() => {
  'use strict';
  const widget = document.querySelector('[data-ilena-chat-widget]');
  if (!widget) return;

  const panel = widget.querySelector('#container-greeting-card');
  const toggle = widget.querySelector('[data-open-ilena-chat]');
  const close = widget.querySelector('[data-close-chat-greeting]');
  const transcript = widget.querySelector('[data-chat-messages]');
  const form = widget.querySelector('[data-chat-form]');
  const input = form?.elements.message;
  const status = widget.querySelector('[data-chat-status]');
  const notice = widget.querySelector('[data-chat-notice]');
  const handoff = widget.querySelector('[data-chat-handoff]');
  const csrf = form?.querySelector('input[type="hidden"]');
  if (!panel || !toggle || !form || !input || !csrf) return;

  let opened = false;
  let busy = false;
  let ready = false;
  let cursor = 0;
  let currentStatus = 'ai';

  const safeText = (value) => String(value ?? '');
  const currency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value) || 0);
  function showNotice(message) {
    notice.textContent = message;
    notice.hidden = !message;
  }
  function updateCsrf(data) {
    if (data?.csrfHash) csrf.value = data.csrfHash;
  }
  async function api(url, options = {}) {
    const headers = new Headers(options.headers || {});
    if (options.method === 'POST') {
      headers.set('X-CSRF-TOKEN', csrf.value);
      headers.set('Content-Type', 'application/json');
    }
    const response = await fetch(url, { ...options, headers, credentials: 'same-origin', cache: 'no-store' });
    const data = await response.json();
    updateCsrf(data);
    if (!response.ok || !data.success) throw new Error(data.message || 'Chat sedang tidak tersedia.');
    return data;
  }
  function renderProduct(product) {
    const a = document.createElement('a');
    a.className = 'ilena-chat-product'; a.href = product.url || '#';
    const image = document.createElement('img'); image.src = product.image; image.alt = ''; image.loading = 'lazy'; image.decoding = 'async';
    const info = document.createElement('span');
    const title = document.createElement('strong'); title.textContent = product.name || 'Produk Ilena';
    const price = document.createElement('small'); price.textContent = currency(product.price);
    info.append(title, price);
    const detail = [product.colors, product.materials, product.dimensions].filter(Boolean).join(' · ');
    if (detail) { const meta = document.createElement('small'); meta.textContent = detail; info.append(meta); }
    a.append(image, info); return a;
  }
  function appendMessage(message) {
    const id = Number(message.id) || 0;
    if (!id || id <= cursor) return;
    cursor = id;
    transcript.querySelector('.ilena-chat-empty')?.remove();
    const bubble = document.createElement('div');
    bubble.className = 'ilena-chat-bubble' + (message.sender_type === 'visitor' ? ' is-visitor' : message.sender_type === 'system' ? ' is-system' : '');
    const body = document.createElement('div'); body.textContent = safeText(message.body); bubble.append(body);
    const meta = message.metadata && typeof message.metadata === 'object' ? message.metadata : {};
    if (Array.isArray(meta.products) && meta.products.length) {
      const products = document.createElement('div'); products.className = 'ilena-chat-products';
      meta.products.slice(0, 4).forEach(product => products.append(renderProduct(product)));
      bubble.append(products);
    }
    if (Array.isArray(meta.sources) && meta.sources.length) {
      meta.sources.slice(0, 3).forEach(source => {
        if (!source.source_url) return;
        try {
          const url = new URL(source.source_url, location.origin);
          if (!['http:', 'https:'].includes(url.protocol)) return;
          const link = document.createElement('a'); link.className = 'ilena-chat-source'; link.href = url.href; link.target = '_blank'; link.rel = 'noopener noreferrer'; link.textContent = 'Sumber resmi: ' + safeText(source.title || 'Baca selengkapnya'); bubble.append(link);
        } catch (_) {}
      });
    }
    const time = document.createElement('time'); time.textContent = safeText(message.created_at || ''); bubble.append(time);
    transcript.append(bubble);
    transcript.scrollTop = transcript.scrollHeight;
  }
  function updateStatus(value) {
    currentStatus = value || 'ai';
    if (status) status.textContent = currentStatus === 'human_waiting' ? 'Menunggu staf Ilena bergabung' : currentStatus === 'human' ? 'Sedang dilayani staf Ilena' : 'Assistant produk · staf siap membantu';
    if (handoff) handoff.hidden = currentStatus !== 'ai';
  }
  function renderMessages(items) { (items || []).forEach(appendMessage); }
  async function bootstrap() {
    if (ready || busy) return;
    busy = true;
    try {
      const productId = widget.dataset.contextProduct || '';
      const data = await api('/chat/bootstrap' + (productId ? '?product=' + encodeURIComponent(productId) : ''));
      renderMessages(data.messages);
      updateStatus(data.status);
      ready = true;
      showNotice('');
    } catch (error) {
      showNotice(error.message + ' Anda tetap bisa menghubungi kami melalui WhatsApp.');
    } finally { busy = false; }
  }
  async function poll() {
    if (!opened || !ready || busy || document.hidden) return;
    busy = true;
    try {
      const data = await api('/chat/poll?after=' + cursor);
      renderMessages(data.messages);
      updateStatus(data.status);
      showNotice('');
    } catch (error) { showNotice(error.message); }
    finally { busy = false; }
  }

  toggle.addEventListener('click', async () => {
    opened = !opened;
    panel.classList.toggle('d-none', !opened);
    toggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
    if (opened) { await bootstrap(); input.focus(); }
  });
  close.addEventListener('click', () => { opened = false; panel.classList.add('d-none'); toggle.setAttribute('aria-expanded', 'false'); toggle.focus(); });
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = input.value.trim();
    if (!message || busy) return;
    busy = true; input.disabled = true; showNotice('Mengirim pesan…');
    try {
      const data = await api('/chat/send', { method: 'POST', body: JSON.stringify({ message, productId: widget.dataset.contextProduct || '' }) });
      input.value = ''; ready = true; updateStatus(data.status); showNotice('');
      await poll();
    } catch (error) { showNotice(error.message); }
    finally { busy = false; input.disabled = false; input.focus(); }
  });
  handoff?.addEventListener('click', async () => {
    if (busy || currentStatus !== 'ai') return;
    busy = true; handoff.disabled = true;
    try { const data = await api('/chat/handoff', { method: 'POST', body: '{}' }); updateStatus(data.status); await poll(); showNotice('Permintaan diteruskan ke antrean staf Ilena.'); }
    catch (error) { showNotice(error.message); }
    finally { busy = false; if (currentStatus === 'ai') handoff.disabled = false; }
  });
  document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
  window.setInterval(poll, 6000);
})();
