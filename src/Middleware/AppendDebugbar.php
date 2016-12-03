<?php

namespace Recca0120\LaravelTracy\Middleware;

use Recca0120\LaravelTracy\Debugbar;

class AppendDebugbar
{
    /**
     * $debugbar.
     *
     * @var \Recca0120\LaravelTracy\Debugbar
     */
    protected $debugbar;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param \Recca0120\LaravelTracy\Debugbar $debugbar
     */
    public function __construct(Debugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * handle.
     *
     * @method handle
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, $next)
    {
        return $this->debugbar->render($next($request));
    }
}
