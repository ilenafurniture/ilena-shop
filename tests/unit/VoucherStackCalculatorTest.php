<?php

namespace Tests\Unit;

use App\Services\Payments\VoucherStackCalculator;
use CodeIgniter\Test\CIUnitTestCase;

final class VoucherStackCalculatorTest extends CIUnitTestCase
{
    public function testUsesHighestEligibleAutomaticAndOneClaimedVoucher(): void
    {
        $calculator = new VoucherStackCalculator();
        $result = $calculator->calculate([
            ['id'=>1,'kode'=>'AUTO5','nama'=>'Auto 5','tipe'=>'persen','nilai'=>5],
            ['id'=>2,'kode'=>'AUTO10','nama'=>'Auto 10','tipe'=>'persen','nilai'=>10],
        ], ['id'=>3,'kode'=>'CLAIM20','nama'=>'Claim 20','tipe'=>'rupiah','nilai'=>20000], true, 100000);

        self::assertSame([2, 3], array_column($result['applied'], 'id'));
        self::assertSame([10000, 20000], array_column($result['applied'], 'discount'));
        self::assertSame(30000, $result['discount']);
        self::assertSame(70000, $result['remaining']);
    }

    public function testUnredeemedManualVoucherDoesNotStackWithAutomaticVoucher(): void
    {
        $result = (new VoucherStackCalculator())->calculate(
            [['id'=>1,'kode'=>'AUTO10','tipe'=>'persen','nilai'=>10]],
            ['id'=>2,'kode'=>'LEGACY','tipe'=>'rupiah','nilai'=>15000], false, 100000
        );

        self::assertSame([['id'=>2,'kode'=>'LEGACY','nama'=>'','target'=>'semua','discount'=>15000,'source'=>'redeemed']], $result['applied']);
        self::assertSame(15000, $result['discount']);
    }

    public function testCapsDiscountAtRemainingSubtotalAndDoesNotDuplicateSameVoucher(): void
    {
        $result = (new VoucherStackCalculator())->calculate(
            [['id'=>7,'kode'=>'SAME','tipe'=>'persen','nilai'=>80]],
            ['id'=>7,'kode'=>'SAME','tipe'=>'rupiah','nilai'=>50000], true, 10000
        );

        self::assertCount(1, $result['applied']);
        self::assertSame(8000, $result['discount']);
        self::assertSame(2000, $result['remaining']);
    }
}
