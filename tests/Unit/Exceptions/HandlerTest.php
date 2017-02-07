<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery as m;
use REBELinBLUE\Deployer\Exceptions\Handler;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Tests\Unit\stubs\ExceptionWithHeaders;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Run as Whoops;
use Whoops\RunInterface as WhoopsInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Exceptions\Handler
 * @todo Redo these tests as they are quite fragile because they depend on implementation details hidden in laravel
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
        $this->assertContainsMessage($message, $actual->getContent());
    }

    /**
     * @covers ::render
     */
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
        $this->assertContainsMessage(RuntimeException::class, $actual->getContent());
    }

    /**
     * @dataProvider provideWhoopsExceptions
     * @covers ::render
     * @covers ::isSafeToWhoops
     * @covers ::renderExceptionWithWhoops
     */
    public function testRenderHandlesExpectedExceptionsWhenWhoopsIsBound($class)
    {
        $expected = 'a-whoops-exception-page';

        $exception = new $class();

        $whoops = $this->mockWhoops();
        $whoops->shouldReceive('handleException')->once()->with($exception)->andReturn($expected);

        $actual = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertSame(500, $actual->getStatusCode());
        $this->assertSame($expected, $actual->getContent());
    }

    /**
     * @covers ::render
     * @covers ::isSafeToWhoops
     * @covers ::renderExceptionWithWhoops
     */
    public function testRenderHandlesExpectedExceptionsWithHttpDetails()
    {
        $expected = 'a-whoops-exception-page';
        $code     = 401;
        $headers  = ['foo' => 'bar'];

        $exception = m::mock(ExceptionWithHeaders::class);
        $exception->shouldReceive('getHeaders')->once()->andReturn($headers);
        $exception->shouldReceive('getStatusCode')->once()->andReturn($code);

        $whoops = $this->mockWhoops();
        $whoops->shouldReceive('handleException')->once()->with($exception)->andReturn($expected);

        $actual = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertSame($code, $actual->getStatusCode());
        $this->assertSame($expected, $actual->getContent());
        $this->assertSame('bar', $actual->headers->get('foo'));
    }

    public function provideWhoopsExceptions()
    {
        return $this->fixture('Exceptions/Handler')['safe'];
    }

    /**
     * @dataProvider provideLaravelExceptions
     * @covers ::render
     * @covers ::isSafeToWhoops
     */
    public function testRenderHandlesDoesNotPassLaravelExceptionsToWhoops($class)
    {
        // TODO: Still need to handle \Illuminate\Validation\ValidationException and
        // \Illuminate\Http\Exceptions\HttpResponseException, may need to skip using
        // the yml fixtures for this and create instances from the provider

        $whoops = $this->mockWhoops();
        $whoops->shouldNotReceive('handleException'); // Ensure whoops is not called

        $actual = $this->handler->render($this->request, new $class());

        // Ensure the response is returned from the parent
        $this->assertInstanceOf(Response::class, $actual);
    }

    public function provideLaravelExceptions()
    {
        return $this->fixture('Exceptions/Handler')['unsafe'];
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

    public function assertContainsMessage($expected, $content)
    {
        // If debugging is disabled, which shouldn't happen if unit tests just check it contains the generic message
        if (!config('app.debug', false)) {
            $expected = 'Whoops, looks like something went wrong.';
        }

        $this->assertContains($expected, $content);
    }

    private function mockWhoops()
    {
        // FIXME: Is this right? Because now the handlers are missing so we can't test HTML or json response?
        $whoops = m::mock(WhoopsInterface::class);

        $this->app->bind(Whoops::class, function () use (&$whoops) {
            return $whoops;
        });

        return $whoops;
    }
}
