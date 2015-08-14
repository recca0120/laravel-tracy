<?php namespace Recca0120\LaravelTracy;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tracy\Debugger;
use Tracy\Dumper;
use Tracy\FireLogger;

class LaravelTracy
{
    public static $config = [];

    protected static $ajaxPanel = [
        'Recca0120\LaravelTracy\Panels\ConnectionPanel',
        'Recca0120\LaravelTracy\Panels\UserPanel',
    ];

    public static function register($config = [])
    {
        $config = array_merge([
            'version' => static::normalizeTracyVersion(),
            'strictMode' => Debugger::$strictMode,
            'maxDepth' => Debugger::$maxDepth,
            'maxLen' => Debugger::$maxLen,
            'showLocation' => Debugger::$showLocation,
            'editor' => Debugger::$editor,
            'panels' => [
                'Recca0120\LaravelTracy\Panels\RoutingPanel',
                'Recca0120\LaravelTracy\Panels\ConnectionPanel',
                'Recca0120\LaravelTracy\Panels\SessionPanel',
                'Recca0120\LaravelTracy\Panels\RequestPanel',
                'Recca0120\LaravelTracy\Panels\UserPanel',
            ],
        ], $config);
        switch ($config['version']) {
            case '2.2':
                static::$config = array_merge($config, [
                    'dumpOption' => [
                        Dumper::COLLAPSE => false,
                    ],
                    'handler' => [
                        'exception' => ['\Tracy\Debugger', '_exceptionHandler'],
                        'shutdown' => ['\Tracy\Debugger', '_shutdownHandler'],
                        'error' => ['\Tracy\Debugger', '_errorHandler'],
                    ],
                ]);
                break;
            default:
                static::$config = array_merge($config, [
                    'dumpOption' => [
                        Dumper::COLLAPSE => false,
                        Dumper::LIVE => true,
                    ],
                    'handler' => [
                        'exception' => ['\Tracy\Debugger', 'exceptionHandler'],
                        'shutdown' => ['\Tracy\Debugger', 'shutdownHandler'],
                        'error' => ['\Tracy\Debugger', 'errorHandler'],
                    ],
                ]);
                break;
        }

        Debugger::$time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        Debugger::$maxDepth = static::$config['maxDepth'];
        Debugger::$maxLen = static::$config['maxLen'];
        Debugger::$showLocation = static::$config['showLocation'];
        Debugger::$strictMode = static::$config['strictMode'];
        Debugger::$editor = static::$config['editor'];

        $app = app();
        $app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'Recca0120\LaravelTracy\Exceptions\Handler'
        );
        $kernel = $app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\LaravelTracyMiddleware');

        foreach (static::$config['panels'] as $panel) {
            Debugger::getBar()->addPanel(new $panel(), $panel);
        }
    }

    public static function modifyResponse($request, $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if ($pos !== false and
            $request->isJson() === false and
            $request->wantsJson() === false and
            $request->ajax() === false and
            $request->pjax() === false) {
            ob_start();
            call_user_func_array(static::$config['handler']['shutdown'], []);
            $renderedContent = ob_get_clean();

            $rewriteJavascript = view('laravel-tracy::rewriteJavascript')->render();

            if (static::$config['version'] === '2.2') {
                $rewriteJavascript = $rewriteJavascript;
                $renderedContent = str_replace('window.onload = ', $rewriteJavascript, $renderedContent);
            } else {
                $renderedContent = str_replace('window.addEventListener(\'load\', ', $rewriteJavascript.'(', $renderedContent);
            }

            $content = substr($content, 0, $pos).$renderedContent.substr($content, $pos);

            $response->setContent($content);
        } else {
            $logger = new FireLogger();
            $logger->maxDepth = static::$config['maxDepth'];
            $logger->maxLength = static::$config['maxLen'];
            foreach (static::$ajaxPanel as $panel) {
                if (in_array($panel, static::$config['panels'], true) === true) {
                    $panel = Debugger::getBar()->getPanel($panel);
                    $jsonData = $panel->toJson();
                    $logger->log($jsonData);
                }
            }
        }

        return $response;
    }

    public static function handleException($request, \Exception $e)
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
            call_user_func_array(static::$config['handler']['exception'], [$e, false]);
            $content = ob_get_clean();

            $response = response()->make($content, $status);
        } else {
            $response = (new SymfonyDisplayer(false))->createResponse($e);
        }

        return $response;
    }

    private static function normalizeTracyVersion()
    {
        if (version_compare(Debugger::VERSION, '2.3.0', '<')) {
            $version = '2.2';
        } else {
            $version = '2.3';
        }

        return $version;
    }
}
