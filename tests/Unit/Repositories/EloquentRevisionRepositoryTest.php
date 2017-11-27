<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Mockery as m;
use REBELinBLUE\Deployer\Repositories\Contracts\RevisionRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRevisionRepository;
use REBELinBLUE\Deployer\Revision;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentRevisionRepository
 */
class EloquentRevisionRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(Revision::class, EloquentRevisionRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsRevisionRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            Revision::class,
            EloquentRevisionRepository::class,
            RevisionRepositoryInterface::class
        );
    }

    /**
     * @covers ::getTypes
     */
    public function testGetTypes()
    {
        $expected = m::mock(Revision::class);

        $model = m::mock(Revision::class);
        $model->shouldReceive('select')->once()->andReturnSelf();
        $model->shouldReceive('distinct')->once()->andReturnSelf();
        $model->shouldReceive('get')->once()->andReturn($expected);

        $repository = new EloquentRevisionRepository($model);
        $actual     = $repository->getTypes();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getInstances
     */
    public function testGetInstances()
    {
        $expected = m::mock(Collection::class);
        $type = 'a-fake-type';

        $model = m::mock(Revision::class);
        $model->shouldReceive('select')->once()->andReturnSelf();
        $model->shouldReceive('where')->once()->with(m::type('string'), $type)->andReturnSelf();
        $model->shouldReceive('distinct')->once()->andReturnSelf();
        $model->shouldReceive('get')->once()->andReturn($expected);

        $repository = new EloquentRevisionRepository($model);
        $actual     = $repository->getInstances($type);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLogEntries
     */
    public function testGetLogEntries()
    {
        $paginate = 10;
        $expected = m::mock(Collection::class);

        $model = m::mock(Revision::class);
        $model->shouldNotReceive('where');
        $model->shouldReceive('orderBy')->andReturnSelf();
        $model->shouldReceive('paginate')->with($paginate)->andReturn($expected);

        $repository = new EloquentRevisionRepository($model);
        $actual     = $repository->getLogEntries($paginate);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLogEntries
     */
    public function testGetLogEntriesWithFilterType()
    {
        $paginate = 10;
        $filterByType = 'an-object-type';
        $expected = m::mock(Collection::class);

        $model = m::mock(Revision::class);
        $model->shouldReceive('orderBy')->andReturnSelf();

        $model->shouldReceive('where')->once()->with(m::type('string'), $filterByType)->andReturnSelf();
        $model->shouldReceive('paginate')->with($paginate)->andReturn($expected);

        $repository = new EloquentRevisionRepository($model);
        $actual     = $repository->getLogEntries($paginate, $filterByType);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::getLogEntries
     */
    public function testGetLogEntriesWithFilterInstance()
    {
        $paginate = 10;
        $filterByType = 'an-object-type';
        $filterByInstance = 'a-model-id';

        $expected = m::mock(Collection::class);

        $model = m::mock(Revision::class);
        $model->shouldReceive('orderBy')->andReturnSelf();
        $model->shouldReceive('where')->once()->with(m::type('string'), $filterByType)->andReturnSelf();
        $model->shouldReceive('where')->once()->with(m::type('string'), $filterByInstance)->andReturnSelf();
        $model->shouldReceive('paginate')->with($paginate)->andReturn($expected);

        $repository = new EloquentRevisionRepository($model);
        $actual     = $repository->getLogEntries($paginate, $filterByType, $filterByInstance);

        $this->assertSame($expected, $actual);
    }
}
