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

    public static function appendDebuggerInfo($request, $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');
        if ($pos !== false and
            $request->isJson() === false and
            $request->wantsJson() === false and
            $request->ajax() === false and
            $request->pjax() === false) {
            $barResponse = static::getBar();
            $content = substr($content, 0, $pos).$barResponse.substr($content, $pos);

            $response->setContent($content);
        } else {
            foreach (str_split(base64_encode(@json_encode($debuggerJavascript)), 4990) as $k => $v) {
                $response->header('tracy-ajax-'.$k, $v);
            }
        }

        return $response;
    }
}
