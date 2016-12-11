<?php

use Mockery as m;
use Illuminate\Http\Request;
use Recca0120\LaravelTracy\Debugbar;

class DebugbarTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_dispatch_assets()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => false];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertEmpty($debugbar->dispatchAssets());
        $request->shouldHaveReceived('ajax')->once();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_dispatch()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => false];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertEmpty($debugbar->dispatch());
        $request->shouldHaveReceived('ajax')->once();
    }

    public function test_setup_bar()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = [
            'showBar' => false,
            'panels' => [
                'routing' => false,
                'database' => false,
                'view' => false,
                'event' => false,
                'session' => false,
                'request' => false,
                'user' => true,
                'terminal' => true,
                'html-validator' => true,
            ],
        ];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('ajax')->andReturn(true);

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertInstanceOf('Tracy\Bar', $debugbar->setup());
        $request->shouldHaveReceived('ajax')->once();
    }

    public function test_render_when_showbar_is_false()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => false];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
    }

    public function test_render_when_response_is_binary_file_response()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\BinaryFileResponse');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200);

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
    }

    public function test_render_when_response_is_streamed_response()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\StreamedResponse');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200);

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
    }

    public function test_render_when_response_is_redirection()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('isRedirection')->andReturn(true);

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
        $response->shouldHaveReceived('isRedirection')->once();
    }

    public function test_render_when_response_is_ajax()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getContent')->andReturn('foo.content');

        $request
            ->shouldReceive('ajax')->andReturn(true);

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
        $response->shouldHaveReceived('getContent')->once();
        $response->shouldHaveReceived('setContent')->with('foo.content')->once();
    }

    public function test_render_when_status_code_bigger_then_400()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(400)
            ->shouldReceive('getContent')->andReturn('foo.content');

        $headers
            ->shouldReceive('get')->with('Content-Type')->andReturn('');

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
        $headers->shouldHaveReceived('get')->with('Content-Type')->once();
        $response->shouldHaveReceived('getContent')->once();
        $response->shouldHaveReceived('setContent')->with('foo.content')->once();
    }

    public function test_render_when_accept_is_empty()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true, 'accepts' => []];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getContent')->andReturn('foo.content');

        $headers
            ->shouldReceive('get')->with('Content-Type')->andReturn('text/html; charset=utf-8');

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
        $headers->shouldHaveReceived('get')->with('Content-Type')->once();
        $response->shouldHaveReceived('getContent')->once();
        $response->shouldHaveReceived('setContent')->with('foo.content')->once();
    }

    public function test_render_when_accept_is_text_html()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true, 'accepts' => ['text/html']];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getContent')->andReturn('foo.content');

        $headers
            ->shouldReceive('get')->with('Content-Type')->andReturn('text/html; charset=utf-8');

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
        $headers->shouldHaveReceived('get')->with('Content-Type')->once();
        $response->shouldHaveReceived('getContent')->once();
        $response->shouldHaveReceived('setContent')->with('foo.content')->once();
    }

    public function test_render_when_deny_all()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true, 'accepts' => ['text/css']];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200);

        $headers
            ->shouldReceive('get')->with('Content-Type')->andReturn('text/html; charset=utf-8');

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
    }

    public function test_render_with_body_and_html_validator_panel()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $config = ['showBar' => true, 'accepts' => ['text/html'], 'panels' => ['html-validator' => true]];
        $request = m::spy('Illuminate\Http\Request');
        $app = m::spy('Illuminate\Contracts\Foundation\Application');
        $response = m::spy('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getContent')->andReturn('<body>foo.content</body>');

        $headers
            ->shouldReceive('get')->with('Content-Type')->andReturn('text/html; charset=utf-8');

        $debugbar = new Debugbar($config, $request, $app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($response, $debugbar->render($response));
        $request->shouldHaveReceived('ajax')->once();
        $response->shouldHaveReceived('getStatusCode')->once();
        $headers->shouldHaveReceived('get')->with('Content-Type')->once();
        $response->shouldHaveReceived('getContent')->once();
        $response->shouldHaveReceived('setContent')->with('<body>foo.content</body>')->once();
    }
}
