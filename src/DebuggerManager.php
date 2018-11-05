<?php

namespace Recca0120\LaravelTracy;

use Exception;
use Tracy\Bar;
use Tracy\Helpers;
use ErrorException;
use Tracy\Debugger;
use Tracy\BlueScreen;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Routing\UrlGenerator;

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
     * $session.
     *
     * @var \Recca0120\LaravelTracy\Session
     */
    protected $session;

    /**
     * $urlGenerator.
     *
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $urlGenerator;

    /**
     * __construct.
     *
     * @param array $config
     * @param \Tracy\Bar $bar
     * @param \Tracy\BlueScreen $blueScreen
     */
    public function __construct($config = [], Bar $bar = null, BlueScreen $blueScreen = null, Session $session = null)
    {
        $this->config = $config;
        $this->bar = $bar ?: Debugger::getBar();
        $this->blueScreen = $blueScreen ?: Debugger::getBlueScreen();
        $this->session = $session ?: new Session;
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
     * setUrlGenerator.
     *
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @return $this
     */
    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
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
        if ($this->session->isStarted() === false) {
            $this->session->start();
        }

        return $this->renderBuffer(function () {
            return $this->bar->dispatchAssets();
        });
    }

    /**
     * shutdownHandler.
     *
     * @param string $content
     * @param bool $ajax
     * @param int $error
     * @return string
     */
    public function shutdownHandler($content, $ajax = false, $error = null)
    {
        $error = $error ?: error_get_last();
        if (in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR], true)) {
            return $this->exceptionHandler(
                Helpers::fixStack(
                    new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])
                )
            );
        }

        return array_reduce(['renderLoader', 'renderBar'], function ($content, $method) use ($ajax) {
            return call_user_func([$this, $method], $content, $ajax);
        }, $content);
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
     * renderLoader.
     *
     * @param string $content
     * @param bool $ajax
     * @return string
     */
    protected function renderLoader($content, $ajax = false)
    {
        if ($ajax === true || $this->session->isStarted() === false) {
            return $content;
        }

        return $this->render($content, 'renderLoader', ['head', 'body']);
    }

    /**
     * renderBar.
     *
     * @param string $content
     * @return string
     */
    protected function renderBar($content)
    {
        return $this->render(
            $content,
            'render',
            [Arr::get($this->config, 'appendTo', 'body'), 'body']
        );
    }

    /**
     * render.
     *
     * @param string $content
     * @param string $method
     * @param array $appendTo
     * @return string
     */
    protected function render($content, $method, $appendTags = ['body'])
    {
        $appendHtml = $this->renderBuffer(function () use ($method) {
            $requestUri = $_SERVER['REQUEST_URI'];
            $_SERVER['REQUEST_URI'] = '';
            call_user_func([$this->bar, $method]);
            $_SERVER['REQUEST_URI'] = $requestUri;
        });

        $appendTags = array_unique($appendTags);

        foreach ($appendTags as $appendTag) {
            $pos = strripos($content, '</'.$appendTag.'>');

            if ($pos !== false) {
                return substr_replace($content, $appendHtml, $pos, 0);
            }
        }

        return $content.$appendHtml;
    }

    /**
     * renderBuffer.
     *
     * @param callable $callback
     * @return string
     */
    protected function renderBuffer(callable $callback)
    {
        ob_start();
        $callback();

        return $this->replacePath(ob_get_clean());
    }

    /**
     * replacePath.
     *
     * @param string $content
     * @return string
     */
    protected function replacePath($content)
    {
        static $path;

        if (is_null($path) === true) {
            $path = is_null($this->urlGenerator) === false
                ? $this->urlGenerator->route(Arr::get($this->config, 'route.as').'bar')
                : null;
        }

        return is_null($path) === false
            ? str_replace('?_tracy_bar', $path.'?_tracy_bar', $content)
            : $content;
    }
}
