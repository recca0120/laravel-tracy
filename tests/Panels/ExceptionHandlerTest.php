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

    public function test_handler()
    {
        $exception = new Exception();

        $tracy = m::mock(Tracy::class)
            ->shouldReceive('renderBlueScreen')->once()
            ->mock();

        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class)
            ->shouldReceive('make')
            ->mock();

        $exeptionHandler = m::mock(ExceptionHandlerContract::class)
            ->shouldReceive('report')->with($exception)
            ->mock();

        $handler = new Handler($tracy, $responseFactory, $exeptionHandler);
        $handler->render($request, $exception);
        $handler->report($exception);
    }

    public function test_http_exception()
    {
        $statusCode = 404;
        $headers = [];

        $exception = new HttpException($statusCode, null, null, $headers);

        $tracy = m::mock(Tracy::class)
            ->shouldReceive('renderBlueScreen')->once()
            ->mock();

        $request = m::mock(Request::class);
        $responseFactory = m::mock(ResponseFactory::class)
            ->shouldReceive('make')->with(m::any(), $statusCode, $headers)->once()
            ->mock();

        $exeptionHandler = m::mock(ExceptionHandlerContract::class);

        $handler = new Handler($tracy, $responseFactory, $exeptionHandler);
        $handler->render($request, $exception);
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
