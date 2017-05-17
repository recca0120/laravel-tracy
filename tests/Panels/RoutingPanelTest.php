<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\RoutingPanel;

class RoutingPanelTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testRender()
    {
        $panel = new RoutingPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $laravel->shouldReceive('offsetGet')->once()->with('router')->andReturn(
             $router = m::mock('Illuminate\Contracts\Routing\Registrar')
        );
        $router->shouldReceive('getCurrentRoute')->once()->andReturn(
             $currentRoute = m::mock('Illuminate\Routing\Route')
        );
        $currentRoute->shouldReceive('uri')->once()->andReturn($uri = 'foo');
        $currentRoute->shouldReceive('getAction')->once()->andReturn($action = ['foo' => 'bar']);

        $template->shouldReceive('setAttributes')->once()->with([
            'rows' => array_merge([
                'uri' => $uri,
            ], $action),
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testRenderNative()
    {
        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $_SERVER['REQUEST_URI'] = '/foo';
        $panel = new RoutingPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );

        $template->shouldReceive('setAttributes')->once()->with([
            'rows' => [
                'uri' => $_SERVER['REQUEST_URI'],
            ],
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
