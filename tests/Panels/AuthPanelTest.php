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

    public function test_laravel52()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->username = 'username';
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([]);

        $auth
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user);

        $events->shouldReceive('listen')->with('Illuminate\Auth\Events\Login', m::type(Closure::class))->andReturnUsing(function ($eventName, $closure) {
            $closure($eventName);
        });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

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

    public function test_laravel51()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->username = 'username';
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([]);

        $auth
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user);

        $events->shouldReceive('listen')->with('auth.login', m::type(Closure::class))->andReturnUsing(function ($eventName, $closure) {
            $closure($eventName);
        });

        $app
            ->shouldReceive('version')->andReturn(5.1)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

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
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([]);

        $auth
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user);

        $events->shouldReceive('listen')->andReturnUsing(function ($eventName, $closure) {
            $closure($eventName);
        });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

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
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([]);

        $auth
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user);

        $events->shouldReceive('listen')->andReturnUsing(function ($eventName, $closure) {
            $closure($eventName);
        });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

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
        $auth = m::mock(Guard::class);
        $events = m::mock(DispatcherContract::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([]);

        $auth
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user);

        $events->shouldReceive('listen')->with('Illuminate\Auth\Events\Login', m::type(Closure::class))->andReturnUsing(function ($eventName, $closure) {
            $closure('Illuminate\Auth\Events\Login');
        });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

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
