<?php

namespace REBELinBLUE\Deployer\Tests\Unit;

use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\View\Presenters\CommandPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Template
 */
class TemplateTest extends TestCase
{
    // FIXME: Test the ProjectRelation trait methods

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
}
