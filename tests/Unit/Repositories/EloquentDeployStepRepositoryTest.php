<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Repositories;

use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Repositories\Contracts\DeployStepRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentDeployStepRepository;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Repositories\EloquentDeployStepRepository
 */
class EloquentDeployStepRepositoryTest extends EloquentRepositoryTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsEloquentRepository()
    {
        $this->assertExtendsEloquentRepository(DeployStep::class, EloquentDeployStepRepository::class);
    }

    /**
     * @covers ::__construct
     */
    public function testImplementsConfigFileRepositoryInterface()
    {
        $this->assertImplementsRepositoryInterface(
            DeployStep::class,
            EloquentDeployStepRepository::class,
            DeployStepRepositoryInterface::class
        );
    }
}
