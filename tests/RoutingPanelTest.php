<?php

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Routing\Registrar as RegistrarContract;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\RoutingPanel;

class RoutingPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_routing_panel_with_laravel()
    {
        $route = m::mock(stdClass::class)
            ->shouldReceive('uri')
            ->shouldReceive('getAction')->andReturn([])
            ->mock();

        $router = m::mock(RegistrarContract::class)
            ->shouldReceive('getCurrentRoute')->andReturn($route)
            ->mock();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('router')->andReturn($router)
            ->mock();

        $panel = new RoutingPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_routing_panel_without_laravel()
    {
        $panel = new RoutingPanel();
        $panel->getTab();
        $panel->getPanel();
    }

    public function test_routing_panel_without_laravel_with_host()
    {
        $panel = new RoutingPanel();
        $_SERVER['HTTP_HOST'] = 'http://localhost';
        $panel->getTab();
        $panel->getPanel();
    }
}
