<?php

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Session\SessionInterface;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\SessionPanel;

class SessionPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_session_panel_with_laravel()
    {
        $session = m::mock(SessionInterface::class)
            ->shouldReceive('getId')
            ->shouldReceive('getSessionConfig')->andReturn([])
            ->shouldReceive('all')->andReturn([])
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->mock();

        $panel = new SessionPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_session_panel_without_laravel()
    {
        $panel = new SessionPanel();

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_session_panel_without_laravel_session_start()
    {
        @session_start();
        $panel = new SessionPanel();

        $panel->getTab();
        $panel->getPanel();
    }
}
