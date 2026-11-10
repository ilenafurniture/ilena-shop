<?php

namespace Tests\Unit;

use App\Services\ProductDescriptionSanitizer;
use CodeIgniter\Test\CIUnitTestCase;

final class ProductDescriptionSanitizerTest extends CIUnitTestCase
{
    public function testEncodedProductMarkupIsRenderedAsSafeMarkupInsteadOfVisibleHtmlSource(): void
    {
        $sanitizer = new ProductDescriptionSanitizer();
        $result = $sanitizer->sanitizeHtml('&lt;p&gt;Kayu &lt;strong&gt;solid&lt;/strong&gt;&lt;/p&gt;');

        $this->assertStringContainsString('<p>', $result);
        $this->assertStringContainsString('<strong>solid</strong>', $result);
        $this->assertStringNotContainsString('&lt;p&gt;', $result);
    }

    public function testProductMarkupRemovesExecutableContentAndUnsafeAttributes(): void
    {
        $sanitizer = new ProductDescriptionSanitizer();
        $result = $sanitizer->sanitizeHtml('&lt;p onclick=&quot;alert(1)&quot;&gt;Aman&lt;/p&gt;&lt;script&gt;alert(2)&lt;/script&gt;&lt;a href=&quot;javascript:alert(3)&quot;&gt;tautan&lt;/a&gt;');

        $this->assertStringContainsString('<p>Aman</p>', $result);
        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringNotContainsString('<script', $result);
        $this->assertStringNotContainsString('javascript:', $result);
    }

    public function testSpecificationTextDoesNotExposeEncodedMarkup(): void
    {
        $sanitizer = new ProductDescriptionSanitizer();
        $result = $sanitizer->plainText('&lt;p&gt;Bersihkan dengan kain lembut.&lt;/p&gt;');

        $this->assertSame('Bersihkan dengan kain lembut.', $result);
        $this->assertStringNotContainsString('<p>', $result);
    }

    public function testDoubleEncodedLegacyMarkupIsNormalized(): void
    {
        $sanitizer = new ProductDescriptionSanitizer();

        $this->assertSame('Periksa ukuran sebelum membeli.', $sanitizer->plainText('&amp;lt;p&amp;gt;Periksa ukuran sebelum membeli.&amp;lt;/p&amp;gt;'));
    }
}
