<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\Traits\ProductRelations;
use REBELinBLUE\Deployer\Tests\Unit\Traits\TestsModel;
use REBELinBLUE\Deployer\View\Presenters\CommandPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Template
 */
class TemplateTest extends TestCase
{
    use TestsModel, ProductRelations;

    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $template = new Template();

        $this->assertInstanceOf(HasPresenter::class, $template);
    }

    /**
     * @covers ::getPresenterClass
     */
    public function testGetPresenterClass()
    {
        $template      = new Template();
        $presenter     = $template->getPresenterClass();

        $this->assertSame(CommandPresenter::class, $presenter);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\ProjectRelations
     */
    public function testHasProjectRelations()
    {
        $this->assertHasProjectRelations(Template::class);
    }
}
