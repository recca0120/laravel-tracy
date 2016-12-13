<?php

namespace Recca0120\LaravelTracy\Middleware;

use Recca0120\LaravelTracy\Debugbar;
use Recca0120\LaravelTracy\Session\StoreWrapper;
use Illuminate\Contracts\Routing\ResponseFactory;

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
     * @param \Recca0120\LaravelTracy\StoreWrapper          $storeWrapper
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     */
    public function __construct(Debugbar $debugbar, StoreWrapper $storeWrapper, ResponseFactory $responseFactory)
    {
        $this->debugbar = $debugbar;
        $this->storeWrapper = $storeWrapper;
        $this->responseFactory = $responseFactory;
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
        $this->storeWrapper->start();

        return $request->has('_tracy_bar') === true ?
             $this->dispatchAssets($request, $next) :
             $this->dispatchContent($request, $next);
    }

    /**
     * dispatchAssets.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function dispatchAssets($request, $next)
    {
        $this->storeWrapper->restore();
        $assets = $request->get('_tracy_bar');

        switch ($assets) {
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
                $content = $this->debugbar->dispatchContent();
                $headers = [
                    'content-type' => 'text/javascript; charset=utf-8',
                ];

                $this->storeWrapper->clean($assets);
                break;
        }

        return $this->responseFactory->make($content, 200, array_merge($headers, [
            'content-length' => strlen($content),
        ]));
    }

    /**
     * dispatchContent.
     *
     * @method dispatchContent
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function dispatchContent($request, $next)
    {
        $response = $next($request);
        $this->debugbar->dispatchContent();
        $response = $this->debugbar->render($response);
        $this->storeWrapper->store();

        return $response;
    }
}
