<?php

use Mockery as m;
use Recca0120\LaravelTracy\Middleware\Dispatch;

class DispatchTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_dispatch_css()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $debugbar = m::spy('Recca0120\LaravelTracy\Debugbar');
        $storeWrapper = m::spy('Recca0120\LaravelTracy\Session\StoreWrapper');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::spy('Illuminate\Http\Request');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $next = function () use ($response) {
            return $response;
        };

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('has')->with('_tracy_bar')->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar')->andReturn('css');

        $debugbar
            ->shouldreceive('dispatchAssets')->andReturn('foo.content');

        $responseFactory
            ->shouldReceive('make')->andReturn('foo.response');

        $dispatch = new Dispatch($debugbar, $storeWrapper, $responseFactory);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo.response', $dispatch->handle($request, $next));

        $storeWrapper->shouldHaveReceived('start')->once();
        $request->shouldHaveReceived('has')->with('_tracy_bar')->once();
        $request->shouldHaveReceived('get')->with('_tracy_bar')->once();
        $storeWrapper->shouldHaveReceived('restore')->once();
        $debugbar->shouldHaveReceived('dispatchAssets')->once();
        $responseFactory->shouldHaveReceived('make')->with('foo.content', 200, [
            'content-type' => 'text/css; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen('foo.content'),
        ]);
    }

    public function test_dispatch_js()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $debugbar = m::spy('Recca0120\LaravelTracy\Debugbar');
        $storeWrapper = m::spy('Recca0120\LaravelTracy\Session\StoreWrapper');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::spy('Illuminate\Http\Request');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $next = function () use ($response) {
            return $response;
        };

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('has')->with('_tracy_bar')->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar')->andReturn('js');

        $debugbar
            ->shouldreceive('dispatchAssets')->andReturn('foo.content');

        $responseFactory
            ->shouldReceive('make')->andReturn('foo.response');

        $dispatch = new Dispatch($debugbar, $storeWrapper, $responseFactory);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo.response', $dispatch->handle($request, $next));

        $storeWrapper->shouldHaveReceived('start')->once();
        $request->shouldHaveReceived('has')->with('_tracy_bar')->once();
        $request->shouldHaveReceived('get')->with('_tracy_bar')->once();
        $storeWrapper->shouldHaveReceived('restore')->once();
        $debugbar->shouldHaveReceived('dispatchAssets')->once();
        $responseFactory->shouldHaveReceived('make')->with('foo.content', 200, [
            'content-type' => 'text/javascript; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen('foo.content'),
        ]);
    }

    public function test_dispatch_assets()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $debugbar = m::spy('Recca0120\LaravelTracy\Debugbar');
        $storeWrapper = m::spy('Recca0120\LaravelTracy\Session\StoreWrapper');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::spy('Illuminate\Http\Request');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $next = function () use ($response) {
            return $response;
        };

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('has')->with('_tracy_bar')->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar')->andReturn('assets');

        $debugbar
            ->shouldreceive('dispatchAssets')->andReturn('foo.content');

        $responseFactory
            ->shouldReceive('make')->andReturn('foo.response');

        $dispatch = new Dispatch($debugbar, $storeWrapper, $responseFactory);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo.response', $dispatch->handle($request, $next));

        $storeWrapper->shouldHaveReceived('start')->once();
        $request->shouldHaveReceived('has')->with('_tracy_bar')->once();
        $request->shouldHaveReceived('get')->with('_tracy_bar')->once();
        $storeWrapper->shouldHaveReceived('restore')->once();
        $debugbar->shouldHaveReceived('dispatchAssets')->once();
        $responseFactory->shouldHaveReceived('make')->with('foo.content', 200, [
            'content-type' => 'text/javascript; charset=utf-8',
            'cache-control' => 'max-age=86400',
            'content-length' => strlen('foo.content'),
        ]);
    }

    public function test_dispatch_asset_content()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $debugbar = m::spy('Recca0120\LaravelTracy\Debugbar');
        $storeWrapper = m::spy('Recca0120\LaravelTracy\Session\StoreWrapper');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::spy('Illuminate\Http\Request');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $next = function () use ($response) {
            return $response;
        };

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('has')->with('_tracy_bar')->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar');

        $debugbar
            ->shouldreceive('dispatchContent')->andReturn('foo.content');

        $responseFactory
            ->shouldReceive('make')->andReturn('foo.response');

        $dispatch = new Dispatch($debugbar, $storeWrapper, $responseFactory);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo.response', $dispatch->handle($request, $next));

        $storeWrapper->shouldHaveReceived('start')->once();
        $request->shouldHaveReceived('has')->with('_tracy_bar')->once();
        $request->shouldHaveReceived('get')->with('_tracy_bar')->once();
        $storeWrapper->shouldHaveReceived('restore')->once();
        $debugbar->shouldHaveReceived('dispatchContent')->once();
        $responseFactory->shouldHaveReceived('make')->with('foo.content', 200, [
            'content-type' => 'text/javascript; charset=utf-8',
            'content-length' => strlen('foo.content'),
        ]);
    }

    public function test_dispatch_content()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $debugbar = m::spy('Recca0120\LaravelTracy\Debugbar');
        $storeWrapper = m::spy('Recca0120\LaravelTracy\Session\StoreWrapper');
        $responseFactory = m::spy('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::spy('Illuminate\Http\Request');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $next = function () use ($response) {
            return $response;
        };

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('has')->with('_tracy_bar')->andReturn(false);

        $debugbar
            ->shouldReceive('render')->with($response)->andReturnUsing(function ($response) {
                return $response;
            });

        $dispatch = new Dispatch($debugbar, $storeWrapper, $responseFactory);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $dispatch->handle($request, $next));

        $storeWrapper->shouldHaveReceived('start')->once();
        $request->shouldHaveReceived('has')->with('_tracy_bar')->once();
        $debugbar->shouldHaveReceived('dispatchContent')->once();
        $debugbar->shouldHaveReceived('render')->with($response)->once();
        $storeWrapper->shouldHaveReceived('store')->once();
    }
}
