<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\EventPanel;

class EventPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_event_panel()
    {
        $events = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->with('*', m::any())->andReturnUsing(function ($eventName, $closure) {
                $closure(['a' => '']);
            })
            ->shouldReceive('firing')->andReturn('event')
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->mock();

        $panel = new EventPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }
}
