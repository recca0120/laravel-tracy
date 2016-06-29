<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\View\View as ViewContract;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\ViewPanel;

class ViewPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_view_panel()
    {
        $listeners = [];
        $events = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->andReturnUsing(function ($eventName, $closure) use (&$listeners) {
                $listeners[$eventName] = $closure;
            })
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->mock();

        $panel = new ViewPanel();
        $panel->setLaravel($app);

        $view = m::mock(ViewContract::class)
            ->shouldReceive('getName')->andReturn('name')
            ->shouldReceive('getData')->andReturn([])
            ->shouldReceive('getPath')->andReturn(__FILE__)
            ->mock();

        $listeners['composing:*']($view);

        $panel->getTab();
        $panel->getPanel();
    }
}
