<?php

namespace Recca0120\LaravelTracy\Middleware;

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
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, $next)
    {
        return $request->has('_tracy_bar') === true
            ? $this->renderBar($request, $next)
            : $this->appendBar($request, $next);
    }

    /**
     * appendBar.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderBar($request, $next)
    {
        $response = $next($request);
        $type = $request->get('_tracy_bar');
        if ($request->hasSession() === true && in_array($type, ['js', 'css'], true) === false) {
            $request->session()->reflash();
        }

        return $response;
    }

    /**
     * appendBar.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function appendBar($request, $next)
    {
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
