<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Recca0120\LaravelTracy\Tracy;
use Symfony\Component\Debug\ExceptionHandler as SymfonyDisplayer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
        'Illuminate\Database\Eloquent\ModelNotFoundException',
    ];

    /*
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */

    public function render($request, Exception $e)
    {
        if (method_exists($this, 'isUnauthorizedException')) {
            if ($this->isUnauthorizedException($e)) {
                $e = new HttpException(403, $e->getMessage());
            }
        }

        if ($this->isHttpException($e)) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        } else {
            return $this->toIlluminateResponse($this->convertExceptionToResponse($e), $e);
        }
    }

    protected function toIlluminateResponse($response, Exception $e)
    {
        $response = response($response->getContent(), $response->getStatusCode(), $response->headers->all());
        $response->exception = $e;

        return $response;
    }

    protected function convertExceptionToResponse(Exception $e)
    {
        $debug = config('app.debug');
        if ($debug === false) {
            return (new SymfonyDisplayer(config('app.debug')))->createResponse($e);
        } else {
            return Tracy::getHttpResponse(Tracy::getBlueScreen($e), $e);
        }
    }
}
