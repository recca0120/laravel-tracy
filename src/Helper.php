<?php

// hm39168
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

        $basePath = config('tracy.base_path');

        if (empty($basePath) === false) {
            $compiled = '#(?P<uri>'.strtr(Debugger::$editor, [
                '%file' => '(?P<file>.+)',
                '%line' => '(?P<line>\d+)',
                '?' => '\?',
                '&' => '(&|&amp;)',
            ]).')#';

            if (preg_match_all($compiled, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $uri = $match['uri'];
                    $file = str_replace(base_path(), $basePath, rawurldecode($match['file']));
                    $line = $match['line'];
                    $editor = strtr(Debugger::$editor, ['%file' => rawurlencode($file), '%line' => $line ? (int) $line : '']);
                    $content = str_replace($uri, $editor, $content);
                }
            }
        }

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

    public static function appendDebuggerBar($request, $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');
        $barResponse = static::getBar();
        if ($pos !== false and
            // $request->isJson() === false and
            // $request->wantsJson() === false and
            $request->ajax() === false and
            $request->pjax() === false) {
            $barResponse .= file_get_contents(__DIR__.'/../resources/views/updateDebugger.php');
            $content = substr($content, 0, $pos).$barResponse.substr($content, $pos);

            $response->setContent($content);
        } else {
            if (method_exists($response, 'header') === true) {
                foreach (str_split(base64_encode(@json_encode($barResponse)), 4990) as $k => $v) {
                    $response->header('X-Tracy-Error-Ajax-'.$k, $v);
                }
            }
        }

        return $response;
    }
}
