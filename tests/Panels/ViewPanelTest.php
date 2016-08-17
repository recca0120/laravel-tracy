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

    public function test_render()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $events = m::mock(DispatcherContract::class);
        $view = m::mock(ViewContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $panel = new ViewPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $view
            ->shouldReceive('getName')->andReturn('name')
            ->shouldReceive('getData')->andReturn([])
            ->shouldReceive('getPath')->andReturn(__FILE__);

        $events
            ->shouldReceive('listen')->with('composing:*', m::any())->andReturnUsing(function ($eventName, $closure) use ($view) {
                return $closure($view);
            });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);
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
