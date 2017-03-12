<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Redirector;
use Mockery as m;
use REBELinBLUE\Deployer\Events\JsonWebTokenExpired;
use REBELinBLUE\Deployer\Http\Middleware\RefreshJsonWebToken;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Middleware\RefreshJsonWebToken
 */
class RefreshJsonWebTokenTest extends TestCase
{
    private $jwt;
    private $redirector;
    private $dispatcher;
    private $response;
    private $auth;
    private $user;
    private $expectedGuard;
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->jwt        = m::mock(JWTAuth::class);
        $this->redirector = m::mock(Redirector::class);
        $this->dispatcher = $this->app->make('events');
        $this->response   = m::mock(ResponseFactory::class);
        $this->auth       = m::mock(Factory::class);
        $this->user       = m::mock(User::class);

        $this->expectedGuard = 'session';

        $this->request = m::mock(Request::class);
        $this->request->shouldReceive('session')->andReturnSelf();

        $this->auth->shouldReceive('guard')->with($this->expectedGuard)->andReturnSelf();
        $this->auth->shouldReceive('user')->andReturn($this->user);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWithOutTokenInSession()
    {
        $this->request->shouldReceive('has')->with('jwt')->andReturn(false);

        $this->response->shouldNotReceive('make');
        $this->redirector->shouldNotReceive('guest');
        $this->jwt->shouldNotReceive('authenticate');

        $closure = function ($request) {
            $this->assertSame($this->request, $request);

            return true;
        };

        $this->expectsEvents(JsonWebTokenExpired::class);

        $middleware = new RefreshJsonWebToken(
            $this->jwt,
            $this->dispatcher,
            $this->redirector,
            $this->response,
            $this->auth
        );
        $actual = $middleware->handle($this->request, $closure, $this->expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleWithValidTokenInSession()
    {
        $token          = 'an-expected-token';
        $expectedUserId = 1000;

        $this->request->shouldReceive('has')->with('jwt')->andReturn(true);
        $this->request->shouldReceive('get')->with('jwt')->andReturn($token);
        $this->request->shouldNotReceive('ajax');

        $this->response->shouldNotReceive('make');
        $this->redirector->shouldNotReceive('guest');

        $this->user->shouldReceive('getAttribute')->with('id')->andReturn($expectedUserId);

        $this->jwt->shouldReceive('authenticate')->with($token)->andReturn((object) ['id' => $expectedUserId]);

        $closure = function ($request) {
            $this->assertSame($this->request, $request);

            return true;
        };

        $this->doesntExpectEvents(JsonWebTokenExpired::class);

        $middleware = new RefreshJsonWebToken(
            $this->jwt,
            $this->dispatcher,
            $this->redirector,
            $this->response,
            $this->auth
        );
        $actual = $middleware->handle($this->request, $closure, $this->expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleExpiredTokenGeneratesToken()
    {
        $token = 'an-expected-token';

        $this->request->shouldReceive('has')->with('jwt')->andReturn(true);
        $this->request->shouldReceive('get')->with('jwt')->andReturn($token);

        $this->jwt->shouldReceive('authenticate')->with($token)->andThrow(TokenExpiredException::class);

        $this->response->shouldNotReceive('make');
        $this->redirector->shouldNotReceive('guest');

        $closure = function ($request) {
            $this->assertSame($this->request, $request);

            return true;
        };

        $this->expectsEvents(JsonWebTokenExpired::class);

        $middleware = new RefreshJsonWebToken(
            $this->jwt,
            $this->dispatcher,
            $this->redirector,
            $this->response,
            $this->auth
        );
        $actual = $middleware->handle($this->request, $closure, $this->expectedGuard);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleTokenMismatchViaAjaxReturnsUnauthenticated()
    {
        $token    = 'an-expected-token';
        $expected = 'an-ajax-response';

        $this->request->shouldReceive('has')->with('jwt')->andReturn(true);
        $this->request->shouldReceive('get')->with('jwt')->andReturn($token);
        $this->request->shouldReceive('ajax')->andReturn(true);

        $this->user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $this->jwt->shouldReceive('authenticate')->with($token)->andReturn((object) ['id' => 1000]);

        $this->response->shouldReceive('make')
            ->with(m::type('string'), Response::HTTP_UNAUTHORIZED)
            ->andReturn($expected);

        $this->redirector->shouldNotReceive('guest');

        $closure = function () {
            // Nothing much here, should not execute
            $this->assertFalse(true);
        };

        $this->doesntExpectEvents(JsonWebTokenExpired::class);

        $middleware = new RefreshJsonWebToken(
            $this->jwt,
            $this->dispatcher,
            $this->redirector,
            $this->response,
            $this->auth
        );
        $actual = $middleware->handle($this->request, $closure, $this->expectedGuard);

        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::__construct
     * @covers ::handle
     */
    public function testHandleTokenMismatchNotViaAjaxReturnsUnauthenticated()
    {
        $token    = 'an-expected-token';
        $expected = 'a-redirect-response';

        $this->request->shouldReceive('has')->with('jwt')->andReturn(true);
        $this->request->shouldReceive('get')->with('jwt')->andReturn($token);
        $this->request->shouldReceive('ajax')->andReturn(false);

        $this->user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $this->jwt->shouldReceive('authenticate')->with($token)->andReturn((object) ['id' => 1000]);
        $this->response->shouldNotReceive('make');

        $this->redirector->shouldReceive('guest')->with(m::type('string'))->andReturn($expected);

        $closure = function () {
            // Nothing much here, should not execute
            $this->assertFalse(true);
        };

        $this->doesntExpectEvents(JsonWebTokenExpired::class);

        $middleware = new RefreshJsonWebToken(
            $this->jwt,
            $this->dispatcher,
            $this->redirector,
            $this->response,
            $this->auth
        );
        $actual = $middleware->handle($this->request, $closure, $this->expectedGuard);

        $this->assertSame($expected, $actual);
    }
}
