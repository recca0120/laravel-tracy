<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Events\Dispatcher;
use Recca0120\LaravelTracy\Helper;
use Tracy\Debugger;

class EventPanel extends AbstractPanel
{
    public $attributes = [
        'count'     => 0,
        'totalTime' => 0,
        'events'    => [],
    ];

    public function subscribe(Dispatcher $event)
    {
        $key   = get_class($this);
        $timer = Debugger::timer($key);
        $event->listen('*', function ($params) use ($key, $event) {
            $execTime = Debugger::timer($key);
            // $dispatcher = static::findDispatcher();
            // $firing = array_get($dispatcher, 'dispatcher.args.0');
            $firing = $event->firing();
            $editorLink = Helper::getEditorLink(Helper::findSource());
            $this->attributes['totalTime'] += $execTime;
            $this->attributes['events'][] = compact('execTime', 'firing', 'params', 'editorLink');
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
