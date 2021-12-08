<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Exception;
use Illuminate\Foundation\Application;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\TerminalPanel;
use Recca0120\LaravelTracy\Template;
use Recca0120\Terminal\Http\Controllers\TerminalController;
use Symfony\Component\HttpFoundation\Response;

class TerminalPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $laravel = m::spy(new Application());
        $controller = m::spy(TerminalController::class);
        $terminal = 'foo';
        $response = m::spy(Response::class);
        $response->expects('getContent')->andReturns($terminal);

        $laravel->expects('make')->with(TerminalController::class)->andReturns($controller);
        $laravel->expects('call')->with([$controller, 'index'], ['view' => 'panel'])->andReturns($response);

        $template = m::spy(new Template());
        $panel = new TerminalPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(['terminal' => $terminal]);
        $template->expects('minify')->with(false);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testException()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('make')->andThrow(new Exception('foo'));

        $template = m::spy(new Template());
        $panel = new TerminalPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(['terminal' => 'foo']);
        $template->expects('minify')->with(false);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
