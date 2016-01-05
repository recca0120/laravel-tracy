<?php

namespace Recca0120\LaravelTracy\Panels;

use Recca0120\LaravelTracy\Helper;
use Tracy\Debugger;

class EventPanel extends AbstractPanel
{
    public $attributes = [
        'count'     => 0,
        'totalTime' => 0,
        'logs'      => [],
    ];

    public function subscribe()
    {
        $key = get_class($this);
        $timer = Debugger::timer($key);
        $event = $this->app['events'];
        $event->listen('*', function ($params) use ($key, $event) {
            $execTime = Debugger::timer($key);
            $firing = $event->firing();
            $editorLink = Helper::getEditorLink(Helper::findSource());
            $this->attributes['count']++;
            $this->attributes['totalTime'] += $execTime;
            $this->attributes['logs'][] = compact('execTime', 'firing', 'params', 'editorLink');
        });
    }
}
