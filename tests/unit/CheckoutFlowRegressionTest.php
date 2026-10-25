<?php

use App\Controllers\Pages;
use App\Services\CheckoutState;
use App\Services\CatalogPricing;
use CodeIgniter\Test\CIUnitTestCase;

final class CheckoutFlowRegressionTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        session()->remove(['email','isLogin','active','role','checkout_order_tokens']);
        parent::tearDown();
    }

    public function testCheckoutIgnoresPresentationButDetectsQuantityAndPriceChanges(): void
    {
        $cart = [['id_barang'=>'P1','varian'=>'Oak','jumlah'=>2,'detail'=>['harga'=>100000,'diskon'=>0]]];
        $decorated = $cart;
        $decorated[0]['src_gambar'] = '/new-image.webp';
        $decorated[0]['detail']['pengunjung'] = 999;
        $args = [['email_pemesan'=>'fixture@example.test'], ['harga'=>10000], 'bca', null];
        $hash = CheckoutState::fingerprint($cart, ...$args);
        $this->assertSame($hash, CheckoutState::fingerprint($decorated, ...$args));
        $decorated[0]['jumlah'] = 3;
        $this->assertNotSame($hash, CheckoutState::fingerprint($decorated, ...$args));
        $decorated = $cart;
        $decorated[0]['detail']['harga'] = 150000;
        $this->assertNotSame($hash, CheckoutState::fingerprint($decorated, ...$args));
    }

    public function testExpiredScheduledDiscountDoesNotReduceCharge(): void
    {
        $row=['harga'=>100000,'diskon'=>20,'pakai_jadwal_diskon'=>1,'diskon_mulai'=>'2000-01-01','diskon_selesai'=>'2000-01-02'];
        $this->assertSame(100000, CatalogPricing::unitPrice($row));
        $row['pakai_jadwal_diskon']=0;
        $this->assertSame(80000, CatalogPricing::unitPrice($row));
    }

    public function testVariantIsNotLostWhenGatewayLabelIsTruncated(): void
    {
        $r=new ReflectionClass(Pages::class);
        $pages=$r->newInstanceWithoutConstructor();
        $item=['name'=>str_repeat('Long furniture ',5),'variant'=>'Oak'];
        $this->assertSame('Oak',$r->getMethod('itemVariantName')->invoke($pages,$item));
        $this->assertSame('Walnut',$r->getMethod('itemVariantName')->invoke($pages,['name'=>'Legacy chair (Walnut)']));
    }

    public function testPublicReturnDoesNotIssueAnotherOrdersToken(): void
    {
        session()->remove(['email','isLogin','active','role','checkout_order_tokens']);
        $r=new ReflectionClass(Pages::class);
        $pages=$r->newInstanceWithoutConstructor();
        $model=new class {
            public function getPemesanan($id): array { return ['id_midtrans'=>'FIXTURE','email'=>'fixture@example.test','status'=>'Proses','data_mid'=>json_encode(['_app_order_access_token'=>str_repeat('a',64)])]; }
        };
        $r->getProperty('pemesananModel')->setValue($pages,$model);
        $request=service('request');
        $request->setGlobal('get',['order_id'=>'FIXTURE']);
        $r->getProperty('request')->setValue($pages,$request);
        $response=$r->getMethod('redirectFromMidtrans')->invoke($pages);
        $this->assertStringNotContainsString('access_token=', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString(str_repeat('a',64), $response->getHeaderLine('Location'));
    }

    public function testCartMergeCombinesWithoutPersistingRenderingDetails(): void
    {
        $existing=[['id_barang'=>'P1','varian'=>'Oak','jumlah'=>2]];
        $guest=[['id_barang'=>'P1','varian'=>'oak','jumlah'=>1,'detail'=>['secret'=>'not a cart field']]];
        $merged=CheckoutState::mergeCart($existing,$guest);
        $this->assertCount(1,$merged);
        $this->assertSame(3,$merged[0]['jumlah']);
        $this->assertArrayNotHasKey('detail',$merged[0]);
    }

    public function testGeneratedOrderIdsFitTheExistingSchema(): void
    {
        $ids=[];
        for($i=0;$i<50;$i++) foreach(['web','market','shop'] as $method) {
            $id=\App\Services\OrderIdentifiers::$method();
            $this->assertSame(20,strlen($id)); $ids[]=$id;
        }
        $this->assertCount(count($ids),array_unique($ids));
    }
}
