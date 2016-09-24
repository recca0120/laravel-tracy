<?php

use Mockery as m;
use Recca0120\LaravelTracy\Middleware\Dispatch;

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

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $request = m::mock('Illuminate\Http\Request');
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
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $response->send();
    }

    public function test_handle_dispatch()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
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
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $response->send();
    }

    public function test_handle_next()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $debugbar = m::mock('Recca0120\LaravelTracy\Debugbar');
        $request = m::mock('Illuminate\Http\Request');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
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
