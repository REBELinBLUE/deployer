<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events;

use Mockery as m;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Events\UrlUp;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\UrlUp
 */
class UrlUpTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers \REBELinBLUE\Deployer\Events\UrlChanged::__construct
     */
    public function testUrlProperty()
    {
        $url = m::mock(CheckUrl::class);

        $event = new UrlUp($url);

        $this->assertSame($url, $event->url);
    }
}
