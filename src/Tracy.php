<?php

namespace Recca0120\LaravelTracy;

use ErrorException;
use Exception;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tracy\Debugger;
use Tracy\Helpers;
use Tracy\IBarPanel;

class Tracy
{
    /**
     * $panels.
     *
     * @var array
     */
    public $panels = [];

    /**
     * init.
     *
     * @method init
     *
     * @param  array                                         $config
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function init($config = [], ApplicationContract $app = null)
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

        $isAjax = false;
        if (is_null($app) === false) {
            $isAjax = $app['request']->ajax();
        }

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

            if ($isAjax === true && $class->supportAjax === false) {
                continue;
            }

            $class->setLaravel($app);
            $this->addPanel($class, $panel);
        }
    }

    /**
     * renderException.
     *
     * @method renderException
     *
     * @param  \Exception $exception
     *
     * @return string
     */
    public function renderException(Exception $exception)
    {
        $error = error_get_last();
        if (in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR], true)) {
            $exception = Helpers::fixStack(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        }

        ob_start();
        Helpers::improveException($exception);
        Debugger::getBlueScreen()->render($exception);
        $content = ob_get_clean();
        $content .= $this->renderBarPanels();

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

        $content = $response->getContent();
        $barPanels = $this->renderBarPanels();
        $pos = strripos($content, '</body>');
        if ($pos !== false) {
            $content = substr($content, 0, $pos).$barPanels.substr($content, $pos);
        } else {
            $content .= $barPanels;
        }
        $response->setContent($content);

        return $response;
    }

    /**
     * addPanel description.
     *
     * @method addPanel
     *
     * @param \Tracy\IBarPanel  $panel
     * @param string            $id
     */
    public function addPanel(IBarPanel $panel, $id)
    {
        $bar = Debugger::getBar();
        $bar->addPanel($panel);
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
     * renderBarPanels.
     *
     * @method renderBarPanels
     *
     * @return string
     */
    public function renderBarPanels()
    {
        $this->startSession();
        ob_start();
        Debugger::getBar()->render();
        $content = ob_get_clean();
        $this->closeSession();

        return $content;
    }

    public function obStart()
    {
        ob_start();
    }

    public function obEnd()
    {
        ob_end_flush();
    }

    /**
     * startSession.
     *
     * @method startSession
     */
    private function startSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_httponly', '1');
            @session_start();
        }
    }

    /**
     * closeSession.
     *
     * @method closeSession
     */
    private function closeSession()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }
}
