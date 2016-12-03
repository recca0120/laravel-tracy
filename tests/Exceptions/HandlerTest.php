<?php

use Mockery as m;
use Recca0120\LaravelTracy\Exceptions\Handler;

class HandlerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handler_report()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $exception = new Exception();

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $handler = new Handler($bluescreen, $exceptionHandler);
        $handler->report($exception);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldHaveReceived('report')->with($exception)->once();
    }

    public function test_handler_render_exception_with_redirect_response()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $request = m::spy('Illuminate\Http\Request');
        $exception = new Exception();
        $response = m::spy('Symfony\Component\HttpFoundation\RedirectResponse');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldReceive('render')->andReturn($response);

        $handler = new Handler($bluescreen, $exceptionHandler);


        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function test_handler_render_exception_with_response_content_is_view()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $request = m::spy('Illuminate\Http\Request');
        $exception = new Exception();
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $view = m::spy('Illuminate\Contracts\View\View');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldReceive('render')->andReturn($response);
        $response->shouldReceive('getContent')->andReturn($view);

        $handler = new Handler($bluescreen, $exceptionHandler);


        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function test_handler_render_exception()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $request = m::spy('Illuminate\Http\Request');
        $exception = new Exception();
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldReceive('render')->andReturn($response);
        $bluescreen->shouldReceive('render')->with($exception)->andReturn('bluescreen');

        $handler = new Handler($bluescreen, $exceptionHandler);


        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $handler->render($request, $exception));
        $exceptionHandler->shouldHaveReceived('render')->with($request, $exception)->once();
        $response->shouldHaveReceived('getContent')->once();
        $response->shouldHaveReceived('setContent')->with('bluescreen')->once();
        $bluescreen->shouldHaveReceived('render')->with($exception)->once();
    }

    public function test_handler_render_for_console()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $exception = new Exception();
        $output = m::spy('\Symfony\Component\Console\Output\OutputInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $handler = new Handler($bluescreen, $exceptionHandler);
        $handler->renderForConsole($output, $exception);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldHaveReceived('renderForConsole')->with($output, $exception)->once();
    }
}
