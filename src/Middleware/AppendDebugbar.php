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

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            $this->exceptionHandler->report($e);
            $response = $this->exceptionHandler->render($request, $e);
        }
        $response = Helper::appendDebugbar($request, $response);

        return $response;
    }
}
