<?php

namespace Tests\Unit;

use App\Services\LocalePreference;
use PHPUnit\Framework\TestCase;

final class LocalePreferenceTest extends TestCase
{
    public function testSavedChoiceOverridesCountryDetection(): void
    {
        self::assertSame('en', LocalePreference::choose('en', 'ID'));
        self::assertSame('id', LocalePreference::choose('id', 'US'));
    }

    public function testCountrySetsFirstVisitLanguageAndUnknownFallsBackToIndonesian(): void
    {
        self::assertSame('id', LocalePreference::choose(null, 'ID'));
        self::assertSame('en', LocalePreference::choose(null, 'US'));
        self::assertSame('id', LocalePreference::choose(null, 'XX'));
        self::assertSame('id', LocalePreference::choose(null, 'T1'));
        self::assertSame('id', LocalePreference::choose(null, null));
    }

    public function testLocalizedPathPreservesPathAndQuery(): void
    {
        self::assertSame('/en/product?koleksi=cody', LocalePreference::localizePath('/product?koleksi=cody', 'en'));
        self::assertSame('/product?koleksi=cody', LocalePreference::localizePath('/en/product?koleksi=cody', 'id'));
    }

    public function testExternalOrMalformedReturnDestinationFallsBackToLocalizedHome(): void
    {
        self::assertSame('/en', LocalePreference::localizePath('https://evil.example/path', 'en'));
        self::assertSame('/en', LocalePreference::localizePath('//evil.example/path', 'en'));
        self::assertSame('/en', LocalePreference::localizePath('/language/en', 'en'));
    }
}
