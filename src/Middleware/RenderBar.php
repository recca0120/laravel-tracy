<?php

namespace Recca0120\LaravelTracy\Middleware;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Contracts\Events\Dispatcher;
use Recca0120\LaravelTracy\DebuggerManager;
use Symfony\Component\HttpFoundation\Response;
use Recca0120\LaravelTracy\Events\BeforeBarRender;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RenderBar
{
    /**
     * $debuggerManager.
     *
     * @var \Recca0120\LaravelTracy\DebuggerManager
     */
    protected $debuggerManager;

    /**
     * $events.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * __construct.
     *
     *
     * @param \Recca0120\LaravelTracy\DebuggerManager $debuggerManager
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(DebuggerManager $debuggerManager, Dispatcher $events)
    {
        $this->debuggerManager = $debuggerManager;
        $this->events = $events;
    }

    /**
     * handle.
     *
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function handle($request, $next)
    {
        if ($request->has('_tracy_bar') === true) {
            return $next($request->duplicate(
                null, null, null, null, null, Arr::except(array_merge($request->server(), [
                    'REQUEST_URI' => '/_tracy/'.$request->get('_tracy_bar')
                ]), ['REDIRECT_URL', 'REDIRECT_QUERY_STRING'])
            ));
        }

        $this->debuggerManager->dispatch();

        $response = $next($request);

        if ($this->shouldNotRenderBar($response, $request) === true) {
            return $response;
        }

        $this->events->fire(new BeforeBarRender($request, $response));

        $response->setContent(
            $this->debuggerManager->shutdownHandler(
                $response->getContent()
            )
        );

        return $response;
    }

    /**
     * shouldNotRenderBar.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
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
        if ((empty($contentType) === true && $response->getStatusCode() >= 400) ||
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
