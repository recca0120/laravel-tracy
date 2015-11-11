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
        $content = static::updateEditorUri($content);

        return $content;
    }

    public static function updateEditorUri($content)
    {
        $basePath = config('tracy.base_path');

        if (empty($basePath) === false) {
            $compiled = '#(?P<uri>'.strtr(Debugger::$editor, [
                '%file' => '(?P<file>.+)',
                '%line' => '(?P<line>\d+)',
                '?'     => '\?',
                '&'     => '(&|&amp;)',
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
        if ($response->isRedirection() === true) {
            return $response;
        }
        $content = $response->getContent();
        $pos = strripos($content, '</body>');
        $barResponse = static::getBar();

        if ($pos !== false and
            // $request->isJson() === false and
            // $request->wantsJson() === false and
            $request->ajax() === false and
            $request->pjax() === false) {
            // $barResponse .= file_get_contents(__DIR__.'/../resources/views/updateDebugger.php');
            $content = substr($content, 0, $pos).$barResponse.substr($content, $pos);

            $response->setContent($content);
        } else {
            // if (method_exists($response, 'header') === true) {
            //     $barResponse = static::lzwCompress($barResponse);
            //     foreach (str_split(base64_encode(@json_encode($barResponse)), 4990) as $k => $v) {
            //         $response->header('X-Tracy-Error-Ajax-'.$k, $v);
            //     }
            // }
        }

        return $response;
    }

    public static function lzwCompress($string)
    {
        // compression
        $dictionary = array_flip(range("\0", "\xFF"));
        $word = '';
        $codes = [];
        for ($i = 0; $i <= strlen($string); $i++) {
            $x = substr($string, $i, 1);
            if (strlen($x) && isset($dictionary[$word.$x])) {
                $word .= $x;
            } elseif ($i) {
                $codes[] = $dictionary[$word];
                $dictionary[$word.$x] = count($dictionary);
                $word = $x;
            }
        }

        // convert codes to binary string
        $dictionary_count = 256;
        $bits = 8; // ceil(log($dictionary_count, 2))
        $return = '';
        $rest = 0;
        $rest_length = 0;
        foreach ($codes as $code) {
            $rest = ($rest << $bits) + $code;
            $rest_length += $bits;
            $dictionary_count++;
            if ($dictionary_count >> $bits) {
                $bits++;
            }
            while ($rest_length > 7) {
                $rest_length -= 8;
                $return .= chr($rest >> $rest_length);
                $rest &= (1 << $rest_length) - 1;
            }
        }

        return $return.($rest_length ? chr($rest << (8 - $rest_length)) : '');
    }
}
