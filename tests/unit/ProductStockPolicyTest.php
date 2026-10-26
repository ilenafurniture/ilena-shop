<?php
use App\Services\ProductStockPolicy;
use CodeIgniter\Test\CIUnitTestCase;
final class ProductStockPolicyTest extends CIUnitTestCase
{
    public function testStaleProductFormCannotRestoreSoldStockOrCreateStock(): void
    {
        $result=ProductStockPolicy::rebase([['nama'=>'Oak','stok'=>2]],[['nama'=>'Oak','stok'=>10,'urutan_gambar'=>'2'],['nama'=>'Walnut','stok'=>99]]);
        $this->assertSame(2,$result[0]['stok']); $this->assertSame(0,$result[1]['stok']); $this->assertSame('2',$result[0]['urutan_gambar']);
    }
    public function testStockedVariantCannotDisappearDuringMetadataEdit(): void
    {
        $this->expectException(\RuntimeException::class);
        ProductStockPolicy::rebase([['nama'=>'Oak','stok'=>1]],[['nama'=>'Renamed','stok'=>1]]);
    }

    public function testMalformedPersistedVariantFailsClosed(): void
    {
        $this->expectException(\RuntimeException::class);
        ProductStockPolicy::rebase([['stok'=>3]],[['nama'=>'Oak','stok'=>3]]);
    }

    public function testStockedVariantNameCannotChangeCase(): void
    {
        $this->expectException(\RuntimeException::class);
        ProductStockPolicy::rebase([['nama'=>'Oak','stok'=>1]],[['nama'=>'OAK','stok'=>1]]);
    }
}
