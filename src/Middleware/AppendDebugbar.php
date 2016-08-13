<?php

namespace Recca0120\LaravelTracy\Middleware;

use Recca0120\LaravelTracy\Tracy;

class AppendDebugbar
{
    /**
     * $tracy.
     *
     * @var \Recca0120\LaravelTracy\Tracy
     */
    protected $tracy;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param \Recca0120\LaravelTracy\Tracy $tracy
     */
    public function __construct(Tracy $tracy)
    {
        $this->tracy = $tracy;
    }

    /**
     * handle.
     *
     * @method handle
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, $next)
    {
        $this->tracy->startBuffering();
        $response = $this->tracy->renderResponse($next($request));
        $this->tracy->stopBuffering();

        return $response;
    }
}
