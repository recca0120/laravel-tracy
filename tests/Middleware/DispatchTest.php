<?php

namespace Recca0120\LaravelTracy\Tests\Middleware;

use Mockery as m;
use Recca0120\LaravelTracy\Middleware\Dispatch;

class DispatchTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testHandleCss()
    {
        $dispatch = new Dispatch(
            $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar'),
            $storeWrapper = m::mock('Recca0120\LaravelTracy\Session\StoreWrapper'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(true);
        $request->shouldReceive('get')->once()->with('_tracy_bar')->andReturn('css');
        $debugbar->shouldReceive('dispatchAssets')->once()->andReturn($content = 'foo');
        $responseFactory->shouldReceive('make')->once()->with($content, 200, [
            'content-type' => 'text/css; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen($content),
        ])->andReturn($content);
        $this->assertSame($content, $dispatch->handle(
            $request,
            $next = function () {
            }
        ));
    }

    public function testHandleJs()
    {
        $dispatch = new Dispatch(
            $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar'),
            $storeWrapper = m::mock('Recca0120\LaravelTracy\Session\StoreWrapper'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(true);
        $request->shouldReceive('get')->once()->with('_tracy_bar')->andReturn('js');
        $debugbar->shouldReceive('dispatchAssets')->once()->andReturn($content = 'foo');
        $responseFactory->shouldReceive('make')->once()->with($content, 200, [
            'content-type' => 'text/javascript; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen($content),
        ])->andReturn($content);
        $this->assertSame($content, $dispatch->handle(
            $request,
            $next = function () {
            }
        ));
    }

    public function testHandleAssets()
    {
        $dispatch = new Dispatch(
            $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar'),
            $storeWrapper = m::mock('Recca0120\LaravelTracy\Session\StoreWrapper'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(true);
        $request->shouldReceive('get')->once()->with('_tracy_bar')->andReturn('assets');
        $debugbar->shouldReceive('dispatchAssets')->once()->andReturn($content = 'foo');
        $responseFactory->shouldReceive('make')->once()->with($content, 200, [
            'content-type' => 'text/javascript; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen($content),
        ])->andReturn($content);
        $this->assertSame($content, $dispatch->handle(
            $request,
            $next = function () {
            }
        ));
    }

    public function testHandleContent()
    {
        $dispatch = new Dispatch(
            $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar'),
            $storeWrapper = m::mock('Recca0120\LaravelTracy\Session\StoreWrapper'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(true);
        $request->shouldReceive('get')->once()->with('_tracy_bar')->andReturn(
            $contentId = 'content.'.uniqid()
        );
        $storeWrapper->shouldReceive('start')->once();
        $storeWrapper->shouldReceive('restore')->once();
        $debugbar->shouldReceive('dispatchContent')->once()->andReturn($content = 'foo');
        $storeWrapper->shouldReceive('clean')->once()->with($contentId)->andReturnSelf();
        $storeWrapper->shouldReceive('close')->once();
        $responseFactory->shouldReceive('make')->once()->with($content, 200, [
            'content-type' => 'text/javascript; charset=utf-8',
            'content-length' => strlen($content),
        ])->andReturn($content);
        $this->assertSame($content, $dispatch->handle(
            $request,
            $next = function () {
            }
        ));
    }

    public function testHandleWithOutTracy()
    {
        $dispatch = new Dispatch(
            $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar'),
            $storeWrapper = m::mock('Recca0120\LaravelTracy\Session\StoreWrapper'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $next = function ($httpRequest) use ($request, $response) {
            $this->assertSame($request, $httpRequest);

            return $response;
        };
        $request->shouldReceive('has')->once()->with('_tracy_bar')->andReturn(false);
        $storeWrapper->shouldReceive('start')->once();
        $debugbar->shouldReceive('dispatchContent')->once();
        $debugbar->shouldReceive('render')->once()->with($response)->andReturn($response);
        $storeWrapper->shouldReceive('store')->once()->andReturnSelf();
        $storeWrapper->shouldReceive('close')->once();
        $this->assertSame($response, $dispatch->handle(
           $request,
           $next
       ));
    }
}
