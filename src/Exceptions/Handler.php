<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Contracts\View\View;
use Recca0120\LaravelTracy\DebuggerManager;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Handler implements ExceptionHandler
{
    /**
     * app exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * $debuggerManager.
     *
     * @var \Recca0120\LaravelTracy\DebuggerManager
     */
    protected $debuggerManager;

    /**
     * __construct.
     *
     * @param \Illuminate\Contracts\Debug\ExceptionHandler $exceptionHandler
     * @param \Recca0120\LaravelTracy\DebuggerManager $debuggerManager
     */
    public function __construct(ExceptionHandler $exceptionHandler, DebuggerManager $debuggerManager)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->debuggerManager = $debuggerManager;
    }

    /**
     * Report or log an exception.
     *
     * @param \Exception $e
     */
    public function report(Exception $e)
    {
        $this->exceptionHandler->report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        $response = $this->exceptionHandler->render($request, $e);
        if ($response instanceof RedirectResponse ||
            $response instanceof JsonResponse ||
            $response->getContent() instanceof View
        ) {
            return $response;
        }

        $response->setContent(
            $this->debuggerManager->exceptionHandler($e)
        );

        return $response;
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception $e
     */
    public function renderForConsole($output, Exception $e)
    {
        $this->exceptionHandler->renderForConsole($output, $e);
    }
}
