<?php

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\AuthPanel;

class AuthPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_session_not_exists()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->username = 'username';
        $session = m::mock(stdClass::class);
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $auth->shouldReceive('getName')->once()->andReturn('foo');

        $session->shouldReceive('has')->with('foo')->once()->andReturn(false);

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->once()->with('auth')->andReturn($auth)
            ->shouldReceive('offsetGet')->once()->with('session')->andReturn($session);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_username()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->username = 'username';
        $session = m::mock(stdClass::class);
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->once()->andReturn(0)
            ->shouldReceive('toArray')->once()->andReturn([]);

        $auth
            ->shouldReceive('user')->once()->andReturn($user)
            ->shouldReceive('getName')->once()->andReturn('foo');

        $session->shouldReceive('has')->with('foo')->once()->andReturn(true);

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->once()->with('auth')->andReturn($auth)
            ->shouldReceive('offsetGet')->once()->with('session')->andReturn($session);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_email()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->email = 'email';
        $session = m::mock(stdClass::class);
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->once()->andReturn(0)
            ->shouldReceive('toArray')->once()->andReturn([]);

        $auth
            ->shouldReceive('user')->once()->andReturn($user)
            ->shouldReceive('getName')->once()->andReturn('foo');

        $session->shouldReceive('has')->with('foo')->once()->andReturn(true);

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->once()->andReturn($auth)
            ->shouldReceive('offsetGet')->with('session')->once()->andReturn($session);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_name()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->name = 'name';
        $session = m::mock(stdClass::class);
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->once()->andReturn(0)
            ->shouldReceive('toArray')->once()->andReturn([]);

        $auth
            ->shouldReceive('user')->once()->andReturn($user)
            ->shouldReceive('getName')->once()->andReturn('foo');

        $session->shouldReceive('has')->with('foo')->once()->andReturn(true);

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->once()->andReturn($auth)
            ->shouldReceive('offsetGet')->with('session')->once()->andReturn($session);

        $panel = new AuthPanel();
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
