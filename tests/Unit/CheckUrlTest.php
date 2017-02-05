<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\CheckUrl
 */
class CheckUrlTest extends TestCase
{
    /**
     * @dataProvider provideStatuses
     * @covers ::isHealthy
     */
    public function testIsHealthy($status, $expected)
    {
        $url = new CheckUrl();

        $this->assertFalse($url->isHealthy());

        $url->status = $status;

        $this->assertSame($expected, $url->isHealthy());
    }

    /**
     * @covers ::project
     */
    public function testProject()
    {
        $url    = new CheckUrl();
        $actual = $url->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertSame('project', $actual->getRelation());
    }

    public function provideStatuses()
    {
        return $this->fixture('CheckUrl')['healthy'];
    }
}
