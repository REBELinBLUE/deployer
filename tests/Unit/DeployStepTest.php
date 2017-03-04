<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\DeployStepPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\DeployStep
 */
class DeployStepTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $step = new DeployStep();

        $this->assertInstanceOf(HasPresenter::class, $step);
    }

    /**
     * @covers ::getPresenterClass
     */
    public function testGetPresenterClass()
    {
        $step      = new DeployStep();
        $presenter = $step->getPresenterClass();

        $this->assertSame(DeployStepPresenter::class, $presenter);
    }
}
