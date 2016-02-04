<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Recca0120\LaravelTracy\Debugger;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler implements ExceptionHandlerContract
{
    /**
     * response factory.
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * app exception handler.
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * __construct.
     * @param \Illuminate\Contracts\Routing\ResponseFactory       $responseFactory
     * @param \Illuminate\Contracts\Debug\ExceptionHandler        $exceptionHandler
     */
    public function __construct(
        ResponseFactoryContract $responseFactory,
        ExceptionHandlerContract $exceptionHandler = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if (is_null($this->exceptionHandler) === false) {
            $this->exceptionHandler->report($e);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        $statusCode = 500;
        $headers = [];
        if (is_null($this->exceptionHandler) === false) {
            if ($this->isHttpException($e) === true) {
                $statusCode = $e->getStatusCode();
                $headers = $e->getHeaders();
                try {
                    return $this->responseFactory->view("errors.{$statusCode}", [], $statusCode);
                } catch (Exception $fileNotFoundException) {
                }
                // run in console
                // $this->exceptionHandler->render($request, $e);
            }
        }

        return $this->responseFactory->make(Debugger::getBlueScreen($e), $statusCode, $headers);
    }

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Exception  $e
     * @return void
     */
    public function renderForConsole($output, Exception $e)
    {
        if (is_null($this->exceptionHandler) === false) {
            $this->exceptionHandler->renderForConsole($output, $e);
        }
    }

    /**
     * is http exception.
     * @param  \Exception $e
     * @return bool
     */
    protected function isHttpException(Exception $e)
    {
        return ($e instanceof HttpException) === true;
    }
}
