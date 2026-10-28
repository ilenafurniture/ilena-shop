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
  const sendButton = form?.querySelector('button[type="submit"]');
  const status = widget.querySelector('[data-chat-status]');
  const notice = widget.querySelector('[data-chat-notice]');
  const handoff = widget.querySelector('[data-chat-handoff]');
  const typing = widget.querySelector('[data-chat-typing]');
  const typingCopy = widget.querySelector('[data-chat-typing-copy]');
  const soundButton = widget.querySelector('[data-chat-sound]');
  const waLink = widget.querySelector('[data-kbot-wa]');
  const teaser = widget.querySelector('[data-chat-greeting]');
  const csrf = form?.querySelector('input[type="hidden"]');
  if (!panel || !toggle || !form || !input || !csrf) return;

  let opened = false;
  let sending = false;
  let polling = false;
  let ready = false;
  let cursor = 0;
  let currentStatus = 'ai';
  let humanAvailable = null;
  let waitingForReply = false;
  let soundEnabled = false;
  let audioContext = null;
  let pollTimer = null;

  const safeText = (value) => String(value ?? '');
  const currency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value) || 0);
  function showNotice(message, linkUrl = '') {
    notice.replaceChildren();
    if (message) {
      notice.append(document.createTextNode(message));
      if (linkUrl && waLink) {
        const link = document.createElement('a');
        link.href = linkUrl; link.target = '_blank'; link.rel = 'noopener noreferrer';
        link.textContent = ' Buka WhatsApp';
        notice.append(link);
        waLink.href = linkUrl;
      }
    }
    notice.hidden = !message;
  }
  function updateCsrf(data) { if (data?.csrfHash) csrf.value = data.csrfHash; }
  async function api(url, options = {}) {
    const headers = new Headers(options.headers || {});
    if (options.method === 'POST') {
      headers.set('X-CSRF-TOKEN', csrf.value);
      headers.set('Content-Type', 'application/json');
    }
    let response;
    try {
      response = await fetch(url, { ...options, headers, credentials: 'same-origin', cache: 'no-store' });
    } catch (_) {
      const error = new Error('Chat sedang sulit terhubung. Anda tetap bisa menghubungi tim Ilena melalui WhatsApp.');
      error.data = { code: 'network_error' };
      throw error;
    }
    let data;
    try { data = await response.json(); } catch (_) { data = {}; }
    updateCsrf(data);
    if (!response.ok || !data.success) {
      const known = ['Tunggu sebentar sebelum mengirim pesan lagi.', 'Pesan harus berisi 1–1.200 karakter.'];
      const error = new Error(known.includes(data.message) ? data.message : 'Chat belum dapat memproses pesan. Silakan coba lagi atau lanjutkan melalui WhatsApp.');
      error.data = data;
      throw error;
    }
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
  function playReplyTone() {
    if (!soundEnabled || !audioContext) return;
    try {
      const start = audioContext.currentTime;
      [660, 880].forEach((frequency, index) => {
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();
        oscillator.type = 'sine'; oscillator.frequency.value = frequency;
        gain.gain.setValueAtTime(0.0001, start + index * 0.1);
        gain.gain.exponentialRampToValueAtTime(0.035, start + index * 0.1 + 0.025);
        gain.gain.exponentialRampToValueAtTime(0.0001, start + index * 0.1 + 0.16);
        oscillator.connect(gain); gain.connect(audioContext.destination);
        oscillator.start(start + index * 0.1); oscillator.stop(start + index * 0.1 + 0.17);
      });
    } catch (_) {}
  }
  function appendMessage(message, notify = false) {
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
    transcript.insertBefore(bubble, typing);
    transcript.scrollTop = transcript.scrollHeight;
    if (message.sender_type === 'assistant' || message.sender_type === 'admin') {
      waitingForReply = false;
      setTyping(false);
      if (notify) playReplyTone();
    }
  }
  function updateStatus(value, availability = humanAvailable) {
    currentStatus = value || 'ai';
    humanAvailable = availability;
    if (status) {
      if (currentStatus === 'human_waiting') status.textContent = humanAvailable ? 'Tim Ilena segera bergabung' : 'Tim sedang di luar jam layanan';
      else if (currentStatus === 'human') status.textContent = 'Anda sedang dilayani tim Ilena';
      else status.textContent = humanAvailable ? 'Asisten Ilena · Tim 08.00–16.00 WIB' : 'Asisten Ilena · Tim kembali pukul 08.00 WIB';
    }
    if (handoff) handoff.hidden = currentStatus !== 'ai' || humanAvailable === false;
    if (humanAvailable === false) {
      input.disabled = true;
      if (sendButton) sendButton.disabled = true;
      input.placeholder = 'Lanjutkan pertanyaan melalui WhatsApp';
    } else {
      input.disabled = sending;
      if (sendButton) sendButton.disabled = sending;
      input.placeholder = 'Tulis pertanyaan…';
    }
  }
  function setTyping(value, copy = 'Asisten sedang mengetik') {
    if (!typing) return;
    typing.hidden = !value;
    if (typingCopy) typingCopy.textContent = copy;
    if (value) transcript.scrollTop = transcript.scrollHeight;
  }
  function renderMessages(items, notify = false) { (items || []).forEach(item => appendMessage(item, notify)); }
  function handleError(error) {
    const data = error?.data || {};
    if (data.code === 'outside_hours') {
      humanAvailable = false;
      updateStatus(currentStatus, false);
      waitingForReply = false; setTyping(false);
      showNotice(data.message || 'Tim Ilena sedang di luar jam layanan. Silakan tinggalkan pesan melalui WhatsApp.', data.whatsappUrl || '');
      return;
    }
    waitingForReply = false; setTyping(false);
    showNotice(error?.message || 'Chat sedang sulit terhubung. Silakan lanjutkan melalui WhatsApp.');
  }
  async function bootstrap() {
    if (ready || sending) return;
    sending = true;
    try {
      const productId = widget.dataset.contextProduct || '';
      const data = await api('/chat/bootstrap' + (productId ? '?product=' + encodeURIComponent(productId) : ''));
      humanAvailable = data.humanAvailable === true;
      renderMessages(data.messages);
      updateStatus(data.status, humanAvailable);
      ready = true;
      if (!humanAvailable) showNotice('Tim Ilena melayani ' + (data.serviceHours || '08.00–16.00 WIB') + '. Di luar waktu tersebut, lanjutkan melalui WhatsApp.', data.whatsappUrl || '');
      else showNotice('');
    } catch (error) { handleError(error); }
    finally { sending = false; updateStatus(currentStatus, humanAvailable); }
  }
  async function poll() {
    if (!opened || !ready || polling || document.hidden) return;
    polling = true;
    try {
      const data = await api('/chat/poll?after=' + cursor);
      renderMessages(data.messages, true);
      updateStatus(data.status, data.humanAvailable === true);
      if (data.humanAvailable === false && !waitingForReply) showNotice('Tim Ilena melayani ' + (data.serviceHours || '08.00–16.00 WIB') + '. Anda bisa lanjut melalui WhatsApp.', data.whatsappUrl || '');
    } catch (error) { handleError(error); }
    finally { polling = false; }
  }
  function schedulePoll(delay = 0) {
    if (!opened || !ready || pollTimer) return;
    pollTimer = window.setTimeout(async () => {
      pollTimer = null;
      await poll();
      if (opened) schedulePoll(waitingForReply ? 2400 : 7000);
    }, delay);
  }

  function openChat() {
    opened = true; panel.classList.remove('d-none');
    toggle.setAttribute('aria-expanded', 'true');
    if (teaser) teaser.hidden = true;
    bootstrap().finally(() => { if (!input.disabled) input.focus(); schedulePoll(); });
  }
  toggle.addEventListener('click', () => {
    if (opened) { opened = false; panel.classList.add('d-none'); toggle.setAttribute('aria-expanded', 'false'); if (pollTimer) window.clearTimeout(pollTimer); pollTimer = null; }
    else openChat();
  });
  widget.querySelector('[data-greeting-open]')?.addEventListener('click', openChat);
  widget.querySelector('[data-greeting-dismiss]')?.addEventListener('click', () => { if (teaser) teaser.hidden = true; });
  close.addEventListener('click', () => { opened = false; panel.classList.add('d-none'); toggle.setAttribute('aria-expanded', 'false'); if (pollTimer) window.clearTimeout(pollTimer); pollTimer = null; toggle.focus(); });
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = input.value.trim();
    if (!message || sending) return;
    sending = true; input.disabled = true; if (sendButton) sendButton.disabled = true; showNotice('');
    try {
      const data = await api('/chat/send', { method: 'POST', body: JSON.stringify({ message, productId: widget.dataset.contextProduct || '' }) });
      input.value = ''; ready = true; updateStatus(data.status, data.humanAvailable);
      appendMessage({ id: data.messageId, sender_type: 'visitor', body: message, created_at: data.createdAt || '' });
      waitingForReply = data.status === 'ai';
      if (waitingForReply) setTyping(true, 'Asisten sedang menyiapkan jawaban');
      else if (data.status === 'human_waiting') setTyping(true, 'Pesan Anda menunggu tim Ilena');
      showNotice('');
    } catch (error) { handleError(error); }
    finally { sending = false; updateStatus(currentStatus, humanAvailable); if (!input.disabled) input.focus(); poll(); }
  });
  handoff?.addEventListener('click', async () => {
    if (sending || currentStatus !== 'ai') return;
    sending = true; handoff.disabled = true;
    try {
      const data = await api('/chat/handoff', { method: 'POST', body: '{}' });
      updateStatus(data.status, data.humanAvailable); await poll();
      showNotice('Permintaan Anda sudah diteruskan ke tim Ilena.');
    } catch (error) { handleError(error); }
    finally { sending = false; if (currentStatus === 'ai' && humanAvailable) handoff.disabled = false; }
  });
  soundButton?.addEventListener('click', async () => {
    soundEnabled = !soundEnabled;
    if (soundEnabled) {
      try {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) { audioContext ||= new AudioContextClass(); await audioContext.resume(); }
      } catch (_) { soundEnabled = false; }
    }
    soundButton.setAttribute('aria-pressed', soundEnabled ? 'true' : 'false');
    soundButton.setAttribute('aria-label', soundEnabled ? 'Matikan suara notifikasi' : 'Aktifkan suara notifikasi');
    soundButton.title = soundEnabled ? 'Suara notifikasi aktif' : 'Suara notifikasi nonaktif';
    if (soundEnabled) playReplyTone();
  });
  document.addEventListener('visibilitychange', () => {
    if (document.hidden) return;
    if (pollTimer) window.clearTimeout(pollTimer);
    pollTimer = null;
    poll().finally(() => schedulePoll(waitingForReply ? 2400 : 7000));
  });
  let greetingSeen = false;
  try { greetingSeen = sessionStorage.getItem('ilenaChatGreetingSeen') === '1'; } catch (_) {}
  if (teaser && !greetingSeen) {
    window.setTimeout(() => {
      let seen = false;
      try { seen = sessionStorage.getItem('ilenaChatGreetingSeen') === '1'; } catch (_) {}
      if (!opened && !seen) {
        teaser.hidden = false;
        try { sessionStorage.setItem('ilenaChatGreetingSeen', '1'); } catch (_) {}
      }
    }, 1800);
  }
})();
