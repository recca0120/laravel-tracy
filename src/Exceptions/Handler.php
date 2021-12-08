<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Recca0120\LaravelTracy\DebuggerManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class Handler implements ExceptionHandler
{
    /**
     * app exception handler.
     *
     * @var ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * $debuggerManager.
     *
     * @var DebuggerManager
     */
    protected $debuggerManager;

    /**
     * __construct.
     *
     * @param ExceptionHandler $exceptionHandler
     * @param DebuggerManager $debuggerManager
     */
    public function __construct(ExceptionHandler $exceptionHandler, DebuggerManager $debuggerManager)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->debuggerManager = $debuggerManager;
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     *
     * @throws Exception
     * @throws Throwable
     */
    public function report(Throwable $e)
    {
        $this->exceptionHandler->report($e);
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param Throwable $e
     * @return bool
     */
    public function shouldReport(Throwable $e)
    {
        return $this->exceptionHandler->shouldReport($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = $this->exceptionHandler->render($request, $e);

        if ($this->shouldRenderException($response) === true) {
            $_SERVER = $request->server();
            $response->setContent($this->debuggerManager->exceptionHandler($e));
        }

        return $response;
    }

    /**
     * Render an exception to the console.
     *
     * @param OutputInterface $output
     * @param Throwable $e
     * @return void
     */
    public function renderForConsole($output, Throwable $e)
    {
        $this->exceptionHandler->renderForConsole($output, $e);
    }

    /**
     * shouldRenderException.
     *
     * @param Response|\Symfony\Component\HttpFoundation\Response $response
     * @return bool
     */
    protected function shouldRenderException($response)
    {
        if (
            $response instanceof RedirectResponse ||
            $response instanceof JsonResponse ||
            $response->getContent() instanceof View ||
            ($response instanceof Response && $response->getOriginalContent() instanceof View)
        ) {
            return false;
        }

        return true;
    }
}
