<?php

use Mockery as m;
use Recca0120\LaravelTracy\Exceptions\Handler;

class HandlerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_exception_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $exception = new Exception();
        $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen');
        $request = m::mock('Illuminate\Http\Request');
        $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $handler = new Handler($blueScreen, $exceptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $blueScreen->shouldReceive('render')->once()->with($exception);

        $exceptionHandler
            ->shouldReceive('report')->with($exception)->once()
            ->shouldReceive('render')->once()->andReturn($response);

        $response
            ->shouldReceive('getContent')->once()->andReturn($exception)
            ->shouldReceive('setContent')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $handler->render($request, $exception);
        $handler->report($exception);
    }

    public function test_view_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $exception = new Exception();
        $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen');
        $request = m::mock('Illuminate\Http\Request');
        $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $view = m::mock('Illuminate\Contracts\View\View');
        $handler = new Handler($blueScreen, $exceptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $exceptionHandler
            ->shouldReceive('report')->with($exception)->once()
            ->shouldReceive('render')->once()->andReturn($response);

        $response
            ->shouldReceive('getContent')->once()->andReturn($view);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $handler->render($request, $exception);
        $handler->report($exception);
    }

    public function test_redirect_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $exception = new Exception();
        $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen');
        $request = m::mock('Illuminate\Http\Request');
        $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler');
        $response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse');
        $handler = new Handler($blueScreen, $exceptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $exceptionHandler
            ->shouldReceive('report')->with($exception)->once()
            ->shouldReceive('render')->once()->andReturn($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $handler->render($request, $exception);
        $handler->report($exception);
    }

    public function test_console()
    {
        $output = '';
        $exception = new Exception();

        $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen');
        $exeptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler')
            ->shouldReceive('renderForConsole')->with($output, $exception)->once()
            ->mock();

        $handler = new Handler($blueScreen, $exeptionHandler);
        $handler->renderForConsole($output, $exception);
    }
}
