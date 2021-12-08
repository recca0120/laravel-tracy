<?php

namespace Recca0120\LaravelTracy\Panels;

use Recca0120\LaravelTracy\Contracts\IAjaxPanel;
use Tracy\Debugger;

class EventPanel extends AbstractSubscribePanel implements IAjaxPanel
{
    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @var float
     */
    protected $totalTime = 0.0;

    /**
     * @var array
     */
    protected $events = [];

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
}
