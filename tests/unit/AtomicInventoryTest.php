<?php
use App\Services\AtomicOperation;
use App\Services\InventoryAvailability;
use CodeIgniter\Test\CIUnitTestCase;
final class AtomicInventoryTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp(); $this->db=\Config\Database::connect(['DBDriver'=>'SQLite3','database'=>':memory:','DBPrefix'=>'','DBDebug'=>true],false);
        $this->db->query('CREATE TABLE entries (id INTEGER PRIMARY KEY, value INTEGER)');
        $this->db->query('CREATE TABLE checkout_stock_reservations (id INTEGER PRIMARY KEY, product_id TEXT, variant_name TEXT, quantity INTEGER, order_id TEXT, status TEXT, expires_at TEXT)');
    }
    protected function tearDown(): void { $this->db->close(); parent::tearDown(); }
    public function testEarlyReturnRollsBackAllWritesButCommitPersists(): void
    {
        (function () { $tx=new AtomicOperation($this->db); $this->db->table('entries')->insert(['value'=>1]); return; })();
        $this->assertSame(0,$this->db->table('entries')->countAllResults());
        (function () { $tx=new AtomicOperation($this->db); $this->db->table('entries')->insert(['value'=>2]); $tx->commit(); })();
        $this->assertSame(1,$this->db->table('entries')->countAllResults());
    }
    public function testOfflineCannotConsumeStockReservedByAnotherCheckout(): void
    {
        $this->db->table('checkout_stock_reservations')->insert(['product_id'=>'P1','variant_name'=>'Oak','quantity'=>2,'order_id'=>'OTHER','status'=>'reserved','expires_at'=>gmdate('Y-m-d H:i:s',time()+300)]);
        InventoryAvailability::assertRemaining($this->db,'P1','oak',2);
        InventoryAvailability::assertRemaining($this->db,'P1','Oak',0,'OTHER');
        $this->expectException(\RuntimeException::class); InventoryAvailability::assertRemaining($this->db,'P1','Oak',1);
    }
    public function testMigrationCreatesRequiredTablesAndIsIdempotent(): void
    {
        $this->db->query('DROP TABLE checkout_stock_reservations');
        require_once APPPATH.'Database/Migrations/2026-10-25-000002_AddCheckoutReservationsAndDocumentSequences.php';
        $forge=\Config\Database::forge($this->db);
        $migration=new \App\Database\Migrations\AddCheckoutReservationsAndDocumentSequences($forge);
        $migration->up(); $migration->up();
        $this->assertTrue($this->db->tableExists('checkout_stock_reservations')); $this->assertTrue($this->db->tableExists('document_sequences'));
    }
}
