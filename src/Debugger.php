<?php

namespace Recca0120\LaravelTracy;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tracy\Debugger as TracyDebugger;
use Tracy\Dumper;
use Tracy\FireLogger;

class Debugger
{
    public static $config = [];

    public static function register($config = [])
    {
        $config = array_merge([
            'version' => static::normalizeTracyVersion(),
            'strictMode' => TracyDebugger::$strictMode,
            'maxDepth' => TracyDebugger::$maxDepth,
            'maxLen' => TracyDebugger::$maxLen,
            'showLocation' => TracyDebugger::$showLocation,
            'editor' => TracyDebugger::$editor,
            'panels' => [
                'Recca0120\LaravelTracy\Panels\RoutingPanel',
                'Recca0120\LaravelTracy\Panels\ConnectionPanel',
                'Recca0120\LaravelTracy\Panels\SessionPanel',
                'Recca0120\LaravelTracy\Panels\RequestPanel',
                'Recca0120\LaravelTracy\Panels\EventPanel',
                'Recca0120\LaravelTracy\Panels\UserPanel',
            ],
        ], $config);
        switch ($config['version']) {
            case '2.2':
                static::$config = array_merge($config, [
                    'dumpOption' => [
                        Dumper::COLLAPSE => false,
                    ],
                ]);
                break;
            default:
                static::$config = array_merge($config, [
                    'dumpOption' => [
                        Dumper::COLLAPSE => false,
                        Dumper::LIVE => true,
                    ],
                ]);
                break;
        }

        TracyDebugger::$time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        TracyDebugger::$maxDepth = static::$config['maxDepth'];
        TracyDebugger::$maxLen = static::$config['maxLen'];
        TracyDebugger::$showLocation = static::$config['showLocation'];
        TracyDebugger::$strictMode = static::$config['strictMode'];
        TracyDebugger::$editor = static::$config['editor'];

        $app = app();
        $app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'Recca0120\LaravelTracy\Exceptions\Handler'
        );
        $kernel = $app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\LaravelTracyMiddleware');

        foreach (static::$config['panels'] as $panel) {
            TracyDebugger::getBar()->addPanel(new $panel(), $panel);
        }
    }

    public static function modifyResponse($request, $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        ob_start();
        TracyDebugger::getBar()->render();
        $debuggerJavascript = ob_get_clean();

        if ($pos !== false and
            $request->isJson() === false and
            $request->wantsJson() === false and
            $request->ajax() === false and
            $request->pjax() === false) {
            $debuggerJavascript .= file_get_contents(__DIR__.'/../resources/views/updateDebugger.blade.php');

            // if (static::$config['version'] === '2.2') {
            //     $debuggerJavascript = str_replace('(function(onloadOrig) {', '', $debuggerJavascript);
            //     $debuggerJavascript = str_replace('})(window.onload);', '', $debuggerJavascript);
            //     $debuggerJavascript = str_replace('if (typeof onloadOrig === \'function\') onloadOrig();', '', $debuggerJavascript);
            //     $debuggerJavascript = str_replace('window.onload =', 'window._TracyDebugger =', $debuggerJavascript);
            // } else {
            //     $debuggerJavascript = str_replace('window.addEventListener(\'load\',', 'window._TracyDebugger = (', $debuggerJavascript);
            // }
            // $scriptEndPos = strripos($debuggerJavascript, '</script>');
            // $onloadScript = '_TracyDebugger();';
            // $debuggerJavascript = substr($debuggerJavascript, 0, $scriptEndPos).$onloadScript.substr($debuggerJavascript, $scriptEndPos);

            $content = substr($content, 0, $pos).$debuggerJavascript.substr($content, $pos);

            $response->setContent($content);
        } else {
            foreach (str_split(base64_encode(@json_encode($debuggerJavascript)), 4990) as $k => $v) {
                $response->header('tracy-ajax-'.$k, $v);
            }
            // $logger = new FireLogger();
            // $logger->maxDepth = static::$config['maxDepth'];
            // $logger->maxLength = static::$config['maxLen'];
            // foreach (static::$ajaxPanel as $panel) {
            //     if (in_array($panel, static::$config['panels'], true) === true) {
            //         $panel = TracyDebugger::getBar()->getPanel($panel);
            //         $jsonData = $panel->toJson();
            //         $logger->log($jsonData);
            //     }
            // }
        }

        return $response;
    }

    public static function handleException($request, Exception $e)
    {
        $status = 500;
        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            if (view()->exists("errors.{$status}")) {
                $response = response()->view("errors.{$status}", [], $status);
            }
        }

        if (config('app.debug') === true) {
            ob_start();
            TracyDebugger::getBlueScreen()->render($e);
            $content = ob_get_clean();
            $response = response()->make($content, $status);
        } else {
            $response = (new SymfonyDisplayer(false))->createResponse($e);
        }

        return $response;
    }

    private static function normalizeTracyVersion()
    {
        if (version_compare(TracyDebugger::VERSION, '2.3.0', '<')) {
            $version = '2.2';
        } else {
            $version = '2.3';
        }

        return $version;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(['\Tracy\Debugger', $name], $arguments);
    }
}
