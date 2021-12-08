<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\RequestPanel;
use Recca0120\LaravelTracy\Template;

class RequestPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $laravel = m::spy(new Application());
        $request = m::spy(Request::class);
        $request->expects('ip')->andReturns('foo.ip');
        $request->expects('ips')->andReturns('foo.ips');
        $request->expects('query')->andReturns('foo.query');
        $request->expects('all')->andReturns('foo.request');
        $request->expects('file')->andReturns('foo.file');
        $request->expects('cookie')->andReturns('foo.cookies');
        $request->expects('format')->andReturns('foo.format');
        $request->expects('path')->andReturns('foo.path');
        $request->expects('server')->andReturns('foo.server');

        $laravel['request'] = $request;

        $template = m::spy(new Template());
        $panel = new RequestPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with([
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
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
