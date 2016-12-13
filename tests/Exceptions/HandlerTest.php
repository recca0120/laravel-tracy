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

        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exception = new Exception();

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $handler = new Handler($exceptionHandler, $bluescreen);
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

        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $request = m::spy('Illuminate\Http\Request');
        $exception = new Exception();
        $response = m::spy('Symfony\Component\HttpFoundation\RedirectResponse');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldReceive('render')->andReturn($response);

        $handler = new Handler($exceptionHandler, $bluescreen);

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

        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
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

        $handler = new Handler($exceptionHandler, $bluescreen);

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

        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
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

        $handler = new Handler($exceptionHandler, $bluescreen);

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

        $exceptionHandler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');
        $bluescreen = m::spy('Recca0120\LaravelTracy\BlueScreen');
        $exception = new Exception();
        $output = m::spy('\Symfony\Component\Console\Output\OutputInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $handler = new Handler($exceptionHandler, $bluescreen);
        $handler->renderForConsole($output, $exception);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $exceptionHandler->shouldHaveReceived('renderForConsole')->with($output, $exception)->once();
    }
}
