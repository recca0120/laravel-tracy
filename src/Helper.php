<?php

namespace Recca0120\LaravelTracy;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tracy\Debugger;

class Helper
{
    public static function getBar()
    {
        ob_start();
        Debugger::getBar()->render();
        $content = ob_get_clean();

        return $content;
    }

    public static function getBlueScreen($e)
    {
        ob_start();
        Debugger::getBlueScreen()
            ->render($e);
        $content = ob_get_clean();

        return $content;
    }

    public static function getHttpResponse($content, Exception $e)
    {
        if (($e instanceof HttpException) === true) {
            $statusCode = $e->getStatusCode();
            $headers = $e->getHeaders();
        } else {
            $statusCode = 500;
            $headers = [];
        }

        return response($content, $statusCode, $headers);
    }
}
