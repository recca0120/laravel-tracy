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
                    $mimeType = 'text/css';
                    $content = $this->debugbar->dispatchAssets();
                    break;
                case 'js':
                    $mimeType = 'text/javascript';
                    $content = $this->debugbar->dispatchAssets();
                    break;
                default:
                    $mimeType = 'text/javascript';
                    $content = $this->debugbar->dispatch();
                    break;
            }

            return $this->sendStreamedResponse($content, $mimeType);
        }

        return $next($request);
    }

    /**
     * sendStreamedResponse.
     *
     * @method sendStreamedResponse
     *
     * @param string $content
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function sendStreamedResponse($content, $mimeType)
    {
        $headers = [
            'content-type' => $mimeType.'; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen($content),
        ];

        return $this->responseFactory->stream(function () use ($content) {
            echo $content;
        }, 200, $headers);
    }
}
