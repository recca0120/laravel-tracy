<?php

use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Recca0120\LaravelTracy\Tracy;
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

        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $response = m::mock(Response::class);
        $next = function ($request) use ($response) {
            return $response;
        };
        $middleware = new AppendDebugbar($tracy);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy
            ->shouldReceive('startBuffering')->once()
            ->shouldReceive('renderResponse')->with($response)->once()->andReturn($response)
            ->shouldReceive('stopBuffering')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($response, $middleware->handle($request, $next));
    }
}
