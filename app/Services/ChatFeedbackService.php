<?php

namespace App\Services;

final class ChatFeedbackService
{
    public const IDLE_SECONDS = 300;
    public const PROMPT_BODY = 'Kak, semoga jawaban Kak Wawa membantu. Boleh beri penilaian untuk pengalaman chat ini?';

    public static function isEligible(string $senderType, string $createdAt, ?int $now = null): bool
    {
        if (! in_array($senderType, ['assistant', 'admin'], true)) return false;
        $lastReply = strtotime($createdAt);
        if ($lastReply === false) return false;
        return (($now ?? time()) - $lastReply) > self::IDLE_SECONDS;
    }

    /** Add one rating prompt after five minutes of silence following an Ilena reply. */
    public function issuePromptIfIdle($db, array $conversation, string $visitorHash): bool
    {
        $conversationId = (int) ($conversation['id'] ?? 0);
        $lastMessageAt = (string) ($conversation['last_message_at'] ?? '');
        $lastActivity = $lastMessageAt !== '' ? strtotime($lastMessageAt) : false;
        if ($conversationId < 1
            || $visitorHash === ''
            || ! array_key_exists('feedback_prompted_at', $conversation)
            || ! empty($conversation['feedback_prompted_at'])
            || $lastActivity === false
            || (time() - $lastActivity) <= self::IDLE_SECONDS
            || ! $db->tableExists('ilena_chat_feedback')
            || ! $db->fieldExists('feedback_prompted_at', 'ilena_chat_conversations')
        ) {
            return false;
        }
        if ($db->table('ilena_chat_feedback')->where('conversation_id', $conversationId)->countAllResults() > 0) {
            return false;
        }

        $db->transBegin();
        try {
            $latest = $db->table('ilena_chat_messages')
                ->select('sender_type,created_at')
                ->where('conversation_id', $conversationId)
                ->orderBy('id', 'DESC')->get(1)->getRowArray();
            if (! $latest || ! self::isEligible((string) $latest['sender_type'], (string) $latest['created_at'])) {
                $db->transRollback();
                return false;
            }

            $now = date('Y-m-d H:i:s');
            $db->table('ilena_chat_conversations')
                ->where('id', $conversationId)
                ->where('visitor_hash', $visitorHash)
                ->where('feedback_prompted_at', null)
                ->where('last_message_at', $lastMessageAt)
                ->update(['feedback_prompted_at' => $now, 'last_message_at' => $now, 'updated_at' => $now]);

            if ($db->affectedRows() !== 1) {
                $db->transRollback();
                return false;
            }

            $db->table('ilena_chat_messages')->insert([
                'conversation_id' => $conversationId,
                'sender_type' => 'assistant',
                'body' => self::PROMPT_BODY,
                'metadata' => json_encode(['type' => 'kak_wawa_rating_prompt'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
            ]);
            if ($db->transStatus() === false) throw new \RuntimeException('Gagal menyimpan permintaan penilaian.');
            $db->transCommit();
            return true;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Ilena chat feedback prompt failed: {message}', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
