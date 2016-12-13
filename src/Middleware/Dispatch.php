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
        $response = $next($request);

        $this->storeWrapper->start();

        return $request->has('_tracy_bar') === true ?
             $this->dispatchAssets($request->get('_tracy_bar'), $response) :
             $this->dispatchContent($response);
    }

    /**
     * dispatchAssets.
     *
     * @param  string $assets
     * @param  \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function dispatchAssets($assets, $response)
    {
        $this->storeWrapper->restore();

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

        return $response = $this->responseFactory->make($content, 200, array_merge($headers, [
            'content-length' => strlen($content),
        ]));
    }

    /**
     * dispatchContent.
     *
     * @method dispatchContent
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function dispatchContent($response)
    {
        if ($response->getStatusCode() === 200) {
            $this->debugbar->dispatchContent();
        }

        $response = $this->debugbar->render($response);

        $this->storeWrapper->store();

        return $response;
    }
}
