<?php

namespace Recca0120\LaravelTracy\Middleware;

use Recca0120\LaravelTracy\Debugbar;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dispatch
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
     * @param \Closure $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, $next)
    {
        $dispatchAssets = $this->debugbar->dispatchAssets();
        if (empty($dispatchAssets) === false) {
            return $this->streamedResponse($dispatchAssets);
        }

        $dispatch = $this->debugbar->dispatch();
        if (empty($dispatch) === false) {
            return $this->streamedResponse($dispatch);
        }

        return $next($request);
    }

    protected function streamedResponse($content) {
        return new StreamedResponse(function () use ($content) {
            echo $content;
        });
    }
}
