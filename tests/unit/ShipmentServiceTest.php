<?php
use App\Services\ShipmentService;
use App\Services\DocumentSequence;
use CodeIgniter\Test\CIUnitTestCase;

final class ShipmentServiceTest extends CIUnitTestCase
{
    private ShipmentService $service;
    protected function setUp(): void
    {
        parent::setUp();
        $this->db=\Config\Database::connect(['DBDriver'=>'SQLite3','database'=>':memory:','DBPrefix'=>'','DBDebug'=>true],false);
        foreach ([
            'CREATE TABLE pemesanan_offline (id INTEGER PRIMARY KEY, id_pesanan TEXT UNIQUE, jenis TEXT, is_draft INTEGER DEFAULT 0)',
            'CREATE TABLE pemesanan_offline_item (id INTEGER PRIMARY KEY, id_pesanan TEXT, id_barang INTEGER, varian TEXT)',
            'CREATE TABLE project_interior (id INTEGER PRIMARY KEY, kode_project TEXT, kode_sj TEXT)',
            'CREATE TABLE project_interior_item (id INTEGER PRIMARY KEY, project_id INTEGER, kode_barang TEXT, nama_barang TEXT, qty INTEGER)',
            'CREATE TABLE project_interior_payment (id INTEGER PRIMARY KEY, project_id INTEGER, jenis TEXT, nominal INTEGER)',
            'CREATE TABLE surat_jalan (id INTEGER PRIMARY KEY, id_pesanan TEXT, no_sj TEXT, sj_ke INTEGER, tanggal TEXT, status TEXT, finalized_at TEXT)',
            'CREATE TABLE surat_jalan_item (id INTEGER PRIMARY KEY, surat_jalan_id INTEGER, id_barang INTEGER NULL, kode_barang TEXT, nama_barang TEXT, varian TEXT, qty INTEGER)',
            'CREATE TABLE document_sequences (sequence_key TEXT PRIMARY KEY, counter INTEGER NOT NULL)',
        ] as $sql) $this->db->query($sql);
        $this->db->table('pemesanan_offline')->insert(['id_pesanan'=>'SJ00000001','jenis'=>'sale']);
        for ($i=0;$i<3;$i++) $this->db->table('pemesanan_offline_item')->insert(['id_pesanan'=>'SJ00000001','id_barang'=>1,'varian'=>'Oak']);
        $this->service=new ShipmentService($this->db);
    }
    protected function tearDown(): void { $this->db->close(); parent::tearDown(); }

    public function testDraftFinalizeAndRemainingShipmentAreConsistent(): void
    {
        $id=$this->service->createOffline('SJ00000001');
        $sj=$this->db->table('surat_jalan')->where('id',$id)->get()->getRowArray();
        $this->assertSame('draft',$sj['status']); $this->assertNull($sj['no_sj']);
        $item=$this->db->table('surat_jalan_item')->where('surat_jalan_id',$id)->get()->getRowArray();
        $this->service->save($id,[$item['id']],[2],'2026-10-25T10:30');
        $this->service->finalize($id); $this->service->finalize($id);
        $this->assertSame(1,(int)$this->db->table('document_sequences')->get()->getRowArray()['counter']);
        $next=$this->service->createOffline('SJ00000001');
        $this->assertSame(1,(int)$this->db->table('surat_jalan_item')->where('surat_jalan_id',$next)->get()->getRowArray()['qty']);
        $this->service->finalize($next);
        $this->assertSame(2,(int)$this->db->table('document_sequences')->get()->getRowArray()['counter']);
    }

    public function testForeignItemCannotChangeAnotherShipmentOrDate(): void
    {
        $first=$this->service->createOffline('SJ00000001'); $second=$this->service->createOffline('SJ00000001');
        $foreign=$this->db->table('surat_jalan_item')->where('surat_jalan_id',$second)->get()->getRowArray();
        $before=$this->db->table('surat_jalan')->where('id',$first)->get()->getRowArray()['tanggal'];
        try { $this->service->save($first,[$foreign['id']],[0],'2020-01-01T00:00'); $this->fail('foreign item accepted'); }
        catch (\RuntimeException $e) { $this->assertStringContainsString('bukan milik',$e->getMessage()); }
        $this->assertSame(3,(int)$this->db->table('surat_jalan_item')->where('id',$foreign['id'])->get()->getRowArray()['qty']);
        $this->assertSame($before,$this->db->table('surat_jalan')->where('id',$first)->get()->getRowArray()['tanggal']);
    }

    public function testTwoDraftsCannotOvershipWhenFinalized(): void
    {
        $one=$this->service->createOffline('SJ00000001'); $two=$this->service->createOffline('SJ00000001');
        $this->service->finalize($one);
        $this->expectException(\RuntimeException::class); $this->service->finalize($two);
    }

    public function testFinalShipmentCannotBeEdited(): void
    {
        $id=$this->service->createOffline('SJ00000001'); $this->service->finalize($id);
        $item=$this->db->table('surat_jalan_item')->where('surat_jalan_id',$id)->get()->getRowArray();
        $this->expectException(\RuntimeException::class); $this->service->save($id,[$item['id']],[1],null);
    }

    public function testInteriorUsesReservedOfflineKeyAndCustomItemFields(): void
    {
        $this->db->table('project_interior')->insert(['id'=>1,'kode_project'=>'PI0001','kode_sj'=>'SJ00000001']);
        $this->db->table('project_interior_item')->insert(['project_id'=>1,'kode_barang'=>'CUSTOM-A','nama_barang'=>'Kitchen custom','qty'=>2]);
        $this->db->table('project_interior_payment')->insert(['project_id'=>1,'jenis'=>'dp','nominal'=>100000]);
        $id=$this->service->createInterior('PI0001',['I||CUSTOM-A']);
        $sj=$this->db->table('surat_jalan')->where('id',$id)->get()->getRowArray();
        $this->assertSame('SJ00000001',$sj['id_pesanan']);
        $row=$this->db->table('surat_jalan_item')->where('surat_jalan_id',$id)->get()->getRowArray();
        $this->assertNull($row['id_barang']); $this->assertSame('CUSTOM-A',$row['kode_barang']); $this->assertSame('Kitchen custom',$row['nama_barang']);
        $this->service->save($id,[$row['id']],[1],null); $this->service->finalize($id);
        $this->assertSame('final',$this->db->table('surat_jalan')->where('id',$id)->get()->getRowArray()['status']);
    }

    public function testInteriorNeedsDpAndRollsBackOnFailure(): void
    {
        $this->db->table('project_interior')->insert(['id'=>1,'kode_project'=>'PI0001','kode_sj'=>'SJ00000001']);
        try { $this->service->createInterior('PI0001',['I||X']); $this->fail('missing DP accepted'); }
        catch (\RuntimeException $e) { $this->assertStringContainsString('DP',$e->getMessage()); }
        $this->assertSame(0,$this->db->table('surat_jalan')->countAllResults());
    }

    public function testSequenceHonorsLegacyNumbersAndTransactionRollback(): void
    {
        $this->db->transBegin();
        $this->assertSame(101,DocumentSequence::next($this->db,'SJ:2026-10',100));
        $this->db->transCommit(); $this->db->transBegin();
        $this->assertSame(102,DocumentSequence::next($this->db,'SJ:2026-10',0));
        $this->db->transRollback();
        $this->assertSame(101,(int)$this->db->table('document_sequences')->get()->getRowArray()['counter']);
    }
}
