<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Events\BeforeBarRender;
use Recca0120\LaravelTracy\Panels\HtmlValidatorPanel;
use Recca0120\LaravelTracy\Template;
use Symfony\Component\HttpFoundation\Response;

class HtmlValidatorPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $laravel = m::spy(new Application());

        $html = '<!DOCTYPE html><html><head><title>title</title></head><body></body></html>';
        $events = m::spy(Dispatcher::class);
        $events->expects('listen')
            ->with(BeforeBarRender::class, m::on(function ($closure) use ($html) {
                $response = m::spy(Response::class);
                $response->expects('getContent')->andReturns($html);
                $closure(new BeforeBarRender(m::mock(Request::class), $response));

                return true;
            }));

        $laravel['events'] = $events;

        $template = m::spy(new Template());
        $panel = new HtmlValidatorPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with([
            'severenity' => [
                LIBXML_ERR_WARNING => 'Warning',
                LIBXML_ERR_ERROR => 'Error',
                LIBXML_ERR_FATAL => 'Fatal error',
            ],
            'counter' => 0,
            'errors' => [],
            'html' => $html,
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
