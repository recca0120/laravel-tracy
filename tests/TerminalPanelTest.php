<?php

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\TerminalPanel;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_terminal_panel()
    {
        $controller = m::mock(TerminalController::class)
            ->shouldReceive('render')
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('make')->with(TerminalController::class)->andReturn($controller)
            ->shouldReceive('call')->with([$controller, 'index'], ['view' => 'panel'])->andReturn($controller)
            ->mock();

        $panel = new TerminalPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }
}
