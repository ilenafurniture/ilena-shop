<?php

namespace Tests\Unit;

use App\Services\ChatWhatsappLink;
use CodeIgniter\Test\CIUnitTestCase;

final class ChatWhatsappLinkTest extends CIUnitTestCase
{
    public function testLinkContainsEncodedQuestionForUserReview(): void
    {
        $url = ChatWhatsappLink::forQuestion('  Apakah   Cody tersedia?  ');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

        $this->assertStringStartsWith('https://wa.me/628112938158?text=', $url);
        $this->assertSame('Halo Ilena, saya ingin bertanya: Apakah Cody tersedia?', $params['text'] ?? null);
    }

    public function testQuestionLengthIsBounded(): void
    {
        $url = ChatWhatsappLink::forQuestion(str_repeat('x', 1000));
        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

        $this->assertLessThanOrEqual(400, mb_strlen(substr((string) ($params['text'] ?? ''), strlen('Halo Ilena, saya ingin bertanya: '))));
    }

    public function testEmptyQuestionUsesGenericMessage(): void
    {
        $url = ChatWhatsappLink::forQuestion(' ');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

        $this->assertSame('Halo Ilena, saya membutuhkan bantuan seputar produk atau layanan.', $params['text'] ?? null);
    }

    public function testEnglishHandoffLinkStartsWithEnglishMessage(): void
    {
        $url = ChatWhatsappLink::forQuestion('Is Cody available?', 'en');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

        $this->assertSame('Hello Ilena, I have a question: Is Cody available?', $params['text'] ?? null);
    }
}
