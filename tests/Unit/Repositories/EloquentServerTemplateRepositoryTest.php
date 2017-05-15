<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\ServerTemplateRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentServerTemplateRepository;
use REBELinBLUE\Deployer\ServerTemplate;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentServerTemplateRepositoryTest
 */
class EloquentServerTemplateRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(ServerTemplate::class, EloquentServerTemplateRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsServerTemplateRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            ServerTemplate::class,
            EloquentServerTemplateRepository::class,
            ServerTemplateRepositoryInterface::class
        );
    }
}
