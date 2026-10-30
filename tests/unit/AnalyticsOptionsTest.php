<?php

namespace Tests\Unit;

use App\Services\AnalyticsOptions;
use CodeIgniter\Test\CIUnitTestCase;

final class AnalyticsOptionsTest extends CIUnitTestCase
{
    public function testDateRangeIsStrictAndBounded(): void
    {
        $options = AnalyticsOptions::normalize([
            'start' => '2020-01-01',
            'end' => '2026-10-30',
        ], '2026-10-30');

        $this->assertSame('2025-10-30', $options['start']);
        $this->assertSame('2026-10-30', $options['end']);
        $this->assertSame(366, (int) ((strtotime($options['end']) - strtotime($options['start'])) / 86400) + 1);
    }

    public function testInvalidAndReversedDatesNormalizeWithoutUnboundedRange(): void
    {
        $options = AnalyticsOptions::normalize([
            'start' => '2026-02-30',
            'end' => '2026-01-01',
        ], '2026-10-30');

        $this->assertSame('2026-01-01', $options['start']);
        $this->assertSame('2026-01-01', $options['end']);
    }

    public function testThresholdsAndExclusionIpsAreBoundedAndValidated(): void
    {
        $options = AnalyticsOptions::normalize([
            'min_duration' => '999999999',
            'exclude_low_avg_duration' => '-4',
            'exclude_high_hits_per_day' => '999999999',
            'exclude_ips' => '192.0.2.1,not-an-ip,2001:db8::1',
        ], '2026-10-30');

        $this->assertSame(86400, $options['min_duration']);
        $this->assertSame(0, $options['exclude_low_avg_duration']);
        $this->assertSame(100000, $options['exclude_high_hits_per_day']);
        $this->assertSame(['192.0.2.1', '2001:db8::1'], $options['exclude_ips']);
    }

    public function testArrayInputDoesNotTriggerStringCastAndIpListIsCapped(): void
    {
        $ips = implode(',', array_map(static fn (int $lastOctet): string => '192.0.2.' . $lastOctet, range(1, 55)));
        $options = AnalyticsOptions::normalize([
            'start' => ['2025-01-01'],
            'exclude_ips' => ['192.0.2.1'],
        ], '2026-10-30');
        $capped = AnalyticsOptions::normalize(['exclude_ips' => $ips], '2026-10-30');

        $this->assertSame('2026-10-01', $options['start']);
        $this->assertSame([], $options['exclude_ips']);
        $this->assertCount(50, $capped['exclude_ips']);
    }
}
