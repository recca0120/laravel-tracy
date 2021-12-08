<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\ViewPanel;
use Recca0120\LaravelTracy\Template;

class ViewPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('version')->andReturns(5.4);

        $collection = m::spy(new Collection());
        $events = m::spy('Illuminate\Contracts\Event\Dispatcher');
        $events->expects('listen')->with('composing:*', m::on(function ($closure) use ($collection) {
            $event = m::spy('stdClass');
            $event->expects('getName')->andReturns($name = 'foo');

            $collection->expects('count')->andReturns(100);
            $collection->expects('take')->andReturns(50)->andReturnSelf();

            $event->expects('getData')->andReturns([
                range(1, 100), $collection,
            ]);
            $event->expects('getPath')->andReturns('');
            $closure('foo', [$event]);

            return true;
        }));
        $laravel['events'] = $events;

        $template = m::spy(new Template());
        $panel = new ViewPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with([
            'rows' => [[
                'name' => 'foo',
                'data' => [range(1, 50), $collection],
                'path' => '',
            ]],
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderAndLaravel50()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('version')->andReturns(5.3);

        $collection = m::spy(new Collection());
        $events = m::spy('Illuminate\Contracts\Event\Dispatcher');
        $events->expects('listen')->with('composing:*', m::on(function ($closure) use ($collection) {
            $event = m::spy('stdClass');
            $event->expects('getName')->andReturns($name = 'foo');

            $collection->expects('count')->andReturns(100);
            $collection->expects('take')->andReturns(50)->andReturnSelf();

            $event->expects('getData')->andReturns([
                '__env' => [],
                'app' => [],
                range(1, 100),
                $collection,
            ]);
            $event->expects('getPath')->andReturns('');
            $closure($event);

            return true;
        }));

        $laravel['events'] = $events;

        $template = m::spy(new Template());
        $panel = new ViewPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with([
            'rows' => [[
                'name' => 'foo',
                'data' => [range(1, 50), $collection],
                'path' => '',
            ]],
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
