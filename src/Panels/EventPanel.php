<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\Debugger;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class EventPanel extends AbstractSubscriablePanel implements IAjaxPanel
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
     */
    protected function subscribe()
    {
        $id = get_class($this);
        Debugger::timer($id);
        $events = $this->laravel['events'];

        if (version_compare($this->laravel->version(), 5.4, '>=') === true) {
            $events->listen('*', function ($key, $payload) use ($id) {
                $execTime = Debugger::timer($id);
                $editorLink = static::editorLink(static::findSource());
                $this->totalTime += $execTime;
                $this->events[] = compact('execTime', 'key', 'payload', 'editorLink');
            });
        } else {
            $events->listen('*', function ($payload) use ($id, $events) {
                $execTime = Debugger::timer($id);
                $key = $events->firing();
                $editorLink = static::editorLink(static::findSource());
                $this->totalTime += $execTime;
                $this->events[] = compact('execTime', 'key', 'payload', 'editorLink');
            });
        }
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return [
            'counter' => $this->counter,
            'totalTime' => $this->totalTime,
            'events' => $this->events,
        ];
    }
}
