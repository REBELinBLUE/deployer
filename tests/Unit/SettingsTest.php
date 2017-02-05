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

        $this->assertInternalType('array', $actual);
        foreach ($actual as $element) {
            $this->assertInternalType('string', $element);
        }
    }

    /**
     * @covers ::themes
     */
    public function testThemes()
    {
        $settings = new Settings();
        $actual   = $settings->themes();

        $this->assertInternalType('array', $actual);
        foreach ($actual as $element) {
            $this->assertInternalType('string', $element);
        }
    }
}
