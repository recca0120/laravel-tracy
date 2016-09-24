<?php

use Illuminate\Contracts\Foundation\Application;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\TerminalPanel;
use Recca0120\Terminal\Http\Controllers\TerminalController;

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

        $controller = m::mock(TerminalController::class);
        $app = m::mock(Application::class.','.ArrayAccess::class);
        $panel = new TerminalPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $controller->shouldReceive('getContent');

        $app
            ->shouldReceive('make')->with(TerminalController::class)->andReturn($controller)
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

        $controller = m::mock(TerminalController::class);
        $app = m::mock(Application::class.','.ArrayAccess::class);
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
