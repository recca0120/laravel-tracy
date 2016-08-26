<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Support\Arr;
use Tracy\Debugger;

class Tracy
{
    /**
     * $debugbar.
     *
     * @var \Recca0120\LaravelTracy\Debugbar
     */
    protected $debugbar;

    /**
     * __construct.
     *
     * @method __construct
     *
     * @param  array                                            $config
     * @param  \Illuminate\Contracts\Foundation\Application     $app
     */
    public function __construct($config = [], ApplicationContract $app = null)
    {
        $this->config = $config;
        $this->app = $app;
    }

    /**
     * dispatch.
     *
     * @method dispatch
     *
     * @return bool
     */
    public function dispatch($test = false)
    {
        if ($this->isRunningInConsole() === true || Arr::get($this->config, 'enabled', true) === false) {
            return false;
        }

        if ($test === false) {
            if (Debugger::getBar()->dispatchAssets() === true) {
                exit;
            }

            if (session_status() === PHP_SESSION_ACTIVE) {
                Debugger::dispatch();
            }
        }

        Debugger::$editor = Arr::get($this->config, 'editor', Debugger::$editor);
        Debugger::$maxDepth = Arr::get($this->config, 'maxDepth', Debugger::$maxDepth);
        Debugger::$maxLength = Arr::get($this->config, 'maxLength', Debugger::$maxLength);
        Debugger::$scream = Arr::get($this->config, 'scream', true);
        Debugger::$showLocation = Arr::get($this->config, 'showLocation', true);
        Debugger::$strictMode = Arr::get($this->config, 'strictMode', true);
        Debugger::$time = Arr::get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        Debugger::$editorMapping = Arr::get($this->config, 'editorMapping', []);

        return true;
    }

    /**
     * getConfig.
     *
     * @method getConfig
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * setDebugbar.
     *
     * @method setDebugbar
     *
     * @param  Debugbar    $debugbar
     */
    public function setDebugbar(Debugbar $debugbar)
    {
        $this->debugbar = $debugbar;

        return $this;
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
        return $this->debugbar->get($id);
    }

    /**
     * isRunningInConsole.
     *
     * @method isRunningInConsole
     *
     * @return bool
     */
    protected function isRunningInConsole()
    {
        return is_null($this->app) === false && $this->app->runningInConsole() === true;
    }

    /**
     * instance.
     *
     * @method instance
     *
     * @param  array$config
     * @return static
     */
    public static function instance($config = [])
    {
        static $instance;

        if (is_null($instance) === false) {
            return $instance;
        }

        $config = array_merge([
            'enabled'      => true,
            'showBar'      => true,
            'editor'       => 'subl://open?url=file://%file&line=%line',
            'maxDepth'     => 4,
            'maxLength'    => 1000,
            'scream'       => true,
            'showLocation' => true,
            'strictMode'   => true,
            'editorMapping' => [],
            'panels'       => [
                'routing'  => false,
                'database' => true,
                'view'     => false,
                'event'    => false,
                'session'  => true,
                'request'  => true,
                'auth'     => false,
                'terminal' => false,
            ],
        ], $config);
        Debugger::enable();
        $tracy = new static($config);
        $debugbar = new Debugbar($tracy);
        $debugbar->setupBar();
        $tracy->setDebugbar($debugbar);

        return $instance = $tracy;
    }
}
