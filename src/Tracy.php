<?php

namespace Recca0120\LaravelTracy;

use Tracy\Bar;
use Tracy\Debugger;

class Tracy
{
    /**
     * $bar.
     *
     * @var \Tracy\Bar
     */
    protected $bar;

    /**
     * __construct.
     *
     * @param array $config
     * @param BarManager $barManager
     * @param \Tracy\Bar $bar
     */
    public function __construct($config = [], BarManager $barManager = null, Bar $bar = null)
    {
        $config = array_merge([
            'directory' => null,
            'email' => null,
            'emailSnooze' => null,
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
                'auth' => true,
                'terminal' => false,
            ],
        ], $config);

        $mode = $config['enabled'] === true ? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
        $config = DebuggerManager::init($config);
        Debugger::enable($mode, $config['directory'], $config['email']);
        if (is_null($config['emailSnooze']) === false) {
            Debugger::getLogger()->emailSnooze = $config['emailSnooze'];
        }

        $bar = $bar ?: Debugger::getBar();
        $barManager = $barManager ?: new BarManager($bar);

        $this->bar = $barManager->loadPanels($config['panels'])->getBar();
    }

    /**
     * __call.
     *
     * @param string $method
     * @param array $parameters
     * @return mix
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->bar, $method) === true) {
            return call_user_func_array([$this->bar, $method], $parameters);
        }

        return call_user_func_array(['\Tracy\Debugger', $method], $parameters);
    }

    /**
     * instance.
     *
     * @param  array$config
     * @return static
     */
    public static function instance($config = [], BarManager $barManager = null, Bar $bar = null)
    {
        static $instance;

        if (is_null($instance) === false) {
            return $instance;
        }

        return $instance = new static($config, $barManager, $bar);
    }
}
