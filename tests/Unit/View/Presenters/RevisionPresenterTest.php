<?php

namespace REBELinBLUE\Deployer\Tests\Unit\View\Presenters;

use Illuminate\Contracts\Translation\Translator;
use Mockery as m;
use REBELinBLUE\Deployer\Revision;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use REBELinBLUE\Deployer\View\Presenters\RevisionPresenter;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\View\Presenters\RevisionPresenter
 */
class RevisionPresenterTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @covers ::presentCreator
     */
    public function testPresentCreatorReturnsUser()
    {
        $expected = 'John Smith';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->atLeast()->once()->with('name')->andReturn($expected);

        $revision = m::mock(Revision::class);
        $revision->shouldReceive('getAttribute')->atLeast()->once()->with('user')->andReturn($user);

        $presenter = new RevisionPresenter($this->translator);
        $presenter->setWrappedObject($revision);

        $actual    = $presenter->presentCreator();

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::presentCreator
     */
    public function testPresentCreatorReturnSystemWhenNoUser()
    {
        $expected = 'a-translated-string';

        $this->translator->shouldReceive('trans')->with('revisions.system')->andReturn($expected);

        $revision = m::mock(Revision::class);
        $revision->shouldReceive('getAttribute')->atLeast()->once()->with('user')->andReturn(null);

        $presenter = new RevisionPresenter($this->translator);
        $presenter->setWrappedObject($revision);

        $actual    = $presenter->presentCreator();

        $this->assertSame($expected, $actual);
    }
}
