<?php

use \Recca0120\LaravelTracy\Debugbar;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Symfony\Component\HttpFoundation\Response;

class AppendDebugbarTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_middleware()
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
        $middleware = new AppendDebugbar($debugbar);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $debugbar->shouldReceive('render')->with($response)->once()->andReturn($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($response, $middleware->handle($request, $next));
    }
}
