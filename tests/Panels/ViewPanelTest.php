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
        $events = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->with('composing:*', m::any())->andReturnUsing(function ($eventName, $closure) {
                $view = m::mock(ViewContract::class)
                    ->shouldReceive('getName')->andReturn('name')
                    ->shouldReceive('getData')->andReturn([])
                    ->shouldReceive('getPath')->andReturn(__FILE__)
                    ->mock();

                $closure($view);
            })
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->mock();

        $panel = new ViewPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }
}
