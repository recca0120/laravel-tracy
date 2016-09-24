<?php

use Mockery as m;
use Recca0120\LaravelTracy\Debugbar;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;

class AppendDebugbarTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handle()
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

    // public function test_dispatch_assets() {
    //     /*
    //     |------------------------------------------------------------
    //     | Set
    //     |------------------------------------------------------------
    //     */
    //
    //     $debugbar = m::mock(Debugbar::class);
    //     $request = m::mock(Request::class);
    //     $response = m::mock(Response::class);
    //     $next = function ($request) use ($response) {
    //         return $response;
    //     };
    //     $middleware = new AppendDebugbar($debugbar);
    //
    //     /*
    //     |------------------------------------------------------------
    //     | Expectation
    //     |------------------------------------------------------------
    //     */
    //
    //     $debugbar
    //         ->shouldReceive('dispatchAssets')->once()->andReturn('testing');
    //
    //     /*
    //     |------------------------------------------------------------
    //     | Assertion
    //     |------------------------------------------------------------
    //     */
    //
    //     $this->expectOutputString('testing');
    //     $response = $middleware->handle($request, $next);
    //     $this->assertInstanceOf(StreamedResponse::class, $response);
    //     $response->send();
    // }
}
