<?php

namespace Recca0120\LaravelTracy;

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
     * @param  array                                        $config
     * @param  \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * enable.
     *
     * @method enable
     *
     * @return bool
     */
    public function enable()
    {
        if ($this->config['enabled'] === false) {
            return false;
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
            'enabled' => true,
            'showBar' => true,
            'editor' => 'subl://open?url=file://%file&line=%line',
            'maxDepth' => 4,
            'maxLength' => 1000,
            'scream' => true,
            'showLocation' => true,
            'strictMode' => true,
            'panels' => [
                'routing' => false,
                'database' => true,
                'view' => false,
                'event' => false,
                'session' => true,
                'request' => true,
                'auth' => false,
                'terminal' => false,
            ],
        ], $config);

        $config['enabled'] = Arr::get($config, 'enabled', false);
        $config['showBar'] = Arr::get($config, 'showBar', false);

        $mode = Debugger::DETECT;
        switch ($config['enabled']) {
            case true:
                $mode = Debugger::DEVELOPMENT;
                break;
            case false:
                $mode = Debugger::PRODUCTION;
                break;
        }
        Debugger::enable($mode);

        $tracy = new static($config);
        $tracy->enable();
        $debugbar = new Debugbar($config);
        $debugbar->setupBar();
        $tracy->setDebugbar($debugbar);

        return $instance = $tracy;
    }
}
