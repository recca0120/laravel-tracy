<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use Illuminate\Http\Request;
use Recca0120\LaravelTracy\Panels\RequestPanel;

class RequestPanelTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRender()
    {
        $panel = new RequestPanel();
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $laravel->shouldReceive('offsetGet')->once()->with('request')->andReturn(
            $request = m::mock('Illuminate\Http\Request')
        );

        $request
            ->shouldReceive('ip')->once()->andReturn('foo.ip')
            ->shouldReceive('ips')->once()->andReturn('foo.ips')
            ->shouldReceive('query')->once()->andReturn('foo.query')
            ->shouldReceive('all')->once()->andReturn('foo.request')
            ->shouldReceive('file')->once()->andReturn('foo.file')
            ->shouldReceive('cookie')->once()->andReturn('foo.cookies')
            ->shouldReceive('format')->once()->andReturn('foo.format')
            ->shouldReceive('path')->once()->andReturn('foo.path')
            ->shouldReceive('server')->once()->andReturn('foo.server');

        $panel->getTab();
        $panel->getPanel();
        $this->assertAttributeSame([
            'rows' => [
                'ip' => 'foo.ip',
                'ips' => 'foo.ips',
                'query' => 'foo.query',
                'request' => 'foo.request',
                'file' => 'foo.file',
                'cookies' => 'foo.cookies',
                'format' => 'foo.format',
                'path' => 'foo.path',
                'server' => 'foo.server',
            ],
        ], 'attributes', $panel);
    }
}
