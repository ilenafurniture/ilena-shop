<?php

namespace App\Services\Auth;

use App\Services\AccountSuspensionService;
use CodeIgniter\Database\BaseConnection;

/** Stateful opaque tokens, stored only as SHA-256 hashes and limited to customer accounts. */
final class CustomerApiTokenService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function issue(array $user): ?array
    {
        if (!$this->isCustomer($user) || !$this->db->tableExists('customer_api_tokens')) return null;

        $plainToken = bin2hex(random_bytes(32));
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $expires = $now->modify('+30 days');
        $email = strtolower(trim((string) $user['email']));
        $inserted = $this->db->table('customer_api_tokens')->insert([
            'user_email' => $email,
            'token_hash' => hash('sha256', $plainToken),
            'auth_version' => max(1, (int) ($user['auth_version'] ?? 1)),
            'created_at' => $now->format('Y-m-d H:i:s'),
            'expires_at' => $expires->format('Y-m-d H:i:s'),
            'last_used_at' => $now->format('Y-m-d H:i:s'),
            'revoked_at' => null,
        ]);

        return $inserted ? [
            'access_token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $expires->format(DATE_ATOM),
            'expires_in' => $expires->getTimestamp() - $now->getTimestamp(),
        ] : null;
    }

    /** Resolve a bearer credential to an active, verified customer account. */
    public function authenticate(string $authorization): ?array
    {
        if (!$this->db->tableExists('customer_api_tokens') || !preg_match('/\ABearer\s+([a-f0-9]{64})\z/i', trim($authorization), $matches)) {
            return null;
        }

        $tokenHash = hash('sha256', strtolower($matches[1]));
        $token = $this->db->table('customer_api_tokens')->where('token_hash', $tokenHash)
            ->where('revoked_at', null)->where('expires_at >', gmdate('Y-m-d H:i:s'))->get(1)->getRowArray();
        if (!$token) return null;

        $user = $this->db->table('user')->where('LOWER(TRIM(email))', strtolower((string) $token['user_email']))->get(1)->getRowArray();
        if (!$user || (string) ($user['active'] ?? '0') !== '1'
            || (int) ($user['auth_version'] ?? 1) !== (int) $token['auth_version']
            || !$this->isCustomer($user)) {
            $this->revokeById((int) $token['id']);
            return null;
        }

        $now = gmdate('Y-m-d H:i:s');
        $lastUsed = strtotime((string) ($token['last_used_at'] ?? '') . ' UTC');
        if ($lastUsed === false || $lastUsed < time() - 300) {
            $this->db->table('customer_api_tokens')->where('id', (int) $token['id'])->update(['last_used_at' => $now]);
        }

        return ['token_id' => (int) $token['id'], 'user' => $user, 'token_hash' => $tokenHash];
    }

    public function revoke(array $authenticated): bool
    {
        $id = (int) ($authenticated['token_id'] ?? 0);
        return $id > 0 && $this->revokeById($id);
    }

    public function isCustomer(array $user): bool
    {
        $email = strtolower(trim((string) ($user['email'] ?? '')));
        if ($email === '' || !in_array((string) ($user['role'] ?? ''), ['0', '4'], true)) return false;
        if ((new AccountSuspensionService($this->db))->isSuspended($email)) return false;
        return !$this->hasAdminAssignment($email);
    }

    private function hasAdminAssignment(string $email): bool
    {
        $roles = 'admin_rbac_roles';
        $assignments = 'admin_rbac_user_roles';
        if (!$this->db->tableExists($roles) || !$this->db->tableExists($assignments)) return false;

        return $this->db->table($assignments . ' ur')
            ->join($roles . ' r', 'r.id = ur.role_id', 'inner')
            ->where('LOWER(TRIM(ur.email))', strtolower(trim($email)))
            ->countAllResults() > 0;
    }

    private function revokeById(int $id): bool
    {
        return $this->db->table('customer_api_tokens')->where('id', $id)->where('revoked_at', null)
            ->update(['revoked_at' => gmdate('Y-m-d H:i:s')]);
    }
}
