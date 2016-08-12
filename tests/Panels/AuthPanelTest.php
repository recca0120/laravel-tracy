<?php

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Session\SessionInterface;
use Illuminate\Support\Str;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\AuthPanel;

class AuthPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testUsername()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->username = 'username';
        $auth = m::mock(Guard::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $panel = new AuthPanel();
        $session = m::mock(SessionInterface::class);
        $token = Str::random(40);

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

        $session
            ->shouldReceive('token')->once()->andReturn($token)
            ->shouldReceive('put')->with('_token', $token)->once();

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth)
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

    public function testEmail()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->email = 'email';
        $auth = m::mock(Guard::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $panel = new AuthPanel();
        $session = m::mock(SessionInterface::class);
        $token = Str::random(40);

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

        $session
            ->shouldReceive('token')->once()->andReturn($token)
            ->shouldReceive('put')->with('_token', $token)->once();

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth)
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

    public function testName()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock(stdClass::class);
        $user->name = 'name';
        $auth = m::mock(Guard::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $panel = new AuthPanel();
        $session = m::mock(SessionInterface::class);
        $token = Str::random(40);

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

        $session
            ->shouldReceive('token')->once()->andReturn($token)
            ->shouldReceive('put')->with('_token', $token)->once();

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth)
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
}
