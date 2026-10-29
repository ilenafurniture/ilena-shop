(() => {
  'use strict';
  const widget = document.querySelector('[data-ilena-chat-widget]');
  if (!widget) return;

  const panel = widget.querySelector('#container-greeting-card');
  const toggle = widget.querySelector('[data-open-ilena-chat]');
  const close = widget.querySelector('[data-close-chat-greeting]');
  const transcript = widget.querySelector('[data-chat-messages]');
  const form = widget.querySelector('[data-chat-form]');
  const contactForm = widget.querySelector('[data-chat-contact-form]');
  const contactCancel = widget.querySelector('[data-chat-contact-cancel]');
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
  const wawaImageBase = widget.dataset.wawaImageBase || '';
  const wawaHeaderAvatar = widget.querySelector('[data-wawa-avatar="header"]');
  const wawaGreetingAvatar = widget.querySelector('[data-wawa-avatar="greeting"]');
  const wawaTypingAvatar = widget.querySelector('[data-wawa-avatar="typing"]');
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
  let waitingSince = 0;
  let slowReplyNoticeShown = false;
  let lastNoticeKey = null;
  let lastWhatsappUrl = '';
  const pendingRequestIds = new Map();
  let soundEnabled = true;
  let arrivalSoundPending = false;
  let arrivalSoundPlayed = false;
  let audioContext = null;
  let greetingAvatarFile = '';
  let greetingDismissed = false;
  let greetingOpened = false;
  let greetingAutoHideTimer = null;
  const wawaAvatarPools = {
    greeting: ['wawa1-sapaan', 'wawa2-sapaan'],
    thinking: ['wawa1-berfikir', 'wawa2-berfikir'],
    response: ['wawa1-respon', 'wawa1-mencatat', 'wawa2-solusi'],
    note: ['wawa1-mencatat'],
    unsure: ['wawa2-binggung'],
    gratitude: ['wawa1-terimakasih'],
    happy: ['wawa1-senang', 'wawa2-senang'],
    solved: ['wawa2-sip'],
    solution: ['wawa2-solusi'],
  };
  const wawaAvatarCursors = new Map();
  let pollTimer = null;
  let launcherIdleTimer = null;
  let lastActivityRefresh = 0;

  const safeText = (value) => String(value ?? '');
  const cleanChatCopy = (value) => safeText(value).replace(/^(?:saya\s+memahami\s+(?:pertanyaan|pesan|bahasa)\b[^.!?]{0,240}(?:bahasa\s+indonesia|dalam\s+bahasa\s+indonesia)[^.!?]*[.!?]\s*)+/iu, '').trim();
  function nextWawaAvatar(mood) {
    const pool = wawaAvatarPools[mood] || wawaAvatarPools.response;
    const index = wawaAvatarCursors.get(mood) ?? Math.floor(Math.random() * pool.length);
    wawaAvatarCursors.set(mood, (index + 1) % pool.length);
    return pool[index % pool.length];
  }
  function applyWawaAvatar(image, file) {
    if (!image || !file || !wawaImageBase || image.dataset.wawaFile === file) return;
    image.dataset.wawaFile = file;
    image.src = wawaImageBase + encodeURIComponent(file) + '.webp';
  }
  function setWawaHeader(mood, file = '') {
    applyWawaAvatar(wawaHeaderAvatar, file || nextWawaAvatar(mood));
  }
  function assistantAvatarMood(message, metadata) {
    const reason = safeText(metadata?.fallbackReason);
    const copy = cleanChatCopy(message.body || '').toLocaleLowerCase('id-ID');
    if (metadata?.type === 'kak_wawa_resolved') return 'solved';
    if (metadata?.type === 'kak_wawa_rating_prompt') return 'greeting';
    if (metadata?.type === 'kak_wawa_rating_thanks') return 'gratitude';
    if (['no_retrieval_evidence', 'provider_request_failed', 'provider_http_error', 'provider_empty'].includes(reason)) return 'unsure';
    if (/^(halo|hai|hi|selamat pagi|selamat siang|selamat sore|selamat malam)\b|ada yang bisa.{0,40}bantu/.test(copy)) return 'greeting';
    if (/terima kasih|terimakasih|sama-sama|sama sama/.test(copy)) return 'gratitude';
    if (/tersimpan|sudah dicatat|saya catat|kami catat|diteruskan ke tim/.test(copy)) return 'note';
    if (Array.isArray(metadata?.products) && metadata.products.length) return 'solution';
    if (/senang|berhasil|sudah cukup|sip, kak/.test(copy)) return 'happy';
    if (/pilihan|rekomendasi|solusi|cara yang bisa/.test(copy)) return 'solution';
    return 'response';
  }
  const currency = (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value) || 0);
  function parseUtcChatTimestamp(value) {
    const match = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})$/.exec(String(value || '').trim());
    if (!match) return null;
    const date = new Date(Date.UTC(Number(match[1]), Number(match[2]) - 1, Number(match[3]), Number(match[4]), Number(match[5]), Number(match[6])));
    return Number.isNaN(date.getTime()) ? null : date;
  }
  function formatChatTimestamp(value) {
    const date = parseUtcChatTimestamp(value);
    if (!date) return safeText(value);
    return new Intl.DateTimeFormat('id-ID', {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23', timeZoneName: 'short',
    }).format(date);
  }
  function showNotice(message, linkUrl = '') {
    const noticeKey = JSON.stringify([message || '', linkUrl || '']);
    if (noticeKey === lastNoticeKey) return;
    lastNoticeKey = noticeKey;
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
  function showOfflineNotice() {
    showNotice('Mohon maaf, CS sedang tidak tersedia di luar jam layanan (08.00–16.00 WIB). Kak Wawa tetap siap membantu Kakak di sini.', lastWhatsappUrl);
  }
  async function api(url, options = {}) {
    const headers = new Headers(options.headers || {});
    const { timeoutMs = options.method === 'POST' ? 15000 : 10000, ...fetchOptions } = options;
    if (options.method === 'POST') {
      headers.set('X-CSRF-TOKEN', csrf.value);
      headers.set('Content-Type', 'application/json');
    }
    const controller = new AbortController();
    const timeout = window.setTimeout(() => controller.abort(), timeoutMs);
    try {
      let response;
      try {
        response = await fetch(url, { ...fetchOptions, headers, credentials: 'same-origin', cache: 'no-store', signal: controller.signal });
      } catch (cause) {
        const timedOut = controller.signal.aborted;
        const error = new Error(timedOut ? 'Kak Wawa belum menerima konfirmasi dalam batas waktu.' : 'Koneksi Kak Wawa terputus sementara.');
        error.data = { code: timedOut ? 'request_timeout' : 'network_error' };
        error.cause = cause;
        throw error;
      }
      let data;
      try {
        data = await response.json();
      } catch (cause) {
        if (controller.signal.aborted) {
          const error = new Error('Kak Wawa belum menerima konfirmasi dalam batas waktu.');
          error.data = { code: 'request_timeout' };
          error.cause = cause;
          throw error;
        }
        data = {};
      }
      updateCsrf(data);
      if (!response.ok || !data.success) {
        const known = ['Tunggu sebentar sebelum mengirim pesan lagi.', 'Pesan harus berisi 1–1.200 karakter.'];
        const error = new Error(data.code === 'invalid_contact' || known.includes(data.message) ? data.message : 'Chat belum dapat memproses pesan. Silakan coba lagi atau lanjutkan melalui WhatsApp.');
        error.data = data;
        throw error;
      }
      return data;
    } finally {
      window.clearTimeout(timeout);
    }
  }

  function getClientRequestId(message) {
    if (pendingRequestIds.has(message)) return pendingRequestIds.get(message);
    const requestId = typeof window.crypto?.randomUUID === 'function'
      ? window.crypto.randomUUID()
      : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, character => {
        const random = Math.floor(Math.random() * 16);
        return (character === 'x' ? random : (random & 0x3) | 0x8).toString(16);
      });
    pendingRequestIds.set(message, requestId);
    while (pendingRequestIds.size > 8) pendingRequestIds.delete(pendingRequestIds.keys().next().value);
    return requestId;
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
  function getChatAudioContext() {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (!AudioContextClass) return null;
    if (!audioContext || audioContext.state === 'closed') audioContext = new AudioContextClass();
    return audioContext;
  }
  async function playReplyTone() {
    if (!soundEnabled) return;
    try {
      const context = getChatAudioContext();
      if (!context) return;
      if (context.state === 'suspended') await context.resume();
      if (!soundEnabled || context.state !== 'running') return;
      const start = context.currentTime + 0.01;
      [660, 880].forEach((frequency, index) => {
        const oscillator = context.createOscillator();
        const gain = context.createGain();
        oscillator.type = 'sine'; oscillator.frequency.value = frequency;
        gain.gain.setValueAtTime(0.0001, start + index * 0.1);
        gain.gain.exponentialRampToValueAtTime(0.085, start + index * 0.1 + 0.025);
        gain.gain.exponentialRampToValueAtTime(0.0001, start + index * 0.1 + 0.16);
        oscillator.connect(gain); gain.connect(context.destination);
        oscillator.start(start + index * 0.1); oscillator.stop(start + index * 0.1 + 0.17);
      });
    } catch (_) {}
  }
  function playWelcomeTone() {
    if (!arrivalSoundPending || arrivalSoundPlayed) return;
    try {
      const context = getChatAudioContext();
      if (!context) return;
      const play = () => {
        if (context.state !== 'running' || arrivalSoundPlayed) return;
        const start = context.currentTime;
        [[523.25, 0], [659.25, .13]].forEach(([frequency, offset]) => {
          const oscillator = context.createOscillator();
          const gain = context.createGain();
          oscillator.type = 'sine';
          oscillator.frequency.value = frequency;
          gain.gain.setValueAtTime(.0001, start + offset);
          gain.gain.exponentialRampToValueAtTime(.055, start + offset + .025);
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
      const context = getChatAudioContext();
      if (context?.state === 'suspended') context.resume().catch(() => {});
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
    const body = document.createElement('div'); body.textContent = cleanChatCopy(message.body); bubble.append(body);
    const meta = message.metadata && typeof message.metadata === 'object' ? message.metadata : {};
    let messageNode = bubble;
    if (message.sender_type === 'assistant') {
      const avatarFile = nextWawaAvatar(assistantAvatarMood(message, meta));
      const row = document.createElement('div'); row.className = 'ilena-chat-message-row';
      const avatar = document.createElement('span'); avatar.className = 'ilena-chat-message-avatar'; avatar.setAttribute('aria-hidden', 'true');
      const portrait = document.createElement('img'); portrait.alt = ''; portrait.width = 360; portrait.height = 360; portrait.loading = 'lazy'; portrait.decoding = 'async';
      applyWawaAvatar(portrait, avatarFile);
      avatar.append(portrait); row.append(avatar, bubble); messageNode = row;
      applyWawaAvatar(wawaHeaderAvatar, avatarFile);
    }
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
          link.textContent = 'Lanjutkan pertanyaan ini melalui WhatsApp'; bubble.append(link);
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
            if (result.message) appendMessage({ ...result.message, metadata: { type: 'kak_wawa_rating_thanks' } });
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
          if (result.message) appendMessage({ ...result.message, metadata: { type: 'kak_wawa_resolved' } });
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
    const time = document.createElement('time');
    const messageTime = parseUtcChatTimestamp(message.created_at);
    if (messageTime) time.dateTime = messageTime.toISOString();
    time.textContent = formatChatTimestamp(message.created_at || '');
    bubble.append(time);
    transcript.insertBefore(messageNode, typing);
    transcript.scrollTop = transcript.scrollHeight;
    if (message.sender_type === 'assistant' || message.sender_type === 'admin') {
      waitingForReply = false;
      waitingSince = 0;
      setTyping(false);
      if (slowReplyNoticeShown) {
        slowReplyNoticeShown = false;
        if (humanAvailable === false) showOfflineNotice();
        else showNotice('');
      }
      if (notify) playReplyTone();
    }
  }
  function updateStatus(value, availability = humanAvailable) {
    const previousStatus = currentStatus;
    currentStatus = value || 'ai';
    humanAvailable = availability;
    if (currentStatus === 'human_waiting' && previousStatus !== 'human_waiting') setWawaHeader('note');
    if (status) {
      if (currentStatus === 'human_waiting') status.textContent = humanAvailable ? 'Tim Ilena segera bergabung' : 'CS sedang tidak tersedia';
      else if (currentStatus === 'human') status.textContent = 'Anda sedang dilayani tim Ilena';
      else if (currentStatus === 'closed') status.textContent = 'Percakapan selesai · Kakak bisa mengirim pesan untuk memulai yang baru';
      else status.textContent = humanAvailable ? 'Kak Wawa · CS 08.00–16.00 WIB' : 'CS sedang tidak tersedia · Kak Wawa siap membantu';
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
    if (value) {
      const mood = /menunggu tim|diteruskan ke tim/i.test(copy) ? 'note' : 'thinking';
      const avatarFile = nextWawaAvatar(mood);
      applyWawaAvatar(wawaTypingAvatar, avatarFile);
      applyWawaAvatar(wawaHeaderAvatar, avatarFile);
    }
    if (value) transcript.scrollTop = transcript.scrollHeight;
  }
  function renderMessages(items, notify = false) { (items || []).forEach(item => appendMessage(item, notify)); }
  function resetTranscriptForConversationChange() {
    cursor = 0;
    transcript.querySelectorAll('.ilena-chat-bubble, .ilena-chat-empty, .ilena-chat-welcome').forEach(node => node.remove());
    waitingForReply = false;
    waitingSince = 0;
    slowReplyNoticeShown = false;
    setTyping(false);
    showNotice('');
  }
  async function requestHandoff() {
    if (sending || currentStatus !== 'ai') return;
    if (!humanAvailable) { showOfflineNotice(); return; }
    if (contactForm) {
      contactForm.hidden = false;
      showNotice('Lengkapi nama dan nomor WhatsApp agar permintaan dapat diteruskan ke staf.');
      contactForm.elements.contactName?.focus();
      return;
    }
    showNotice('Formulir kontak belum tersedia. Silakan hubungi Ilena melalui WhatsApp.', lastWhatsappUrl);
  }
  async function submitHandoff(event) {
    event.preventDefault();
    if (sending || currentStatus !== 'ai' || !contactForm) return;
    if (!contactForm.checkValidity()) {
      showNotice(!contactForm.elements.contactName.value.trim()
        ? 'Mohon isi nama Kakak terlebih dahulu.'
        : 'Mohon isi nomor WhatsApp yang valid agar staf dapat menghubungi Kakak.');
      contactForm.querySelector(':invalid')?.focus();
      return;
    }
    sending = true;
    transcript.querySelectorAll('.ilena-chat-resolution button').forEach(button => { button.disabled = true; });
    const submitButton = contactForm.querySelector('button[type="submit"]');
    if (submitButton) submitButton.disabled = true;
    try {
      const data = await api('/chat/handoff', { method: 'POST', body: JSON.stringify({ name: contactForm.elements.contactName.value, phone: contactForm.elements.contactPhone.value }) });
      updateStatus(data.status, data.humanAvailable);
      contactForm.hidden = true;
      contactForm.reset();
      await poll();
      showNotice('Permintaan Kakak sudah diteruskan ke tim Ilena.');
    } catch (error) {
      handleError(error);
    } finally {
      sending = false;
      updateStatus(currentStatus, humanAvailable);
      if (currentStatus === 'ai') transcript.querySelectorAll('.ilena-chat-resolution button').forEach(button => { button.disabled = false; });
      if (submitButton) submitButton.disabled = false;
    }
  }
  contactForm?.addEventListener('submit', submitHandoff);
  contactCancel?.addEventListener('click', () => { contactForm.hidden = true; contactForm.reset(); showNotice(''); });
  function handleError(error, preservePendingReply = false) {
    const data = error?.data || {};
    if (data.code === 'outside_hours') {
      humanAvailable = false;
      updateStatus(currentStatus, false);
      waitingForReply = false; waitingSince = 0; setTyping(false);
      showNotice(data.message || 'Di luar jam layanan. Pesan Anda tetap bisa dikirim dan akan dibalas saat tim kembali.', data.whatsappUrl || '');
      return;
    }
    if (preservePendingReply && waitingForReply && ['request_timeout', 'network_error'].includes(data.code)) {
      setTyping(false);
      showNotice('Koneksi sempat terputus. Kak Wawa akan mencoba lagi tanpa mengirim ulang pesan Kakak.', lastWhatsappUrl);
      return;
    }
    waitingForReply = false; waitingSince = 0; setTyping(false);
    if (['request_timeout', 'network_error'].includes(data.code)) {
      showNotice('Kak Wawa belum menerima konfirmasi. Jika pesan belum muncul, kirim ulang pesan yang sama; pesan tidak akan digandakan.', lastWhatsappUrl);
      return;
    }
    showNotice(error?.message || 'Chat sedang sulit terhubung. Silakan lanjutkan melalui WhatsApp.');
  }
  async function bootstrap() {
    if (ready || sending) return;
    sending = true;
    try {
      const productId = widget.dataset.contextProduct || '';
      const data = await api('/chat/bootstrap' + (productId ? '?product=' + encodeURIComponent(productId) : ''));
      currentConversationKey = data.conversationKey || currentConversationKey;
      lastWhatsappUrl = data.whatsappUrl || lastWhatsappUrl;
      humanAvailable = data.humanAvailable === true;
      transcript.setAttribute('aria-live', 'off');
      renderMessages(data.messages);
      updateStatus(data.status, humanAvailable);
      ready = true;
      const latestMessage = Array.isArray(data.messages) ? data.messages[data.messages.length - 1] : null;
      if (data.status === 'ai' && latestMessage?.sender_type === 'visitor') {
        waitingForReply = true;
        waitingSince = Date.now();
        setTyping(true, 'Kak Wawa sedang menyiapkan jawaban');
      }
      if (!humanAvailable) showOfflineNotice();
      else showNotice('');
    } catch (error) { handleError(error); }
    finally { transcript.setAttribute('aria-live', 'polite'); sending = false; updateStatus(currentStatus, humanAvailable); }
  }
  async function poll() {
    if (!opened || !ready || polling || document.hidden) return;
    polling = true;
    try {
      const data = await api('/chat/poll?after=' + cursor + '&conversationKey=' + encodeURIComponent(currentConversationKey));
      const conversationChanged = !!data.conversationKey && !!currentConversationKey && data.conversationKey !== currentConversationKey;
      if (conversationChanged) resetTranscriptForConversationChange();
      currentConversationKey = data.conversationKey || currentConversationKey;
      lastWhatsappUrl = data.whatsappUrl || lastWhatsappUrl;
      renderMessages(data.messages, true);
      updateStatus(data.status, data.humanAvailable === true);
      if (data.humanAvailable === false && !waitingForReply && !slowReplyNoticeShown) showOfflineNotice();
    } catch (error) { handleError(error, true); }
    finally { polling = false; }
  }
  function nextPollDelay() {
    if (waitingForReply) {
      const elapsed = Date.now() - (waitingSince || Date.now());
      if (elapsed >= 30000 && !slowReplyNoticeShown) {
        slowReplyNoticeShown = true;
        setTyping(false);
        showNotice('Maaf, jawaban Kak Wawa masih tertunda. Kakak tidak perlu mengirim ulang pesan; Kak Wawa akan terus memeriksa jawabannya.', lastWhatsappUrl);
      }
      return elapsed >= 30000 ? 6000 : 2000;
    }
    if (currentStatus === 'human') return 4000;
    if (currentStatus === 'human_waiting') return 10000;
    return 15000;
  }
  function schedulePoll(delay = 0) {
    if (!opened || !ready || pollTimer) return;
    pollTimer = window.setTimeout(async () => {
      pollTimer = null;
      await poll();
      if (opened) schedulePoll(nextPollDelay());
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
  function scheduleGreetingAutoHide(delay = 22000) {
    if (!teaser || teaser.hidden || opened || greetingOpened || greetingDismissed) return;
    if (greetingAutoHideTimer) window.clearTimeout(greetingAutoHideTimer);
    greetingAutoHideTimer = window.setTimeout(() => {
      greetingAutoHideTimer = null;
      const pointerIsHovering = window.matchMedia?.('(hover: hover)').matches && widget.matches(':hover');
      const focusIsInside = widget.contains(document.activeElement);
      if (!opened && !greetingOpened && !greetingDismissed && !pointerIsHovering && !focusIsInside) teaser.hidden = true;
    }, delay);
  }
  function wakeLauncher() {
    if (launcherIdleTimer) window.clearTimeout(launcherIdleTimer);
    launcherIdleTimer = null;
    widget.classList.remove('is-chat-idle');
    scheduleGreetingAutoHide();
    if (!opened) scheduleLauncherIdle();
  }
  function refreshLauncherIdleFromActivity() {
    const now = Date.now();
    if (now - lastActivityRefresh < 1000) return;
    lastActivityRefresh = now;
    scheduleLauncherIdle();
    scheduleGreetingAutoHide();
  }

  function openChat() {
    opened = true;
    greetingOpened = true;
    if (greetingAutoHideTimer) window.clearTimeout(greetingAutoHideTimer);
    greetingAutoHideTimer = null;
    try { sessionStorage.setItem('ilenaChatGreetingOpened', '1'); } catch (_) {}
    if (greetingAvatarFile) setWawaHeader('greeting', greetingAvatarFile);
    else setWawaHeader('greeting');
    panel.classList.remove('d-none');
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
  widget.addEventListener('pointerleave', () => { scheduleLauncherIdle(); scheduleGreetingAutoHide(); });
  widget.addEventListener('focusin', wakeLauncher);
  widget.addEventListener('focusout', () => window.setTimeout(scheduleLauncherIdle, 0));
  document.addEventListener('pointerdown', refreshLauncherIdleFromActivity, { passive: true });
  document.addEventListener('touchstart', refreshLauncherIdleFromActivity, { passive: true });
  document.addEventListener('keydown', refreshLauncherIdleFromActivity);
  document.addEventListener('scroll', refreshLauncherIdleFromActivity, { passive: true });
  teaser?.querySelector('[data-greeting-open]')?.addEventListener('click', openChat);
  teaser?.querySelector('[data-greeting-dismiss]')?.addEventListener('click', () => {
    greetingDismissed = true;
    if (greetingAutoHideTimer) window.clearTimeout(greetingAutoHideTimer);
    greetingAutoHideTimer = null;
    teaser.hidden = true;
    try {
      sessionStorage.setItem('ilenaChatGreetingSeen', '1');
      sessionStorage.setItem('ilenaChatGreetingDismissed', '1');
    } catch (_) {}
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
    const clientRequestId = getClientRequestId(message);
    const sendPayload = JSON.stringify({ message, productId: widget.dataset.contextProduct || '', clientRequestId });
    try {
      let data;
      try {
        data = await api('/chat/send', { method: 'POST', body: sendPayload });
      } catch (firstError) {
        // A timeout can happen after the server commits. Retry once with the
        // exact same request ID so the database returns the original message.
        if (!['request_timeout', 'network_error'].includes(firstError?.data?.code)) throw firstError;
        data = await api('/chat/send', { method: 'POST', body: sendPayload });
      }
      pendingRequestIds.delete(message);
      const conversationChanged = !!data.conversationKey && !!currentConversationKey && data.conversationKey !== currentConversationKey;
      if (conversationChanged || data.newConversation) resetTranscriptForConversationChange();
      currentConversationKey = data.conversationKey || currentConversationKey;
      lastWhatsappUrl = data.whatsappUrl || lastWhatsappUrl;
      input.value = ''; ready = true; updateStatus(data.status, data.humanAvailable);
      if (data.conversationStartMessage) appendMessage(data.conversationStartMessage);
      appendMessage({ id: data.messageId, sender_type: 'visitor', body: message, created_at: data.createdAt || '' });
      waitingForReply = data.status === 'ai';
      waitingSince = waitingForReply ? Date.now() : 0;
      slowReplyNoticeShown = false;
      if (waitingForReply) setTyping(true, 'Kak Wawa sedang menyiapkan jawaban');
      else if (data.status === 'human_waiting') setTyping(true, 'Pesan Anda menunggu tim Ilena');
      if (data.humanAvailable === false) {
        showOfflineNotice();
      }
    } catch (error) { handleError(error); }
    finally {
      sending = false;
      updateStatus(currentStatus, humanAvailable);
      if (!input.disabled) input.focus();
      if (pollTimer) window.clearTimeout(pollTimer);
      pollTimer = null;
      poll().finally(() => schedulePoll(nextPollDelay()));
    }
  });
  handoff?.addEventListener('click', requestHandoff);
  soundButton?.addEventListener('click', async () => {
    soundEnabled = !soundEnabled;
    if (soundEnabled) {
      try {
        const context = getChatAudioContext();
        if (context?.state === 'suspended') await context.resume();
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
    primeReplySound();
    if (pollTimer) window.clearTimeout(pollTimer);
    pollTimer = null;
    poll().finally(() => schedulePoll(nextPollDelay()));
  });
  scheduleLauncherIdle();
  const unlockArrivalSound = () => playWelcomeTone();
  document.addEventListener('pointerdown', unlockArrivalSound, { passive: true });
  document.addEventListener('keydown', unlockArrivalSound);
  let greetingSeen = false;
  try {
    greetingSeen = sessionStorage.getItem('ilenaChatGreetingSeen') === '1';
    greetingDismissed = sessionStorage.getItem('ilenaChatGreetingDismissed') === '1';
    greetingOpened = sessionStorage.getItem('ilenaChatGreetingOpened') === '1';
  } catch (_) {}
  function showGreetingTeaser(reminder = false) {
    if (!teaser || opened || greetingOpened || greetingDismissed || document.hidden) return false;
    greetingAvatarFile = nextWawaAvatar('greeting');
    applyWawaAvatar(wawaGreetingAvatar, greetingAvatarFile);
    teaser.hidden = false;
    teaser.classList.add('is-arrival');
    arrivalSoundPending = true;
    toggle.classList.add('is-arrival');
    window.setTimeout(() => teaser.classList.remove('is-arrival'), 2300);
    window.setTimeout(() => toggle.classList.remove('is-arrival'), 1450);
    try {
      sessionStorage.setItem('ilenaChatGreetingSeen', '1');
      if (reminder) sessionStorage.setItem('ilenaChatGreetingReminderShown', '1');
    } catch (_) {}
    if (navigator.userActivation?.hasBeenActive) playWelcomeTone();
    scheduleGreetingAutoHide(reminder ? 18000 : 22000);
    return true;
  }
  if (teaser && !greetingSeen && !greetingDismissed && !greetingOpened) {
    window.setTimeout(() => {
      if (showGreetingTeaser()) {
        window.setTimeout(() => {
          let reminderShown = false;
          try { reminderShown = sessionStorage.getItem('ilenaChatGreetingReminderShown') === '1'; } catch (_) {}
          if (!reminderShown) showGreetingTeaser(true);
        }, 8 * 60 * 1000);
      }
    }, 2200);
  }
})();
