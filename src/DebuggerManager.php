<?php

namespace Recca0120\LaravelTracy;

use Closure;
use Exception;
use Tracy\Bar;
use Tracy\Helpers;
use ErrorException;
use Tracy\Debugger;
use Tracy\BlueScreen;
use Illuminate\Support\Arr;

class DebuggerManager
{
    /**
     * $config.
     *
     * @var array
     */
    protected $config;

    /**
     * $bar.
     *
     * @var \Tracy\Bar
     */
    protected $bar;

    /**
     * $blueScreen.
     *
     * @var \Tracy\BlueScreen
     */
    protected $blueScreen;

    /**
     * __construct.
     *
     * @param array $config
     * @param \Tracy\Bar $bar
     * @param \Tracy\BlueScreen $blueScreen
     */
    public function __construct($config = [], Bar $bar = null, BlueScreen $blueScreen = null)
    {
        $this->config = $config;
        $this->bar = $bar ?: Debugger::getBar();
        $this->blueScreen = $blueScreen ?: Debugger::getBlueScreen();
    }

    /**
     * init.
     *
     * @param array $config
     * @return array
     */
    public static function init($config = [])
    {
        $config = array_merge([
            'accepts' => [],
            'appendTo' => 'body',
            'showBar' => false,
            'editor' => Debugger::$editor,
            'maxDepth' => Debugger::$maxDepth,
            'maxLength' => Debugger::$maxLength,
            'scream' => true,
            'showLocation' => true,
            'strictMode' => true,
            'currentTime' => $_SERVER['REQUEST_TIME_FLOAT'] ?: microtime(true),
            'editorMapping' => isset(Debugger::$editorMapping) === true ? Debugger::$editorMapping : [],
        ], $config);

        Debugger::$editor = $config['editor'];
        Debugger::$maxDepth = $config['maxDepth'];
        Debugger::$maxLength = $config['maxLength'];
        Debugger::$scream = $config['scream'];
        Debugger::$showLocation = $config['showLocation'];
        Debugger::$strictMode = $config['strictMode'];
        Debugger::$time = $config['currentTime'];

        if (isset(Debugger::$editorMapping) === true) {
            Debugger::$editorMapping = $config['editorMapping'];
        }

        return $config;
    }

    /**
     * enabled.
     *
     * @return bool
     */
    public function enabled()
    {
        return Arr::get($this->config, 'enabled', true) === true;
    }

    /**
     * showBar.
     *
     * @return bool
     */
    public function showBar()
    {
        return Arr::get($this->config, 'showBar', true) === true;
    }

    /**
     * accepts.
     *
     * @return array
     */
    public function accepts()
    {
        return Arr::get($this->config, 'accepts', []);
    }

    /**
     * dispatchAssets.
     *
     * @param string $type
     * @return array
     */
    public function dispatchAssets($type)
    {
        switch ($type) {
            case 'css':
            case 'js':
            case 'assets':
                $headers = [
                    'Content-Type' => $type === 'css' ? 'text/css; charset=utf-8' : 'text/javascript; charset=utf-8',
                    'Cache-Control' => 'max-age=86400',
                ];
                $content = $this->renderBuffer(function () {
                    return $this->bar->dispatchAssets();
                });
                break;
            default:
                $headers = [
                    'Content-Type' => 'text/javascript; charset=utf-8',
                ];
                $content = $this->dispatch();
                break;
        }

        return [
            array_merge($headers, [
                'Content-Length' => strlen($content),
            ]),
            $content,
        ];
    }

    /**
     * dispatch.
     *
     * @return string
     */
    public function dispatch()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_httponly', '1');
            session_start();
        }

        return $this->renderBuffer(function () {
            return method_exists($this->bar, 'dispatchContent') === true ?
                    $this->bar->dispatchContent() : $this->bar->dispatchAssets();
        });
    }

    /**
     * shutdownHandler.
     *
     * @param string $content
     * @return string
     */
    public function shutdownHandler($content, $error = null)
    {
        $error = $error ?: error_get_last();
        if (in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR], true)) {
            return $this->exceptionHandler(
                Helpers::fixStack(
                    new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
                )
            );
        }

        $bar = $this->renderBuffer(function () {
            $this->bar->render();
        });

        $appendTo = Arr::get($this->config, 'appendTo', 'body');
        $pos = strripos($content, '</'.$appendTo.'>');

        return $pos !== false
            ? substr($content, 0, $pos).$bar.substr($content, $pos)
            : $content.$bar;
    }

    /**
     * exceptionHandler.
     *
     * @param \Exception $exception
     * @return string
     */
    public function exceptionHandler(Exception $exception)
    {
        return $this->renderBuffer(function () use ($exception) {
            Helpers::improveException($exception);
            $this->blueScreen->render($exception);
        });
    }

    /**
     * renderBuffer.
     *
     * @param \Closure $callback
     * @return string
     */
    protected function renderBuffer(Closure $callback)
    {
        ob_start();
        $callback();

        return ob_get_clean();
    }
}
