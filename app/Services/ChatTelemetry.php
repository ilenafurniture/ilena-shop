<?php

namespace App\Services;

/** Stores low-cardinality operational metrics without chat text or identities. */
final class ChatTelemetry
{
    public static function record($db, string $mode, string $reason, int $durationMs, int $queueDelayMs = 0): void
    {
        if (! $db->tableExists('ilena_chat_events')) return;
        try {
            $db->table('ilena_chat_events')->insert([
                'event_type' => in_array($mode, ['assisted', 'fallback', 'catalog', 'policy', 'handoff'], true) ? $mode : 'fallback',
                'reason' => $reason === '' ? null : mb_substr($reason, 0, 64),
                'duration_ms' => max(0, min(120000, $durationMs)),
                'queue_delay_ms' => max(0, min(86400000, $queueDelayMs)),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Kak Wawa telemetry could not be stored.');
        }
    }

    public static function summary($db, int $days = 7): array
    {
        if (! $db->tableExists('ilena_chat_events')) return ['available' => false, 'items' => []];
        $since = date('Y-m-d H:i:s', time() - max(1, min(90, $days)) * 86400);
        $rows = $db->query(
            'SELECT event_type, reason, COUNT(*) AS total, AVG(duration_ms) AS avg_duration_ms, MAX(duration_ms) AS max_duration_ms, AVG(queue_delay_ms) AS avg_queue_delay_ms '
            . 'FROM ilena_chat_events WHERE created_at >= ? GROUP BY event_type, reason ORDER BY event_type, reason', [$since]
        )->getResultArray();
        return ['available' => true, 'items' => $rows];
    }
}
