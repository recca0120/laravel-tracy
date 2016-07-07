<?php

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\RequestPanel;

class RequestPanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_request_panel_with_laravel()
    {
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('request')->andReturn(Request::capture())
            ->mock();

        $panel = new RequestPanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_request_panel_without_laravel()
    {
        $panel = new RequestPanel();

        $panel->getTab();
        $panel->getPanel();
    }
}
