<?php

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Mockery as m;
use Recca0120\LaravelTracy\Debugbar;
use Recca0120\LaravelTracy\Tracy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(BinaryFileResponse::class);

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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(StreamedResponse::class);

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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);

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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;

        $auth = m::mock(GuardContract::class);
        $user = m::mock(stdClass::class);
        $user->username = 'username';
        $events = m::mock(DispatcherContract::class);

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
        $tracy = m::mock(Tracy::class);
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $response = m::mock(BinaryFileResponse::class);

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
}
