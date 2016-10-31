<?php

namespace Recca0120\LaravelTracy;

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

        Debugger::$editor = array_get($config, 'editor', Debugger::$editor);
        Debugger::$maxDepth = array_get($config, 'maxDepth', Debugger::$maxDepth);
        Debugger::$maxLength = array_get($config, 'maxLength', Debugger::$maxLength);
        Debugger::$scream = array_get($config, 'scream', true);
        Debugger::$showLocation = array_get($config, 'showLocation', true);
        Debugger::$strictMode = array_get($config, 'strictMode', true);
        Debugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        Debugger::$editorMapping = array_get($config, 'editorMapping', []);
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



        return true;
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

        $config['enabled'] = array_get($config, 'enabled', false);
        $config['showBar'] = array_get($config, 'showBar', false);

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
