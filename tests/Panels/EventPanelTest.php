<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\EventPanel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class EventPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $panel = new EventPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('events')->andReturn(
            $events = m::mock('lluminate\Contracts\Event\Dispatcher')
        );
        $laravel->shouldReceive('version')->once()->andReturn(5.4);
        $events->shouldReceive('listen')->once()->with('*', m::on(function ($closure) {
            $closure(
                'foo',
                ['foo' => 'bar']
            );

            return true;
        }));
        $panel->setLaravel($laravel);

        $template->shouldReceive('setAttributes')->once()->with(m::type('array'));
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderAndLaravel53()
    {
        $panel = new EventPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('events')->andReturn(
            $events = m::mock('lluminate\Contracts\Event\Dispatcher')
        );
        $laravel->shouldReceive('version')->once()->andReturn(5.3);
        $events->shouldReceive('firing')->once()->andReturn('foo');
        $events->shouldReceive('listen')->once()->with('*', m::on(function ($closure) {
            $closure(['foo' => 'bar']);

            return true;
        }));
        $panel->setLaravel($laravel);

        $template->shouldReceive('setAttributes')->once()->with(m::type('array'));
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
