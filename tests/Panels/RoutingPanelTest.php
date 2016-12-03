<?php

use Mockery as m;
use Recca0120\LaravelTracy\Panels\RoutingPanel;

class RoutingPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_render_with_laravel()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $router = m::spy('Illuminate\Contracts\Routing\Registrar');
        $currentRoute = m::spy('Illuminate\Routing\Route');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('router')->andReturn($router);

        $router
            ->shouldReceive('getCurrentRoute')->andReturn($currentRoute);

        $currentRoute
            ->shouldReceive('uri')->andReturn('foo.uri')
            ->shouldReceive('getAction')->andReturn(['foo.action']);

        $panel = new RoutingPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'uri' => 'foo.uri',
            'action' => ['foo.action'],
        ], $panel->getAttributes());

        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $router->shouldHaveReceived('getCurrentRoute')->twice();
        $currentRoute->shouldHaveReceived('uri')->twice();
        $currentRoute->shouldHaveReceived('getAction')->twice();
    }

    public function test_render_without_laravel_not_found()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $panel = new RoutingPanel();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'uri' => 404,
            'action' => [],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));
    }

    public function test_render_without_laravel()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $backupServer = $_SERVER;
        $_SERVER['HTTP_HOST'] = 'http://localhost';
        $_SERVER['REQUEST_URI'] = 'foo.request_uri';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $panel = new RoutingPanel();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'uri' => 'foo.request_uri',
            'action' => [],
        ], $panel->getAttributes());
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $_SERVER = $backupServer;
    }
}
