<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Tracy\Debugger as TracyDebugger;

class Debugger
{
    /**
     * bar panel instances.
     *
     * @var Tracy\IBarPanel
     */
    public $panels = [];

    /**
     * options.
     *
     * @var array
     */
    public static $options = [];

    /**
     * construct.
     *
     * @param array $options
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(
        $options = [],
        Application $app = null,
        RepositoryContract $config = null,
        Dispatcher $events = null
    ) {
        static::$options = ($config !== null) ? array_merge($options, $config->get('tracy')) : $options;
        TracyDebugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        TracyDebugger::$maxDepth = array_get(static::$options, 'maxDepth');
        TracyDebugger::$maxLen = array_get(static::$options, 'maxLen');
        TracyDebugger::$showLocation = array_get(static::$options, 'showLocation');
        TracyDebugger::$strictMode = array_get(static::$options, 'strictMode');
        TracyDebugger::$editor = array_get(static::$options, 'editor');

        $bar = TracyDebugger::getBar();
        foreach (array_get(static::$options, 'panels') as $key => $enabled) {
            if ($enabled === true) {
                $class = '\\'.__NAMESPACE__.'\Panels\\'.ucfirst($key).'Panel';
                if (class_exists($class) === false) {
                    $class = $key;
                }
                $this->panels[$key] = new $class($app, static::$options);
                $bar->addPanel($this->panels[$key], $class);
            }
        }

        if ($events !== null) {
            $events->listen('kernel.handled', function ($request, $response) {
                return static::appendDebugbar($request, $response);
            });
        } else {
            TracyDebugger::enable();
        }
    }

    /**
     * update editor uri.
     *
     * @param  string $content
     * @return string
     */
    public static function updateEditorUri($content)
    {
        $basePath = array_get(static::$options, 'base_path');

        if (empty($basePath) === true) {
            return $content;
        }

        $compiled = '#(?P<uri>'.strtr(TracyDebugger::$editor, [
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
                $editor = strtr(TracyDebugger::$editor, [
                    '%file' => rawurlencode($file),
                    '%line' => $line ? (int) $line : '',
                ]);
                $content = str_replace($uri, $editor, $content);
            }
        }

        return $content;
    }

    /**
     * get tracy bar panel.
     *
     * @return string
     */
    public static function getBarResponse()
    {
        ob_start();
        TracyDebugger::getBar()->render();
        $content = ob_get_clean();

        return $content;
    }

    /**
     * get tracy bluescreen.
     *
     * @param  \Exception $e
     * @return string
     */
    public static function getBlueScreen($e)
    {
        ob_start();
        TracyDebugger::getBlueScreen()->render($e);
        $content = ob_get_clean();
        $content = static::updateEditorUri($content);

        return $content;
    }

    /**
     * append debugger to Response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public static function appendDebugbar($request, $response)
    {
        if ($response->isRedirection() === true) {
            return $response;
        }

        if ($request->ajax() === true or
            $request->pjax() === true) {
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
}
