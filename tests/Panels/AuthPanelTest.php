<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Application;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\AuthPanel;
use Recca0120\LaravelTracy\Template;
use stdClass;

class AuthPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRenderFromGuard()
    {
        $laravel = m::spy(new Application());
        $auth = m::spy(Guard::class);
        $laravel['auth'] = $auth;

        $template = m::spy(Template::class);
        $panel = new AuthPanel($template);
        $panel->setLaravel($laravel);

        $user = m::mock('stdClass');
        $auth->expects('user')->andReturns($user);
        $user->expects('toArray')->andReturns($rows = ['username' => 'foo']);
        $user->expects('getAuthIdentifier')->andReturns($id = 1);

        $template->expects('setAttributes')->with([
            'id' => 'foo',
            'rows' => ['username' => 'foo'],
        ]);

        $template
            ->expects('render')
            ->twice()
            ->with(m::type('string'))
            ->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderFromSentinel()
    {
        $laravel = m::spy(new Application());
        $user = m::spy('stdClass');
        $sentinel = m::spy(stdClass::class);
        $sentinel->expects('check')->andReturn($user);
        $laravel['sentinel'] = $sentinel;

        $template = m::spy(Template::class);
        $panel = new AuthPanel($template);
        $panel->setLaravel($laravel);

        $user->expects('toArray')->andReturns($rows = ['username' => 'foo']);

        $template->expects('setAttributes')->with([
            'id' => 'foo',
            'rows' => ['username' => 'foo'],
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderFromUserResolver()
    {
        $template = m::spy(new Template());
        $panel = new AuthPanel($template);

        $panel->setUserResolver(function () {
            return ['username' => 'foo'];
        });
        $template->expects('setAttributes')->with([
            'id' => 'foo',
            'rows' => ['username' => 'foo'],
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderFromUserResolverAndEmpty()
    {
        $template = m::spy(Template::class);
        $panel = new AuthPanel($template);

        $panel->setUserResolver(static function () {
        });

        $template->expects('setAttributes')->with([
            'id' => 'Guest',
            'rows' => [],
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
