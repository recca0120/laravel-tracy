<?php

namespace Recca0120\LaravelTracy\Middleware;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Recca0120\LaravelTracy\Helper;

class Tracy
{
    /**
     * The Laravel Application.
     *
     * @var Application
     */
    protected $app;

    /**
     * The Exception Handler.
     *
     * @var ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * Create a new middleware instance.
     *
     * @param Application      $app
     * @param ExceptionHandler $exceptionHandler
     */
    public function __construct(Application $app, ExceptionHandler $exceptionHandler)
    {
        $this->app = $app;
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
        $response = Helper::appendDebuggerInfo($request, $response);

        return $response;
    }
}
