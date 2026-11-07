<?php

use App\Services\OrderInvoiceNumber;
use CodeIgniter\Test\CIUnitTestCase;

final class OrderInvoiceNumberTest extends CIUnitTestCase
{
    public function testFormatsStableShortNumberFromOrderPrimaryKey(): void
    {
        $this->assertSame('IL00000080', OrderInvoiceNumber::fromPrimaryKey(80));
        $this->assertSame('IL00000001', OrderInvoiceNumber::fromPrimaryKey('1'));
        $this->assertSame('IL123456789', OrderInvoiceNumber::fromPrimaryKey(123456789));
    }

    public function testRejectsMissingOrInvalidPrimaryKeys(): void
    {
        $this->assertSame('—', OrderInvoiceNumber::fromPrimaryKey(null));
        $this->assertSame('—', OrderInvoiceNumber::fromPrimaryKey(0));
        $this->assertSame('—', OrderInvoiceNumber::fromPrimaryKey('not-an-id'));
    }
}
