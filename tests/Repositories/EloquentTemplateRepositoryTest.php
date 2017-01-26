<?php

namespace REBELinBLUE\Deployer\Tests\Repositories;

use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\Repositories\EloquentTemplateRepository;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\TestCase;

class EloquentTemplateRepositoryTest extends TestCase
{
    public function testExtendsEloquentRepository()
    {
        $model      = m::mock(Template::class);
        $repository = new EloquentTemplateRepository($model);

        $this->assertInstanceOf(EloquentRepository::class, $repository);
    }

    public function testImplementsTemplateRepositoryInterfaceInterface()
    {
        $model      = m::mock(Template::class);
        $repository = new EloquentTemplateRepository($model);

        $this->assertInstanceOf(TemplateRepositoryInterface::class, $repository);
    }

    public function testGetAll()
    {
        $expected = m::mock(Template::class);
        $expected->shouldReceive('get')->andReturnSelf();

        $model  = m::mock(Template::class);
        $model->shouldReceive('orderBy')->with('name')->andReturn($expected);

        $repository = new EloquentTemplateRepository($model);
        $actual     = $repository->getAll();

        $this->assertEquals($expected, $actual);
    }
}
