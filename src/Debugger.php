<?php

namespace Recca0120\LaravelTracy;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

        if (array_get(static::$options, 'panels.terminal') === true) {
            $serviceProvider = '\Recca0120\Terminal\ServiceProvider';
            if ($app->getProvider($serviceProvider) === null) {
                $app->register($serviceProvider);
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
            strpos(strtolower($response->headers->get('content-type')), 'text/html') === false ||
            $response instanceof BinaryFileResponse ||
            $response instanceof StreamedResponse
        ) {
            return $response;
        }

        $isJsonResponse = $response instanceof JsonResponse;

        $content = $response->getContent();
        $barResponse = static::getBarResponse();

        if ($request->ajax() === true ||
            $request->pjax() === true ||
            $request->wantsJson() === true ||
            $isJsonResponse === true
        ) {
            $startString = 'var debug =';
            $startPos = strpos($barResponse, $startString);
            $endString = "debug.style.display = 'block';";
            $endPos = strpos($barResponse, $endString) - $startPos + strlen($endString);
            $barResponse = '(function(){ var n = document.getElementById("tracy-debug"); if (n) { document.body.removeChild(n);'.substr($barResponse, $startPos, $endPos).'};})();';
        }

        if ($request->wantsJson() === true || $isJsonResponse === true) {
            // $response->setCookie($barResponse);
            // $content = json_decode($content, true);
            // $content['TracyDebug'] = $barResponse;
            // $content = json_encode($content);
        } elseif ($request->pjax() === true || $request->ajax() === true) {
            $content .= '<script>'.$barResponse.'</script>';
        } else {
            // $barResponse .= static::ajaxMonitor();
            $pos = strripos($content, '</body>');
            if ($pos !== false) {
                $content = substr($content, 0, $pos).$barResponse.substr($content, $pos);
            } else {
                $content .= $barResponse;
            }
        }
        $response->setContent($content);

        return $response;
    }

    public static function ajaxMonitor()
    {
        return '<script>'.file_get_contents(__DIR__.'/../public/js/monitor.js').'</script>';
        // return  '<script>!function(){var AjaxMonitor=function(request){return function(mode){var req=new request(mode),onReadyStateChange=function(){if(4===req.readyState&&200===req.status)try{if("arraybuffer"!=req.responseType.toLowerCase()){var data=eval("("+req.responseText+")");if(data.TracyDebug){var code=data.TracyDebug;eval(code)}}}catch(e){}};return req.addEventListener("readystatechange",onReadyStateChange),req}};window.ActiveXObject&&(window.ActiveXObject=AjaxMonitor(window.ActiveXObject)),window.XMLHttpRequest&&(window.XMLHttpRequest=AjaxMonitor(window.XMLHttpRequest))}();</script>';
    }
}
