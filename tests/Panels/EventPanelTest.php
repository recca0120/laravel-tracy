<?php

use Mockery as m;
use Illuminate\Support\Arr;
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
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Event\Dispatcher');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $events
            ->shouldReceive('listen')->with('*', m::type('Closure'))->andReturnUsing(function ($eventName, $closure) {
                $closure([]);
            })
            ->shouldReceive('firing')->andReturn('foo.firing');

        $panel = new EventPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo.firing', Arr::get($panel->getAttributes(), 'events.0.firing'));
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $events->shouldHaveReceived('listen')->once();
        $events->shouldHaveReceived('firing')->once();
    }
}
