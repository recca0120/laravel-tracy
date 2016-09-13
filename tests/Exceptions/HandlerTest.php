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

        $blueScreen = m::mock('\Recca0120\LaravelTracy\BlueScreen');

        $request = m::mock('\Illuminate\Http\Request');
        $responseFactory = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');

        $exceptionHandler = m::mock('\Illuminate\Contracts\Debug\ExceptionHandler');
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
        $blueScreen = m::mock('\Recca0120\LaravelTracy\BlueScreen');
        $request = m::mock('\Illuminate\Http\Request');
        $responseFactory = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');
        $exeptionHandler = m::mock('\Illuminate\Contracts\Debug\ExceptionHandler');
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
        $exception = new HttpResponseException(m::mock('\Symfony\Component\HttpFoundation\Response'));

        $blueScreen = m::mock('\Recca0120\LaravelTracy\BlueScreen');
        $request = m::mock('\Illuminate\Http\Request');
        $responseFactory = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');

        $exeptionHandler = m::mock('\Illuminate\Contracts\Debug\ExceptionHandler');

        $handler = new Handler($blueScreen, $responseFactory, $exeptionHandler);
        $handler->render($request, $exception);
    }

    public function test_console()
    {
        $output = '';
        $exception = new Exception();

        $blueScreen = m::mock('\Recca0120\LaravelTracy\BlueScreen');
        $responseFactory = m::mock('\Illuminate\Contracts\Routing\ResponseFactory');
        $exeptionHandler = m::mock('\Illuminate\Contracts\Debug\ExceptionHandler')
            ->shouldReceive('renderForConsole')->with($output, $exception)->once()
            ->mock();

        $handler = new Handler($blueScreen, $responseFactory, $exeptionHandler);
        $handler->renderForConsole($output, $exception);
    }
}
