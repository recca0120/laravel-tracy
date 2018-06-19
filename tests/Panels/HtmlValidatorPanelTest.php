<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Events\BeforeBarRender;
use Recca0120\LaravelTracy\Panels\HtmlValidatorPanel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class HtmlValidatorPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $panel = new HtmlValidatorPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );

        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('events')->andReturn(
            $events = m::mock('Illuminate\Contracts\Event\Dispatcher')
        );

        $html = '<!DOCTYPE html><html><head><title>title</title></head><body></body></html>';
        $events->shouldReceive('listen')->once()->with('Recca0120\LaravelTracy\Events\BeforeBarRender', m::on(function ($closure) use ($html) {
            $response = m::mock('Symfony\Component\HttpFoundation\Response');
            $response->shouldReceive('getContent')->once()->andReturn($html);
            $closure(new BeforeBarRender(
                m::mock('Illuminate\Http\Request'),
                $response
            ));

            return true;
        }));

        $panel->setLaravel($laravel);

        $template->shouldReceive('setAttributes')->once()->with([
            'severenity' => [
                LIBXML_ERR_WARNING => 'Warning',
                LIBXML_ERR_ERROR => 'Error',
                LIBXML_ERR_FATAL => 'Fatal error',
            ],
            'counter' => 0,
            'errors' => [],
            'html' => $html,
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
