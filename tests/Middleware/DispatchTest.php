<?php

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Debugbar;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Symfony\Component\HttpFoundation\Response;

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

        $debugbar = m::mock(Debugbar::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $request = m::mock(Request::class);
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

        $responseFactory->shouldReceive('stream')->once()->andReturnUsing(function ($closure) {
            $closure();
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->expectOutputString('testing tracy css');
        $response = $middleware->handle($request, $next);
    }

    public function test_handle_dispatch_js()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock(Debugbar::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $request = m::mock(Request::class);
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

        $responseFactory->shouldReceive('stream')->once()->andReturnUsing(function ($closure) {
            $closure();
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->expectOutputString('testing tracy js');
        $response = $middleware->handle($request, $next);
    }

    public function test_handle_dispatch()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock(Debugbar::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $request = m::mock(Request::class);
        $response = m::mock(Response::class);
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

        $responseFactory->shouldReceive('stream')->once()->andReturnUsing(function ($closure) {
            $closure();
        });

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->expectOutputString('testing dispatch');
        $response = $middleware->handle($request, $next);
    }

    public function test_handle_next()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock(Debugbar::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $request = m::mock(Request::class);
        $response = m::mock(Response::class);
        $next = function ($request) use ($response) {
            return $response;
        };
        $middleware = new Dispatch($debugbar, $responseFactory);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $request->shouldReceive('has')->with('_tracy_bar')->once()->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($response, $middleware->handle($request, $next));
    }
}
