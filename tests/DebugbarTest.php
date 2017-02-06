<?php

namespace Recca0120\LaravelTracy\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Debugbar;

class DebugbarTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testDispatchAssets()
    {
        $debugbar = new Debugbar(
            $config = [],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->dispatchAssets();
    }

    public function testDispatchContent()
    {
        $debugbar = new Debugbar(
            $config = [],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->dispatchContent();
    }

    public function testDispatchContentAndBarHasDispatchContentMethod()
    {
        $debugbar = new Debugbar(
            $config = [],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->dispatchContent();
    }

    public function testRenderAndShowBarIsFalse()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => false],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndResponseIsBinaryFileResponse()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $response = m::mock('Symfony\Component\HttpFoundation\BinaryFileResponse');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndResponseIsStreamedResponse()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $response = m::mock('Symfony\Component\HttpFoundation\StreamedResponse');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndResponseIsRedirectResponse()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndRequestIsAjax()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(true);
        $response->shouldReceive('getContent')->once()->andReturn($content = '<body>foo</body>');
        $bar->shouldReceive('render')->once()->andReturnUsing(function () {
            echo 'bar';
        });
        $response->shouldReceive('setContent')->once()->with('<body>foobar</body>');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndResponseStatusCodeBiggerThen400()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $response->headers = $headers = m::mock('stdClass');
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn('');
        $response->shouldReceive('getStatusCode')->once()->andReturn(400);
        $response->shouldReceive('getContent')->once()->andReturn($content = '<body>foo</body>');
        $bar->shouldReceive('render')->once()->andReturnUsing(function () {
            echo 'bar';
        });
        $response->shouldReceive('setContent')->once()->with('<body>foobar</body>');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndAcceptsIsEmpty()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $response->headers = $headers = m::mock('stdClass');
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn('');
        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $response->shouldReceive('getContent')->once()->andReturn($content = '<body>foo</body>');
        $bar->shouldReceive('render')->once()->andReturnUsing(function () {
            echo 'bar';
        });
        $response->shouldReceive('setContent')->once()->with('<body>foobar</body>');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndAcceptsIsAllow()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true, 'accepts' => ['text/html']],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $response->headers = $headers = m::mock('stdClass');
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn('text/html; charset=utf-8');
        $response->shouldReceive('getContent')->once()->andReturn($content = '<body>foo</body>');
        $bar->shouldReceive('render')->once()->andReturnUsing(function () {
            echo 'bar';
        });
        $response->shouldReceive('setContent')->once()->with('<body>foobar</body>');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderAndAcceptsIsNotAllow()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true, 'accepts' => ['application/json']],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $response->headers = $headers = m::mock('stdClass');
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn('text/html; charset=utf-8');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRender()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true, 'accepts' => ['application/json']],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $response->headers = $headers = m::mock('stdClass');
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn('text/html; charset=utf-8');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testRenderWithHtmlValidatorPanel()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true, 'accepts' => ['text/html']],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $htmlValidatorPanel = m::mock('Tracy\IBarPanel');
        $htmlValidatorPanel->shouldReceive('setLaravel')->once()->with($app);
        $debugbar->put($htmlValidatorPanel, 'html-validator');
        $debugbar->setBar($bar = m::mock('Tracy\Bar'));
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $response->headers = $headers = m::mock('stdClass');
        $headers->shouldReceive('get')->once()->with('Content-Type')->andReturn('text/html; charset=utf-8');
        $response->shouldReceive('getContent')->once()->andReturn($content = '<body>foo</body>');
        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $htmlValidatorPanel->shouldReceive('setHtml')->once()->with($content);
        $bar->shouldReceive('render')->once()->andReturnUsing(function () {
            echo 'bar';
        });
        $response->shouldReceive('setContent')->once()->with('<body>foobar</body>');
        $this->assertSame($response, $debugbar->render($response));
    }

    public function testLoadPanelsl()
    {
        $debugbar = new Debugbar(
            $config = ['showBar' => true, 'panels' => ['user' => true, 'terminal' => true]],
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application')
        );
        $request->shouldReceive('ajax')->once()->andReturn(true);
        $debugbar->loadPanels();
    }
}
