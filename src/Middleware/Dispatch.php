<?php

namespace Recca0120\LaravelTracy\Middleware;

use Illuminate\Contracts\Routing\ResponseFactory;
use Recca0120\LaravelTracy\Debugbar;

class Dispatch
{
    /**
     * $debugbar.
     *
     * @var \Recca0120\LaravelTracy\Debugbar
     */
    protected $debugbar;

    /**
     * $responseFactory.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param \Recca0120\LaravelTracy\Debugbar              $debugbar
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     */
    public function __construct(Debugbar $debugbar, ResponseFactory $responseFactory)
    {
        $this->debugbar = $debugbar;
        $this->responseFactory = $responseFactory;
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
        if ($request->has('_tracy_bar') === true) {
            $tracyBar = $request->get('_tracy_bar');

            switch ($tracyBar) {
                case 'css':
                    $content = $this->debugbar->dispatchAssets();
                    $headers = [
                        'content-type' => 'text/css; charset=utf-8',
                        'cache-control' => 'max-age=86400',
                    ];
                    break;
                case 'js':
                case 'assets':
                    $content = $this->debugbar->dispatchAssets();
                    $headers = [
                        'content-type' => 'text/javascript; charset=utf-8',
                        'cache-control' => 'max-age=86400',
                    ];
                    break;
                default:
                    $content = $this->debugbar->dispatch();
                    $headers = [
                        'content-type' => 'text/javascript; charset=utf-8',
                    ];
                    break;
            }

            return $this->sendStreamedResponse($content, array_merge($headers, [
                'content-length' => strlen($content),
            ]));
        }
        $this->debugbar->dispatch();

        return $next($request);
    }

    /**
     * sendStreamedResponse.
     *
     * @method sendStreamedResponse
     *
     * @param string $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendStreamedResponse($content, $headers)
    {
        return $this->responseFactory->make($content, 200, $headers);
    }
}
