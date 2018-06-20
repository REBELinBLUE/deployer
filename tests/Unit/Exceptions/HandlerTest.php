<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery as m;
use REBELinBLUE\Deployer\Exceptions\Handler;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Exceptions\Handler
 */
class HandlerTest extends TestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Handler
     */
    private $handler;

    public function setUp()
    {
        parent::setUp();

        $this->request = m::mock(Request::class);
        $this->handler = new Handler($this->app);
    }

    /**
     * @covers ::unauthenticated
     */
    public function testUnauthenticated()
    {
        $exception = new AuthenticationException();

        $this->request->shouldReceive('expectsJson')->andReturn(false);

        $actual = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(RedirectResponse::class, $actual);
        $this->assertSame(config('app.url') . '/login', $actual->headers->get('location'));
    }

    /**
     * @covers ::unauthenticated
     */
    public function testUnauthenticatedWhenExpectsJson()
    {
        $exception = new AuthenticationException();

        $this->request->shouldReceive('expectsJson')->andReturn(true);

        $actual = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $actual);
        $this->assertSame(401, $actual->getStatusCode());
        $this->assertSame(['error' => 'Unauthenticated.'], $actual->original);
    }
}
