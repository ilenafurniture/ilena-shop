<?php

namespace App\Commands;

use App\Services\SmartProductAssistant;
use App\Services\ChatConversationContext;
use App\Services\ChatTelemetry;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ChatWork extends BaseCommand
{
    protected $group = 'Ilena Support';
    protected $name = 'chat:work';
    protected $description = 'Process queued Ilena AI assistant replies outside LSAPI.';
    protected $usage = 'chat:work [--once]';

    public function run(array $params)
    {
        $once = in_array('--once', $params, true);
        $lastMaintenance = 0;
        do {
            try {
                if (time() - $lastMaintenance >= 86400) {
                    $this->cleanupExpiredConversations();
                    $lastMaintenance = time();
                }
                $processed = $this->processOne();
            } catch (\Throwable $e) {
                log_message('error', 'Ilena chat worker error: {message}', ['message' => $e->getMessage()]);
                $processed = false;
            }
            if ($once) break;
            if (! $processed) usleep(1200000);
        } while (true);
    }

    private function cleanupExpiredConversations(): void
    {
        $db = db_connect();
        if (! $db->tableExists('ilena_chat_conversations')) return;
        $days = max(30, min(730, (int)env('ILENA_CHAT_RETENTION_DAYS', 180)));
        $cutoff = date('Y-m-d H:i:s', time() - ($days * 86400));
        $expired = $db->table('ilena_chat_conversations')->select('id')->where('last_message_at <', $cutoff)->limit(250)->get()->getResultArray();
        if ($db->tableExists('ilena_chat_events')) {
            $db->table('ilena_chat_events')->where('created_at <', date('Y-m-d H:i:s', time() - 90 * 86400))->delete();
        }
        if (! $expired) return;
        $ids = array_map(static fn($row) => (int)$row['id'], $expired);
        $db->transBegin();
        foreach (['ilena_chat_jobs', 'ilena_chat_messages'] as $table) {
            if ($db->tableExists($table)) $db->table($table)->whereIn('conversation_id', $ids)->delete();
        }
        // Conversations use id, not conversation_id.
        $db->table('ilena_chat_conversations')->whereIn('id', $ids)->delete();
        if ($db->transStatus() === false) {
            $db->transRollback();
            log_message('error', 'Ilena chat retention cleanup failed.');
            return;
        }
        $db->transCommit();
    }

    private function processOne(): bool
    {
        $db = db_connect();
        if (! $db->tableExists('ilena_chat_jobs')) return false;
        $staleBefore = date('Y-m-d H:i:s', time() - 300);
        $db->table('ilena_chat_jobs')->where('status', 'processing')->where('locked_at <', $staleBefore)
            ->where('attempts <', 3)->update(['status' => 'queued', 'locked_at' => null, 'available_at' => date('Y-m-d H:i:s')]);
        $db->table('ilena_chat_jobs')->where('status', 'processing')->where('locked_at <', $staleBefore)
            ->where('attempts >=', 3)->update(['status' => 'failed']);
        $job = $db->table('ilena_chat_jobs')->where('status', 'queued')
            ->where('available_at <=', date('Y-m-d H:i:s'))->orderBy('id', 'ASC')->get(1)->getRowArray();
        if (! $job) return false;

        $now = date('Y-m-d H:i:s');
        $db->transBegin();
        $db->table('ilena_chat_jobs')->where('id', (int)$job['id'])->where('status', 'queued')->update([
            'status' => 'processing', 'attempts' => (int)$job['attempts'] + 1, 'locked_at' => $now,
        ]);
        $claimed = $db->affectedRows() === 1;
        if ($claimed) $db->transCommit(); else $db->transRollback();
        if (! $claimed) return false;

        $message = $db->table('ilena_chat_messages')->where('id', (int)$job['user_message_id'])->get()->getRowArray();
        $conversation = $db->table('ilena_chat_conversations')->where('id', (int)$job['conversation_id'])->get()->getRowArray();
        if (! $message || ! $conversation) {
            $db->table('ilena_chat_jobs')->where('id', (int)$job['id'])->update(['status' => 'failed']);
            return true;
        }

        // A handoff can happen while a queued job waits. Never call the model
        // or publish an assistant reply after a staff member takes over.
        if (($conversation['status'] ?? '') !== 'ai') {
            $db->table('ilena_chat_jobs')->where('id', (int)$job['id'])->where('status', 'processing')->update(['status' => 'skipped']);
            return true;
        }

        $historyRows = $db->table('ilena_chat_messages')->where('conversation_id', (int) $conversation['id'])
            ->where('id <', (int) $message['id'])->whereIn('sender_type', ['visitor', 'assistant'])
            ->orderBy('id', 'DESC')->get(8)->getResultArray();
        $historyRows = array_reverse($historyRows);
        $startedAt = microtime(true);
        $result = (new SmartProductAssistant($db))->respond(
            (string) $message['body'],
            $conversation['context_product_id'] ?? null,
            ChatConversationContext::normalize($historyRows)
        );
        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $now = date('Y-m-d H:i:s');
        $db->transBegin();
        $metadata = json_encode(['products' => $result['products'], 'sources' => $result['sources'], 'resolutionCheck' => true,
            'answerMode' => $result['mode'] ?? 'assisted', 'fallbackReason' => $result['fallbackReason'] ?? null,
            'durationMs' => $durationMs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // INSERT ... SELECT makes the status check atomic with publication.
        // The SQL form is supported by both production MySQL and test SQLite.
        $db->query(
            'INSERT INTO ilena_chat_messages (conversation_id, sender_type, body, metadata, created_at) '
            . 'SELECT id, ?, ?, ?, ? FROM ilena_chat_conversations WHERE id = ? AND status = ?',
            ['assistant', $result['answer'], $metadata, $now, (int)$conversation['id'], 'ai']
        );
        $published = $db->affectedRows() === 1;
        if ($published) {
            $db->table('ilena_chat_conversations')->where('id', (int)$conversation['id'])->where('status', 'ai')->update(['last_message_at' => $now, 'updated_at' => $now]);
        }
        $db->table('ilena_chat_jobs')->where('id', (int)$job['id'])->update(['status' => $published ? 'completed' : 'skipped']);
        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \RuntimeException('Failed to persist assistant response.');
        }
        $db->transCommit();
        $queueDelayMs = ! empty($job['created_at']) ? max(0, (time() - strtotime((string) $job['created_at'])) * 1000) : 0;
        ChatTelemetry::record($db, (string) ($result['mode'] ?? 'assisted'), (string) ($result['fallbackReason'] ?? ''), $durationMs, $queueDelayMs);
        CLI::write('Chat job ' . (int)$job['id'] . ' completed.', 'green');
        return true;
    }
}
