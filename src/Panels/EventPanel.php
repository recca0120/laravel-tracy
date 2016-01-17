<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\Debugger;

class EventPanel extends AbstractPanel
{
    /**
     * All of the attributes set on the container.
     *
     * @var array
     */
    public $attributes = [
        'count'     => 0,
        'totalTime' => 0,
        'logs'      => [],
    ];

    /**
     * if laravel will auto subscribe.
     *
     * @return void
     */
    public function subscribe()
    {
        $key = get_class($this);
        $timer = Debugger::timer($key);
        $event = $this->app['events'];
        $event->listen('*', function ($params) use ($key, $event) {
            $execTime = Debugger::timer($key);
            $firing = $event->firing();
            $editorLink = static::getEditorLink(static::findSource());
            $this->attributes['count']++;
            $this->attributes['totalTime'] += $execTime;
            $this->attributes['logs'][] = compact('execTime', 'firing', 'params', 'editorLink');
        });
    }
}
