<?php

use Mockery as m;
use Illuminate\Support\Arr;
use Recca0120\LaravelTracy\Panels\TerminalPanel;

class TerminalPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_render_with_terminal()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $controller = m::spy('Recca0120\Terminal\Http\Controllers\TerminalController');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('make')->with('Recca0120\Terminal\Http\Controllers\TerminalController')->andReturn($controller)
            ->shouldReceive('call')->with([$controller, 'index'], ['view' => 'panel'])->andReturn($response);

        $response
            ->shouldReceive('getContent')->andReturn('foo.content');

        $panel = new TerminalPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame('foo.content', Arr::get($panel->getAttributes(), 'html'));
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $app->shouldHaveReceived('make')->with('Recca0120\Terminal\Http\Controllers\TerminalController')->twice();
        $app->shouldHaveReceived('call')->with([$controller, 'index'], ['view' => 'panel'])->twice();
        $response->shouldHaveReceived('getContent')->twice();
    }

    public function test_render_throw_exception()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('make')->with('Recca0120\Terminal\Http\Controllers\TerminalController')->andReturnUsing(function () {
                throw new Exception();
            });

        $panel = new TerminalPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(null, Arr::get($panel->getAttributes(), 'html'));

        $app->shouldHaveReceived('make')->with('Recca0120\Terminal\Http\Controllers\TerminalController')->once();
    }
}
