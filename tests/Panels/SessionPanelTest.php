<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\SessionInterface;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\SessionPanel;

class SessionPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_with_laravel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $session = m::mock(SessionInterface::class);
        $app = m::mock(Application::class.','.ArrayAccess::class);
        $panel = new SessionPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $session
            ->shouldReceive('getId')
            ->shouldReceive('getSessionConfig')->andReturn([])
            ->shouldReceive('all')->andReturn([]);
        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('session')->andReturn($session);
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_without_laravel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $panel = new SessionPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_without_laravel_andession_start()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        @session_start();
        $panel = new SessionPanel();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }
}
