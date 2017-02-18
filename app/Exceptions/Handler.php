<?php

namespace REBELinBLUE\Deployer\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Run as Whoops;

/**
 * Exception handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * Exceptions which should not be handled by whoops.
     *
     * @var array
     */
    protected $skipWhoops = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($this->isHttpException($exception)) {
            return $this->renderHttpException($exception);
        }

        // Use whoops if it is bound to the container and the exception is safe to pass to whoops
        if ($this->container->bound(Whoops::class) && $this->isSafeToWhoops($exception)) {
            return $this->renderExceptionWithWhoops($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Render an exception using Whoops.
     *
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function renderExceptionWithWhoops(Exception $exception)
    {
        /** @var Whoops $whoops */
        $whoops = $this->container->make(Whoops::class);

        $statusCode = 500;
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        $headers = [];
        if (method_exists($exception, 'getHeaders')) {
            $headers = $exception->getHeaders();
        }

        return new Response(
            $whoops->handleException($exception),
            $statusCode,
            $headers
        );
    }

    /**
     * Don't allow the exceptions which laravel handles specially to be converted to Whoops.
     *
     * @param \Exception $exception
     *
     * @return bool
     */
    protected function isSafeToWhoops(Exception $exception)
    {
        return is_null(collect($this->skipWhoops)->first(function ($type) use ($exception) {
            return $exception instanceof $type;
        }));
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('auth.login'));
    }
}
