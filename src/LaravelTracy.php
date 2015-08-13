<?php namespace Recca0120\LaravelTracy;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tracy\Debugger;
use Tracy\Dumper;
use Tracy\FireLogger;

class LaravelTracy
{
    public static $tracyData = [

    ];

    protected static $panels = [
        'Routing' => [
            'panel' => 'Recca0120\LaravelTracy\Panels\RoutingPanel',
            'toJson' => false,
        ],
        'Database' => [
            'panel' => 'Recca0120\LaravelTracy\Panels\ConnectionPanel',
            'toJson' => true,
        ],
        'Session' => [
            'panel' => 'Recca0120\LaravelTracy\Panels\SessionPanel',
            'toJson' => false,
        ],
        'Request' => [
            'panel' => 'Recca0120\LaravelTracy\Panels\RequestPanel',
            'toJson' => false,
        ],
        'User' => [
            'panel' => 'Recca0120\LaravelTracy\Panels\UserPanel',
            'toJson' => true,
        ],
    ];

    public static function register()
    {
        $version = static::normalizeTracyVersion();
        $maxDepth = 4;
        $maxLength = 1000;
        $showLocation = true;
        $strictMode = true;
        switch ($version) {
            case '2.2':
                static::$tracyData = [
                    'VERSION' => $version,
                    'MAX_DEPTH' => $maxDepth,
                    'MAX_LENGTH' => $maxLength,
                    'SHOW_LOCATION' => $showLocation,
                    'STRICT_MODE' => $strictMode,
                    'DUMP_OPTION' => [
                        Dumper::COLLAPSE => false,
                    ],
                    'HANDLER' => [
                        'EXCEPTION' => ['\Tracy\Debugger', '_exceptionHandler'],
                        'SHUTDOWN' => ['\Tracy\Debugger', '_shutdownHandler'],
                        'ERROR' => ['\Tracy\Debugger', '_errorHandler'],
                    ],
                ];
                break;
            default:
                static::$tracyData = [
                    'VERSION' => $version,
                    'MAX_DEPTH' => $maxDepth,
                    'MAX_LENGTH' => $maxLength,
                    'SHOW_LOCATION' => $showLocation,
                    'STRICT_MODE' => $strictMode,
                    'DUMP_OPTION' => [
                        Dumper::COLLAPSE => false,
                        Dumper::LIVE => true,
                    ],
                    'HANDLER' => [
                        'EXCEPTION' => ['\Tracy\Debugger', 'exceptionHandler'],
                        'SHUTDOWN' => ['\Tracy\Debugger', 'shutdownHandler'],
                        'ERROR' => ['\Tracy\Debugger', 'errorHandler'],
                    ],
                ];
                break;
        }

        Debugger::$time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        Debugger::$maxDepth = static::$tracyData['MAX_DEPTH'];
        Debugger::$maxLen = static::$tracyData['MAX_LENGTH'];
        Debugger::$showLocation = static::$tracyData['SHOW_LOCATION'];
        Debugger::$strictMode = static::$tracyData['STRICT_MODE'];

        $app = app();
        $app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'Recca0120\LaravelTracy\Exceptions\Handler'
        );
        $kernel = $app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\LaravelTracyMiddleware');

        foreach (static::$panels as $panelId => $panelData) {
            $panel = $panelData['panel'];
            Debugger::getBar()->addPanel(new $panel(), $panelId);
        }
    }

    public static function modifyResponse($request, $response)
    {
        $app = app();
        if ($app->runningInConsole() === true) {
            return $response;
        }

        if ($request->isJson() === true or
            $request->wantsJson() === true or
            $request->ajax() === true or
            $request->pjax() === true
        ) {
            $logger = new FireLogger();
            $logger->maxDepth = static::$tracyData['MAX_DEPTH'];
            $logger->maxLength = tatic::$tracyData['MAX_LENGTH'];
            foreach (static::$panels as $panelId => $panelData) {
                if ($panelData['toJson'] === true) {
                    $panel = Debugger::getBar()->getPanel($panelId);
                    $jsonData = $panel->toJson();
                    $logger->log($jsonData);
                }
            }
        } else {
            $response = static::injectBar($response);
        }

        return $response;
    }

    protected static function injectBar($response)
    {
        $content = $response->getContent();
        ob_start();
        call_user_func_array(static::$tracyData['HANDLER']['SHUTDOWN'], []);
        $renderedContent = ob_get_clean();

        $rewriteJavascript = <<<EOF
function bindReady(handler){

    var called = false

    var ready = function() {
        if (called) return
        called = true
        handler()
    }

    if ( document.addEventListener ) { // native event
        document.addEventListener( "DOMContentLoaded", ready, false )
    } else if ( document.attachEvent ) {  // IE

        try {
            var isFrame = window.frameElement != null
        } catch(e) {}

        // IE, the document is not inside a frame
        if ( document.documentElement.doScroll && !isFrame ) {
            function tryScroll(){
                if (called) return
                try {
                    document.documentElement.doScroll("left")
                    ready()
                } catch(e) {
                    setTimeout(tryScroll, 10)
                }
            }
            tryScroll()
        }

        // IE, the document is inside a frame
        document.attachEvent("onreadystatechange", function(){
            if ( document.readyState === "complete" ) {
                ready()
            }
        })
    }

    // Old browsers
    if (window.addEventListener)
        window.addEventListener('load', ready, false)
    else if (window.attachEvent)
        window.attachEvent('onload', ready)
    else {
        var fn = window.onload // very old browser, copy old onload
        window.onload = function() { // replace by new onload and call the old one
            fn && fn()
            ready()
        }
    }
}

var readyList = []

function onReady(handler) {

    function executeHandlers() {
        for(var i=0; i<readyList.length; i++) {
            readyList[i]()
        }
    }

    if (!readyList.length) { // set handler on first run
        bindReady(executeHandlers)
    }

    readyList.push(handler)
}


var _T = null
var completed = function() {
    _T()
    _T = function() {}
}
var onLoad = window.onload;
if (typeof onLoad === 'function') {
    bindReady(onLoad);
    window.onload = function() {}
}
var fire = function() {
    if (window.addEventListener) {
        bindReady(completed);
    } else if (_T != null){
        bindReady(completed);
    } else {
        setTimeout(fire, 50);
    }
}
fire();
_T =
EOF;
        if (static::$tracyData['VERSION'] === '2.2') {
            $rewriteJavascript = $rewriteJavascript;
            $renderedContent = str_replace('window.onload = ', $rewriteJavascript, $renderedContent);
        } else {
            $renderedContent = str_replace('window.addEventListener(\'load\', ', $rewriteJavascript.'(', $renderedContent);
        }

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos).$renderedContent.substr($content, $pos);
        } else {
            $content = $content.$renderedContent;
        }

        $response->setContent($content);

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
            call_user_func_array(static::$tracyData['HANDLER']['EXCEPTION'], [$e, false]);
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
