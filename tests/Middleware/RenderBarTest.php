<?php

namespace Recca0120\LaravelTracy\Tests\Middleware;

use Mockery as m;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Middleware\RenderBar;

class RenderBarTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandleAssets()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');

        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(true);
        $request->shouldReceive('get')->once()->with('_tracy_bar')->andReturn($tracyBar = 'assets');

        $request->shouldReceive('server')->once()->andReturn(
            $server = [
                'foo' => 'bar',
                'REDIRECT_URL' => 'foo',
                'REDIRECT_QUERY_STRING' => 'bar',
            ]
        );
        $request->shouldReceive('duplicate')->once()->with(
            null, null, null, null, null, [
                'foo' => 'bar',
                'REQUEST_URI' => '/_tracy/'.$tracyBar,
            ]
        )->andReturnSelf();

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleShowBarIsFalse()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(false);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleBinaryFileResponse()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\BinaryFileResponse');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleStreamedResponse()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\StreamedResponse');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleRedirectResponse()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleElse()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->headers = $headers = m::mock('stdClass');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn($contentType = '');
        $debuggerManager->shouldReceive('accepts')->once()->andReturn($accepts = ['text/html']);
        $response->shouldReceive('getStatusCode')->once()->andReturn(200);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleAjax()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->headers = $headers = m::mock('stdClass');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(true);

        $events->shouldReceive('fire')->once()->with(m::type('Recca0120\LaravelTracy\Events\BeforeBarRender'));

        $response->shouldReceive('getContent')->once()->andReturn($content = 'foo');
        $debuggerManager->shouldReceive('shutdownHandler')->once()->with($content)->andReturn($content);
        $response->shouldReceive('setContent')->once()->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleStatusCodeBiggerThan400()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->headers = $headers = m::mock('stdClass');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn($contentType = '');
        $debuggerManager->shouldReceive('accepts')->once()->andReturn($accepts = ['text/html']);
        $response->shouldReceive('getStatusCode')->once()->andReturn(400);

        $events->shouldReceive('fire')->once()->with(m::type('Recca0120\LaravelTracy\Events\BeforeBarRender'));

        $response->shouldReceive('getContent')->once()->andReturn($content = 'foo');
        $debuggerManager->shouldReceive('shutdownHandler')->once()->with($content)->andReturn($content);
        $response->shouldReceive('setContent')->once()->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleEmptyAccepts()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->headers = $headers = m::mock('stdClass');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn($contentType = '');
        $debuggerManager->shouldReceive('accepts')->once()->andReturn($accepts = []);
        $response->shouldReceive('getStatusCode')->once()->andReturn(200);

        $events->shouldReceive('fire')->once()->with(m::type('Recca0120\LaravelTracy\Events\BeforeBarRender'));

        $response->shouldReceive('getContent')->once()->andReturn($content = 'foo');
        $debuggerManager->shouldReceive('shutdownHandler')->once()->with($content)->andReturn($content);
        $response->shouldReceive('setContent')->once()->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }

    public function testHandleAcceptContentType()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher')
        );

        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->headers = $headers = m::mock('stdClass');
        $next = function (Request $request) use ($response) {
            return $response;
        };

        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $debuggerManager->shouldReceive('dispatch')->once();
        $debuggerManager->shouldReceive('showBar')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn($contentType = 'text/html');
        $debuggerManager->shouldReceive('accepts')->once()->andReturn($accepts = ['text/html']);

        $events->shouldReceive('fire')->once()->with(m::type('Recca0120\LaravelTracy\Events\BeforeBarRender'));

        $response->shouldReceive('getContent')->once()->andReturn($content = 'foo');
        $debuggerManager->shouldReceive('shutdownHandler')->once()->with($content)->andReturn($content);
        $response->shouldReceive('setContent')->once()->with($content);

        $this->assertSame($response, $renderBar->handle($request, $next));
    }
}
