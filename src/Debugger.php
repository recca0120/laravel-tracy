<?php

namespace Recca0120\LaravelTracy;

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
     */
    public function __construct($options = [], $app = null)
    {
        if ($app !== null) {
            $options = array_merge($options, $app['config']->get('tracy'));
            $app['events']->listen('kernel.handled', function ($request, $response) {
                return static::appendDebugbar($request, $response);
            });
        } else {
            TracyDebugger::enable();
        }

        static::$options = $options;
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
     * @param  \Exception $exception
     * @return string
     */
    public static function getBlueScreen($exception)
    {
        ob_start();
        TracyDebugger::getBlueScreen()->render($exception);
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
        if ($response->isRedirection() === true ||
            ($request->ajax() === true && $request->pjax() === false)
        ) {
            return $response;
        }

        $content = $response->getContent();
        $barResponse = static::getBarResponse();

        if ($request->pjax() === true) {
            $startString = 'var debug =';
            $startPos = strpos($barResponse, $startString);
            $endString = "debug.style.display = 'block';";
            $endPos = strpos($barResponse, $endString) - $startPos + strlen($endString);
            $barResponse = '<script>(function(){ var n = document.getElementById("tracy-debug"); if (n) { document.body.removeChild(n);'.substr($barResponse, $startPos, $endPos).'};})();</script>';
            $response->setContent($content.$barResponse);

            return $response;
        }

        $pos = strripos($content, '</body>');
        if ($pos === false) {
            return $response;
        }

        $response->setContent(
            substr($content, 0, $pos).$barResponse.substr($content, $pos)
        );

        return $response;
    }
}
