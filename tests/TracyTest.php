<?php

use Illuminate\Contracts\Auth\Guard as GuardContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery as m;
use Recca0120\LaravelTracy\Tracy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tracy\IBarPanel;

class TracyTest extends PHPUnit_Framework_TestCase
{
    protected function getConfig()
    {
        return [
            'enabled'      => true,
            'showBar'      => true,
            'accepts'      => [
                'text/html',
            ],
            'editor'       => 'subl://open?url=file://%file&line=%line',
            'maxDepth'     => 4,
            'maxLength'    => 1000,
            'scream'       => true,
            'showLocation' => true,
            'strictMode'   => true,
            'panels'       => [
                'routing'  => false,
                'database' => false,
                'view'     => false,
                'event'    => false,
                'session'  => false,
                'request'  => false,
                'user'     => true,
                'terminal' => false,
            ],
        ];
    }

    public function tearDown()
    {
        m::close();
    }

    public function test_not_running_in_console()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
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
            ->shouldReceive('runningInConsole')->once()->andReturn(false)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app);
        $this->assertTrue($tracy->initialize());
    }

    public function test_running_in_console()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $request = m::mock(Request::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);

        $request->shouldReceive('ajax')->once()->andReturn(false);
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

       $this->assertFalse($tracy->initialize());
    }

    public function test_blue_screen()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $tracy = new Tracy();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy->renderBlueScreen(new Exception());
    }

    public function test_ob_end()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $tracy = new Tracy();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $tracy->startBuffering();
        $tracy->stopBuffering();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
    }

    public function test_hidden_bar()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $tracy = new Tracy(['showBar' => false]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $excepted = 'foo';

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $tracy->appendDebugbar($excepted));
    }

    public function test_ajax_panel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'showBar' => true,
            'panels'  => [
                'auth'     => true,
                'terminal' => true,
            ],
        ];
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
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
            ->shouldReceive('runningInConsole')->once()->andReturn(false)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('auth')->andReturn($auth);

        $request
            ->shouldReceive('ajax')->once()->andReturn(true)
            ->shouldReceive('has')->once()->andReturn(false);

        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy->initialize();
        $tracy->renderPanel();
    }

    public function test_binaryfile_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $response = m::mock(BinaryFileResponse::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $request->shouldReceive('ajax')->once()->andReturn(false);
        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_streamed_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $response = m::mock(StreamedResponse::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $request->shouldReceive('ajax')->once()->andReturn(false);
        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_redirect_response()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response->shouldReceive('isRedirection')->once()->andReturn(true);
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_ajax()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $request->shouldReceive('ajax')->once()->andReturn(true);
        $response
            ->shouldReceive('getContent')->once()
            ->shouldReceive('setContent')->once()
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getStatusCode')->once()->andReturn(200);

        $tracy = new Tracy($config, $app, $request);

        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_accept_content_type()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()
            ->shouldReceive('setContent')->once()
            ->shouldReceive('getStatusCode')->once()->andReturn(200);
        $headers->shouldReceive('get')->with('Content-type')->once()->andReturn('text/html');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_not_accept_content_type()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response->shouldReceive('isRedirection')->once()->andReturn(false);
        $headers->shouldReceive('get')->with('Content-type')->once()->andReturn('application/json');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_without_accepts()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent')
            ->shouldReceive('getStatusCode')->once()->andReturn(200);
        $headers->shouldReceive('get')->with('Content-type')->andReturn('application/json');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_empty_content_type()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getStatusCode')->andReturn(500)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn(null);
        $request->shouldReceive('ajax')->andReturn(false);
        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_accept_content_type_with_body()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn('<body></body>')
            ->shouldReceive('setContent')->once()
            ->shouldReceive('getStatusCode')->once()->andReturn(200);
        $headers->shouldReceive('get')->with('Content-type')->once()->andReturn('text/html');
        $request->shouldReceive('ajax')->once()->andReturn(false);
        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_accept_content_type_without_body()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $response = m::mock(Response::class);
        $headers = m::mock(stdClass::class);
        $response->headers = $headers;
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn('')
            ->shouldReceive('setContent')->once()
            ->shouldReceive('getStatusCode')->once()->andReturn(200);

        $headers->shouldReceive('get')->with('Content-type')->once()->andReturn('text/html');
        $request->shouldReceive('ajax')->once()->andReturn(false);

        $tracy = new Tracy($config, $app, $request);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function test_html_validator_panel()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = ['accepts' => ['text/html']];
        $request = m::mock(Request::class);
        $app = m::mock(ApplicationContract::class);
        $htmlValidatorPanel = m::mock(IBarPanel::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $excepted = 'foo';

        $request->shouldReceive('ajax')->once()->andReturn(false);

        $htmlValidatorPanel
            ->shouldReceive('setLaravel')->with($app)->once()->andReturnSelf()
            ->shouldReceive('setHtml')->with($excepted)->once()->andReturnSelf();

        $tracy = new Tracy($config, $app, $request);
        $tracy->addPanel($htmlValidatorPanel, 'html-validator');
        $tracy->appendDebugbar($excepted);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
    }
}
