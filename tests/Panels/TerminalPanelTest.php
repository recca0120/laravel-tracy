<?php

use Mockery as m;
use Recca0120\LaravelTracy\Panels\TerminalPanel;

class TerminalPanelTest extends PHPUnit_Framework_TestCase
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

        $controller = m::mock('Recca0120\Terminal\Http\Controllers\TerminalController');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $panel = new TerminalPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $controller->shouldReceive('getContent');

        $app
            ->shouldReceive('make')->with('Recca0120\Terminal\Http\Controllers\TerminalController')->andReturn($controller)
            ->shouldReceive('call')->with([$controller, 'index'], ['view' => 'panel'])->andReturn($controller);

        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_not_found()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $controller = m::mock('Recca0120\Terminal\Http\Controllers\TerminalController');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $panel = new TerminalPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $controller->shouldReceive('getContent');

        $app->shouldReceive('call')->with([$controller, 'index'], ['view' => 'panel'])->andReturn($controller);

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
