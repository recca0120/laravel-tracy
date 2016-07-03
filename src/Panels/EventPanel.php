<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\Debugger;

class EventPanel extends AbstractPanel
{
    /**
     * $counter.
     *
     * @var int
     */
    protected $counter = 0;

    /**
     * $totalTime.
     *
     * @var float
     */
    protected $totalTime = 0.0;

    /**
     * $events.
     *
     * @var array
     */
    protected $events = [];

    /**
     * subscribe.
     *
     * @method subscribe
     */
    public function subscribe()
    {
        $key = get_class($this);
        $timer = Debugger::timer($key);
        $this->laravel['events']->listen('*', function ($params) use ($key) {
            $execTime = Debugger::timer($key);
            $firing = $this->laravel['events']->firing();
            $editorLink = self::editorLink(self::findSource());
            $this->totalTime += $execTime;
            $this->events[] = compact('execTime', 'firing', 'params', 'editorLink');
        });
    }

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'counter'   => $this->counter,
            'totalTime' => $this->totalTime,
            'events'    => $this->events,
        ];
    }
}
