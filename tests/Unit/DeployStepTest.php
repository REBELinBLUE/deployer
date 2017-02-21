<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\DeployStepPresenter;
use Robbo\Presenter\PresentableInterface;

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

        $this->assertInstanceOf(PresentableInterface::class, $step);
    }

    /**
     * @covers ::getPresenter
     */
    public function testGetPresenter()
    {
        $step      = new DeployStep();
        $presenter = $step->getPresenter();

        $this->assertInstanceOf(DeployStepPresenter::class, $presenter);
        $this->assertSame($step, $presenter->getObject());
    }
}
