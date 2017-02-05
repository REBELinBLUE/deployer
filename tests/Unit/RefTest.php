<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use REBELinBLUE\Deployer\Ref;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Ref
 */
class RefTest extends TestCase
{
    /**
     * @covers ::project
     */
    public function testProject()
    {
        $ref    = new Ref();
        $actual = $ref->project();

        $this->assertInstanceOf(BelongsTo::class, $actual);
        $this->assertSame('project', $actual->getRelation());
    }
}
