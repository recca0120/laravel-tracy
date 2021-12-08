<?php

namespace Recca0120\LaravelTracy\Tests\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\DebuggerManager;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class HandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRenderResponseWithViewReturnsView()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));

        $view = m::spy(View::class);
        $view->expects('render')->andReturns('Some rendered view string');

        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = new Response($view);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);

        // Response returned from render,
        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testReport()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));
        $exception = new Exception();

        $handler->report($exception);

        $exceptionHandler->shouldHaveReceived('report')->with($exception);
    }

    public function testShouldReport()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));
        $exception = new Exception();
        $exceptionHandler->expects('shouldReport')->with($exception)->andReturns(true);

        $this->assertTrue($handler->shouldReport($exception));
    }

    public function testRenderWithResponseIsRedirectResponse()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));

        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = m::spy(RedirectResponse::class);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderWithResponseIsJsonResponse()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));

        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = m::spy(JsonResponse::class);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderWithResponseContentIsView()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));
        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = m::spy(SymfonyResponse::class);

        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);
        $response->expects('getContent')->andReturns(m::spy(\Illuminate\Contracts\View\View::class));

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderRedirectResponse()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));

        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = m::spy(RedirectResponse::class);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderJsonResponse()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));

        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = m::spy(JsonResponse::class);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRenderView()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $handler = new Handler($exceptionHandler, m::spy(DebuggerManager::class));

        $request = m::spy(Request::class);
        $exception = new Exception();
        $response = m::spy(SymfonyResponse::class);
        $view = m::spy(\Illuminate\Contracts\View\View::class);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);
        $response->expects('getContent')->andReturns($view);

        $this->assertSame($response, $handler->render($request, $exception));
    }

    public function testRender()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $debuggerManager = m::spy(DebuggerManager::class);
        $handler = new Handler($exceptionHandler, $debuggerManager);

        $request = Request::capture();
        $exception = new Exception();
        $response = m::spy(SymfonyResponse::class);
        $exceptionHandler->expects('render')->with($request, $exception)->andReturns($response);

        $response->expects('getContent')->andReturns(null);
        $debuggerManager->expects('exceptionHandler')->with($exception)->andReturns($content = 'foo');
        $response->expects('setContent')->with($content);

        $_SERVER['foo'] = 'bar';

        $this->assertSame($response, $handler->render($request, $exception));
        $this->assertSame($_SERVER, $request->server());
    }

    public function testRenderForConsoleMethod()
    {
        $exceptionHandler = m::spy(ExceptionHandler::class);
        $debuggerManager = m::spy(DebuggerManager::class);
        $handler = new Handler($exceptionHandler, $debuggerManager);

        $output = m::spy(OutputInterface::class);
        $exception = new Exception();

        $handler->renderForConsole($output, $exception);

        $exceptionHandler->shouldHaveReceived('renderForConsole')->with($output, $exception);
    }
}
