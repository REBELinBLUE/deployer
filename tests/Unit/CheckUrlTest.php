<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\CheckUrl
 */
class CheckUrlTest extends TestCase
{
    use TestsModel;

    /**
     * @dataProvider provideStatuses
     * @covers ::isHealthy
     *
     * @param int  $status
     * @param bool $expected
     */
    public function testIsHealthy(int $status, bool $expected)
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
        $this->assertBelongsTo('project', CheckUrl::class);
    }

    public function provideStatuses(): array
    {
        return $this->fixture('CheckUrl')['healthy'];
    }
}
