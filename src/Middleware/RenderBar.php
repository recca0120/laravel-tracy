<?php

namespace Recca0120\LaravelTracy\Middleware;

use Illuminate\Http\Request;
use Illuminate\Contracts\Events\Dispatcher;
use Recca0120\LaravelTracy\DebuggerManager;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Recca0120\LaravelTracy\Events\BeforeBarRender;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RenderBar
{
    /**
     * __construct.
     *
     * @method __construct
     *
     * @param \Recca0120\LaravelTracy\DebuggerManager $debuggerManager
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     */
    public function __construct(DebuggerManager $debuggerManager, Dispatcher $events, ResponseFactory $responseFactory)
    {
        $this->debuggerManager = $debuggerManager;
        $this->events = $events;
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
        if ($request->has('_tracy_bar') === true) {
            list($headers, $content) = $this->debuggerManager->dispatchAssets($request->get('_tracy_bar'));

            return $this->responseFactory->make($content, 200, $headers);
        }

        $this->debuggerManager->dispatch();

        $response = $next($request);

        if ($this->shouldNotRenderBar($response, $request) === true) {
            return $response;
        }

        $this->events->fire(new BeforeBarRender($request, $response));

        $content = $response->getContent();

        $response->setContent(
            $this->debuggerManager->shutdownHandler($content)
        );

        return $response;
    }

    protected function shouldNotRenderBar(Response $response, Request $request)
    {
        if ($this->debuggerManager->showBar() === false ||
            $response instanceof BinaryFileResponse ||
            $response instanceof StreamedResponse ||
            $response instanceof RedirectResponse
        ) {
            return true;
        }

        if ($request->ajax() === true) {
            return false;
        }

        $contentType = strtolower($response->headers->get('Content-Type'));
        $accepts = $this->debuggerManager->accepts();
        if (empty($contentType) === true && $response->getStatusCode() >= 400 ||
            count($accepts) === 0
        ) {
            return false;
        }

        foreach ($accepts as $accept) {
            if (strpos($contentType, $accept) !== false) {
                return false;
            }
        }

        return true;
    }
}
