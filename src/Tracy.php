<?php

namespace Recca0120\LaravelTracy;

use Tracy\Debugger;
use Illuminate\Support\Arr;

class Tracy
{
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
        $debugbar = new Debugbar($config);
        $debugbar->setupBar();

        return $instance = $debugbar;
    }
}
