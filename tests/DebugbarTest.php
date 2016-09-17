<?php

use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Debugbar;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class DebugbarTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_response_is_binary_file()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\BinaryFileResponse');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response->shouldReceive('getStatusCode')->once()->andReturn(200);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_response_is_streamed()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\StreamedResponse');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response->shouldReceive('getStatusCode')->once()->andReturn(200);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_response_is_redirection()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(200)
            ->shouldReceive('isRedirection')->once()->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_request_is_ajax()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $contentType = '';
        $content = '<body></body>';
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(true);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(200)
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn($content)
            ->shouldReceive('setContent');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_empty_content_type_and_status_code_biggerthan_400()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $contentType = '';
        $content = '<body></body>';
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(400)
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn($content)
            ->shouldReceive('setContent');

        $headers->shouldReceive('get')->with('Content-Type')->once()->andReturn($contentType);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_accept_content_is_empty()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $contentType = 'text/html';
        $content = '<body></body>';
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(200)
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn($content)
            ->shouldReceive('setContent');

        $headers->shouldReceive('get')->with('Content-Type')->once()->andReturn($contentType);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_accept_content_type()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'accepts' => [
                'text/html',
            ],
        ];
        $contentType = 'text/html';
        $content = '';
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(200)
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn($content)
            ->shouldReceive('setContent');

        $headers->shouldReceive('get')->with('Content-Type')->andReturn($contentType);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_not_accept_content_type()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'accepts' => [
                'test/test',
            ],
        ];
        $contentType = 'text/html';
        $content = '<body></body>';
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(200)
            ->shouldReceive('isRedirection')->once()->andReturn(false);

        $headers->shouldReceive('get')->with('Content-Type')->once()->andReturn($contentType);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_initializebar()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'accepts' => [
                'text/html',
            ],
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

        $contentType = 'text/html';
        $content = '';
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $headers = m::mock('stdClass');
        $response->headers = $headers;

        $auth = m::mock('Illuminate\Contracts\Auth\Guard');
        $user = m::mock('stdClass');
        $user->username = 'username';
        $events = m::mock('Illuminate\Contracts\Event\Dispatcher');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user
            ->shouldReceive('getAuthIdentifier')->andReturn(0)
            ->shouldReceive('toArray')->andReturn([]);

        $auth->shouldReceive('user')->andReturn($user);

        $events->shouldReceive('listen')->andReturnUsing(function ($eventName, $closure) {
            $closure($eventName);
        });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(true);

        $response
            ->shouldReceive('getStatusCode')->once()->andReturn(200)
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn($content)
            ->shouldReceive('setContent');

        $headers->shouldReceive('get')->with('Content-Type')->andReturn($contentType);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_show_bar_is_false()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['showBar' => false];
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $response = m::mock('Symfony\Component\HttpFoundation\BinaryFileResponse');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);

        $request->shouldReceive('ajax')->once()->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertSame($response, $debugbar->render($response));
    }

    public function test_dispatch_assets()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);
        $request->shouldReceive('ajax')->once()->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $this->assertEmpty($debugbar->dispatchAssets());
    }

    public function test_dispatch()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $request = m::mock('Illuminate\Http\Request');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $session = m::mock('Illuminate\Session\SessionManager');
        $sessionHandler = new NullSessionHandler();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->shouldReceive('getConfig')->once()->andReturn($config);
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $app->shouldReceive('offsetGet')->with('session')->twice()->andReturn($session);

        $session->shouldReceive('getHandler')->once()->andReturn($sessionHandler);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $debugbar = new Debugbar($tracy, $request, $app);
        $debugbar->dispatch();
    }
}
