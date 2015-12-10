<?php

namespace Recca0120\LaravelTracy;

use Tracy\Debugger;
use Tracy\Helpers as TracyHelpers;

class Helper
{
    public static function getBarResponse()
    {
        ob_start();
        Debugger::getBar()->render();
        $content = ob_get_clean();

        return $content;
    }

    public static function getBlueScreen($e)
    {
        ob_start();
        Debugger::getBlueScreen()->render($e);
        $content = ob_get_clean();
        $content = static::updateEditorUri($content);

        return $content;
    }

    public static function updateEditorUri($content)
    {
        $basePath = config('tracy.base_path');

        if (empty($basePath) === true) {
            return $content;
        }

        $compiled = '#(?P<uri>'.strtr(Debugger::$editor, [
            '%file' => '(?P<file>.+)',
            '%line' => '(?P<line>\d+)',
            '?'     => '\?',
            '&'     => '(&|&amp;)',
        ]).')#';

        if (preg_match_all($compiled, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $uri     = $match['uri'];
                $file    = str_replace(base_path(), $basePath, rawurldecode($match['file']));
                $line    = $match['line'];
                $editor  = strtr(Debugger::$editor, ['%file' => rawurlencode($file), '%line' => $line ? (int) $line : '']);
                $content = str_replace($uri, $editor, $content);
            }
        }

        return $content;
    }

    public static function appendDebugbar($request, $response)
    {
        if ($response->isRedirection() === true) {
            return $response;
        }

        // $request->isJson() === true or
        // $request->wantsJson() === true or
        if ($request->ajax() === true or
            $request->pjax() === true) {

        //     if (method_exists($response, 'header') === true) {
        //         $barResponse = static::lzwCompress($barResponse);
        //         foreach (str_split(base64_encode(@json_encode($barResponse)), 4990) as $k => $v) {
        //             $response->header('X-Tracy-Error-Ajax-'.$k, $v);
        //         }
        //     }
            return $response;
        }

        $content = $response->getContent();

        $pos = strripos($content, '</body>');
        if ($pos === false) {
            return $response;
        }

        $response->setContent(
            substr($content, 0, $pos).static::getBarResponse().substr($content, $pos)
        );

        return $response;
    }

    /**
     * Use a backtrace to search for the origin of the query.
     */
    public static function findSource()
    {
        $source = null;
        $trace  = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
        foreach ($trace as $row) {
            if (isset($row['file']) === true && Debugger::getBluescreen()->isCollapsed($row['file']) === false) {
                if ((isset($row['function']) && strpos($row['function'], 'call_user_func') === 0)
                    || (isset($row['class']) && is_subclass_of($row['class'], '\\Illuminate\\Database\\Connection'))
                ) {
                    continue;
                }

                return $source = [$row['file'], (int) $row['line']];
            }
        }

        return $source;
    }

    public static function getEditorLink($source)
    {
        $link = null;
        if ($source !== null) {
            // $link = substr_replace(\Tracy\Helpers::editorLink($source[0], $source[1]), ' class="nette-DbConnectionPanel-source"', 2, 0);
            $file = $source[0];
            $line = $source[1];
            $link = TracyHelpers::editorLink($file, $line);
            $link = self::updateEditorUri($link);
        }

        return $link;
    }

    public static function lzwCompress($string)
    {
        // compression
        $dictionary = array_flip(range("\0", "\xFF"));
        $word       = '';
        $codes      = [];
        for ($i = 0; $i <= strlen($string); $i++) {
            $x = substr($string, $i, 1);
            if (strlen($x) && isset($dictionary[$word.$x])) {
                $word .= $x;
            } elseif ($i) {
                $codes[]              = $dictionary[$word];
                $dictionary[$word.$x] = count($dictionary);
                $word                 = $x;
            }
        }

        // convert codes to binary string
        $dictionary_count = 256;
        $bits             = 8; // ceil(log($dictionary_count, 2))
        $return           = '';
        $rest             = 0;
        $rest_length      = 0;
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
