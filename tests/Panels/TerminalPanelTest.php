<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use Recca0120\LaravelTracy\Panels\TerminalPanel;

class TerminalPanelTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRender()
    {
        $panel = new TerminalPanel();
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
            $html = 'foo'
        );
        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'html' => $html,
        ], 'attributes', $panel);
    }

    public function testException()
    {
        $panel = new TerminalPanel();
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'html' => null,
        ], 'attributes', $panel);
    }
}
