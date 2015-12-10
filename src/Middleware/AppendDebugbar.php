<?php

namespace Recca0120\LaravelTracy\Middleware;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Recca0120\LaravelTracy\Helper;

class AppendDebugbar
{
    protected $exceptionHandler;

    public function __construct(ExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    public function handle($request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            $this->exceptionHandler->report($e);
            $response = $this->exceptionHandler->render($request, $e);
        }

        return Helper::appendDebugbar($request, $response);
    }
}
