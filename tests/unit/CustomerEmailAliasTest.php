<?php

namespace Tests\Unit;

use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

final class CustomerEmailAliasTest extends CIUnitTestCase
{
    public function testHistoricalEmailResolvesToTheCurrentAccountWithoutCreatingAnotherUser(): void
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '', 'DBDebug' => true,
        ], false);
        try {
            $db->query('CREATE TABLE user (id INTEGER PRIMARY KEY, email TEXT, role TEXT, active TEXT, auth_version INTEGER)');
            $db->query('CREATE TABLE customer_email_aliases (id INTEGER PRIMARY KEY, alias_email TEXT UNIQUE, customer_email TEXT, created_at TEXT)');
            $db->table('user')->insert(['id' => 1, 'email' => 'current@example.com', 'role' => '0', 'active' => '1', 'auth_version' => 3]);
            $db->table('customer_email_aliases')->insert(['alias_email' => 'previous@example.com', 'customer_email' => 'current@example.com', 'created_at' => '2026-10-31 00:00:00']);

            $model = new UserModel($db);
            $this->assertSame(1, (int) $model->findByEmailIdentity(' PREVIOUS@example.com ')['id']);
            $this->assertNull($model->findByEmailIdentity('unrelated@example.com'));
            $this->assertSame(1, $db->table('user')->countAllResults());
        } finally {
            $db->close();
        }
    }
}
