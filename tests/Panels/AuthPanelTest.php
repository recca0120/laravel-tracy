<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\AuthPanel;

class AuthPanelTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testRenderFromGuard()
    {
        $panel = new AuthPanel();
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $laravel->shouldReceive('offsetExists')->once()->with('sentinel')->andReturn(false);

        $laravel->shouldReceive('offsetGet')->once()->with('session')->andReturn(
            $sessionManager = m::mock('Illuminate\Session\SessionManager')
        );
        $laravel->shouldReceive('offsetGet')->once()->with('auth')->andReturn(
            $auth = m::mock('Illuminate\Contracts\Auth\Guard')
        );
        $auth->shouldReceive('getName')->once()->andReturn($name = 'foo');
        $sessionManager->shouldReceive('has')->once()->with($name)->andReturn(true);
        $auth->shouldReceive('user')->once()->andReturn(
            $user = m::mock('stdClass')
        );
        $user->shouldReceive('toArray')->once()->andReturn($rows = ['username' => 'foo']);
        $user->shouldReceive('getAuthIdentifier')->once()->andReturn($id = 1);

        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'id' => 'foo',
            'rows' => [
                'username' => 'foo',
            ],
        ], 'attributes', $panel);
    }

    public function testRenderFromSentinel()
    {
        $panel = new AuthPanel();
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $laravel->shouldReceive('offsetExists')->once()->with('sentinel')->andReturn(true);
        $laravel->shouldReceive('offsetGet')->once()->with('sentinel')->andReturn(
            $auth = m::mock('stdClass')
        );
        $auth->shouldReceive('check')->once()->andReturn(
            $user = m::mock('stdClass')
        );
        $user->shouldReceive('toArray')->once()->andReturn($rows = ['username' => 'foo']);

        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'id' => 'foo',
            'rows' => [
                'username' => 'foo',
            ],
        ], 'attributes', $panel);
    }
}
