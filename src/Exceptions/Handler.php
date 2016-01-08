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
    protected $exceptionHandler;

    public function __construct(ExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    public function report(Exception $e)
    {
        $this->exceptionHandler->report($e);
    }

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
