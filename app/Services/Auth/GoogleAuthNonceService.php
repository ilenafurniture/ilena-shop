<?php

namespace App\Services\Auth;

use CodeIgniter\Database\BaseConnection;

/** Single-use server-issued nonces used to bind Google ID tokens to a login attempt. */
final class GoogleAuthNonceService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function issue(): ?string
    {
        if (!$this->db->tableExists('customer_google_auth_nonces')) return null;

        $nonce = bin2hex(random_bytes(32));
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        // Opportunistically prune expired one-time challenges; the indexed delete stays bounded.
        $this->db->table('customer_google_auth_nonces')->where('expires_at <=', $now->format('Y-m-d H:i:s'))->delete();
        $inserted = $this->db->table('customer_google_auth_nonces')->insert([
            'nonce_hash' => hash('sha256', $nonce),
            'created_at' => $now->format('Y-m-d H:i:s'),
            'expires_at' => $now->modify('+5 minutes')->format('Y-m-d H:i:s'),
        ]);

        return $inserted ? $nonce : null;
    }

    /** Atomic DELETE makes the challenge consumable by only one request. */
    public function consume(string $nonce): bool
    {
        if (!$this->db->tableExists('customer_google_auth_nonces') || !preg_match('/\A[a-f0-9]{64}\z/D', $nonce)) return false;

        $this->db->table('customer_google_auth_nonces')
            ->where('nonce_hash', hash('sha256', $nonce))
            ->where('expires_at >', gmdate('Y-m-d H:i:s'))
            ->delete();

        return $this->db->affectedRows() === 1;
    }
}
