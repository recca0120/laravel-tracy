<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Exception;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\TerminalPanel;

class TerminalPanelTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testRender()
    {
        $panel = new TerminalPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $laravel->shouldReceive('make')->once()->with('Recca0120\Terminal\Http\Controllers\TerminalController')->andReturn(
             $controller = m::mock('Recca0120\Terminal\Http\Controllers\TerminalController')
        );
        $laravel->shouldReceive('call')->once()->with([$controller, 'index'], ['view' => 'panel'])->andReturn(
            $response = m::mock('Symfony\Component\HttpFoundation\Response')
        );
        $response->shouldReceive('getContent')->once()->andReturn(
            $terminal = 'foo'
        );

        $template->shouldReceive('setAttributes')->once()->with([
            'terminal' => $terminal,
        ]);

        $template->shouldReceive('minify')->once()->with(false);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testException()
    {
        $panel = new TerminalPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );

        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $laravel->shouldReceive('make')->once()->andThrow(new Exception('foo'));

        $template->shouldReceive('setAttributes')->once()->with([
            'terminal' => 'foo',
        ]);

        $template->shouldReceive('minify')->once()->with(false);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
