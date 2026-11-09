<?php

namespace App\Services\Auth;

use App\Services\AtomicOperation;

/** Creates a one-time reset token and delivers it without invalidating working links on mail failure. */
final class PasswordResetIssuer
{
    public function __construct(private $db = null)
    {
        $this->db ??= db_connect();
    }

    /**
     * The callback receives the raw token and must return true only when the mail transport accepts it.
     */
    public function issue(string $email, callable $deliver): bool
    {
        $db = $this->db;
        $lock = null;
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $resetId = 0;
        $mailAccepted = false;
        $transactionOpen = false;

        try {
            $lock = new AtomicOperation($db, 'ilena-reset-' . substr(hash('sha256', strtolower($email)), 0, 52), false);

            $db->transBegin();
            $transactionOpen = true;
            $inserted = $db->table('user_password_resets')->insert([
                'email' => $email,
                'token_hash' => $tokenHash,
                'expires_at' => date('Y-m-d H:i:s', time() + 1800),
                'created_at' => date('Y-m-d H:i:s'),
                'consumed_at' => null,
            ]);
            if (! $inserted || ! $db->transStatus()) {
                $db->transRollback();
                $transactionOpen = false;
                log_message('error', 'Password reset token could not be stored.');
                return false;
            }

            $resetId = (int) $db->insertID();
            $committed = $resetId > 0 && $db->transCommit();
            $transactionOpen = false;
            if (! $committed) {
                $db->transRollback();
                log_message('error', 'Password reset token transaction could not be committed.');
                return false;
            }

            try {
                $delivered = $deliver($token) === true;
            } catch (\Throwable $error) {
                log_message('error', 'Password reset email delivery raised {type}.', ['type' => get_class($error)]);
                $delivered = false;
            }
            $mailAccepted = $delivered;

            if (! $delivered) {
                $db->transBegin();
                $transactionOpen = true;
                $db->table('user_password_resets')->where('id', $resetId)->where('token_hash', $tokenHash)->delete();
                $cleanupCommitted = $db->transStatus() && $db->transCommit();
                $transactionOpen = false;
                if (! $cleanupCommitted) {
                    $db->transRollback();
                    log_message('error', 'Unsent password reset token cleanup failed.');
                }
                return false;
            }

            // The per-email lock serializes reset requests. Keep only this successfully delivered link.
            try {
                $db->transBegin();
                $transactionOpen = true;
                $db->table('user_password_resets')->where('email', $email)->where('id <>', $resetId)
                    ->where('consumed_at', null)->update(['consumed_at' => date('Y-m-d H:i:s')]);
                $cleanupCommitted = $db->transStatus() && $db->transCommit();
                $transactionOpen = false;
                if (! $cleanupCommitted) {
                    $db->transRollback();
                    log_message('warning', 'Older password reset links could not be invalidated after delivery.');
                }
            } catch (\Throwable $error) {
                if ($transactionOpen) $db->transRollback();
                $transactionOpen = false;
                log_message('warning', 'Older password reset links could not be invalidated after delivery.');
            }

            return true;
        } catch (\Throwable $error) {
            if ($transactionOpen) {
                $db->transRollback();
                $transactionOpen = false;
            }

            if ($resetId > 0 && ! $mailAccepted) {
                try {
                    $db->table('user_password_resets')->where('id', $resetId)->where('token_hash', $tokenHash)->delete();
                } catch (\Throwable) {
                    log_message('error', 'Password reset token cleanup failed after an exception.');
                }
            }

            log_message('error', 'Password reset issuance failed ({type}).', ['type' => get_class($error)]);
            return false;
        } finally {
            $lock?->releaseLock();
        }
    }

    /** SMTP protocol diagnostics only; never include message headers, body, or credentials. */
    public static function safeTransportDiagnostic(object $mailer): string
    {
        if (! method_exists($mailer, 'printDebugger')) {
            return 'SMTP transport returned false';
        }

        $diagnostic = html_entity_decode(strip_tags((string) $mailer->printDebugger([])), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $diagnostic = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[address]', $diagnostic) ?? '';
        $diagnostic = preg_replace('/[\r\n\t]+/', ' ', $diagnostic) ?? '';

        return mb_substr(trim($diagnostic) ?: 'SMTP transport returned false', 0, 600);
    }
}
