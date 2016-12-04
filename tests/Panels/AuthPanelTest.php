<?php

use Mockery as m;
use Recca0120\LaravelTracy\Panels\AuthPanel;

class AuthPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_render_session_isnt_exists()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::spy('Illuminate\Session\SessionManager');
        $auth = m::spy('Illuminate\Contracts\Auth\Guard');
        $authName = 'foo.auth.name';
        $user = m::spy('stdClass');

        $user->name = 'foo.name';
        $user->username = 'foo.username';
        $user->email = 'foo.email';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $auth
            ->shouldReceive('getName')->andReturn($authName)
            ->shouldReceive('user')->andReturn($user);

        $session
            ->shouldReceive('has')->with($authName)->andReturn(false);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'name' => 'Guest',
            'user' => null,
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $auth->shouldHaveReceived('getName')->twice();

        $session->shouldHaveReceived('has')->with($authName)->twice();
    }

    public function test_render()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::spy('Illuminate\Session\SessionManager');
        $auth = m::spy('Illuminate\Contracts\Auth\Guard');
        $authName = 'foo.auth.name';
        $user = m::spy('stdClass');

        $user->name = 'foo.name';
        $user->username = 'foo.username';
        $user->email = 'foo.email';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $auth
            ->shouldReceive('getName')->andReturn($authName)
            ->shouldReceive('user')->andReturn($user);

        $session
            ->shouldReceive('has')->with($authName)->andReturn(true);

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn($user->email)
            ->shouldReceive('toArray')->andReturn(['foo.user.array']);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'name' => $user->email,
            'user' => [
                'foo.user.array',
            ],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $auth->shouldHaveReceived('getName')->twice();
        $auth->shouldHaveReceived('user')->twice();

        $session->shouldHaveReceived('has')->with($authName)->twice();

        $user->shouldHaveReceived('getAuthIdentifier')->twice();
        $user->shouldHaveReceived('toArray')->twice();
    }

    public function test_render_username()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::spy('Illuminate\Session\SessionManager');
        $auth = m::spy('Illuminate\Contracts\Auth\Guard');
        $authName = 'foo.auth.name';
        $user = m::spy('stdClass');

        $user->username = 'foo.username';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $auth
            ->shouldReceive('getName')->andReturn($authName)
            ->shouldReceive('user')->andReturn($user);

        $session
            ->shouldReceive('has')->with($authName)->andReturn(true);

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn(['foo.user.array']);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'name' => $user->username,
            'user' => [
                'foo.user.array',
            ],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $auth->shouldHaveReceived('getName')->twice();
        $auth->shouldHaveReceived('user')->twice();

        $session->shouldHaveReceived('has')->with($authName)->twice();

        $user->shouldHaveReceived('getAuthIdentifier')->twice();
        $user->shouldHaveReceived('toArray')->twice();
    }

    public function test_render_email()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::spy('Illuminate\Session\SessionManager');
        $auth = m::spy('Illuminate\Contracts\Auth\Guard');
        $authName = 'foo.auth.name';
        $user = m::spy('stdClass');

        $user->email = 'foo.email';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $auth
            ->shouldReceive('getName')->andReturn($authName)
            ->shouldReceive('user')->andReturn($user);

        $session
            ->shouldReceive('has')->with($authName)->andReturn(true);

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn(['foo.user.array']);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'name' => $user->email,
            'user' => [
                'foo.user.array',
            ],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $auth->shouldHaveReceived('getName')->twice();
        $auth->shouldHaveReceived('user')->twice();

        $session->shouldHaveReceived('has')->with($authName)->twice();

        $user->shouldHaveReceived('getAuthIdentifier')->twice();
        $user->shouldHaveReceived('toArray')->twice();
    }

    public function test_render_name()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::spy('Illuminate\Session\SessionManager');
        $auth = m::spy('Illuminate\Contracts\Auth\Guard');
        $authName = 'foo.auth.name';
        $user = m::spy('stdClass');

        $user->name = 'foo.name';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('session')->andReturn($session)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $auth
            ->shouldReceive('getName')->andReturn($authName)
            ->shouldReceive('user')->andReturn($user);

        $session
            ->shouldReceive('has')->with($authName)->andReturn(true);

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn(['foo.user.array']);

        $panel = new AuthPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'name' => $user->name,
            'user' => [
                'foo.user.array',
            ],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $auth->shouldHaveReceived('getName')->twice();
        $auth->shouldHaveReceived('user')->twice();

        $session->shouldHaveReceived('has')->with($authName)->twice();

        $user->shouldHaveReceived('getAuthIdentifier')->twice();
        $user->shouldHaveReceived('toArray')->twice();
    }
}
