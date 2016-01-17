<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler as BaseHandler;
use Recca0120\LaravelTracy\Debugger;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends BaseHandler
{
    /**
     * exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * construct.
     *
     * @param \Illuminate\Contracts\Debug\ExceptionHandler $exceptionHandler
     */
    public function __construct(ExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * report.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        $this->exceptionHandler->report($e);
    }

    /**
     * render.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (method_exists($this, 'toIlluminateResponse') === true) {
            return parent::render($request, $e);
        }

        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", [], $status);
            }
        }

        return $this->convertExceptionToResponse($e);
    }

    /**
     * response.
     *
     * @param  Exception $e
     * @return \Illuminate\Http\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        // $debug = config('app.debug');
        // if ($debug === false) {
        //     return (new SymfonyDisplayer(config('app.debug')))->createResponse($e);
        // }
        $statusCode = 500;
        $headers = [];

        if (($e instanceof HttpException) === true) {
            $statusCode = $e->getStatusCode();
            $headers = $e->getHeaders();
        }

        return response(Debugger::getBlueScreen($e), $statusCode, $headers);
    }
}
