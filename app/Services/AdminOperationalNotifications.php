<?php

namespace App\Services;

/** Small, permission-gated counts for actions that need staff attention. */
final class AdminOperationalNotifications
{
    private const HANDOFF_MESSAGE = 'Pelanggan meminta bantuan staf. Pesan berikutnya akan dilayani oleh tim Ilena.';

    public static function waitingHumanChats($db): int
    {
        if (! $db->tableExists('ilena_chat_conversations') || ! $db->tableExists('ilena_chat_messages')) {
            return 0;
        }

        $row = $db->query(
            "SELECT COUNT(*) AS total
             FROM ilena_chat_conversations c
             WHERE c.status = 'human_waiting'
               AND EXISTS (
                   SELECT 1
                   FROM ilena_chat_messages m
                   WHERE m.conversation_id = c.id
                     AND m.sender_type = 'system'
                     AND m.body = ?
               )",
            [self::HANDOFF_MESSAGE]
        )->getRowArray();

        return max(0, (int) ($row['total'] ?? 0));
    }
}
