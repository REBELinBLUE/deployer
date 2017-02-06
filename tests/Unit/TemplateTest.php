<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\TestsModel;
use REBELinBLUE\Deployer\View\Presenters\CommandPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Template
 */
class TemplateTest extends TestCase
{
    use TestsModel, ProductRelationsTests;

    /**
     * @covers ::__construct
     */
    public function testIsPresentable()
    {
        $template = new Template();

        $this->assertInstanceOf(PresentableInterface::class, $template);
    }

    /**
     * @covers ::getPresenter
     */
    public function testGetPresenter()
    {
        $template      = new Template();
        $presenter     = $template->getPresenter();

        $this->assertInstanceOf(CommandPresenter::class, $presenter);
        $this->assertSame($template, $presenter->getObject());
    }

    /**
     * @covers \REBELinBLUE\Deployer\Traits\ProductRelations
     */
    public function testHasProjectRelations()
    {
        $this->assertHasProjectRelations(Template::class);
    }
}
