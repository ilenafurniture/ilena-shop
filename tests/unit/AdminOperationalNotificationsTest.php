<?php

use App\Services\AdminOperationalNotifications;
use CodeIgniter\Test\CIUnitTestCase;

final class AdminOperationalNotificationsTest extends CIUnitTestCase
{
    public function testWaitingChatCountReturnsZeroWhenChatTablesAreNotInstalled(): void
    {
        $db = new class {
            public function tableExists(string $table): bool
            {
                return false;
            }

            public function query(): never
            {
                throw new RuntimeException('The query must not run without chat tables.');
            }
        };

        $this->assertSame(0, AdminOperationalNotifications::waitingHumanChats($db));
    }

    public function testWaitingChatCountUsesSameHandoffCriteriaAsAdminQueue(): void
    {
        $db = new class {
            public string $sql = '';
            public array $binds = [];

            public function tableExists(string $table): bool
            {
                return in_array($table, ['ilena_chat_conversations', 'ilena_chat_messages'], true);
            }

            public function query(string $sql, array $binds): object
            {
                $this->sql = $sql;
                $this->binds = $binds;

                return new class {
                    public function getRowArray(): array
                    {
                        return ['total' => '7'];
                    }
                };
            }
        };

        $this->assertSame(7, AdminOperationalNotifications::waitingHumanChats($db));
        $this->assertStringContainsString("c.status = 'human_waiting'", $db->sql);
        $this->assertStringContainsString('EXISTS', $db->sql);
        $this->assertSame(['Pelanggan meminta bantuan staf. Pesan berikutnya akan dilayani oleh tim Ilena.'], $db->binds);
    }
}
