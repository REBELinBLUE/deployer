<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Exceptions;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Mockery as m;
use REBELinBLUE\Deployer\Exceptions\Handler;
use REBELinBLUE\Deployer\Tests\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Run as Whoops;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Exceptions\Handler
 * @todo Redo these tests as they are quite fragile because they depend on implementation details hidden in laravel
 */
class HandlerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

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

        $this->app     = app();
        $this->request = m::mock(Request::class);
        $this->handler = new Handler($this->app);
    }

    /**
     * @covers ::render
     */
    public function testRenderHandlesHttpException()
    {
        $code    = Response::HTTP_BAD_REQUEST;
        $message = Response::$statusTexts[$code];

        $exception = new HttpException($code, $message);

        $actual = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertSame($code, $actual->getStatusCode());
        $this->assertContains($message, $actual->getContent());
    }

    public function testRenderHandlesExceptionWhenWhoopsIsNotBound()
    {
        // This seems like a horrible way to do it, but we need to ensure that there is no binding in the container
        // and there is no documented way to do it because naturally there wouldn't be any need to unbind at runtime
        if ($this->app->bound(Whoops::class)) {
            $this->app->offsetUnset(Whoops::class);
        }

        $exception = new RuntimeException('Message');

        $actual = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertSame(500, $actual->getStatusCode());
        //$this->assertSame(500, $actual->getStatusText());
        $this->assertContains(RuntimeException::class, $actual->getContent());

        $this->markTestIncomplete('Still needs refactoring');
    }
}
