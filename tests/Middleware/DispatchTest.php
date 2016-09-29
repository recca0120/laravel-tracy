<?php

use Mockery as m;
use Recca0120\LaravelTracy\Middleware\Dispatch;

class DispatchTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle_dispatch_css()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::mock('Illuminate\Http\Request');
        $middleware = new Dispatch($debugbar, $responseFactory);
        $next = function () {
        };

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar->shouldReceive('dispatchAssets')->once()->andReturn('testing tracy css');

        $request
            ->shouldReceive('has')->with('_tracy_bar')->once()->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar')->once()->andReturn('css');

        $responseFactory->shouldReceive('make')->once()->andReturnUsing(function ($content) {
            return $content;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame('testing tracy css', $middleware->handle($request, $next));
    }

    public function test_handle_dispatch_js()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::mock('Illuminate\Http\Request');
        $middleware = new Dispatch($debugbar, $responseFactory);
        $next = function () {
        };

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar->shouldReceive('dispatchAssets')->once()->andReturn('testing tracy js');

        $request
            ->shouldReceive('has')->with('_tracy_bar')->once()->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar')->once()->andReturn('js');

        $responseFactory->shouldReceive('make')->once()->andReturnUsing(function ($content) {
            return $content;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame('testing tracy js', $middleware->handle($request, $next));
    }

    public function test_handle_dispatch()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $next = function ($request) use ($response) {
            return $response;
        };
        $middleware = new Dispatch($debugbar, $responseFactory);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar->shouldReceive('dispatch')->once()->andReturn('testing dispatch');

        $request
            ->shouldReceive('has')->with('_tracy_bar')->once()->andReturn(true)
            ->shouldReceive('get')->with('_tracy_bar')->once()->andReturn('content.abcde');

        $responseFactory->shouldReceive('make')->once()->andReturnUsing(function ($content) {
            return $content;
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame('testing dispatch', $middleware->handle($request, $next));
    }

    public function test_handle_next()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $next = function ($request) use ($response) {
            return $response;
        };
        $middleware = new Dispatch($debugbar, $responseFactory);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar->shouldReceive('dispatch')->once();

        $request->shouldReceive('has')->with('_tracy_bar')->once()->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($response, $middleware->handle($request, $next));
    }
}
