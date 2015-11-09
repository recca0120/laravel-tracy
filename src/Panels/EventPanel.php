<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Events\Dispatcher;
use Tracy\Debugger;

class EventPanel extends AbstractPanel
{
    public $attributes = [
        'count' => 0,
        'totalTime' => 0,
        'events' => [],
    ];

    public function subscribe(Dispatcher $event)
    {
        $key = get_class($this);
        $timer = Debugger::timer($key);
        $event->listen('*', function ($params) use ($key, $event) {
            $execTime = Debugger::timer($key);
            $firing = $event->firing();
            $editorLink = self::getEditorLink(static::findSource());
            $this->attributes['totalTime'] += $execTime;
            $this->attributes['events'][] = compact('execTime', 'firing', 'params', 'editorLink');
        });
    }
}
