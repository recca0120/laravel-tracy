<?php

namespace Recca0120\LaravelTracy;

use Tracy\Debugger;

class Tracy
{
    /**
     * instance.
     *
     * @method instance
     *
     * @param  array$config
     *
     * @return static
     */
    public static function instance($config = [])
    {
        static $instance;

        if (is_null($instance) === false) {
            return $instance;
        }

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
                'auth' => false,
                'auth-sentinel' => false,
                'terminal' => false,
            ],
        ], $config);

        $mode = $config['enabled'] === true ? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
        $config = DebuggerManager::init($config);

        Debugger::enable($mode, $config['directory'], $config['email']);
        if (is_null($config['emailSnooze']) === false) {
            Debugger::getLogger()->emailSnooze = $config['emailSnooze'];
        }

        return (new BarManager(Debugger::getBar()))
            ->loadPanels($config['panels'])
            ->getBar();
    }
}
