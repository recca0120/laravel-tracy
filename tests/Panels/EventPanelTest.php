<?php

use Mockery as m;
use Recca0120\LaravelTracy\Panels\EventPanel;

class EventPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_render()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock('Illuminate\Contracts\Event\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $events
            ->shouldReceive('listen')->with('*', m::any())->andReturnUsing(function ($eventName, $closure) {
                $closure(['a' => '']);
            })
            ->shouldReceive('firing')->andReturn('event');

        $app
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $panel = new EventPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }
}
