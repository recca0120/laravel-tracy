<?php

use Recca0120\LaravelTracy\Debugbar;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DispatchTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle_dispatch_assets()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock(Debugbar::class);
        $request = m::mock(Request::class);
        $middleware = new Dispatch($debugbar);
        $next = function () {
        };

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar->shouldReceive('dispatchAssets')->once()->andReturn('testing assets');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->expectOutputString('testing assets');
        $response = $middleware->handle($request, $next);
        $this->assertInstanceOf(StreamedResponse::class, $response);
        $response->send();
    }

    public function test_handle_dispatch()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock(Debugbar::class);
        $request = m::mock(Request::class);
        $response = m::mock(Response::class);
        $next = function ($request) use ($response) {
            return $response;
        };
        $middleware = new Dispatch($debugbar);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar
            ->shouldReceive('dispatchAssets')->once()->andReturn('')
            ->shouldReceive('dispatch')->once()->andReturn('testing dispatch');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->expectOutputString('testing dispatch');
        $response = $middleware->handle($request, $next);
        $this->assertInstanceOf(StreamedResponse::class, $response);
        $response->send();
    }

    public function test_handle_next()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock(Debugbar::class);
        $request = m::mock(Request::class);
        $response = m::mock(Response::class);
        $next = function ($request) use ($response) {
            return $response;
        };
        $middleware = new Dispatch($debugbar);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar
            ->shouldReceive('dispatchAssets')->once()->andReturn('')
            ->shouldReceive('dispatch')->once()->andReturn('');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($response, $middleware->handle($request, $next));
    }
}
