<?php

use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\RequestPanel;

class RequestPanelTest extends PHPUnit_Framework_TestCase
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
        $request = m::spy('Illuminate\Http\Request');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('request')->andReturn($request);

        $request
            ->shouldReceive('ip')->andReturn('foo.ip')
            ->shouldReceive('ips')->andReturn('foo.ips')
            ->shouldReceive('query')->andReturn('foo.query')
            ->shouldReceive('all')->andReturn('foo.request')
            ->shouldReceive('file')->andReturn('foo.file')
            ->shouldReceive('cookie')->andReturn('foo.cookies')
            ->shouldReceive('format')->andReturn('foo.format')
            ->shouldReceive('path')->andReturn('foo.path')
            ->shouldReceive('server')->andReturn('foo.server');

        $panel = new RequestPanel();
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'request' => [
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
        ], $panel->getAttributes());

        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $request->shouldHaveReceived('ip')->twice();
        $request->shouldHaveReceived('ips')->twice();
        $request->shouldHaveReceived('query')->twice();
        $request->shouldHaveReceived('all')->twice();
        $request->shouldHaveReceived('file')->twice();
        $request->shouldHaveReceived('cookie')->twice();
        $request->shouldHaveReceived('format')->twice();
        $request->shouldHaveReceived('path')->twice();
        $request->shouldHaveReceived('server')->twice();
    }

    public function test_render_without_laravel()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $backupRequest = $_REQUEST;
        $backupFiles = $_FILES;
        $backupCookie = $_COOKIE;
        $backupServer = $_SERVER;

        $_FILES = ['file' => 'file'];
        $_REQUEST = ['request' => 'request'];
        $_COOKIE = ['cookie' => 'cookie'];
        $_SERVER = [
            'REMOTE_ADDR' => 'foo.ip',
            'QUERY_STRING' => 'foo.query_string',
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $panel = new RequestPanel();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'request' => [
                'ip' => 'foo.ip',
                'ips' => 'foo.ip',
                'query' => 'foo.query_string',
                'request' => $_REQUEST,
                'file' => $_FILES,
                'cookies' => $_COOKIE,
                'server' => $_SERVER,
            ],
        ], $panel->getAttributes());

        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $_REQUEST = $backupRequest;
        $_FILES = $backupFiles;
        $_COOKIE = $backupCookie;
        $_SERVER = $backupServer;
    }
}
