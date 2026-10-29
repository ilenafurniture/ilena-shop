<?php

namespace App\Controllers;

use App\Services\SmartProductAssistant;
use App\Services\ChatProviderSettings;
use App\Services\ChatSupportSchedule;
use App\Services\ChatFeedbackService;
use App\Services\ChatTelemetry;
use App\Services\ChatContactDetails;
use App\Services\ChatWhatsappLink;
use CodeIgniter\HTTP\ResponseInterface;

class SupportChatController extends BaseController
{
    private const AI_RESOLVED_IDLE_SECONDS = 86400;

    private function db()
    {
        return db_connect();
    }

    private function visitorHash(bool $create = false): ?string
    {
        $token = (string)($this->request->getCookie('ilena_chat_visitor') ?? '');
        if (preg_match('/^[a-f0-9]{64}$/', $token)) return hash('sha256', $token);
        if (! $create) return null;

        $token = bin2hex(random_bytes(32));
        setcookie('ilena_chat_visitor', $token, [
            'expires' => time() + 60 * 60 * 24 * 180,
            'path' => '/',
            'secure' => $this->request->isSecure(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        return hash('sha256', $token);
    }

    private function json(array $payload, int $status = 200): ResponseInterface
    {
        $payload['csrfHash'] = csrf_hash();
        return $this->response->setStatusCode($status)->setJSON($payload);
    }

    /** Store support hours are 08:00–16:00 Asia/Jakarta, every day. */
    private function supportAvailability(): array
    {
        return ChatSupportSchedule::availability() + ['whatsappUrl' => $this->whatsappUrl()];
    }

    private function whatsappUrl(string $message = ''): string
    {
        return ChatWhatsappLink::forQuestion($message);
    }

    private function ownedConversation(string $visitorHash, bool $includeClosed = false): ?array
    {
        $builder = $this->db()->table('ilena_chat_conversations')->where('visitor_hash', $visitorHash);
        if (! $includeClosed) $builder->whereIn('status', ['ai', 'human_waiting', 'human']);
        return $builder->orderBy('last_message_at', 'DESC')->orderBy('id', 'DESC')->get(1)->getRowArray() ?: null;
    }

    private function latestClosedConversation(string $visitorHash): ?array
    {
        return $this->db()->table('ilena_chat_conversations')->where('visitor_hash', $visitorHash)
            ->where('status', 'closed')->orderBy('closed_at', 'DESC')->orderBy('id', 'DESC')->get(1)->getRowArray() ?: null;
    }

    private function shouldArchiveResolvedAiConversation(array $conversation): bool
    {
        if (($conversation['status'] ?? '') !== 'ai') return false;
        $lastMessage = strtotime((string)($conversation['last_message_at'] ?? ''));
        if (! $lastMessage || time() - $lastMessage < self::AI_RESOLVED_IDLE_SECONDS) return false;

        $db = $this->db();
        $resolved = $db->fieldExists('resolved_at', 'ilena_chat_conversations') && ! empty($conversation['resolved_at']);
        $rated = $db->tableExists('ilena_chat_feedback')
            && $db->table('ilena_chat_feedback')->where('conversation_id', (int)$conversation['id'])->countAllResults() > 0;
        return $resolved || $rated;
    }

    private function archiveResolvedAiConversation(array $conversation): bool
    {
        if (! $this->shouldArchiveResolvedAiConversation($conversation)) return false;
        $db = $this->db();
        $builder = $db->table('ilena_chat_conversations')->where('id', (int)$conversation['id'])
            ->where('visitor_hash', (string)$conversation['visitor_hash'])->where('status', 'ai');
        if (! empty($conversation['last_message_at'])) $builder->where('last_message_at', $conversation['last_message_at']);
        $now = date('Y-m-d H:i:s');
        $builder->update(['status' => 'closed', 'closed_at' => $now, 'updated_at' => $now]);
        return $db->affectedRows() === 1;
    }

    private function restartNoticeMessage(int $conversationId): ?array
    {
        $row = $this->db()->table('ilena_chat_messages')->where('conversation_id', $conversationId)
            ->where('sender_type', 'system')->where('body', 'Kita mulai percakapan baru ya, Kak.')
            ->orderBy('id', 'ASC')->get(1)->getRowArray();
        if (! $row) return null;
        $row['metadata'] = ['type' => 'conversation_restart_notice'];
        return $row;
    }

    private function customerConversation(string $visitorHash): ?array
    {
        $conversation = $this->ownedConversation($visitorHash);
        if ($conversation && $this->archiveResolvedAiConversation($conversation)) $conversation = null;
        if (! $conversation) $conversation = $this->latestClosedConversation($visitorHash);
        return $conversation;
    }

    private function chatMessageByRequestId(int $conversationId, string $requestId): ?array
    {
        if ($conversationId < 1 || $requestId === '' || ! $this->db()->fieldExists('client_request_id', 'ilena_chat_messages')) return null;
        return $this->db()->table('ilena_chat_messages')
            ->where('conversation_id', $conversationId)
            ->where('sender_type', 'visitor')
            ->where('client_request_id', $requestId)
            ->get(1)->getRowArray() ?: null;
    }

    private function chatSendResponse(array $conversation, array $message, array $availability, bool $deduplicated = false, ?array $startMessage = null): ResponseInterface
    {
        $body = (string) ($message['body'] ?? '');
        return $this->json([
            'success' => true,
            'conversationKey' => $conversation['conversation_key'],
            'messageId' => (int) $message['id'],
            'status' => (string) ($conversation['status'] ?? 'ai'),
            'humanAvailable' => $availability['open'],
            'serviceHours' => $availability['hours'],
            'whatsappUrl' => $this->whatsappUrl($body),
            'createdAt' => (string) ($message['created_at'] ?? ''),
            'deduplicated' => $deduplicated,
            'newConversation' => $startMessage !== null,
            'conversationStartMessage' => $startMessage,
        ]);
    }

    public function bootstrap()
    {
        $db = $this->db();
        if (! $db->tableExists('ilena_chat_conversations')) return $this->json(['success' => false, 'message' => 'Chat sedang disiapkan.'], 503);
        $visitor = $this->visitorHash(true);
        $conversation = $this->customerConversation($visitor);
        $contextProductId = trim((string)$this->request->getGet('product'));
        if ($contextProductId !== '' && $conversation && preg_match('/^[A-Za-z0-9_-]{1,64}$/', $contextProductId)) {
            $db->table('ilena_chat_conversations')->where('id', (int)$conversation['id'])->update(['context_product_id' => $contextProductId]);
            $conversation['context_product_id'] = $contextProductId;
        }
        $availability = $this->supportAvailability();
        if ($conversation) (new ChatFeedbackService())->issuePromptIfIdle($db, $conversation, $visitor);
        return $this->json([
            'success' => true,
            'conversationKey' => $conversation['conversation_key'] ?? null,
            'status' => $conversation['status'] ?? 'ai',
            'messages' => $conversation ? $this->messagesFor((int)$conversation['id'], 0, 50) : [],
            'humanAvailable' => $availability['open'],
            'serviceHours' => $availability['hours'],
            'whatsappUrl' => $availability['whatsappUrl'],
        ]);
    }

    public function poll()
    {
        $availability = $this->supportAvailability();
        $visitor = $this->visitorHash();
        if (! $visitor) return $this->json(['success' => true, 'conversationKey' => null, 'messages' => [], 'status' => 'ai', 'humanAvailable' => $availability['open'], 'serviceHours' => $availability['hours'], 'whatsappUrl' => $availability['whatsappUrl']]);
        $conversation = $this->customerConversation($visitor);
        if (! $conversation) return $this->json(['success' => true, 'conversationKey' => null, 'messages' => [], 'status' => 'ai', 'humanAvailable' => $availability['open'], 'serviceHours' => $availability['hours'], 'whatsappUrl' => $availability['whatsappUrl']]);
        (new ChatFeedbackService())->issuePromptIfIdle($this->db(), $conversation, $visitor);
        $requestedKey = trim((string)$this->request->getGet('conversationKey'));
        $after = hash_equals((string)$conversation['conversation_key'], $requestedKey) ? max(0, (int)$this->request->getGet('after')) : 0;
        return $this->json([
            'success' => true,
            'conversationKey' => $conversation['conversation_key'],
            'status' => $conversation['status'],
            'messages' => $this->messagesFor((int)$conversation['id'], $after, 40),
            'humanAvailable' => $availability['open'],
            'serviceHours' => $availability['hours'],
            'whatsappUrl' => $availability['whatsappUrl'],
        ]);
    }

    public function send()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $body = trim((string)($input['message'] ?? ''));
        if ($body === '' || mb_strlen($body) > 1200) return $this->json(['success' => false, 'message' => 'Pesan harus berisi 1–1.200 karakter.'], 422);
        $clientRequestId = trim((string) ($input['clientRequestId'] ?? ''));
        if ($clientRequestId !== '' && ! preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i', $clientRequestId)) {
            return $this->json(['success' => false, 'message' => 'Permintaan chat tidak valid. Muat ulang halaman lalu coba kembali.'], 422);
        }
        $availability = $this->supportAvailability();
        $visitor = $this->visitorHash(true);
        $db = $this->db();
        if (! $db->tableExists('ilena_chat_conversations') || ! $db->tableExists('ilena_chat_jobs')) {
            return $this->json(['success' => false, 'message' => 'Layanan chat belum selesai disiapkan.'], 503);
        }

        $conversation = $this->ownedConversation($visitor);
        if ($clientRequestId !== '' && $conversation) {
            $existing = $this->chatMessageByRequestId((int) $conversation['id'], $clientRequestId);
            if ($existing) {
                if (! hash_equals((string) $existing['body'], $body)) return $this->json(['success' => false, 'message' => 'Pesan ini sudah terkirim dengan isi berbeda.'], 409);
                return $this->chatSendResponse($conversation, $existing, $availability, true, $this->restartNoticeMessage((int)$conversation['id']));
            }
        }
        if ($conversation && $this->archiveResolvedAiConversation($conversation)) $conversation = null;
        $ip = (string) $this->request->getIPAddress();
        if (function_exists('service') && ! service('throttler')->check('ilena-chat-send-' . hash('sha256', $ip), 12, 60)) {
            return $this->json(['success' => false, 'message' => 'Tunggu sebentar sebelum mengirim pesan lagi.'], 429);
        }
        $contextProductId = trim((string)($input['productId'] ?? ''));
        if ($contextProductId !== '' && ! preg_match('/^[A-Za-z0-9_-]{1,64}$/', $contextProductId)) $contextProductId = '';
        $now = date('Y-m-d H:i:s');
        $startMessage = null;
        $db->transBegin();
        try {
            if (! $conversation) {
                $previousConversation = $this->latestClosedConversation($visitor);
                $key = bin2hex(random_bytes(32));
                $db->table('ilena_chat_conversations')->insert([
                    'conversation_key' => $key,
                    'visitor_hash' => $visitor,
                    'customer_email' => null,
                    'status' => 'ai',
                    'context_product_id' => $contextProductId ?: null,
                    'last_message_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $conversation = $db->table('ilena_chat_conversations')->where('conversation_key', $key)->get()->getRowArray();
                if ($previousConversation) {
                    $noticeBody = 'Kita mulai percakapan baru ya, Kak.';
                    $db->table('ilena_chat_messages')->insert([
                        'conversation_id' => (int)$conversation['id'],
                        'sender_type' => 'system',
                        'body' => $noticeBody,
                        'metadata' => json_encode(['type' => 'conversation_restart_notice'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'created_at' => $now,
                    ]);
                    $startMessage = ['id' => (int)$db->insertID(), 'sender_type' => 'system', 'body' => $noticeBody, 'metadata' => ['type' => 'conversation_restart_notice'], 'created_at' => $now];
                }
            }
            $conversationId = (int)$conversation['id'];
            $rateCount = $db->table('ilena_chat_messages')->where('conversation_id', $conversationId)
                ->where('sender_type', 'visitor')->where('created_at >=', date('Y-m-d H:i:s', time() - 60))->countAllResults();
            if ($rateCount >= 12) {
                $db->transRollback();
                return $this->json(['success' => false, 'message' => 'Tunggu sebentar sebelum mengirim pesan lagi.'], 429);
            }
            $db->table('ilena_chat_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_type' => 'visitor',
                'body' => $body,
                ...($clientRequestId !== '' && $db->fieldExists('client_request_id', 'ilena_chat_messages') ? ['client_request_id' => $clientRequestId] : []),
                'created_at' => $now,
            ]);
            $messageId = (int)$db->insertID();
            $db->table('ilena_chat_conversations')->where('id', $conversationId)->update([
                'context_product_id' => $contextProductId ?: ($conversation['context_product_id'] ?? null),
                'last_message_at' => $now,
                'updated_at' => $now,
                ...( $db->fieldExists('feedback_prompted_at', 'ilena_chat_conversations') ? ['feedback_prompted_at' => null] : [] ),
            ]);
            // AI remains the first responder at all hours. Only an explicit
            // handoff action moves the conversation into the staff queue.
            if (($conversation['status'] ?? 'ai') === 'ai') {
                $db->table('ilena_chat_jobs')->insert([
                    'conversation_id' => $conversationId,
                    'user_message_id' => $messageId,
                    'status' => 'queued',
                    'attempts' => 0,
                    'available_at' => $now,
                    'created_at' => $now,
                ]);
            }
            if ($db->transStatus() === false) throw new \RuntimeException('Penyimpanan pesan gagal.');
            $db->transCommit();
            return $this->chatSendResponse($conversation, ['id' => $messageId, 'body' => $body, 'created_at' => $now], $availability, false, $startMessage);
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($clientRequestId !== '') {
                $activeConversation = $this->ownedConversation($visitor);
                $existing = $activeConversation ? $this->chatMessageByRequestId((int) $activeConversation['id'], $clientRequestId) : null;
                if ($activeConversation && $existing) {
                    if (! hash_equals((string) $existing['body'], $body)) return $this->json(['success' => false, 'message' => 'Pesan ini sudah terkirim dengan isi berbeda.'], 409);
                    return $this->chatSendResponse($activeConversation, $existing, $availability, true, $this->restartNoticeMessage((int)$activeConversation['id']));
                }
            }
            log_message('error', 'Ilena chat send failed: {message}', ['message' => $e->getMessage()]);
            return $this->json(['success' => false, 'message' => 'Pesan belum tersimpan. Coba kirim kembali.'], 500);
        }
    }

    public function handoff()
    {
        $availability = $this->supportAvailability();
        if (! $availability['open']) {
            return $this->json([
                'success' => false,
                'code' => 'outside_hours',
                'message' => 'Tim Ilena sedang di luar jam layanan. Pesan Anda tetap bisa dikirim.',
                'whatsappUrl' => $this->whatsappUrl(),
            ], 200);
        }
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        try {
            $contact = ChatContactDetails::normalize((string)($input['name'] ?? ''), (string)($input['phone'] ?? ''));
        } catch (\InvalidArgumentException $error) {
            return $this->json(['success' => false, 'code' => 'invalid_contact', 'message' => $error->getMessage()], 422);
        }
        $visitor = $this->visitorHash();
        if (! $visitor) return $this->json(['success' => false, 'message' => 'Percakapan belum dimulai.'], 404);
        $conversation = $this->ownedConversation($visitor);
        if (! $conversation) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan.'], 404);
        if (($conversation['status'] ?? '') !== 'ai') return $this->json(['success' => false, 'message' => 'Percakapan sudah ditangani tim Ilena.'], 409);
        $db = $this->db();
        if (! $db->fieldExists('customer_name', 'ilena_chat_conversations') || ! $db->fieldExists('customer_phone', 'ilena_chat_conversations')) {
            return $this->json(['success' => false, 'message' => 'Formulir kontak staf sedang disiapkan. Silakan coba kembali sebentar lagi.'], 503);
        }
        $now = date('Y-m-d H:i:s');
        $db->transBegin();
        $db->table('ilena_chat_conversations')->where('id', (int)$conversation['id'])->where('status', 'ai')->update([
            'customer_name' => $contact['name'],
            'customer_phone' => $contact['phone'],
            'status' => 'human_waiting', 'last_message_at' => $now, 'updated_at' => $now,
        ]);
        if ($db->affectedRows() !== 1) {
            $db->transRollback();
            return $this->json(['success' => false, 'message' => 'Percakapan sudah berubah. Muat ulang lalu coba kembali.'], 409);
        }
        $db->table('ilena_chat_messages')->insert([
            'conversation_id' => (int)$conversation['id'], 'sender_type' => 'system',
            'body' => 'Pelanggan meminta bantuan staf. Pesan berikutnya akan dilayani oleh tim Ilena.',
            'created_at' => $now,
        ]);
        if ($db->transStatus() === false) {
            $db->transRollback();
            return $this->json(['success' => false, 'message' => 'Permintaan bantuan belum tersimpan. Coba kembali.'], 500);
        }
        $db->transCommit();
        ChatTelemetry::record($db, 'handoff', 'customer_requested_staff', 0);
        return $this->json(['success' => true, 'status' => 'human_waiting', 'humanAvailable' => true]);
    }

    public function resolve()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $conversationKey = trim((string)($input['conversationKey'] ?? ''));
        if (! preg_match('/^[a-f0-9]{64}$/', $conversationKey)) {
            return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan.'], 422);
        }
        $visitor = $this->visitorHash();
        if (! $visitor) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan.'], 404);
        $db = $this->db();
        $conversation = $db->table('ilena_chat_conversations')->where('conversation_key', $conversationKey)
            ->where('visitor_hash', $visitor)->where('status', 'ai')->get()->getRowArray();
        if (! $conversation) return $this->json(['success' => false, 'message' => 'Jawaban ini sudah diproses atau percakapan sedang ditangani tim.'], 409);

        $now = date('Y-m-d H:i:s');
        $thanks = 'Terima kasih, Kak. Senang bisa membantu. Kakak bisa memulai chat baru kapan saja.';
        $db->transBegin();
        try {
            $db->table('ilena_chat_messages')->insert([
                'conversation_id' => (int)$conversation['id'], 'sender_type' => 'assistant', 'body' => $thanks,
                'metadata' => json_encode(['type' => 'kak_wawa_resolved'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
            ]);
            $messageId = (int)$db->insertID();
            $conversationUpdate = ['last_message_at' => $now, 'updated_at' => $now];
            if ($db->fieldExists('resolved_at', 'ilena_chat_conversations')) $conversationUpdate['resolved_at'] = $now;
            $db->table('ilena_chat_conversations')->where('id', (int)$conversation['id'])->where('status', 'ai')->update($conversationUpdate);
            if ($db->transStatus() === false) throw new \RuntimeException('Gagal menyimpan status percakapan.');
            $db->transCommit();
            return $this->json(['success' => true, 'status' => 'ai', 'message' => ['id' => $messageId, 'sender_type' => 'assistant', 'body' => $thanks, 'created_at' => $now]]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Ilena chat resolution failed: {message}', ['message' => $e->getMessage()]);
            return $this->json(['success' => false, 'message' => 'Percakapan belum dapat ditutup. Silakan coba kembali.'], 500);
        }
    }

    public function rate()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $rating = filter_var($input['rating'] ?? null, FILTER_VALIDATE_INT);
        $conversationKey = trim((string)($input['conversationKey'] ?? ''));
        if ($rating === false || $rating < 1 || $rating > 5 || ! preg_match('/^[a-f0-9]{64}$/', $conversationKey)) {
            return $this->json(['success' => false, 'message' => 'Pilih penilaian dari 1 sampai 5 bintang.'], 422);
        }
        $db = $this->db();
        if (! $db->tableExists('ilena_chat_feedback') || ! $db->fieldExists('feedback_prompted_at', 'ilena_chat_conversations')) {
            return $this->json(['success' => false, 'message' => 'Fitur penilaian sedang disiapkan.'], 503);
        }
        $visitor = $this->visitorHash();
        if (! $visitor) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan.'], 404);
        $conversation = $db->table('ilena_chat_conversations')
            ->where('conversation_key', $conversationKey)->where('visitor_hash', $visitor)->get()->getRowArray();
        if (! $conversation || empty($conversation['feedback_prompted_at'])) {
            return $this->json(['success' => false, 'message' => 'Penilaian belum tersedia untuk percakapan ini.'], 404);
        }

        $existing = $db->table('ilena_chat_feedback')->where('conversation_id', (int)$conversation['id'])->get()->getRowArray();
        if ($existing) return $this->json(['success' => true, 'alreadyRated' => true, 'rating' => (int)$existing['rating']]);

        $now = date('Y-m-d H:i:s');
        $thankYou = 'Terima kasih, Kak. Penilaian Kakak sudah tersimpan dan membantu kami meningkatkan Kak Wawa.';
        $db->transBegin();
        try {
            $db->table('ilena_chat_feedback')->insert([
                'conversation_id' => (int)$conversation['id'],
                'visitor_hash' => $visitor,
                'rating' => $rating,
                'rated_at' => $now,
            ]);
            $db->table('ilena_chat_messages')->insert([
                'conversation_id' => (int)$conversation['id'],
                'sender_type' => 'assistant',
                'body' => $thankYou,
                'metadata' => json_encode(['type' => 'kak_wawa_rating_thanks', 'rating' => $rating], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
            ]);
            $messageId = (int)$db->insertID();
            $db->table('ilena_chat_conversations')->where('id', (int)$conversation['id'])->update(['last_message_at' => $now, 'updated_at' => $now]);
            if ($db->transStatus() === false) throw new \RuntimeException('Penyimpanan penilaian gagal.');
            $db->transCommit();
            return $this->json(['success' => true, 'rating' => $rating, 'message' => ['id' => $messageId, 'sender_type' => 'assistant', 'body' => $thankYou, 'created_at' => $now]]);
        } catch (\Throwable $e) {
            $db->transRollback();
            $existing = $db->table('ilena_chat_feedback')->where('conversation_id', (int)$conversation['id'])->get()->getRowArray();
            if ($existing) return $this->json(['success' => true, 'alreadyRated' => true, 'rating' => (int)$existing['rating']]);
            log_message('error', 'Ilena chat rating save failed: {message}', ['message' => $e->getMessage()]);
            return $this->json(['success' => false, 'message' => 'Penilaian belum tersimpan. Silakan coba kembali.'], 500);
        }
    }

    private function messagesFor(int $conversationId, int $after, int $limit): array
    {
        $builder = $this->db()->table('ilena_chat_messages')->where('conversation_id', $conversationId);
        if ($after > 0) {
            $rows = $builder->where('id >', $after)->orderBy('id', 'ASC')->get($limit)->getResultArray();
        } else {
            $rows = array_reverse($builder->orderBy('id', 'DESC')->get($limit)->getResultArray());
        }
        foreach ($rows as &$row) {
            $meta = json_decode((string)($row['metadata'] ?? ''), true);
            $row['metadata'] = is_array($meta) ? $meta : null;
        }
        unset($row);
        return $rows;
    }

    // ======================== Admin inbox ========================

    private function feedbackData(): array
    {
        $db = $this->db();
        if (! $db->tableExists('ilena_chat_feedback')) return ['stats' => ['count' => 0, 'average' => null], 'rows' => []];
        $stats = $db->table('ilena_chat_feedback')->select('COUNT(*) AS rating_count, AVG(rating) AS average_rating', false)->get()->getRowArray();
        $rows = $db->query('SELECT f.rating,f.rated_at,c.customer_email,c.context_product_id FROM ilena_chat_feedback f INNER JOIN ilena_chat_conversations c ON c.id=f.conversation_id ORDER BY f.rated_at DESC LIMIT 100')->getResultArray();
        return [
            'stats' => ['count' => (int)($stats['rating_count'] ?? 0), 'average' => isset($stats['average_rating']) ? (float)$stats['average_rating'] : null],
            'rows' => $rows,
        ];
    }

    public function adminFeedback()
    {
        $feedback = $this->feedbackData();
        return $this->json(['success' => true, 'stats' => $feedback['stats'], 'items' => $feedback['rows']]);
    }

    public function adminTelemetry()
    {
        return $this->json(['success' => true] + ChatTelemetry::summary($this->db(), 7));
    }

    public function adminIndex()
    {
        $db = $this->db();
        $providerSettings = new ChatProviderSettings($db);
        $knowledge = $db->tableExists('ilena_chat_knowledge')
            ? $db->table('ilena_chat_knowledge')->orderBy('updated_at', 'DESC')->get(200)->getResultArray()
            : [];
        $products = $db->tableExists('barang')
            ? $db->table('barang')->select('id,nama,harga')->where('active', '1')->orderBy('nama', 'ASC')->get(500)->getResultArray()
            : [];
        $specs = $db->tableExists('ilena_chat_product_specs')
            ? $db->table('ilena_chat_product_specs')->orderBy('updated_at', 'DESC')->get(300)->getResultArray()
            : [];
        $feedback = $this->feedbackData();
        return view('admin/supportChat', [
            'title' => 'Live Chat Ilena',
            'knowledge' => $knowledge,
            'products' => $products,
            'productSpecs' => $specs,
            'feedbackStats' => $feedback['stats'],
            'feedbackRows' => $feedback['rows'],
            'isDev' => (string) session()->get('role') === '1',
            'providerSettingsReady' => $providerSettings->tableReady(),
            'providerSettings' => $providerSettings->adminSummary(),
            'encryptionKeyReady' => $providerSettings->encryptionReady(),
            'chatReady' => $db->tableExists('ilena_chat_conversations')
                && $db->tableExists('ilena_chat_messages')
                && $db->tableExists('ilena_chat_jobs')
                && $db->tableExists('ilena_chat_knowledge')
                && $db->tableExists('ilena_chat_product_specs')
                && $db->tableExists('ilena_chat_feedback')
                && $db->tableExists('ilena_chat_events')
                && $db->tableExists('ilena_chat_reads'),
        ]);
    }

    /** Provider credentials are a Dev-only setting, even if staff can use the inbox. */
    public function saveProviderSettings()
    {
        if ((string) session()->get('role') !== '1') {
            return redirect()->to('/admin/support-chat')->with('chat_error', 'Hanya Dev yang dapat mengubah provider AI.');
        }

        $endpoint = trim((string) $this->request->getPost('endpoint'));
        $model = trim((string) $this->request->getPost('model'));
        $apiKey = trim((string) $this->request->getPost('api_key'));
        $enabled = $this->request->getPost('is_enabled') === '1';
        $clearKey = $this->request->getPost('clear_api_key') === '1';
        $parts = $endpoint !== '' ? parse_url($endpoint) : [];
        $endpointHost = strtolower((string) ($parts['host'] ?? ''));
        $validEndpoint = $endpoint === '' || (
            filter_var($endpoint, FILTER_VALIDATE_URL)
            && strtolower((string) ($parts['scheme'] ?? '')) === 'https'
            && ! empty($parts['host'])
            && (($parts['port'] ?? 443) === 443)
            && empty($parts['user'])
            && empty($parts['pass'])
            && empty($parts['fragment'])
            && ! filter_var($endpointHost, FILTER_VALIDATE_IP)
            && ! preg_match('/(?:^|\.)(localhost|local|internal|test)$/i', $endpointHost)
        );
        $validModel = $model === '' || preg_match('~^[A-Za-z0-9][A-Za-z0-9._:/-]{0,159}$~', $model) === 1;
        if (! $validEndpoint || ! $validModel || mb_strlen($endpoint) > 500 || mb_strlen($apiKey) > 4000) {
            return redirect()->back()->withInput()->with('chat_error', 'Periksa endpoint HTTPS, ID model, dan panjang API key. Gunakan endpoint OpenAI-compatible yang tepercaya.');
        }
        if ($enabled && ($endpoint === '' || $model === '')) {
            return redirect()->back()->withInput()->with('chat_error', 'Isi endpoint dan ID model sebelum mengaktifkan provider.');
        }

        $result = (new ChatProviderSettings($this->db()))->save(
            $endpoint,
            $model,
            $apiKey,
            $enabled,
            $clearKey,
            (string) session()->get('email')
        );
        return redirect()->to('/admin/support-chat#provider')
            ->with($result['ok'] ? 'chat_notice' : 'chat_error', $result['message']);
    }

    public function adminQueue()
    {
        // AI-only chats are private to the customer/assistant unless the
        // customer explicitly requests staff. Keep them out of the inbox.
        $db = $this->db();
        $email = mb_substr(trim((string) session()->get('email')), 0, 191);
        $bucket = (string) $this->request->getGet('bucket');
        $bucket = in_array($bucket, ['waiting', 'mine', 'team', 'closed'], true) ? $bucket : 'all';
        $term = mb_substr(trim((string) $this->request->getGet('q')), 0, 100);
        $where = [];
        $binds = [$email];

        if ($bucket === 'closed') {
            $where[] = "c.status='closed'";
        } else {
            $where[] = "(c.status='human' OR (c.status='human_waiting' AND EXISTS (SELECT 1 FROM ilena_chat_messages handoff WHERE handoff.conversation_id=c.id AND handoff.sender_type='system' AND handoff.body='Pelanggan meminta bantuan staf. Pesan berikutnya akan dilayani oleh tim Ilena.')))";
            if ($bucket === 'waiting') $where[] = "c.status='human_waiting'";
            if ($bucket === 'mine') { $where[] = "c.status='human' AND c.assigned_to=?"; $binds[] = $email; }
            if ($bucket === 'team') { $where[] = "c.status='human' AND c.assigned_to IS NOT NULL AND c.assigned_to<>?"; $binds[] = $email; }
        }
        if ($term !== '') {
            $like = '%' . $term . '%';
            $where[] = "(c.customer_name LIKE ? OR c.customer_phone LIKE ? OR c.customer_email LIKE ? OR c.context_product_id LIKE ? OR EXISTS (SELECT 1 FROM ilena_chat_messages search_m WHERE search_m.conversation_id=c.id AND search_m.body LIKE ?))";
            array_push($binds, $like, $like, $like, $like, $like);
        }

        $sql = "SELECT c.id,c.conversation_key,c.status,c.assigned_to,c.customer_name,c.customer_phone,c.customer_email,c.context_product_id,c.last_message_at,c.created_at,"
            . "(SELECT LEFT(m.body,220) FROM ilena_chat_messages m WHERE m.conversation_id=c.id ORDER BY m.id DESC LIMIT 1) AS last_body,"
            . "(SELECT m.sender_type FROM ilena_chat_messages m WHERE m.conversation_id=c.id ORDER BY m.id DESC LIMIT 1) AS last_sender,"
            . "(SELECT COUNT(*) FROM ilena_chat_messages unread WHERE unread.conversation_id=c.id AND unread.sender_type='visitor' AND unread.id>COALESCE((SELECT r.last_read_message_id FROM ilena_chat_reads r WHERE r.conversation_id=c.id AND r.staff_id=?),0)) AS unread_count "
            . 'FROM ilena_chat_conversations c WHERE ' . implode(' AND ', $where)
            . ($bucket === 'closed' ? ' ORDER BY c.closed_at DESC,c.id DESC' : " ORDER BY CASE WHEN c.status='human_waiting' THEN 0 ELSE 1 END,c.last_message_at DESC,c.id DESC")
            . ' LIMIT 80';
        $rows = $db->query($sql, $binds)->getResultArray();
        return $this->json(['success' => true, 'items' => $rows]);
    }

    public function adminMessages(int $id)
    {
        $conversation = $this->db()->table('ilena_chat_conversations')->where('id', $id)->get()->getRowArray();
        if (! $conversation) return $this->json(['success' => false], 404);
        if (! empty($conversation['context_product_id']) && $this->db()->tableExists('barang')) {
            $product = $this->db()->table('barang')->select('nama')->where('id', (string) $conversation['context_product_id'])->get()->getRowArray();
            if ($product) {
                $conversation['context_product_name'] = (string) $product['nama'];
                $conversation['context_product_url'] = '/product/' . rawurlencode(strtolower(str_replace(' ', '-', (string) $product['nama'])));
            }
        }
        $after = max(0, (int)$this->request->getGet('after'));
        return $this->json(['success' => true, 'conversation' => $conversation, 'messages' => $this->messagesFor($id, $after, 80)]);
    }

    /** Save an explicitly selected, staff-authored answer as an unpublished KB draft. */
    public function draftKnowledgeFromChat(int $id)
    {
        $input = $this->request->getJSON(true) ?? [];
        $questionId = max(0, (int) ($input['questionMessageId'] ?? 0));
        $answerId = max(0, (int) ($input['answerMessageId'] ?? 0));
        if ($questionId < 1 || $answerId <= $questionId) {
            return $this->json(['success' => false, 'message' => 'Pilih pasangan tanya-jawab yang valid.'], 422);
        }

        $db = $this->db();
        $pair = $db->table('ilena_chat_messages')
            ->where('conversation_id', $id)
            ->whereIn('id', [$questionId, $answerId])
            ->orderBy('id', 'ASC')->get()->getResultArray();
        if (count($pair) !== 2 || (int) $pair[0]['id'] !== $questionId || (int) $pair[1]['id'] !== $answerId
            || $pair[0]['sender_type'] !== 'visitor' || $pair[1]['sender_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Hanya pertanyaan pelanggan dengan jawaban staf yang dapat dijadikan draf.'], 422);
        }
        $interveningQuestion = $db->table('ilena_chat_messages')->where('conversation_id', $id)
            ->where('sender_type', 'visitor')->where('id >', $questionId)->where('id <', $answerId)->countAllResults();
        if ($interveningQuestion > 0) return $this->json(['success' => false, 'message' => 'Pilih jawaban staf yang tepat untuk pertanyaan tersebut.'], 422);

        $question = trim((string) $pair[0]['body']);
        $answer = trim((string) $pair[1]['body']);
        $sensitive = '/(?:[\w.+-]+@[\w.-]+\.[a-z]{2,}|(?:\+?\d[\d ().-]{8,}\d)|\b(?:alamat lengkap|nomor telepon pribadi|email pribadi|nomor pesanan|order id|tracking order|riwayat pesanan|pesanan pribadi|akun pelanggan|data pelanggan|password|kata sandi|otp|api.?key|access token|kredensial)\b)/iu';
        if ($question === '' || $answer === '' || mb_strlen($question) > 1200 || mb_strlen($answer) > 2000
            || (new SmartProductAssistant($db))->isRestrictedKnowledgeCandidate($question)
            || preg_match($sensitive, $question . "\n" . $answer) === 1) {
            return $this->json(['success' => false, 'message' => 'Pasangan ini berisi data atau topik sensitif, jadi tidak dibuat sebagai draf.'], 422);
        }

        $title = 'FAQ chat: ' . mb_substr(preg_replace('/\s+/u', ' ', $question), 0, 175);
        $content = "Pertanyaan pelanggan: {$question}\n\nJawaban staf: {$answer}";
        $existing = $db->table('ilena_chat_knowledge')->where('title', $title)->where('content', $content)->get(1)->getRowArray();
        if ($existing) return $this->json(['success' => true, 'message' => 'Draf serupa sudah ada di Knowledge base.', 'knowledgeId' => (int) $existing['id'], 'duplicate' => true]);

        $now = date('Y-m-d H:i:s');
        $data = [
            'title' => $title, 'content' => $content, 'source_url' => null, 'is_published' => 0,
            'updated_by' => mb_substr((string) session()->get('email'), 0, 191), 'created_at' => $now, 'updated_at' => $now,
        ];
        if ($db->fieldExists('valid_from', 'ilena_chat_knowledge')) $data += ['valid_from' => null, 'expires_at' => null, 'reviewed_at' => null, 'reviewed_by' => null];
        if ($db->fieldExists('revision', 'ilena_chat_knowledge')) $data['revision'] = 1;
        $db->table('ilena_chat_knowledge')->insert($data);
        if ($db->affectedRows() !== 1) return $this->json(['success' => false, 'message' => 'Draf belum tersimpan.'], 500);
        return $this->json(['success' => true, 'message' => 'Draf tersimpan. Periksa dan terbitkan hanya setelah isinya terverifikasi.', 'knowledgeId' => (int) $db->insertID()]);
    }

    public function adminRead(int $id)
    {
        $db = $this->db();
        $conversation = $db->table('ilena_chat_conversations')->select('id')->where('id', $id)->get()->getRowArray();
        if (! $conversation) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan.'], 404);
        $latest = $db->table('ilena_chat_messages')->selectMax('id', 'last_id')->where('conversation_id', $id)->get()->getRowArray();
        $staff = mb_substr(trim((string) session()->get('email')), 0, 191);
        $db->table('ilena_chat_reads')->replace([
            'conversation_id' => $id,
            'staff_id' => $staff,
            'last_read_message_id' => (int) ($latest['last_id'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->json(['success' => true]);
    }

    public function adminReply(int $id)
    {
        $body = trim((string)($this->request->getJSON(true)['message'] ?? $this->request->getPost('message') ?? ''));
        if ($body === '' || mb_strlen($body) > 2000) return $this->json(['success' => false, 'message' => 'Balasan harus berisi 1–2.000 karakter.'], 422);
        $db = $this->db();
        $conversation = $db->table('ilena_chat_conversations')->where('id', $id)->where('status <>', 'closed')->get()->getRowArray();
        if (! $conversation) return $this->json(['success' => false, 'message' => 'Percakapan sudah ditutup atau tidak ditemukan.'], 404);
        $now = date('Y-m-d H:i:s');
        $email = mb_substr(trim((string)session()->get('email')), 0, 191);
        if (! empty($conversation['assigned_to']) && strcasecmp((string)$conversation['assigned_to'], $email) !== 0) {
            return $this->json(['success' => false, 'message' => 'Percakapan sedang ditangani staf lain.'], 409);
        }
        $db->transBegin();
        if (($conversation['status'] ?? '') === 'human_waiting' || empty($conversation['assigned_to'])) {
            $db->table('ilena_chat_conversations')->where('id', $id)->groupStart()
                ->where('status', 'human_waiting')->orGroupStart()
                    ->where('status', 'human')->groupStart()->where('assigned_to', null)->orWhere('assigned_to', '')->groupEnd()
                ->groupEnd()->groupEnd()
                ->update(['status' => 'human', 'assigned_to' => $email, 'updated_at' => $now]);
            if ($db->affectedRows() !== 1) {
                $db->transRollback();
                $latest = $db->table('ilena_chat_conversations')->where('id', $id)->get()->getRowArray();
                if (! $latest || ($latest['status'] ?? '') === 'closed') return $this->json(['success' => false, 'message' => 'Percakapan sudah ditutup.'], 409);
                if (strcasecmp((string) ($latest['assigned_to'] ?? ''), $email) !== 0) return $this->json(['success' => false, 'message' => 'Percakapan baru saja diambil staf lain.'], 409);
                $db->transBegin();
            }
        }
        $db->table('ilena_chat_messages')->insert(['conversation_id' => $id, 'sender_type' => 'admin', 'sender_id' => $email, 'body' => $body, 'created_at' => $now]);
        $db->table('ilena_chat_conversations')->where('id', $id)->update(['status' => 'human', 'assigned_to' => $email, 'last_message_at' => $now, 'updated_at' => $now]);
        if ($db->transStatus() === false) { $db->transRollback(); return $this->json(['success' => false, 'message' => 'Balasan gagal disimpan.'], 500); }
        $db->transCommit();
        return $this->json(['success' => true, 'csrfHash' => csrf_hash()]);
    }

    public function adminClaim(int $id)
    {
        $db = $this->db();
        $email = mb_substr(trim((string)session()->get('email')), 0, 191);
        $current = $db->table('ilena_chat_conversations')->where('id', $id)->whereIn('status', ['human_waiting', 'human'])->get()->getRowArray();
        if (! $current) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan atau sudah ditutup.'], 404);
        if (($current['status'] ?? '') === 'human' && strcasecmp((string) ($current['assigned_to'] ?? ''), $email) === 0) {
            return $this->json(['success' => true, 'alreadyAssigned' => true]);
        }
        $db->table('ilena_chat_conversations')->where('id', $id)->groupStart()
            ->where('status', 'human_waiting')->orGroupStart()
                ->where('status', 'human')->groupStart()->where('assigned_to', null)->orWhere('assigned_to', '')->groupEnd()
            ->groupEnd()->groupEnd()
            ->update(['status' => 'human', 'assigned_to' => $email, 'updated_at' => date('Y-m-d H:i:s')]);
        if ($db->affectedRows() === 1) return $this->json(['success' => true]);
        $latest = $db->table('ilena_chat_conversations')->where('id', $id)->get()->getRowArray();
        if ($latest && ($latest['status'] ?? '') === 'human' && strcasecmp((string) ($latest['assigned_to'] ?? ''), $email) === 0) {
            return $this->json(['success' => true, 'alreadyAssigned' => true]);
        }
        return $this->json(['success' => false, 'message' => 'Percakapan sudah diambil staf lain. Perbarui antrean.'], 409);
    }

    public function adminClose(int $id)
    {
        $db = $this->db();
        $conversation = $db->table('ilena_chat_conversations')->where('id', $id)->where('status <>', 'closed')->get()->getRowArray();
        if (! $conversation) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan atau sudah ditutup.'], 404);
        $email = mb_substr(trim((string) session()->get('email')), 0, 191);
        if (! empty($conversation['assigned_to']) && strcasecmp((string) $conversation['assigned_to'], $email) !== 0) {
            return $this->json(['success' => false, 'message' => 'Percakapan sedang ditangani staf lain.'], 409);
        }
        if (! $db->table('ilena_chat_conversations')->where('id', $id)->where('status <>', 'closed')->update([
            'status' => 'closed', 'closed_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ])) return $this->json(['success' => false, 'message' => 'Percakapan tidak ditemukan atau gagal ditutup.'], 404);
        return $this->json(['success' => true]);
    }

    public function saveKnowledge()
    {
        $id = max(0, (int)$this->request->getPost('id'));
        $title = trim((string)$this->request->getPost('title'));
        $content = trim((string)$this->request->getPost('content'));
        $source = trim((string)$this->request->getPost('source_url'));
        $sourceParts = $source !== '' ? parse_url($source) : [];
        $validSource = $source === '' || (filter_var($source, FILTER_VALIDATE_URL)
            && in_array(strtolower((string)($sourceParts['scheme'] ?? '')), ['http', 'https'], true)
            && empty($sourceParts['user']) && empty($sourceParts['pass']));
        if ($title === '' || mb_strlen($title) > 191 || $content === '' || mb_strlen($content) > 30000 || ! $validSource) {
            return redirect()->back()->withInput()->with('chat_error', 'Periksa judul, isi (maksimal 30.000 karakter), dan URL sumber.');
        }
        $published = $this->request->getPost('is_published') ? 1 : 0;
        $data = ['title' => $title, 'content' => $content, 'source_url' => $source ?: null,
            'is_published' => $published,
            'updated_by' => mb_substr((string)session()->get('email'), 0, 191), 'updated_at' => date('Y-m-d H:i:s')];
        if ($this->db()->fieldExists('valid_from', 'ilena_chat_knowledge')) {
            $validFrom = trim((string) $this->request->getPost('valid_from'));
            $expiresAt = trim((string) $this->request->getPost('expires_at'));
            foreach ([$validFrom, $expiresAt] as $date) {
                $parsed = $date === '' ? null : \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
                if ($date !== '' && (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || ! $parsed || $parsed->format('Y-m-d') !== $date)) {
                    return redirect()->back()->withInput()->with('chat_error', 'Tanggal berlaku harus menggunakan format tanggal yang benar.');
                }
            }
            if ($validFrom !== '' && $expiresAt !== '' && $expiresAt < $validFrom) {
                return redirect()->back()->withInput()->with('chat_error', 'Tanggal akhir harus sama atau setelah tanggal mulai.');
            }
            $data['valid_from'] = $validFrom ?: null;
            $data['expires_at'] = $expiresAt ?: null;
            $data['reviewed_at'] = $published ? date('Y-m-d H:i:s') : null;
            $data['reviewed_by'] = $published ? mb_substr((string) session()->get('email'), 0, 191) : null;
        }
        $table = $this->db()->table('ilena_chat_knowledge');
        if ($this->db()->fieldExists('revision', 'ilena_chat_knowledge')) {
            $previous = $id ? $this->db()->table('ilena_chat_knowledge')->select('revision')->where('id', $id)->get()->getRowArray() : null;
            $data['revision'] = $previous ? max(1, (int) ($previous['revision'] ?? 1)) + 1 : 1;
        }
        if ($id) $table->where('id', $id)->update($data);
        else { $data['created_at'] = date('Y-m-d H:i:s'); $table->insert($data); }
        return redirect()->to('/admin/support-chat#knowledge')->with('chat_notice', 'Sumber knowledge tersimpan.');
    }

    public function deleteKnowledge(int $id)
    {
        $this->db()->table('ilena_chat_knowledge')->where('id', $id)->delete();
        return redirect()->to('/admin/support-chat#knowledge')->with('chat_notice', 'Sumber knowledge dihapus.');
    }

    public function saveProductSpec()
    {
        $id = trim((string)$this->request->getPost('product_id'));
        $validProduct = $this->db()->table('barang')->select('id')->where('id', $id)->where('active', '1')->get()->getRowArray();
        $values = [trim((string)$this->request->getPost('colors')), trim((string)$this->request->getPost('materials')), trim((string)$this->request->getPost('dimensions')), trim((string)$this->request->getPost('keywords'))];
        if (! $validProduct || max(array_map('mb_strlen', $values)) > 5000) return redirect()->back()->withInput()->with('chat_error', 'Produk tidak valid atau spesifikasi terlalu panjang.');
        $spec = ['product_id' => $id, 'colors' => mb_substr($values[0], 0, 500), 'materials' => mb_substr($values[1], 0, 500), 'dimensions' => mb_substr($values[2], 0, 255), 'keywords' => mb_substr($values[3], 0, 5000), 'updated_by' => mb_substr((string)session()->get('email'), 0, 191), 'updated_at' => date('Y-m-d H:i:s')];
        $table = $this->db()->table('ilena_chat_product_specs');
        $existing = $table->where('product_id', $id)->get()->getRowArray();
        if ($existing) $table->where('product_id', $id)->update($spec); else $table->insert($spec);
        return redirect()->to('/admin/support-chat#products')->with('chat_notice', 'Spesifikasi assistant tersimpan.');
    }
}
