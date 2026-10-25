<?php
use App\Services\SessionMarketCart;
use CodeIgniter\Test\CIUnitTestCase;
final class SessionMarketCartTest extends CIUnitTestCase
{
    public function testStaffCartIsActorScopedAndClearDoesNotAffectOtherStaff(): void
    {
        $cart=new SessionMarketCart();
        session()->set('email','a@example.test'); $cart->clear(); $cart->add('P1','Oak');
        session()->set('email','b@example.test'); $cart->clear(); $this->assertSame([],$cart->items()); $cart->add('P2','Walnut');
        session()->set('email','a@example.test'); $this->assertSame('P1',array_values($cart->items())[0]['id_barang']); $cart->clear();
        session()->set('email','b@example.test'); $this->assertSame('P2',array_values($cart->items())[0]['id_barang']); $cart->clear();
        session()->remove('email');
    }
}
