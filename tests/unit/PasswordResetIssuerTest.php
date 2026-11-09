<?php

use App\Services\Auth\PasswordResetIssuer;
use CodeIgniter\Test\CIUnitTestCase;

final class PasswordResetIssuerTest extends CIUnitTestCase
{
    private $testDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDb = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '', 'DBDebug' => true,
        ], false);
        $this->testDb->query("CREATE TABLE user_password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT, email VARCHAR(191) NOT NULL,
            token_hash CHAR(64) NOT NULL UNIQUE, expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL, consumed_at DATETIME NULL
        )");
    }

    protected function tearDown(): void
    {
        $this->testDb->close();
        parent::tearDown();
    }

    public function testFailedDeliveryRemovesCandidateAndPreservesPreviouslyUsableLink(): void
    {
        $this->testDb->table('user_password_resets')->insert([
            'email' => 'customer@example.test', 'token_hash' => hash('sha256', 'old-token'),
            'expires_at' => date('Y-m-d H:i:s', time() + 1800), 'created_at' => date('Y-m-d H:i:s'), 'consumed_at' => null,
        ]);

        $issuer = new PasswordResetIssuer($this->testDb);
        $deliveredToken = null;
        $result = $issuer->issue('customer@example.test', static function (string $token) use (&$deliveredToken): bool {
            $deliveredToken = $token;
            return false;
        });

        $this->assertFalse($result);
        $this->assertSame(64, strlen((string) $deliveredToken));
        $rows = $this->testDb->table('user_password_resets')->orderBy('id')->get()->getResultArray();
        $this->assertCount(1, $rows);
        $this->assertNull($rows[0]['consumed_at']);
        $this->assertSame(hash('sha256', 'old-token'), $rows[0]['token_hash']);
    }

    public function testSuccessfulDeliveryKeepsNewTokenAndConsumesOlderLinks(): void
    {
        $this->testDb->table('user_password_resets')->insert([
            'email' => 'customer@example.test', 'token_hash' => hash('sha256', 'old-token'),
            'expires_at' => date('Y-m-d H:i:s', time() + 1800), 'created_at' => date('Y-m-d H:i:s'), 'consumed_at' => null,
        ]);

        $issuer = new PasswordResetIssuer($this->testDb);
        $deliveredToken = null;
        $result = $issuer->issue('customer@example.test', static function (string $token) use (&$deliveredToken): bool {
            $deliveredToken = $token;
            return true;
        });

        $this->assertTrue($result);
        $rows = $this->testDb->table('user_password_resets')->orderBy('id')->get()->getResultArray();
        $this->assertCount(2, $rows);
        $this->assertNotNull($rows[0]['consumed_at']);
        $this->assertNull($rows[1]['consumed_at']);
        $this->assertSame(hash('sha256', $deliveredToken), $rows[1]['token_hash']);
        $this->assertSame(64, strlen($deliveredToken));
    }

    public function testThrownMailFailureDoesNotInvalidateOldLink(): void
    {
        $this->testDb->table('user_password_resets')->insert([
            'email' => 'customer@example.test', 'token_hash' => hash('sha256', 'old-token'),
            'expires_at' => date('Y-m-d H:i:s', time() + 1800), 'created_at' => date('Y-m-d H:i:s'), 'consumed_at' => null,
        ]);

        $issuer = new PasswordResetIssuer($this->testDb);
        $result = $issuer->issue('customer@example.test', static function (): bool {
            throw new RuntimeException('SMTP transport failed');
        });

        $this->assertFalse($result);
        $rows = $this->testDb->table('user_password_resets')->get()->getResultArray();
        $this->assertCount(1, $rows);
        $this->assertNull($rows[0]['consumed_at']);
    }

    public function testCleanupFailureAfterAcceptedDeliveryPreservesDeliveredToken(): void
    {
        $this->testDb->query("CREATE TRIGGER fail_old_reset_cleanup BEFORE UPDATE ON user_password_resets
            WHEN OLD.token_hash = '" . hash('sha256', 'old-token') . "'
            BEGIN SELECT RAISE(ABORT, 'cleanup failure'); END");
        $this->testDb->table('user_password_resets')->insert([
            'email' => 'customer@example.test', 'token_hash' => hash('sha256', 'old-token'),
            'expires_at' => date('Y-m-d H:i:s', time() + 1800), 'created_at' => date('Y-m-d H:i:s'), 'consumed_at' => null,
        ]);

        $issuer = new PasswordResetIssuer($this->testDb);
        $deliveredToken = null;
        $result = $issuer->issue('customer@example.test', static function (string $token) use (&$deliveredToken): bool {
            $deliveredToken = $token;
            return true;
        });

        $this->assertTrue($result);
        $delivered = $this->testDb->table('user_password_resets')->where('token_hash', hash('sha256', $deliveredToken))->get()->getRowArray();
        $this->assertNotNull($delivered);
        $this->assertNull($delivered['consumed_at']);
    }
}
