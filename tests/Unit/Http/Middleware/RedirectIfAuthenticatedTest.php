<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Routing\Redirector;
use Mockery as m;
use REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Middleware\RedirectIfAuthenticated
 */
class RedirectIfAuthenticatedTest extends TestCase
{
    private $redirector;
    private $auth;

    public function setUp()
    {
        parent::setUp();

        $this->redirector = m::mock(Redirector::class);
        $this->auth       = m::mock(Factory::class);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsNotAuthenticated()
    {
        $expectedGuard = 'session';

        $expected = m::mock(Request::class);

        $this->redirector->shouldNotReceive('to');

        $closure  = function ($request) use ($expected) {
            $this->assertSame($request, $expected);

            return true;
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('check')->andReturn(false);

        $middleware = new RedirectIfAuthenticated($this->redirector, $this->auth);
        $actual     = $middleware->handle($expected, $closure, $expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsAuthenticated()
    {
        $expectedGuard    = 'session';
        $expectedRedirect = 'a-redirect-url';

        $expected = m::mock(Request::class);

        $this->redirector->shouldReceive('to')->with(m::type('string'))->andReturn($expectedRedirect);

        $closure  = function () {
            // Nothing much here, should not execute
            $this->assertFalse(true);
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('check')->andReturn(true);

        $middleware = new RedirectIfAuthenticated($this->redirector, $this->auth);
        $actual     = $middleware->handle($expected, $closure, $expectedGuard);

        $this->assertSame($expectedRedirect, $actual);
    }
}
