<?php

namespace Recca0120\LaravelTracy\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\View\View;
use Recca0120\LaravelTracy\BlueScreen;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Handler implements ExceptionHandler
{
    /**
     * $blueScreen.
     *
     * @var \Recca0120\LaravelTracy\BlueScreen
     */
    protected $blueScreen;

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
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $exceptionHandler
     */
    public function __construct(BlueScreen $blueScreen, $exceptionHandler)
    {
        $this->blueScreen = $blueScreen;
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
        $response = $this->exceptionHandler->render($request, $e);
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        $content = $response->getContent();
        if ($content instanceof View) {
            return $response;
        }

        $response->setContent($this->blueScreen->render($e));

        return $response;
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
}
