<?php

namespace Tests\Unit;

use App\Services\AnalyticsCsvSanitizer;
use CodeIgniter\Test\CIUnitTestCase;

final class AnalyticsCsvSanitizerTest extends CIUnitTestCase
{
    public function testSpreadsheetFormulaPrefixesAreNeutralized(): void
    {
        foreach (['=1+1', '+SUM(A1:A2)', '-1+2', '@SUM(A1)', "\t=1+1", "  =1+1"] as $value) {
            $this->assertSame("'" . $value, AnalyticsCsvSanitizer::text($value));
        }
    }

    public function testOrdinaryPathsRemainUnchanged(): void
    {
        $this->assertSame('/product/cody?sort=price', AnalyticsCsvSanitizer::text('/product/cody?sort=price'));
    }
}
