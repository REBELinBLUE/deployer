<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\Settings;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Settings
 */
class SettingsTest extends TestCase
{
    /**
     * @covers ::schemes
     */
    public function testSchemes()
    {
        $settings = new Settings();
        $actual   = $settings->schemes();

        $this->assertIsArray($actual);
        foreach ($actual as $element) {
            $this->assertIsString($element);
        }
    }

    /**
     * @covers ::themes
     */
    public function testThemes()
    {
        $settings = new Settings();
        $actual   = $settings->themes();

        $this->assertIsArray($actual);
        foreach ($actual as $element) {
            $this->assertIsString($element);
        }
    }
}
