<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\BlueScreen;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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
        $blueScreen = m::mock(BlueScreen::class);
        $request = m::mock(Request::class);
        $exceptionHandler = m::mock(ExceptionHandler::class);
        $response = m::mock(Response::class);
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
        $blueScreen = m::mock(BlueScreen::class);
        $request = m::mock(Request::class);
        $exceptionHandler = m::mock(ExceptionHandler::class);
        $response = m::mock(Response::class);
        $view = m::mock(View::class);
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
        $blueScreen = m::mock(BlueScreen::class);
        $request = m::mock(Request::class);
        $exceptionHandler = m::mock(ExceptionHandler::class);
        $response = m::mock(RedirectResponse::class);
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

        $blueScreen = m::mock(BlueScreen::class);
        $exeptionHandler = m::mock(ExceptionHandler::class)
            ->shouldReceive('renderForConsole')->with($output, $exception)->once()
            ->mock();

        $handler = new Handler($blueScreen, $exeptionHandler);
        $handler->renderForConsole($output, $exception);
    }
}
