<?php

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Tracy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testHandler()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $exception = new Exception();

        $tracy = m::mock(Tracy::class);

        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);

        $exceptionHandler = m::mock(ExceptionHandlerContract::class);
        $handler = new Handler($tracy, $responseFactory, $exceptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('renderBlueScreen')->once();
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

    public function testHttpException()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $statusCode = 404;
        $headers = [];
        $exception = new HttpException($statusCode, null, null, $headers);
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $exeptionHandler = m::mock(ExceptionHandlerContract::class);
        $handler = new Handler($tracy, $responseFactory, $exeptionHandler);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('renderBlueScreen')->once();
        $responseFactory->shouldReceive('make')->with(m::any(), $statusCode, $headers)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $handler->render($request, $exception);
    }

    public function test_http_exception()
    {
    }

    public function test_http_response_exception()
    {
        $exception = new HttpResponseException(m::mock(Response::class));

        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class);

        $exeptionHandler = m::mock(ExceptionHandlerContract::class);

        $handler = new Handler($tracy, $responseFactory, $exeptionHandler);
        $handler->render($request, $exception);
    }

    public function test_console()
    {
        $output = '';
        $exception = new Exception();

        $tracy = m::mock(Tracy::class);
        $responseFactory = m::mock(ResponseFactory::class);
        $exeptionHandler = m::mock(ExceptionHandlerContract::class)
            ->shouldReceive('renderForConsole')->with($output, $exception)->once()
            ->mock();

        $handler = new Handler($tracy, $responseFactory, $exeptionHandler);
        $handler->renderForConsole($output, $exception);
    }
}
