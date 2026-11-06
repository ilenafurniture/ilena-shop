<?php

namespace Tests\Unit;

use App\Services\ApmShippingRateService;
use CodeIgniter\Test\CIUnitTestCase;

final class ApmShippingRateServiceTest extends CIUnitTestCase
{
    private string $directory;
    private string $defaultPath;
    private string $overridePath;
    private array $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ilena-apm-' . bin2hex(random_bytes(5));
        mkdir($this->directory, 0777, true);
        $this->defaultPath = $this->directory . DIRECTORY_SEPARATOR . 'default.json';
        $this->overridePath = $this->directory . DIRECTORY_SEPARATOR . 'override.json';
        $this->fixture = [
            ['id' => 'APM-001', 'regional' => 'Jawa Barat / Banten / DKI', 'area' => 'JAWA BARAT', 'destination' => 'Bandung', 'rate_per_kg' => 3500, 'minimum_kg' => 30, 'lead_min_days' => 2, 'lead_max_days' => 3, 'schedule' => '', 'source_status' => 'DARI SCAN'],
            ['id' => 'APM-002', 'regional' => 'Jawa Timur / Madura', 'area' => 'MADURA', 'destination' => 'Gianyar', 'rate_per_kg' => 5500, 'minimum_kg' => 30, 'lead_min_days' => 3, 'lead_max_days' => 4, 'schedule' => '', 'source_status' => 'DARI SCAN'],
            ['id' => 'APM-003', 'regional' => 'Bali', 'area' => 'BALI', 'destination' => 'Gianyar', 'rate_per_kg' => 6000, 'minimum_kg' => 50, 'lead_min_days' => 3, 'lead_max_days' => 4, 'schedule' => '', 'source_status' => 'DARI SCAN'],
            ['id' => 'APM-004', 'regional' => 'Sumatera', 'area' => 'SUMATERA UTARA', 'destination' => 'Simalungun', 'rate_per_kg' => 12500, 'minimum_kg' => 30, 'lead_min_days' => 5, 'lead_max_days' => 6, 'schedule' => '', 'source_status' => 'ANOMALI DI PDF'],
            ['id' => 'APM-005', 'regional' => 'Sumatera', 'area' => 'SUMATERA UTARA', 'destination' => 'Simalungun', 'rate_per_kg' => 13900, 'minimum_kg' => 30, 'lead_min_days' => 5, 'lead_max_days' => 6, 'schedule' => '', 'source_status' => 'ANOMALI DI PDF'],
        ];
        file_put_contents($this->defaultPath, json_encode(['version' => 1, 'source' => 'test', 'rates' => $this->fixture]));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->directory);
        parent::tearDown();
    }

    public function testUsesMinimumBillableWeightAndReturnsOneApmRate(): void
    {
        $service = new ApmShippingRateService($this->defaultPath, $this->overridePath);
        $rates = $service->rates(['kab' => 'Kota Bandung', 'prov' => 'Jawa Barat'], [$this->cartItem(8, 1)]);

        $this->assertCount(1, $rates);
        $this->assertSame(105000, $rates[0]['harga']);
        $this->assertSame('APM-001', $rates[0]['tariff_id']);
        $this->assertSame('apm', $rates[0]['provider']);
    }

    public function testImportedWorkbookSnapshotHasOneUniqueIdPerApmRow(): void
    {
        $service = new ApmShippingRateService(APPPATH . 'Data/apm_shipping_rates.json', $this->overridePath);
        $this->assertSame(444, $service->summary()['rows']);
        $rates = $service->rates(['kab' => 'Kota Administrasi Jakarta Selatan', 'prov' => 'DKI Jakarta'], [$this->cartItem(60, 1)]);
        $this->assertCount(1, $rates);
        $this->assertSame('apm', $rates[0]['provider']);
        $this->assertSame('APM-001', $rates[0]['tariff_id']);
    }

    public function testDuplicateCityIsResolvedByProvinceAndNeverAddsRatesTogether(): void
    {
        $service = new ApmShippingRateService($this->defaultPath, $this->overridePath);
        $rates = $service->rates(['kab' => 'Gianyar', 'prov' => 'Bali'], [$this->cartItem(60, 1)]);

        $this->assertCount(1, $rates);
        $this->assertSame('APM-003', $rates[0]['tariff_id']);
        $this->assertSame(360000, $rates[0]['harga']);
    }

    public function testDestinationSuggestionsAreMatchedDeduplicatedAndBounded(): void
    {
        $service = new ApmShippingRateService($this->defaultPath, $this->overridePath);

        $this->assertSame([], $service->destinationSuggestions('B'));
        $suggestions = $service->destinationSuggestions('gian', 1);

        $this->assertCount(1, $suggestions);
        $this->assertSame('Gianyar', $suggestions[0]['destination']);
        $this->assertSame('MADURA', $suggestions[0]['province']);
        $this->assertSame('Gianyar — MADURA', $suggestions[0]['label']);
        $all = $service->destinationSuggestions('gianyar', 12);
        $this->assertCount(2, $all);
        $this->assertSame(['MADURA', 'BALI'], array_column($all, 'province'));
    }

    public function testAmbiguousOrFlaggedDestinationReturnsNoQuoteForCsHandoff(): void
    {
        $service = new ApmShippingRateService($this->defaultPath, $this->overridePath);
        $ambiguous = $service->rates(['kab' => 'Simalungun', 'prov' => 'Sumatera Utara'], [$this->cartItem(80, 1)]);
        $this->assertSame([], $ambiguous);
        $this->assertSame('ambiguous_destination', $service->lastError());

        $flaggedFixture = [$this->fixture[3]];
        file_put_contents($this->defaultPath, json_encode(['version' => 1, 'rates' => $flaggedFixture]));
        $flagged = $service->rates(['kab' => 'Simalungun', 'prov' => 'Sumatera Utara'], [$this->cartItem(80, 1)]);
        $this->assertSame([], $flagged);
        $this->assertSame('tariff_needs_confirmation', $service->lastError());
    }

    public function testCsvImportReplacesSnapshotOnlyWhenIdsAreUnique(): void
    {
        $service = new ApmShippingRateService($this->defaultPath, $this->overridePath);
        $headers = 'id,regional,area,destination,rate_per_kg,minimum_kg,lead_min_days,lead_max_days,schedule,source_status' . PHP_EOL;
        $valid = $this->directory . DIRECTORY_SEPARATOR . 'valid.csv';
        file_put_contents($valid, $headers . 'APM-901,Jawa Barat,JAWA BARAT,Bandung,4000,25,2,3,,DARI SCAN' . PHP_EOL);
        $result = $service->importCsv($valid);
        $this->assertTrue($result['ok']);
        $this->assertSame(1, $service->summary()['rows']);
        $this->assertSame(100000, $service->rates(['kab' => 'Bandung', 'prov' => 'Jawa Barat'], [$this->cartItem(8, 1)])[0]['harga']);

        $replacement = $this->directory . DIRECTORY_SEPARATOR . 'replacement.csv';
        file_put_contents($replacement, $headers . 'APM-902,Jawa Barat,JAWA BARAT,Bandung,5000,25,2,3,,DARI SCAN' . PHP_EOL);
        $this->assertTrue($service->importCsv($replacement)['ok']);
        $this->assertFileExists($this->overridePath . '.previous');
        $this->assertTrue($service->restorePrevious());
        $this->assertSame('APM-901', $service->rates(['kab' => 'Bandung', 'prov' => 'Jawa Barat'], [$this->cartItem(8, 1)])[0]['tariff_id']);

        $duplicate = $this->directory . DIRECTORY_SEPARATOR . 'duplicate.csv';
        file_put_contents($duplicate, $headers . 'APM-901,Jawa Barat,JAWA BARAT,Bandung,4000,25,2,3,,DARI SCAN' . PHP_EOL . 'APM-901,Bali,BALI,Gianyar,6000,50,3,4,,DARI SCAN' . PHP_EOL);
        $rejected = $service->importCsv($duplicate);
        $this->assertFalse($rejected['ok']);
        $this->assertSame(1, $service->summary()['rows']);
        $this->assertSame('APM-901', $service->rates(['kab' => 'Bandung', 'prov' => 'Jawa Barat'], [$this->cartItem(8, 1)])[0]['tariff_id']);
        $this->assertFileExists($this->overridePath . '.previous');
    }

    private function cartItem(float $weight, int $quantity): array
    {
        return [
            'jumlah' => $quantity,
            'detail' => ['deskripsi' => json_encode(['dimensi' => ['paket' => ['berat' => $weight]]])],
        ];
    }
}
