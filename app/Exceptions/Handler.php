<?php

namespace App\Exceptions;

use Bugsnag\BugsnagLaravel\BugsnagExceptionHandler as ExceptionHandler;
use Exception;

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
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        return parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception                $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($this->isHttpException($exception)) {
            return $this->renderHttpException($exception);
        }

        if (config('app.debug')) {
            return $this->renderExceptionWithWhoops($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Render an exception using Whoops.
     *
     * @param  \Exception                $exception
     * @return \Illuminate\Http\Response
     */
    protected function renderExceptionWithWhoops(Exception $exception)
    {
        $json = new \Whoops\Handler\JsonResponseHandler;
        $json->onlyForAjaxRequests(true);

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->pushHandler($json);

        return new \Illuminate\Http\Response(
            $whoops->handleException($exception),
            $exception->getStatusCode(),
            $exception->getHeaders()
        );
    }
}
