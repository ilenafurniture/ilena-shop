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
  const suggestions = widget.querySelector('.ilena-chat-suggestions');
  const teaser = widget.querySelector('[data-chat-greeting]');
  const waLink = widget.querySelector('[data-kbot-wa]');
  const csrf = form?.querySelector('input[type="hidden"]');
  if (!panel || !toggle || !form || !input || !csrf) return;

  let opened = false;
  let sending = false;
  let polling = false;
  let ready = false;
  let cursor = 0;
  let currentConversationKey = '';
  let currentStatus = 'ai';
  let humanAvailable = null;
  let waitingForReply = false;
  let soundEnabled = true;
  let arrivalSoundPending = false;
  let arrivalSoundPlayed = false;
  let audioContext = null;
  let pollTimer = null;
  let launcherIdleTimer = null;
  let lastActivityRefresh = 0;

  const safeText = (value) => String(value ?? '');
  const currency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value) || 0);
  function showNotice(message, linkUrl = '') {
    notice.replaceChildren();
    if (message) {
      notice.append(document.createTextNode(message));
      if (linkUrl && waLink) {
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
    if (product.stockKnown === true && Number.isFinite(Number(product.stockTotalAvailable))) {
      const stock = document.createElement('small');
      const available = Math.max(0, Number(product.stockTotalAvailable));
      stock.textContent = available > 0 ? 'Stok tersedia: ' + new Intl.NumberFormat('id-ID').format(available) : 'Stok habis';
      info.append(stock);
    }
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
        gain.gain.exponentialRampToValueAtTime(0.055, start + index * 0.1 + 0.025);
        gain.gain.exponentialRampToValueAtTime(0.0001, start + index * 0.1 + 0.16);
        oscillator.connect(gain); gain.connect(audioContext.destination);
        oscillator.start(start + index * 0.1); oscillator.stop(start + index * 0.1 + 0.17);
      });
    } catch (_) {}
  }
  function playWelcomeTone() {
    if (!arrivalSoundPending || arrivalSoundPlayed) return;
    try {
      const AudioContextClass = window.AudioContext || window.webkitAudioContext;
      if (!AudioContextClass) return;
      audioContext ||= new AudioContextClass();
      const context = audioContext;
      const play = () => {
        if (context.state !== 'running' || arrivalSoundPlayed) return;
        const start = context.currentTime;
        [[523.25, 0], [659.25, .13]].forEach(([frequency, offset]) => {
          const oscillator = context.createOscillator();
          const gain = context.createGain();
          oscillator.type = 'sine';
          oscillator.frequency.value = frequency;
          gain.gain.setValueAtTime(.0001, start + offset);
          gain.gain.exponentialRampToValueAtTime(.034, start + offset + .025);
          gain.gain.exponentialRampToValueAtTime(.0001, start + offset + .19);
          oscillator.connect(gain); gain.connect(context.destination);
          oscillator.start(start + offset); oscillator.stop(start + offset + .2);
        });
        arrivalSoundPlayed = true;
        arrivalSoundPending = false;
      };
      const ready = context.state === 'suspended' ? context.resume() : Promise.resolve();
      ready.then(play).catch(() => {});
    } catch (_) {}
  }
  function primeReplySound() {
    if (!soundEnabled) return;
    try {
      const AudioContextClass = window.AudioContext || window.webkitAudioContext;
      if (!AudioContextClass) return;
      audioContext ||= new AudioContextClass();
      if (audioContext.state === 'suspended') audioContext.resume().catch(() => {});
    } catch (_) {}
  }
  function appendMessage(message, notify = false) {
    const id = Number(message.id) || 0;
    if (!id || id <= cursor) return;
    cursor = id;
    transcript.querySelector('.ilena-chat-welcome')?.remove();
    transcript.querySelector('.ilena-chat-empty')?.remove();
    const bubble = document.createElement('div');
    bubble.className = 'ilena-chat-bubble' + (message.sender_type === 'visitor' ? ' is-visitor' : message.sender_type === 'system' ? ' is-system' : message.sender_type === 'admin' ? ' is-staff' : ' is-assistant');
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
    if (typeof meta.whatsappUrl === 'string') {
      try {
        const url = new URL(meta.whatsappUrl, location.origin);
        if (url.protocol === 'https:' && url.hostname === 'wa.me') {
          const link = document.createElement('a');
          link.className = 'ilena-chat-source'; link.href = url.href; link.target = '_blank'; link.rel = 'noopener noreferrer';
          link.textContent = 'Silakan hubungi tim Ilena melalui WhatsApp'; bubble.append(link);
        }
      } catch (_) {}
    }
    if (meta.type === 'kak_wawa_rating_prompt') {
      const rating = document.createElement('div');
      rating.className = 'ilena-chat-rating';
      rating.setAttribute('role', 'group');
      rating.setAttribute('aria-label', 'Nilai pengalaman chat Kak Wawa');
      for (let value = 1; value <= 5; value += 1) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = '★';
        button.setAttribute('aria-label', value + ' dari 5 bintang');
        button.title = value + ' dari 5 bintang';
        button.addEventListener('click', async () => {
          rating.querySelectorAll('button').forEach(item => { item.disabled = true; });
          try {
            const result = await api('/chat/rating', { method: 'POST', body: JSON.stringify({ conversationKey: currentConversationKey, rating: value }) });
            rating.classList.add('is-submitted');
            rating.setAttribute('aria-label', 'Penilaian ' + result.rating + ' dari 5 tersimpan');
            if (result.message) appendMessage(result.message);
            else showNotice('Penilaian Kakak sudah tersimpan. Terima kasih.');
          } catch (error) {
            rating.querySelectorAll('button').forEach(item => { item.disabled = false; });
            showNotice(error?.message || 'Penilaian belum tersimpan. Silakan coba kembali.');
          }
        });
        rating.append(button);
      }
      bubble.append(rating);
    }
    if (meta.resolutionCheck === true) {
      const actions = document.createElement('div');
      actions.className = 'ilena-chat-resolution';
      const prompt = document.createElement('p');
      prompt.textContent = 'Apakah jawaban Kak Wawa sudah cukup, atau Kakak ingin dibantu tim Ilena?';
      const satisfied = document.createElement('button');
      satisfied.type = 'button';
      satisfied.textContent = 'Jawaban sudah cukup';
      satisfied.addEventListener('click', async () => {
        actions.querySelectorAll('button').forEach(button => { button.disabled = true; });
        try {
          const result = await api('/chat/resolve', { method: 'POST', body: JSON.stringify({ conversationKey: currentConversationKey }) });
          updateStatus(result.status, humanAvailable);
          if (result.message) appendMessage(result.message);
        } catch (error) {
          actions.querySelectorAll('button').forEach(button => { button.disabled = false; });
          showNotice(error?.message || 'Pilihan Kakak belum tersimpan. Silakan coba kembali.');
        }
      });
      const askStaff = document.createElement('button');
      askStaff.type = 'button';
      askStaff.textContent = 'Ingin bicara dengan CS';
      askStaff.addEventListener('click', requestHandoff);
      actions.append(prompt, satisfied, askStaff);
      bubble.append(actions);
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
      else status.textContent = humanAvailable ? 'Kak Wawa · Tim 08.00–16.00 WIB' : 'Kak Wawa · Tim kembali pukul 08.00 WIB';
    }
    if (handoff) handoff.hidden = currentStatus !== 'ai' || humanAvailable === false;
    if (suggestions) suggestions.hidden = currentStatus === 'human';
    input.disabled = sending;
    if (sendButton) sendButton.disabled = sending;
    input.placeholder = humanAvailable === false ? 'Tulis pesan untuk Ilena…' : 'Tulis pertanyaan…';
  }
  function setTyping(value, copy = 'Kak Wawa sedang mengetik') {
    if (!typing) return;
    typing.hidden = !value;
    if (typingCopy) typingCopy.textContent = copy;
    if (value) transcript.scrollTop = transcript.scrollHeight;
  }
  function renderMessages(items, notify = false) { (items || []).forEach(item => appendMessage(item, notify)); }
  async function requestHandoff() {
    if (sending || currentStatus !== 'ai') return;
    sending = true;
    transcript.querySelectorAll('.ilena-chat-resolution button').forEach(button => { button.disabled = true; });
    if (handoff) handoff.disabled = true;
    try {
      const data = await api('/chat/handoff', { method: 'POST', body: '{}' });
      updateStatus(data.status, data.humanAvailable);
      await poll();
      showNotice('Permintaan Kakak sudah diteruskan ke tim Ilena.');
    } catch (error) {
      handleError(error);
    } finally {
      sending = false;
      updateStatus(currentStatus, humanAvailable);
      if (currentStatus === 'ai') transcript.querySelectorAll('.ilena-chat-resolution button').forEach(button => { button.disabled = false; });
      if (handoff && currentStatus === 'ai' && humanAvailable) handoff.disabled = false;
    }
  }
  function handleError(error) {
    const data = error?.data || {};
    if (data.code === 'outside_hours') {
      humanAvailable = false;
      updateStatus(currentStatus, false);
      waitingForReply = false; setTyping(false);
      showNotice(data.message || 'Di luar jam layanan. Pesan Anda tetap bisa dikirim dan akan dibalas saat tim kembali.', data.whatsappUrl || '');
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
      currentConversationKey = data.conversationKey || currentConversationKey;
      humanAvailable = data.humanAvailable === true;
      renderMessages(data.messages);
      updateStatus(data.status, humanAvailable);
      ready = true;
      if (!humanAvailable) showNotice('Tim CS sedang di luar jam layanan (' + (data.serviceHours || '08.00–16.00 WIB') + '), tetapi Kak Wawa tetap siap membantu di sini.', data.whatsappUrl || '');
      else showNotice('');
    } catch (error) { handleError(error); }
    finally { sending = false; updateStatus(currentStatus, humanAvailable); }
  }
  async function poll() {
    if (!opened || !ready || polling || document.hidden) return;
    polling = true;
    try {
      const data = await api('/chat/poll?after=' + cursor);
      currentConversationKey = data.conversationKey || currentConversationKey;
      renderMessages(data.messages, true);
      updateStatus(data.status, data.humanAvailable === true);
      if (data.humanAvailable === false && !waitingForReply) showNotice('Tim CS sedang di luar jam layanan (' + (data.serviceHours || '08.00–16.00 WIB') + '), tetapi Kak Wawa tetap siap membantu di sini.', data.whatsappUrl || '');
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

  function scheduleLauncherIdle() {
    if (launcherIdleTimer) window.clearTimeout(launcherIdleTimer);
    launcherIdleTimer = null;
    widget.classList.remove('is-chat-idle');
    const pointerIsHovering = window.matchMedia?.('(hover: hover)').matches && widget.matches(':hover');
    const focusIsInside = widget.contains(document.activeElement);
    if (opened || document.hidden || pointerIsHovering || focusIsInside) return;
    launcherIdleTimer = window.setTimeout(() => {
      launcherIdleTimer = null;
      if (!opened && !document.hidden) widget.classList.add('is-chat-idle');
    }, 60000);
  }
  function wakeLauncher() {
    if (launcherIdleTimer) window.clearTimeout(launcherIdleTimer);
    launcherIdleTimer = null;
    widget.classList.remove('is-chat-idle');
    if (!opened) scheduleLauncherIdle();
  }
  function refreshLauncherIdleFromActivity() {
    const now = Date.now();
    if (now - lastActivityRefresh < 1000) return;
    lastActivityRefresh = now;
    scheduleLauncherIdle();
  }

  function openChat() {
    opened = true; panel.classList.remove('d-none');
    if (launcherIdleTimer) window.clearTimeout(launcherIdleTimer);
    launcherIdleTimer = null;
    widget.classList.remove('is-chat-idle');
    if (teaser) teaser.hidden = true;
    toggle.classList.remove('is-arrival');
    primeReplySound();
    toggle.setAttribute('aria-expanded', 'true');
    bootstrap().finally(() => { if (!input.disabled) input.focus(); schedulePoll(); });
  }
  toggle.addEventListener('click', () => {
    if (opened) { opened = false; panel.classList.add('d-none'); toggle.setAttribute('aria-expanded', 'false'); if (pollTimer) window.clearTimeout(pollTimer); pollTimer = null; scheduleLauncherIdle(); }
    else openChat();
  });
  widget.addEventListener('pointerenter', wakeLauncher);
  widget.addEventListener('pointerleave', scheduleLauncherIdle);
  widget.addEventListener('focusin', wakeLauncher);
  widget.addEventListener('focusout', () => window.setTimeout(scheduleLauncherIdle, 0));
  document.addEventListener('pointerdown', refreshLauncherIdleFromActivity, { passive: true });
  document.addEventListener('touchstart', refreshLauncherIdleFromActivity, { passive: true });
  document.addEventListener('keydown', refreshLauncherIdleFromActivity);
  document.addEventListener('scroll', refreshLauncherIdleFromActivity, { passive: true });
  teaser?.querySelector('[data-greeting-open]')?.addEventListener('click', openChat);
  teaser?.querySelector('[data-greeting-dismiss]')?.addEventListener('click', () => {
    teaser.hidden = true;
    try { sessionStorage.setItem('ilenaChatGreetingSeen', '1'); } catch (_) {}
  });
  widget.querySelectorAll('[data-chat-suggestion]').forEach(button => button.addEventListener('click', () => {
    if (input.disabled) return;
    input.value = button.dataset.chatSuggestion || '';
    form.requestSubmit();
  }));
  input.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' || event.shiftKey || event.isComposing || event.keyCode === 229) return;
    event.preventDefault();
    if (typeof form.requestSubmit === 'function') form.requestSubmit();
    else sendButton?.click();
  });
  close.addEventListener('click', () => { opened = false; panel.classList.add('d-none'); toggle.setAttribute('aria-expanded', 'false'); if (pollTimer) window.clearTimeout(pollTimer); pollTimer = null; toggle.focus(); scheduleLauncherIdle(); });
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = input.value.trim();
    if (!message || sending) return;
    transcript.querySelectorAll('.ilena-chat-resolution button').forEach(button => { button.disabled = true; });
    sending = true; input.disabled = true; if (sendButton) sendButton.disabled = true; showNotice('');
    try {
      const data = await api('/chat/send', { method: 'POST', body: JSON.stringify({ message, productId: widget.dataset.contextProduct || '' }) });
      currentConversationKey = data.conversationKey || currentConversationKey;
      input.value = ''; ready = true; updateStatus(data.status, data.humanAvailable);
      appendMessage({ id: data.messageId, sender_type: 'visitor', body: message, created_at: data.createdAt || '' });
      if (data.afterHoursReply) {
        appendMessage({
          id: data.afterHoursReply.messageId,
          sender_type: 'assistant',
          body: data.afterHoursReply.body,
          metadata: { whatsappUrl: data.afterHoursReply.whatsappUrl },
          created_at: data.afterHoursReply.createdAt || data.createdAt || '',
        }, true);
        waitingForReply = false;
        setTyping(false);
        showNotice('Tim CS sedang di luar jam layanan (' + (data.serviceHours || '08.00–16.00 WIB') + '), tetapi Kak Wawa tetap menyiapkan jawaban untuk Kakak. Jika ingin berbicara dengan tim, Kakak bisa menghubungi WhatsApp.', data.afterHoursReply.whatsappUrl || data.whatsappUrl || '');
      } else {
        waitingForReply = data.status === 'ai';
        if (waitingForReply) setTyping(true, 'Kak Wawa sedang menyiapkan jawaban');
        else if (data.status === 'human_waiting') setTyping(true, 'Pesan Anda menunggu tim Ilena');
      }
    } catch (error) { handleError(error); }
    finally { sending = false; updateStatus(currentStatus, humanAvailable); if (!input.disabled) input.focus(); poll(); }
  });
  handoff?.addEventListener('click', requestHandoff);
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
    if (document.hidden) {
      if (launcherIdleTimer) window.clearTimeout(launcherIdleTimer);
      launcherIdleTimer = null;
      return;
    }
    scheduleLauncherIdle();
    if (pollTimer) window.clearTimeout(pollTimer);
    pollTimer = null;
    poll().finally(() => schedulePoll(waitingForReply ? 2400 : 7000));
  });
  scheduleLauncherIdle();
  const unlockArrivalSound = () => playWelcomeTone();
  document.addEventListener('pointerdown', unlockArrivalSound, { passive: true });
  document.addEventListener('keydown', unlockArrivalSound);
  let greetingSeen = false;
  try { greetingSeen = sessionStorage.getItem('ilenaChatGreetingSeen') === '1'; } catch (_) {}
  if (teaser && !greetingSeen) {
    window.setTimeout(() => {
      if (opened || document.hidden) return;
      teaser.hidden = false;
      arrivalSoundPending = true;
      toggle.classList.add('is-arrival');
      window.setTimeout(() => toggle.classList.remove('is-arrival'), 1450);
      try { sessionStorage.setItem('ilenaChatGreetingSeen', '1'); } catch (_) {}
      if (navigator.userActivation?.hasBeenActive) playWelcomeTone();
    }, 2200);
  }
})();
