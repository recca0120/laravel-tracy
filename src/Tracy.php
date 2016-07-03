<?php

namespace Recca0120\LaravelTracy;

use ErrorException;
use Exception;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tracy\Debugger;
use Tracy\Helpers;
use Tracy\IBarPanel;

class Tracy
{
    /**
     * $app.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * $panels.
     *
     * @var array
     */
    public $panels = [];

    /**
     * __construct.
     * @method __construct
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(ApplicationContract $app = null)
    {
        $this->app = $app;
    }

    /**
     * init.
     *
     * @method init
     *
     * @param  array $config
     */
    public function init($config = [])
    {
        if (Debugger::getBar()->dispatchAssets() === true) {
            exit;
        }
        Debugger::$editor = array_get($config, 'editor', Debugger::$editor);
        Debugger::$maxDepth = array_get($config, 'maxDepth', Debugger::$maxDepth);
        Debugger::$maxLength = array_get($config, 'maxLength', Debugger::$maxLength);
        Debugger::$scream = array_get($config, 'scream', true);
        Debugger::$showLocation = array_get($config, 'showLocation', true);
        Debugger::$strictMode = array_get($config, 'strictMode', true);
        Debugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));

        $panels = array_get($config, 'panels', []);
        foreach ($panels as $panel => $enabled) {
            if ($panel === 'user') {
                $panel = 'auth';
            }
            if ($enabled === false) {
                continue;
            }

            $className = '\\'.__NAMESPACE__.'\Panels\\'.ucfirst($panel).'Panel';
            $class = new $className();

            $this->addPanel($class, $panel);
        }

        return $this;
    }

    /**
     * renderBlueScreen.
     *
     * @method renderBlueScreen
     *
     * @param  \Exception $exception
     *
     * @return string
     */
    public function renderBlueScreen(Exception $exception)
    {
        $error = error_get_last();
        if (in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR], true)) {
            $exception = Helpers::fixStack(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }

        ob_start();
        Helpers::improveException($exception);
        Debugger::getBlueScreen()->render($exception);
        $content = ob_get_clean();

        return $content;
    }

    /**
     * renderResponse.
     *
     * @method renderResponse
     * @param  \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderResponse(Response $response)
    {
        if (
            $response instanceof BinaryFileResponse ||
            $response instanceof StreamedResponse ||
            $response->isRedirection() === true
        ) {
            return $response;
        }

        $response->setContent($this->appendDebugbar($response->getContent()));

        return $response;
    }

    /**
     * appendDebugbar.
     *
     * @method appendDebugbar
     *
     * @param string $content
     *
     * @return string
     */
    public function appendDebugbar($content)
    {
        $barPanels = $this->renderPanel();
        $pos = strripos($content, '</body>');
        if ($pos !== false) {
            $content = substr($content, 0, $pos).$barPanels.substr($content, $pos);
        } else {
            $content .= $barPanels;
        }

        return $content;
    }

    /**
     * addPanel description.
     *
     * @method addPanel
     *
     * @param \Tracy\IBarPanel  $panel
     * @param string            $id
     *
     * @return $this
     */
    public function addPanel(IBarPanel $panel, $id)
    {
        $panel->setLaravel($this->app);
        $this->panels[$id] = $panel;

        return $this;
    }

    /**
     * getPanel.
     *
     * @method getPanel
     *
     * @param string  $id
     *
     * @return \Tracy\IBarPanel
     */
    public function getPanel($id)
    {
        return array_get($this->panels, $id);
    }

    /**
     * getPanels.
     *
     * @method getPanels
     *
     * @return array
     */
    public function getPanels()
    {
        return $this->panels;
    }

    /**
     * renderPanel.
     *
     * @method renderPanel
     *
     * @return string
     */
    public function renderPanel()
    {
        $this->sessionStart();
        $isAjax = $this->isAjax();
        Debugger::dispatch();
        ob_start();
        $bar = Debugger::getBar();
        foreach ($this->getPanels() as $panel) {
            if ($isAjax === true && $panel->supportAjax === false) {
                continue;
            }
            $bar->addPanel($panel);
        }
        $bar->render();
        $content = ob_get_clean();
        $this->sessionClose();

        return $content;
    }

    /**
     * obStart.
     *
     * @method obStart
     *
     * @return $this
     */
    public function obStart()
    {
        ob_start();

        return $this;
    }

    /**
     * obEnd.
     *
     * @method obEnd
     *
     * @return $this
     */
    public function obEnd()
    {
        ob_end_flush();

        return $this;
    }

    /**
     * sessionStart.
     *
     * @method sessionStart
     */
    public function sessionStart()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_httponly', '1');
            @session_start();
        }

        return $this;
    }

    /**
     * sessionClose.
     *
     * @method sessionClose
     */
    private function sessionClose()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        return $this;
    }

    /**
     * isAjax.
     *
     * @method isAjax
     *
     * @return bool
     */
    public function isAjax()
    {
        $request = (is_null($this->app) === false && is_null($this->app['request']) === false) ?
            $this->app['request'] : Request::capture();

        return $request->ajax();
    }

    /**
     * enable.
     *
     * @method enable
     *
     * @param  array$config
     * @return static
     */
    public static function enable($config = [], $sessionStart = true)
    {
        $config = array_merge([
            'editor'       => 'subl://open?url=file://%file&line=%line',
            'maxDepth'     => 4,
            'maxLength'    => 1000,
            'scream'       => true,
            'showLocation' => true,
            'strictMode'   => true,
            'panels'       => [
                'routing'  => false,
                'database' => true,
                'view'     => false,
                'event'    => false,
                'session'  => true,
                'request'  => true,
                'auth'     => true,
                'terminal' => false,
            ],
        ], $config);

        $tracy = (new static())->init($config);
        if ($sessionStart === true) {
            $tracy->sessionStart();
        }

        return $tracy;
    }
}
