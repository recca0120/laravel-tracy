<?php

namespace Recca0120\LaravelTracy\Tests\Exceptions;

use Exception;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Exceptions\Handler;

class HandlerTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testReportMethod()
    {
        $handler = new Handler(
            $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
            $bluescreen = m::mock('Recca0120\LaravelTracy\BlueScreen')
        );
        $exception = new Exception();
        $exceptionHandler->shouldReceive('report')->once()->with($exception);
        $handler->report($exception);
    }

    public function testRednerWithResponseIsRedirectResponse()
    {
        $handler = new Handler(
            $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
            $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen')
        );
        $exceptionHandler->shouldReceive('render')
            ->once()
            ->with(
                $request = m::mock('Illuminate\Http\Request'),
                $exception = new Exception()
            )->andReturn(
                $response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse')
            );
        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRednerWithResponseIsJsonResponse()
    {
        $handler = new Handler(
            $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
            $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen')
        );
        $exceptionHandler->shouldReceive('render')
            ->once()
            ->with(
                $request = m::mock('Illuminate\Http\Request'),
                $exception = new Exception()
            )->andReturn(
                $response = m::mock('Symfony\Component\HttpFoundation\JsonResponse')
            );
        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRednerWithResponseContentIsView()
    {
        $handler = new Handler(
            $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
            $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen')
        );
        $exceptionHandler->shouldReceive('render')
            ->once()
            ->with(
                $request = m::mock('Illuminate\Http\Request'),
                $exception = new Exception()
            )->andReturn(
                $response = m::mock('Symfony\Component\HttpFoundation\Response')
            );
        $response->shouldReceive('getContent')->once()->andReturn(
            m::mock('Illuminate\Contracts\View\View')
        );
        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderBlueScreenRender()
    {
        $handler = new Handler(
            $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
            $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen')
        );
        $exceptionHandler->shouldReceive('render')
            ->once()
            ->with(
                $request = m::mock('Illuminate\Http\Request'),
                $exception = new Exception()
            )->andReturn(
                $response = m::mock('Symfony\Component\HttpFoundation\Response')
            );
        $response->shouldReceive('getContent')->once()->andReturn(
            m::mock('stdClass')
        );
        $blueScreen->shouldReceive('render')->once()->with($exception)->andReturn($content = 'foo');
        $response->shouldReceive('setContent')->once()->with($content);
        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderForConsoleMethod()
    {
        $handler = new Handler(
            $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
            $blueScreen = m::mock('Recca0120\LaravelTracy\BlueScreen')
        );
        $exceptionHandler->shouldReceive('renderForConsole')->once()->with(
            $output = m::mock('Symfony\Component\Console\Output\OutputInterface'),
            $exception = new Exception()
        );
        $handler->renderForConsole($output, $exception);
    }
}
