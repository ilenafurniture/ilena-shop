<?php

use CodeIgniter\Test\CIUnitTestCase;

final class AnalyticsLocaleRouteOrderingTest extends CIUnitTestCase
{
    public function testAnalyticsRoutesAreRegisteredBeforeLocaleOnlyRouting(): void
    {
        $routes = file_get_contents(APPPATH . 'Config/Routes.php');
        $localeSwitch = strpos($routes, '$routes->useSupportedLocalesOnly(true);');

        $this->assertNotFalse($localeSwitch);
        foreach ([
            "Analytics::index",
            "Analytics::live",
            "Analytics::exportCsv",
            "Analytics::exportPdf",
            "Analytics::addBlacklist",
            "Analytics::delBlacklist",
        ] as $handler) {
            $position = strpos($routes, $handler);
            $this->assertNotFalse($position, $handler . ' route exists');
            $this->assertLessThan($localeSwitch, $position, $handler . ' route precedes locale-only routing');
        }
    }
}
