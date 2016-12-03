<?php

use Mockery as m;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;

class AppendDebugbarTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_append_debug_bar_handler()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $debugBar = m::spy('Recca0120\LaravelTracy\Debugbar');
        $request = m::spy('Illuminate\Http\Request');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $next = function($request) use ($response) {
            $response->setRequest($request);

            return $response;
        };


        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $debugBar->shouldReceive('render')->with($response)->andReturn('foo');

        $appendDebugbar = new AppendDebugbar($debugBar);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo', $appendDebugbar->handle($request, $next));
        $response->shouldHaveReceived('setRequest')->with($request)->once();
    }
}
