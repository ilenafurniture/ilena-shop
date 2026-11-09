<?php

use App\Services\CommerceJobQueue;
use CodeIgniter\Test\CIUnitTestCase;

final class CommerceJobQueueTest extends CIUnitTestCase
{
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = \Config\Database::connect(['DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '', 'DBDebug' => true], false);
        $this->db->query("CREATE TABLE commerce_jobs (
            id INTEGER PRIMARY KEY AUTOINCREMENT, job_key TEXT NOT NULL UNIQUE, job_type TEXT NOT NULL,
            order_id TEXT NOT NULL, payload TEXT NULL, status TEXT NOT NULL DEFAULT 'queued', attempts INTEGER NOT NULL DEFAULT 0,
            available_at TEXT NOT NULL, locked_at TEXT NULL, completed_at TEXT NULL, last_error TEXT NULL,
            created_at TEXT NOT NULL, updated_at TEXT NOT NULL
        )");
        $this->db->query('CREATE TABLE pemesanan (id_midtrans TEXT PRIMARY KEY, data_mid TEXT NULL)');
    }

    protected function tearDown(): void
    {
        $this->db->close();
        parent::tearDown();
    }

    public function testJobKeyIsUniqueAndDuplicateEnqueueReturnsSameJob(): void
    {
        $queue = new CommerceJobQueue($this->db);
        $first = $queue->enqueue('checkout-charge:IL00000001', 'midtrans.charge', 'IL00000001', ['idempotency_key' => 'one']);
        $duplicate = $queue->enqueue('checkout-charge:IL00000001', 'midtrans.charge', 'IL00000001', ['idempotency_key' => 'one']);
        $this->assertSame($first, $duplicate);
        $this->assertSame(1, $this->db->table('commerce_jobs')->countAllResults());
    }

    public function testClaimCompletesExactlyOneJobAndClearsSensitivePayload(): void
    {
        $queue = new CommerceJobQueue($this->db);
        $id = $queue->enqueue('checkout-charge:IL00000002', 'midtrans.charge', 'IL00000002', ['customer_email' => 'buyer@example.test']);
        $claimed = $queue->claim();
        $this->assertSame($id, (int) $claimed['id']);
        $this->assertSame(1, (int) $claimed['attempts']);
        $this->assertNull($queue->claim(), 'A processing job must not be claimed twice.');
        $queue->complete($id);
        $saved = $this->db->table('commerce_jobs')->where('id', $id)->get()->getRowArray();
        $this->assertSame('completed', $saved['status']);
        $this->assertNull($saved['payload']);
    }

    public function testAmbiguousNonIdempotentChargeIsParkedForReconciliation(): void
    {
        $queue = new CommerceJobQueue($this->db);
        $id = $queue->enqueue('checkout-charge:IL00000003', 'midtrans.charge', 'IL00000003', ['idempotency_key' => null]);
        $job = $queue->claim();
        $queue->retryOrPark($job, 'Connection dropped after request.', false);
        $saved = $this->db->table('commerce_jobs')->where('id', $id)->get()->getRowArray();
        $this->assertSame('uncertain', $saved['status']);
        $this->assertSame(0, $this->db->table('commerce_jobs')->where('status', 'queued')->countAllResults());
    }
}
