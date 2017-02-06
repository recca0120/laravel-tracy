<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\RoutingPanel;

class RoutingPanelTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRender()
    {
        $panel = new RoutingPanel();
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
        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'rows' => array_merge([
                'uri' => $uri,
            ], $action),
        ], 'attributes', $panel);
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testRenderNative()
    {
        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $_SERVER['REQUEST_URI'] = '/foo';
        $panel = new RoutingPanel();
        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'rows' => [
                'uri' => $_SERVER['REQUEST_URI'],
            ],
        ], 'attributes', $panel);
    }
}
