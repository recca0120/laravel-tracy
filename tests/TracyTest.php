<?php

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery as m;
use Recca0120\LaravelTracy\Tracy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function test_is_enabled()
    {
        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('runningInConsole')->andReturn(false)
            ->mock();

        $tracy = new Tracy($config, $app);
        $tracy->sessionStart();
        $tracy->initialize();

        $panel = $tracy->getPanel('auth');
    }

    public function test_is_enabled_false()
    {
        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('runningInConsole')->andReturn(true)
            ->mock();

        $tracy = new Tracy($config, $app);
        $tracy->initialize();
    }

    public function test_render_blue_screen()
    {
        $tracy = new Tracy();
        $tracy->renderBlueScreen(new Exception());
    }

    public function test_ob_start_end()
    {
        $tracy = new Tracy();
        $tracy
            ->obStart()
            ->obEnd();
    }

    public function test_show_bar_false()
    {
        $content = 'test';
        $tracy = new Tracy(['showBar' => false]);
        $this->assertSame($tracy->appendDebugbar($content), $content);
    }

    public function test_static_enable()
    {
        $tracy = Tracy::enable();
        $tracy->getPanel('request');
        $tracy->getPanel('routing');
        $tracy->getPanel('database');
        $tracy->getPanel('session');
        $tracy->getPanel('request');
    }

    protected function sendResponse($response, $contentType = 'text/html', $config = [], $ajax = false)
    {
        $response->headers = m::mock(stdClass::class)
            ->shouldReceive('get')->with('Content-type')->andReturn($contentType)
            ->mock();

        $request = m::mock(Request::class)
            ->shouldReceive('ajax')->andReturn($ajax)
            ->mock();

        $app = m::mock(ApplicationContract::class);

        $tracy = new Tracy($config, $app, $request);

        $result = $tracy->renderResponse($response);
        $this->assertSame($result, $response);
    }

    public function test_binaryfile_response()
    {
        $response = m::mock(BinaryFileResponse::class);
        $this->sendResponse($response);
    }

    public function test_streamed_response()
    {
        $response = m::mock(StreamedResponse::class);
        $this->sendResponse($response);
    }

    public function test_redirect_response()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(true)
            ->mock();
        $this->sendResponse($response);
    }

    public function test_accept_content_type()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn('test')
            ->shouldReceive('setContent')->once()
            ->mock();

        $this->sendResponse($response, 'text/html', ['accepts' => ['text/html']]);
    }

    public function test_reject_content_type()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->mock();

        $this->sendResponse($response, 'application/json', ['accepts' => ['text/html']]);
    }

    public function test_accept_content_type_ajax()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn('test')
            ->shouldReceive('setContent')->once()
            ->mock();

        $this->sendResponse($response, 'text/html', ['accepts' => ['text/html']], true);
    }

    public function test_reject_content_type_ajax()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn('test')
            ->shouldReceive('setContent')->once()
            ->mock();

        $this->sendResponse($response, 'application/json', [
            'accepts' => ['text/html'],
        ], true);
    }

    public function test_without_accepts()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->mock();

        $this->sendResponse($response, 'application/json', []);
    }

    public function test_response_with_body()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->andReturn('<body></body>')
            ->shouldReceive('setContent')
            ->mock();

        $this->sendResponse($response, 'text/html', ['accepts' => ['text/html']]);
    }

    public function test_ajax_render_panel()
    {
        $config = [
            'showBar' => true,
            'panels'  => [
                'auth'     => true,
                'terminal' => true,
            ],
        ];

        $request = m::mock(Request::class)
            ->shouldReceive('ajax')->andReturn(true)
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('runningInConsole')->andReturn(false)
            ->shouldReceive('offsetGet')->with('request')->andReturnSelf()
            ->shouldReceive('ajax')->andReturn(true)
            ->mock();

        $tracy = new Tracy($config, $app, $request);
        $tracy->initialize();
        $tracy->renderPanel();
    }
}
