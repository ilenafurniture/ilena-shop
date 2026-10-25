<?php
use App\Services\SessionOperation;
use CodeIgniter\Test\CIUnitTestCase;
final class SessionOperationTest extends CIUnitTestCase
{
    public function testReceiptSupportsRetryButIsBoundToActorAndScope(): void
    {
        session()->set('email','a@example.test');
        $token=SessionOperation::issue('manual_order'); $this->assertNull(SessionOperation::lookup('manual_order',$token));
        SessionOperation::complete('manual_order',$token,['success'=>true,'order_id'=>'FIXTURE']);
        $this->assertSame('FIXTURE',SessionOperation::lookup('manual_order',$token)['order_id']);
        session()->set('email','b@example.test');
        try { SessionOperation::lookup('manual_order',$token); $this->fail('cross-account receipt accepted'); }
        catch(\RuntimeException $e) { $this->assertStringContainsString('kedaluwarsa',$e->getMessage()); }
        session()->set('email','a@example.test');
        $this->expectException(\RuntimeException::class); SessionOperation::lookup('offline_order',$token);
    }
}
