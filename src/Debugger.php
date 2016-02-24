<?php

namespace Recca0120\LaravelTracy;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tracy\Debugger as TracyDebugger;

class Debugger
{
    protected static $basePath = null;

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
    public $options = [];

    /**
     * construct.
     *
     * @param array $options
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($config = [], $app = null)
    {
        if ($app === null) {
            TracyDebugger::enable();
        }

        $this->config = $config;
        TracyDebugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        TracyDebugger::$maxDepth = array_get($this->config, 'maxDepth');
        TracyDebugger::$maxLen = array_get($this->config, 'maxLen');
        TracyDebugger::$showLocation = array_get($this->config, 'showLocation');
        TracyDebugger::$strictMode = array_get($this->config, 'strictMode');
        TracyDebugger::$editor = array_get($this->config, 'editor');

        $bar = TracyDebugger::getBar();
        foreach (array_get($this->config, 'panels') as $key => $enabled) {
            if ($enabled === true) {
                $class = '\\'.__NAMESPACE__.'\Panels\\'.ucfirst($key).'Panel';
                if (class_exists($class) === false) {
                    $class = $key;
                }
                $this->panels[$key] = new $class($this->config, $app);
                $bar->addPanel($this->panels[$key], $class);
            }
        }
    }

    /**
     * bar dump.
     *
     * return mixed
     */
    public function barDump()
    {
        return call_user_func_array('\Tracy\Debugger::barDump', func_get_args());
    }

    /**
     * get tracy bar panel.
     *
     * @return string
     */
    public function getBarResponse()
    {
        ob_start();
        TracyDebugger::getBar()->render();
        $content = ob_get_clean();

        return $content;
    }

    /**
     * append debugger to Response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public function appendDebugbar($request, $response)
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
        $barResponse = $this->getBarResponse();

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
            $barResponse =
                $this->getJavascript('tracy.js').
                $barResponse;
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

    /**
     * get javascript.
     * @param  string $file
     * @return string
     */
    protected function getJavascript($file)
    {
        return '<script>'.file_get_contents(__DIR__.'/../public/js/'.$file).'</script>';
    }

    /**
     * set base path.
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        static::$basePath = $basePath;
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
        // $content = $this->updateEditorUri($content);

        return $content;
    }

    /**
     * update editor uri.
     *
     * @param  string $content
     * @return string
     */
    protected static function updateEditorUri($content)
    {
        $basePath = static::$basePath;

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
}
