<?php

use Illuminate\Support\Arr;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\SessionPanel;

class SessionPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_render_with_laravel()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        @session_start();
        $_SESSION = ['test' => 'test'];
        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::spy('Illuminate\Session\SessionInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app->shouldReceive('offsetGet')->with('session')->andReturn($session);

        $session
            ->shouldReceive('getId')->andReturn('foo.id')
            ->shouldReceive('getSessionConfig')->andReturn('foo.config')
            ->shouldReceive('all')->andReturn(['foo.all']);

        $panel = new SessionPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'sessionId' => 'foo.id',
            'config' => 'foo.config',
            'laravelSession' => ['foo.all'],
            'nativeSession' => ['test' => 'test'],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $session->shouldHaveReceived('getId')->twice();
        $session->shouldHaveReceived('getSessionConfig')->twice();
        $session->shouldHaveReceived('all')->twice();

        @session_write_close();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_render_without_laravel()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        @session_start();
        $_SESSION = ['test' => 'test'];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $panel = new SessionPanel();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(['test' => 'test'], Arr::get($panel->getAttributes(), 'nativeSession'));
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        @session_write_close();
    }
}
