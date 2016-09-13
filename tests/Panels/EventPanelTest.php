<?php

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
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

        $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('\Illuminate\Contracts\Foundation\Application'.','.'\ArrayAccess');

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
            ->shouldReceive('version')->andReturn(5.2)
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
