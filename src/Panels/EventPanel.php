<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Events\Dispatcher;
use Tracy\Debugger;

class EventPanel extends AbstractPanel
{
    public $attributes = [
        'count' => 0,
        'totalTime' => 0,
        'events' => [],
    ];

    public function subscribe(Dispatcher $events)
    {
        $key = get_class($this);
        $timer = Debugger::timer($key);
        $events->listen('*', function () use ($key) {
            $execTime = Debugger::timer($key);
            $dispatcher = static::findDispatcher();
            $editorLink = self::getEditorLink(static::findSource());
            $this->attributes['totalTime'] += $execTime;
            $this->attributes['events'][] = compact('execTime', 'dispatcher', 'editorLink');
        });
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
