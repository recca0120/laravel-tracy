<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Events\Dispatcher;

class EventPanel extends AbstractPanel
{
    public $data = [];

    public $time;

    private static $initialize = false;

    public function __construct()
    {
        if (static::$initialize === false) {
            static::$initialize = true;
            $app = app();
            $this->time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
            $events = $app['events'];
            $events->listen('*', function () {
                $dispatcher = static::findDispatcher();
                if (empty($dispatcher) === false) {
                    $endTime = microtime(true);
                    $this->data['events'][] = [
                        'time' => round(($endTime - $this->time), 2),
                        'dispatcher' => static::findDispatcher(),
                    ];
                    $this->timer = microtime(true);
                }
            });
        }
    }

    public static function findDispatcher()
    {
        $backtrace = debug_backtrace();
        foreach ($backtrace as $trace) {
            $object = array_get($trace, 'object');
            if ($object instanceof Dispatcher) {
                // if (empty($trace['args']['1']) === false) {
                //     foreach ($trace['args']['1'] as $key => $value) {
                //         $trace['args']['1'][$key] = (is_object($value) === true) ? get_class($value) : $value;
                //     }
                // }

                return $trace;
            }
        }
    }
}
