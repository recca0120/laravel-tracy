<?php

namespace Recca0120\LaravelTracy\Middleware;

use Recca0120\LaravelTracy\Debugbar;
use Recca0120\LaravelTracy\StoreWrapper;
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

        return $response = $request->has('_tracy_bar') === true ?
            $this->dispatchAssets($request->get('_tracy_bar')) :
            $this->dispatchContent($request, $next);
    }

    /**
     * dispatchAssets.
     *
     * @param  string $assets
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function dispatchAssets($assets)
    {
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
                break;
        }

        return $this->sendStreamedResponse($content, array_merge($headers, [
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
        $this->debugbar->dispatchContent();
        $response = $this->debugbar->render($next($request));
        $this->storeWrapper->store();

        return $response;
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
