<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentTemplateRepository;
use REBELinBLUE\Deployer\Template;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentTemplateRepository
 */
class EloquentTemplateRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Template::class, EloquentTemplateRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsTemplateRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Template::class,
            EloquentTemplateRepository::class,
            TemplateRepositoryInterface::class
        );
    }

    /**
     * @covers ::getAll
     */
    public function testGetAll()
    {
        $expected = m::mock(Template::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Template::class);
        $model->shouldReceive('orderBy')->once()->with('name')->andReturn($expected);

        $repository = new EloquentTemplateRepository($model);
        $actual     = $repository->getAll();

        $this->assertSame($expected, $actual);
    }
}
