<?php

namespace App\Services;

/** Builds a bounded, customer/assistant-only context for the AI provider. */
final class ChatConversationContext
{
    /** @param array<int,array<string,mixed>> $rows */
    public static function normalize(array $rows, int $maxTurns = 8, int $maxChars = 700): array
    {
        $messages = [];
        foreach (array_slice($rows, -max(1, min(16, $maxTurns))) as $row) {
            $sender = (string) ($row['sender_type'] ?? (($row['role'] ?? '') === 'user' ? 'visitor' : ($row['role'] ?? '')));
            if (! in_array($sender, ['visitor', 'assistant'], true)) continue;
            $body = trim(strip_tags((string) ($row['body'] ?? $row['content'] ?? '')));
            if ($body === '') continue;
            $messages[] = [
                'role' => $sender === 'visitor' ? 'user' : 'assistant',
                'content' => mb_substr($body, 0, max(80, min(1200, $maxChars))),
            ];
        }

        return $messages;
    }
}
