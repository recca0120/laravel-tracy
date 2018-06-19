<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\AuthPanel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class AuthPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRenderFromGuard()
    {
        $panel = new AuthPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $laravel->shouldReceive('offsetExists')->once()->with('sentinel')->andReturn(false);

        $laravel->shouldReceive('offsetGet')->once()->with('auth')->andReturn(
            $auth = m::mock('Illuminate\Contracts\Auth\Guard')
        );

        $auth->shouldReceive('user')->once()->andReturn(
            $user = m::mock('stdClass')
        );
        $user->shouldReceive('toArray')->once()->andReturn($rows = ['username' => 'foo']);
        $user->shouldReceive('getAuthIdentifier')->once()->andReturn($id = 1);

        $template->shouldReceive('setAttributes')->once()->with([
            'id' => 'foo',
            'rows' => [
                'username' => 'foo',
            ],
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderFromSentinel()
    {
        $panel = new AuthPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
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

        $template->shouldReceive('setAttributes')->once()->with([
            'id' => 'foo',
            'rows' => [
                'username' => 'foo',
            ],
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderFromUserResolver()
    {
        $panel = new AuthPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );

        $panel->setUserResolver(function () {
            return [
                'username' => 'foo',
            ];
        });

        $template->shouldReceive('setAttributes')->once()->with([
            'id' => 'foo',
            'rows' => [
                'username' => 'foo',
            ],
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderFromUserResolverAndEmpty()
    {
        $panel = new AuthPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );

        $panel->setUserResolver(function () {
        });

        $template->shouldReceive('setAttributes')->once()->with([
            'id' => 'Guest',
            'rows' => [
            ],
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
