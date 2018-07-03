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
            ? $this->keepFlashSession($request, $next)
            : $this->render($request, $next);
    }

    /**
     * keepFlashSession.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function keepFlashSession($request, $next)
    {
        $type = $request->get('_tracy_bar');
        if ($request->hasSession() === true && in_array($type, ['js', 'css'], true) === false) {
            $request->session()->reflash();
        }

        return $next($request);
    }

    /**
     * render.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function render($request, $next)
    {
        $this->debuggerManager->dispatch();

        $response = $next($request);

        $ajax = $request->ajax();

        if ($this->reject($response, $request, $ajax) === true) {
            return $response;
        }

        $this->events->fire(new BeforeBarRender($request, $response));

        $response->setContent(
            $this->debuggerManager->shutdownHandler(
                $response->getContent(), $ajax
            )
        );

        return $response;
    }

    /**
     * reject.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Illuminate\Http\Request $request
     * @param bool $ajax
     *
     * @return bool
     */
    protected function reject(Response $response, Request $request, $ajax)
    {
        if (
            $response instanceof BinaryFileResponse ||
            $response instanceof StreamedResponse ||
            $response instanceof RedirectResponse
        ) {
            return true;
        }

        if ($ajax === true) {
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
