<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\BlueScreen;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HandlerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_handler()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $exception = new Exception();

        $blueScreen = m::mock(BlueScreen::class);

        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);

        $exceptionHandler = m::mock(ExceptionHandler::class);
        $handler = new Handler($blueScreen, $responseFactory, $exceptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $blueScreen->shouldReceive('render')->with($exception);
        $responseFactory->shouldReceive('make');
        $exceptionHandler->shouldReceive('report')->with($exception);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $handler->render($request, $exception);
        $handler->report($exception);
    }

    public function test_http_exception()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $statusCode = 404;
        $headers = [];
        $exception = new HttpException($statusCode, null, null, $headers);
        $blueScreen = m::mock(BlueScreen::class);
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $exeptionHandler = m::mock(ExceptionHandler::class);
        $handler = new Handler($blueScreen, $responseFactory, $exeptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $blueScreen->shouldReceive('render')->with($exception)->once();
        $responseFactory->shouldReceive('make')->with(m::any(), $statusCode, $headers)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $handler->render($request, $exception);
    }

    public function test_http_response_exception()
    {
        $exception = new HttpResponseException(m::mock(Response::class));

        $blueScreen = m::mock(BlueScreen::class);
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);

        $exeptionHandler = m::mock(ExceptionHandler::class);

        $handler = new Handler($blueScreen, $responseFactory, $exeptionHandler);
        $handler->render($request, $exception);
    }

    public function test_console()
    {
        $output = '';
        $exception = new Exception();

        $blueScreen = m::mock(BlueScreen::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $exeptionHandler = m::mock(ExceptionHandler::class)
            ->shouldReceive('renderForConsole')->with($output, $exception)->once()
            ->mock();

        $handler = new Handler($blueScreen, $responseFactory, $exeptionHandler);
        $handler->renderForConsole($output, $exception);
    }
}
