<?php
use App\Services\Payments\PaymentTransitionService;
use App\Services\Payments\MidtransStatusClient;
use CodeIgniter\Test\CIUnitTestCase;

final class PaymentTransitionServiceTest extends CIUnitTestCase
{
    private PaymentTransitionService $service;
    protected function setUp(): void
    {
        parent::setUp();
        $this->db=\Config\Database::connect(['DBDriver'=>'SQLite3','database'=>':memory:','DBPrefix'=>'','DBDebug'=>true],false);
        $this->db->query('CREATE TABLE pemesanan (id INTEGER PRIMARY KEY, id_midtrans TEXT UNIQUE, status TEXT, data_mid TEXT)');
        $this->db->query('CREATE TABLE effects (id INTEGER PRIMARY KEY, order_id TEXT)');
        $this->db->query('CREATE TABLE checkout_stock_reservations (id INTEGER PRIMARY KEY, order_id TEXT, status TEXT)');
        $this->db->table('pemesanan')->insert(['id_midtrans'=>'FIXTURE','status'=>'Menunggu Pembayaran','data_mid'=>json_encode(['gross_amount'=>100000,'payment_type'=>'bank_transfer','_app_order_access_token'=>str_repeat('a',64),'_app_payment_effects_applied'=>false])]);
        $this->service=new PaymentTransitionService($this->db);
    }
    protected function tearDown(): void { $this->db->close(); parent::tearDown(); }
    private function body(string $status='settlement'): array { return ['order_id'=>'FIXTURE','gross_amount'=>'100000.00','transaction_id'=>'TX1','transaction_status'=>$status,'status_code'=>'200']; }
    private function effect(): callable { return fn($row)=>$this->db->table('effects')->insert(['order_id'=>$row['id_midtrans']]); }

    public function testEarlyCallbackAndLateChargeCannotDowngradeOrDuplicateStock(): void
    {
        $first=$this->service->apply($this->body(),'production',$this->effect());
        $this->assertTrue($first['applied']);
        $late=$this->service->apply($this->body('pending'),'production',$this->effect());
        $this->assertSame('Proses',$late['order']['status']);
        $this->service->apply($this->body(),'production',$this->effect());
        $this->assertSame(1,$this->db->table('effects')->countAllResults());
    }
    public function testFailureRollsBackStatusAndEffectsThenRetrySucceeds(): void
    {
        try { $this->service->apply($this->body(),'production',function($row) { ($this->effect())($row); throw new \RuntimeException('simulated failure'); }); $this->fail('failure accepted'); }
        catch (\RuntimeException $e) { $this->assertSame('simulated failure',$e->getMessage()); }
        $this->assertSame(0,$this->db->table('effects')->countAllResults());
        $this->assertSame('Menunggu Pembayaran',$this->db->table('pemesanan')->get()->getRowArray()['status']);
        $this->service->apply($this->body(),'production',$this->effect()); $this->assertSame(1,$this->db->table('effects')->countAllResults());
    }
    public function testImmediatelyPaidOrderWithPendingMarkerCanRecoverOnlyOnce(): void
    {
        $this->db->table('pemesanan')->update(['status'=>'Proses']);
        $this->service->apply($this->body(),'production',$this->effect()); $this->service->apply($this->body(),'production',$this->effect());
        $this->assertSame(1,$this->db->table('effects')->countAllResults());
    }
    public function testProviderCannotOverwriteApplicationAccessTokenOrMarker(): void
    {
        $body=$this->body(); $body['_app_order_access_token']='evil'; $body['_app_payment_effects_applied']=true;
        $result=$this->service->apply($body,'production',$this->effect()); $mid=json_decode($result['order']['data_mid'],true);
        $this->assertSame(str_repeat('a',64),$mid['_app_order_access_token']); $this->assertTrue($mid['_app_payment_effects_applied']);
        $this->assertSame(1,$this->db->table('effects')->countAllResults());
    }
    public function testWrongProviderAmountNeverAppliesPayment(): void
    {
        $body=$this->body(); $body['gross_amount']='1.00';
        $this->expectException(\RuntimeException::class); $this->service->apply($body,'production',$this->effect());
    }
    public function testSandboxDoesNotChangeProductionStock(): void
    {
        $result=$this->service->apply($this->body(),'sandbox',$this->effect());
        $this->assertSame('Proses',$result['order']['status']); $this->assertSame(0,$this->db->table('effects')->countAllResults());
    }
    public function testStatusClientChecksResponseIdentityAndUsesTimeouts(): void
    {
        $fake=new class {
            public array $options=[]; public string $url='';
            public function get($url,$options) { $this->url=$url; $this->options=$options; return new class {
                public function getStatusCode(){return 200;}
                public function getBody(){return json_encode(['order_id'=>'FIXTURE','gross_amount'=>'100000.00']);}
            }; }
        };
        $body=(new MidtransStatusClient($fake))->fetch('FIXTURE','sandbox','fixture-key');
        $this->assertSame('FIXTURE',$body['order_id']); $this->assertSame('https://api.sandbox.midtrans.com/v2/FIXTURE/status',$fake->url);
        $this->assertTrue($fake->options['verify']); $this->assertSame(12,$fake->options['timeout']);
    }
}
