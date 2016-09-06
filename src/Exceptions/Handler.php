<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Recca0120\LaravelTracy\BlueScreen;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler implements ExceptionHandlerContract
{
    /**
     * $blueScreen.
     *
     * @var \Recca0120\LaravelTracy\BlueScreen
     */
    protected $blueScreen;

    /**
     * response factory.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;

    /**
     * app exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param  \Recca0120\LaravelTracy\BlueScreen            $blueScreen
     * @param  \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $exceptionHandler
     */
    public function __construct(
        BlueScreen $blueScreen,
        ResponseFactoryContract $responseFactory,
        $exceptionHandler
    ) {
        $this->blueScreen = $blueScreen;
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
        if (method_exists($e, 'getResponse') === true) {
            return $e->getResponse();
        }
        $statusCode = 500;
        $headers = [];
        if (
            is_null($this->exceptionHandler) === false &&
            $this->isHttpException($e) === true
        ) {
            $statusCode = $e->getStatusCode();
            $headers = $e->getHeaders();
            try {
                return $this->responseFactory->view("errors.{$statusCode}", [], $statusCode);
            } catch (Exception $fileNotFoundException) {
            }
        }

        return $this->responseFactory->make(
            $this->blueScreen->render($e),
            $statusCode,
            $headers
        );
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
