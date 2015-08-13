<?php namespace Recca0120\LaravelTracy\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Recca0120\LaravelTracy\LaravelTracy;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $e)
    {
        return LaravelTracy::handleException($request, $e);
    }
}
