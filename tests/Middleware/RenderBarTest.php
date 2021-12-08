<?php

namespace Recca0120\LaravelTracy\Tests\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\DebuggerManager;
use Recca0120\LaravelTracy\Events\BeforeBarRender;
use Recca0120\LaravelTracy\Middleware\RenderBar;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RenderBarTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testHandleAssets()
    {
        $renderBar = new RenderBar(m::spy(DebuggerManager::class), m::spy(Dispatcher::class));

        $request = m::spy(Request::class);

        $request->expects('has')->with('_tracy_bar')->andReturns(true);
        $request->expects('get')->with('_tracy_bar')->andReturns('foo');

        $request->expects('hasSession')->andReturns(true);
        $request->expects('session->reflash');

        $response = m::spy(Response::class);

        $next = function (Request $request) use ($response) {
            return $response;
        };

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleBinaryFileResponse()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $renderBar = new RenderBar($debuggerManager, m::spy(Dispatcher::class));

        $request = m::spy(Request::class);
        $response = m::spy(BinaryFileResponse::class);
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $request->expects('ajax')->andReturns(false);
        $debuggerManager->expects('dispatch');

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleStreamedResponse()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $renderBar = new RenderBar($debuggerManager, m::spy(Dispatcher::class));

        $request = m::spy(Request::class);
        $response = m::spy(StreamedResponse::class);
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $request->expects('ajax')->andReturns(false);
        $debuggerManager->expects('dispatch');

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleRedirectResponse()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $renderBar = new RenderBar($debuggerManager, m::spy(Dispatcher::class));

        $request = m::spy(Request::class);
        $response = m::spy(RedirectResponse::class);
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $request->expects('ajax')->andReturns(false);
        $debuggerManager->expects('dispatch');

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleElse()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $renderBar = new RenderBar($debuggerManager, m::spy(Dispatcher::class));

        $headers = m::spy('stdClass');
        $request = m::spy(Request::class);
        $response = m::spy(Response::class);
        $response->headers = $headers;
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $debuggerManager->expects('dispatch');
        $request->expects('ajax')->andReturns(false);
        $headers->expects('get')->with('Content-Type')->andReturns($contentType = '');
        $debuggerManager->expects('accepts')->andReturns($accepts = ['text/html']);
        $response->expects('getStatusCode')->andReturns(200);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleAjax()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $renderBar = new RenderBar($debuggerManager, $events = m::spy(Dispatcher::class));

        $request = m::spy(Request::class);
        $response = m::spy(Response::class);
        $response->headers = m::spy('stdClass');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $debuggerManager->expects('dispatch');
        $request->expects('ajax')->andReturns($ajax = true);

        $events->expects(method_exists($events, 'dispatch') ? 'dispatch' : 'fire')
            ->with(m::type(BeforeBarRender::class));

        $response->expects('getContent')->andReturns($content = 'foo');
        $debuggerManager->expects('shutdownHandler')->with($content, $ajax)->andReturns($content);
        $response->expects('setContent')->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleStatusCodeBiggerThan400()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $events = m::spy(Dispatcher::class);
        $renderBar = new RenderBar($debuggerManager, $events);

        $headers = m::spy('stdClass');
        $request = m::spy(Request::class);
        $response = m::spy(Response::class);
        $response->headers = $headers;
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $debuggerManager->expects('dispatch');
        $request->expects('ajax')->andReturns($ajax = false);

        $headers->expects('get')->with('Content-Type')->andReturns('');
        $debuggerManager->expects('accepts')->andReturns($accepts = ['text/html']);
        $response->expects('getStatusCode')->andReturns(400);

        $events->expects(method_exists($events, 'dispatch') ? 'dispatch' : 'fire')
            ->with(m::type(BeforeBarRender::class));

        $response->expects('getContent')->andReturns($content = 'foo');
        $debuggerManager->expects('shutdownHandler')->with($content, $ajax)->andReturns($content);
        $response->expects('setContent')->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleEmptyAccepts()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $events = m::spy(Dispatcher::class);
        $renderBar = new RenderBar($debuggerManager, $events);

        $headers = m::spy('stdClass');
        $request = m::spy(Request::class);
        $response = m::spy(Response::class);
        $response->headers = $headers;
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $debuggerManager->expects('dispatch');
        $request->expects('ajax')->andReturns($ajax = false);

        $headers->expects('get')->with('Content-Type')->andReturns($contentType = '');
        $debuggerManager->expects('accepts')->andReturns($accepts = []);
        $response->expects('getStatusCode')->andReturns(200);

        $events->expects(method_exists($events, 'dispatch') ? 'dispatch' : 'fire')
            ->with(m::type(BeforeBarRender::class));

        $response->expects('getContent')->andReturns($content = 'foo');
        $debuggerManager->expects('shutdownHandler')->with($content, $ajax)->andReturns($content);
        $response->expects('setContent')->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleAcceptContentType()
    {
        $debuggerManager = m::spy(DebuggerManager::class);
        $events = m::spy(Dispatcher::class);
        $renderBar = new RenderBar($debuggerManager, $events);

        $headers = m::spy('stdClass');
        $request = m::spy(Request::class);
        $response = m::spy(Response::class);
        $response->headers = $headers;
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->expects('has')->with('_tracy_bar')->andReturns(false);
        $debuggerManager->expects('dispatch');
        $request->expects('ajax')->andReturns($ajax = false);

        $headers->expects('get')->with('Content-Type')->andReturns($contentType = 'text/html');
        $debuggerManager->expects('accepts')->andReturns($accepts = ['text/html']);

        $events->allows(method_exists($events, 'dispatch') ? 'dispatch' : 'fire')
            ->with(m::type(BeforeBarRender::class));

        $response->expects('getContent')->andReturns($content = 'foo');
        $debuggerManager->expects('shutdownHandler')->with($content, $ajax)->andReturns($content);
        $response->expects('setContent')->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }
}
