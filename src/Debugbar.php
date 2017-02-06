<?php

namespace Recca0120\LaravelTracy;

use Tracy\Bar;
use Tracy\Debugger;
use Tracy\IBarPanel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * $bar.
     *
     * @var \Tracy\Bar
     */
    protected $bar;

    /**
     * $app.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param array                                        $config
     * @param \Illuminate\Http\Request                     $request
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($config, Request $request = null, $app = null)
    {
        $this->config = array_merge([
            'accepts' => [],
            'showBar' => false,
            'editor' => Debugger::$editor,
            'maxDepth' => Debugger::$maxDepth,
            'maxLength' => Debugger::$maxLength,
            'scream' => true,
            'showLocation' => true,
            'strictMode' => true,
            'currentTime' => $_SERVER['REQUEST_TIME_FLOAT'] ?: microtime(true),
            'editorMapping' => property_exists('Debugger', 'editorMapping') ? Debugger::$editorMapping : [],
        ], $config);

        $this->request = $request ?: Request::capture();
        $this->app = $app;
        $this->bar = Debugger::getBar();
        $this->initializeTracyDebuger();
    }

    /**
     * initializeTracyDebuger.
     *
     * @method initializeTracyDebuger
     *
     * @param array $config
     *
     * @return static
     */
    protected function initializeTracyDebuger()
    {
        Debugger::$editor = $this->config['editor'];
        Debugger::$maxDepth = $this->config['maxDepth'];
        Debugger::$maxLength = $this->config['maxLength'];
        Debugger::$scream = $this->config['scream'];
        Debugger::$showLocation = $this->config['showLocation'];
        Debugger::$strictMode = $this->config['strictMode'];
        Debugger::$time = $this->config['currentTime'];
        Debugger::$editorMapping = $this->config['editorMapping'];

        return $this;
    }

    /**
     * loadPanels.
     *
     * @method loadPanels
     */
    public function loadPanels()
    {
        $panels = Arr::get($this->config, 'panels', []);
        if (isset($panels['user']) === true) {
            $panels['auth'] = $panels['user'];
            unset($panels['user']);
        }

        $ajax = $this->request->ajax();

        foreach ($panels as $name => $enabled) {
            if ($enabled === false) {
                continue;
            }

            $class = '\\'.__NAMESPACE__.'\Panels\\'.Str::studly($name).'Panel';
            $panel = new $class();

            if ($ajax === true && $panel->supportAjax === false) {
                continue;
            }

            $this->put($panel, $name);
        }

        return $this;
    }

    /**
     * setBar.
     *
     * @param \Tracy\Bar $bar
     */
    public function setBar(Bar $bar)
    {
        $this->bar = $bar;

        return $this;
    }

    /**
     * put.
     *
     * @method put
     *
     * @param \Tracy\IBarPanel $panel
     * @param string           $id
     *
     * @return static
     */
    public function put(IBarPanel $panel, $id)
    {
        $panel->setLaravel($this->app);
        $this->panels[$id] = $panel;
        $this->bar->addPanel($panel);

        return $this;
    }

    /**
     * get.
     *
     * @method get
     *
     * @param string $id
     *
     * @return \Tracy\IBarPanel
     */
    public function get($id)
    {
        return isset($this->panels[$id]) ? $this->panels[$id] : null;
    }

    /**
     * renderBar.
     *
     * @method renderBar
     *
     * @return string
     */
    protected function renderBar()
    {
        ob_start();
        $this->bar->render();

        return ob_get_clean();
    }

    /**
     * rejectRender.
     *
     * @method rejectRender
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int                                        $statusCode
     *
     * @return bool
     */
    protected function rejectRender(Response $response)
    {
        if ($this->config['showBar'] === false ||
            $response instanceof BinaryFileResponse ||
            $response instanceof StreamedResponse ||
            $response instanceof RedirectResponse
        ) {
            return true;
        }

        if ($this->request->ajax() === true) {
            return false;
        }

        $contentType = strtolower($response->headers->get('Content-Type'));
        if (empty($contentType) === true && $response->getStatusCode() >= 400 ||
            count($this->config['accepts']) === 0
        ) {
            return false;
        }

        foreach ($this->config['accepts'] as $accept) {
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
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Response $response)
    {
        if ($this->rejectRender($response) === true) {
            return $response;
        }

        $content = $response->getContent();
        $htmlValidatorPanel = $this->getPanel('html-validator');
        if (is_null($htmlValidatorPanel) === false && $response->getStatusCode() === 200) {
            $htmlValidatorPanel->setHtml($content);
        }

        $bar = $this->renderBar();
        $pos = strripos($content, '</body>');
        $response->setContent(
            $pos !== false ?
                substr($content, 0, $pos).$bar.substr($content, $pos) :
                $content.$bar
        );

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
        $this->bar->dispatchAssets();

        return ob_get_clean();
    }

    /**
     * dispatch.
     *
     * @method dispatch
     *
     * @return string
     */
    public function dispatchContent()
    {
        ob_start();
        method_exists($this->bar, 'dispatchContent') === true ?
            $this->bar->dispatchContent() : $this->bar->dispatchAssets();

        return ob_get_clean();
    }

    /**
     * getPanel.
     *
     * @method getPanel
     *
     * @param string $id
     *
     * @return \Tracy\IPanelBar
     */
    public function getPanel($id)
    {
        return $this->get($id);
    }
}
