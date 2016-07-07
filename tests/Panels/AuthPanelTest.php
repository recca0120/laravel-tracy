<?php

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\AuthPanel;

class AuthPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_auth_panel_username()
    {
        $user = m::mock(stdClass::class)
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([])
            ->mock();

        $user->username = 'username';

        $auth = m::mock(Guard::class)
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user)
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth)
            ->mock();

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_auth_panel_email()
    {
        $user = m::mock(stdClass::class)
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([])
            ->mock();

        $user->email = 'email';

        $auth = m::mock(Guard::class)
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user)
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth)
            ->mock();

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_auth_panel_name()
    {
        $user = m::mock(stdClass::class)
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([])
            ->mock();

        $user->name = 'name';

        $auth = m::mock(Guard::class)
            ->shouldReceive('check')->andReturn(true)
            ->shouldReceive('user')->andReturn($user)
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth)
            ->mock();

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }
}
