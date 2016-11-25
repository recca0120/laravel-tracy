<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tracy\Debugger;
use Tracy\IBarPanel;

class Debugbar
{
    /**
     * $panels.
     *
     * @var array
     */
    protected $panels = [];

    /**
     * $request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * $ajax.
     *
     * @var bool
     */
    protected $ajax;

    /**
     * $app.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * $accepts.
     *
     * @var array
     */
    protected $accepts = [];

    /**
     * $showBar.
     *
     * @var array
     */
    protected $showBar = true;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param  array                                        $config
     * @param  \Illuminate\Http\Request                     $request
     * @param  \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($config, Request $request = null, Application $app = null)
    {
        $this->request = is_null($request) === true ? Request::capture() : $request;
        $this->ajax = $this->request->ajax();
        $this->app = $app;
        $this->accepts = Arr::get($config, 'accepts', []);
        $this->showBar = Arr::get($config, 'showBar', false);

        $this->initializeTracyDebuger($config);
        $this->loadPanels($config);
    }

    /**
     * initializeTracyDebuger.
     *
     * @method initializeTracyDebuger
     *
     * @param array $config
     */
    protected function initializeTracyDebuger($config)
    {
        Debugger::$editor = Arr::get($config, 'editor', Debugger::$editor);
        Debugger::$maxDepth = Arr::get($config, 'maxDepth', Debugger::$maxDepth);
        Debugger::$maxLength = Arr::get($config, 'maxLength', Debugger::$maxLength);
        Debugger::$scream = Arr::get($config, 'scream', true);
        Debugger::$showLocation = Arr::get($config, 'showLocation', true);
        Debugger::$strictMode = Arr::get($config, 'strictMode', true);
        Debugger::$time = Arr::get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        Debugger::$editorMapping = Arr::get($config, 'editorMapping', []);
    }

    /**
     * loadPanels.
     *
     * @method loadPanels
     *
     * @param  array $config
     */
    protected function loadPanels($config)
    {
        $panels = Arr::get($config, 'panels', []);
        if (isset($panels['user']) === true) {
            $panels['auth'] = $panels['user'];
            unset($panels['user']);
        }
        foreach ($panels as $name => $enabled) {
            if ($enabled === false) {
                continue;
            }

            $class = '\\'.__NAMESPACE__.'\Panels\\'.Str::studly($name).'Panel';
            $panel = new $class();

            if ($this->ajax === true && $panel->supportAjax === false) {
                continue;
            }

            $this->put($panel, $name);
        }
    }

    /**
     * put.
     *
     * @method put
     *
     * @param  \Tracy\IBarPanel $panel
     * @param  string           $id
     *
     * @return static
     */
    public function put(IBarPanel $panel, $id)
    {
        $panel->setLaravel($this->app);
        $this->panels[$id] = $panel;

        return $this;
    }

    /**
     * get.
     *
     * @method get
     *
     * @param  string $id
     *
     * @return \Tracy\IBarPanel
     */
    public function get($id)
    {
        return isset($this->panels[$id]) ? $this->panels[$id] : null;
    }

    /**
     * setupBar.
     *
     * @method setupBar
     *
     * @return \Tracy\Bar
     */
    public function setupBar()
    {
        $bar = Debugger::getBar();
        foreach ($this->panels as $panel) {
            $bar->addPanel($panel);
        }

        return $bar;
    }

    /**
     * getBar.
     *
     * @method getBar
     *
     * @return string
     */
    protected function getBar()
    {
        $bar = $this->setupBar();
        ob_start();
        $bar->render();

        return ob_get_clean();
    }

    /**
     * deny.
     *
     * @method deny
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @param  int                                        $statusCode
     *
     * @return bool
     */
    protected function deny(Response $response, $statusCode)
    {
        if ($response instanceof BinaryFileResponse) {
            return true;
        }

        if ($response instanceof StreamedResponse) {
            return true;
        }

        if ($response->isRedirection() === true) {
            return true;
        }

        if ($this->ajax === true) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type');

        if (empty($contentType) === true && $statusCode >= 400) {
            return false;
        }

        if (count($this->accepts) === 0) {
            return false;
        }

        $contentType = strtolower($contentType);
        foreach ($this->accepts as $accept) {
            if (strpos($contentType, $accept) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * render.
     *
     * @method render
     *
     * @param  \Symfony\Component\HttpFoundation\Response   $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Response $response)
    {
        if ($this->showBar === false) {
            return $response;
        }

        $statusCode = $response->getStatusCode();

        if ($this->deny($response, $statusCode) === true) {
            return $response;
        }

        $content = $response->getContent();

        $htmlValidatorPanel = $this->get('html-validator');
        if (is_null($htmlValidatorPanel) === false && $statusCode === 200) {
            $htmlValidatorPanel->setHtml($content);
        }

        $bar = $this->getBar();
        $pos = strripos($content, '</body>');
        if ($pos !== false) {
            $content = substr($content, 0, $pos).$bar.substr($content, $pos);
        } else {
            $content .= $bar;
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * dispatchAssets.
     *
     * @method dispatchAssets
     *
     * @return string
     */
    public function dispatchAssets()
    {
        ob_start();
        Debugger::getBar()->dispatchAssets();

        return ob_get_clean();
    }

    /**
     * dispatch.
     *
     * @method dispatch
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
            @session_start();
        }

        ob_start();
        Debugger::getBar()->dispatchContent();

        return ob_get_clean();
    }

    /**
     * getPanel.
     *
     * @method getPanel
     *
     * @param  string   $id
     *
     * @return \Tracy\IPanelBar
     */
    public function getPanel($id)
    {
        return $this->get($id);
    }
}
