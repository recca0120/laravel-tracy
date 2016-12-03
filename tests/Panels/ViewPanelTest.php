<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\ViewPanel;

class ViewPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_subscribe()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Event\Dispatcher');
        $view = m::spy('Illuminate\Contracts\View\View');

        $testData = [];
        for ($i = 0; $i < 100; ++$i) {
            $testData[] = $i;
        }
        $viewData = [
            '__env' => [],
            '__app' => [],
            'collection' => new Collection($testData),
            'array' => $testData,
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $events
            ->shouldReceive('listen')->with('composing:*', m::type('Closure'))->andReturnUsing(function ($eventName, $closure) use ($view) {
                $closure($view);
            });

        $view
            ->shouldReceive('getName')->andReturn('foo')
            ->shouldReceive('getData')->andReturn($viewData)
            ->shouldReceive('getPath')->andReturn('foo.path');

        $panel = new ViewPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo', Arr::get($panel->getAttributes(), 'views.0.name'));
        $this->assertSame(50, Arr::get($panel->getAttributes(), 'views.0.data.collection')->count());
        $this->assertSame(50, count(Arr::get($panel->getAttributes(), 'views.0.data.array')));
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $app->shouldHaveReceived('offsetGet')->with('events')->once();
        $events->shouldHaveReceived('listen')->with('composing:*', m::type('Closure'))->once();
        $view->shouldHaveReceived('getName')->once();
        $view->shouldHaveReceived('getData')->once();
        $view->shouldHaveReceived('getPath')->once();
    }
}
