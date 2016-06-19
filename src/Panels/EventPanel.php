<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
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
     * setLaravel.
     *
     * @method setLaravel
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     *
     * @return self;
     */
    public function setLaravel(ApplicationContract $laravel)
    {
        parent::setLaravel($laravel);
        $key = get_class($this);
        $timer = Debugger::timer($key);
        $this->laravel->events->listen('*', function ($params) use ($key) {
            $execTime = Debugger::timer($key);
            $firing = $this->laravel->events->firing();
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
