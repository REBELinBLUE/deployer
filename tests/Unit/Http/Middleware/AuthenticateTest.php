<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Mockery as m;
use REBELinBLUE\Deployer\Http\Middleware\Authenticate;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Middleware\Authenticate
 */
class AuthenticateTest extends TestCase
{
    private $redirector;
    private $response;
    private $auth;

    public function setUp()
    {
        parent::setUp();

        $this->redirector = m::mock(Redirector::class);
        $this->response   = m::mock(ResponseFactory::class);
        $this->auth       = m::mock(Factory::class);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsAuthenticated()
    {
        $expectedGuard = 'session';

        $expected = m::mock(Request::class);
        $expected->shouldNotReceive('ajax');

        $this->response->shouldNotReceive('make');
        $this->redirector->shouldNotReceive('guest');

        $closure  = function ($request) use ($expected) {
            $this->assertSame($request, $expected);

            return true;
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('guest')->andReturn(false);

        $middleware = new Authenticate($this->redirector, $this->response, $this->auth);
        $actual     = $middleware->handle($expected, $closure, $expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsNotAuthenticatedOnAjaxRequest()
    {
        $expectedGuard = 'session';
        $expected      = 'an-ajax-response';

        $request = m::mock(Request::class);
        $request->shouldReceive('ajax')->andReturn(true);

        $this->response->shouldReceive('make')
                       ->with(m::type('string'), Response::HTTP_UNAUTHORIZED)
                       ->andReturn($expected);

        $this->redirector->shouldNotReceive('guest');

        $closure  = function () {
            // Nothing much here, should not execute
            $this->assertFalse(true);
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('guest')->andReturn(true);

        $middleware = new Authenticate($this->redirector, $this->response, $this->auth);
        $actual     = $middleware->handle($request, $closure, $expectedGuard);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleIsNotAuthenticatedOnNonAjaxRequest()
    {
        $expectedGuard = 'session';
        $expected      = 'a-redirect-response';

        $request = m::mock(Request::class);
        $request->shouldReceive('ajax')->andReturn(false);

        $this->response->shouldNotReceive('make');
        $this->redirector->shouldReceive('guest')->with(m::type('string'))->andReturn($expected);

        $closure  = function () {
            // Nothing much here, should not execute
            $this->assertFalse(true);
        };

        $this->auth->shouldReceive('guard')->with($expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('guest')->andReturn(true);

        $middleware = new Authenticate($this->redirector, $this->response, $this->auth);
        $actual     = $middleware->handle($request, $closure, $expectedGuard);

        $this->assertSame($expected, $actual);
    }
}
