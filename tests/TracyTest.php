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

    public function test_stramed_reponse()
    {
        $response = m::mock(StreamedResponse::class);
        $tracy = new Tracy();
        $result = $tracy->renderResponse($response);
        $this->assertSame($result, $response);
    }

    public function test_binary_file_reponse()
    {
        $response = m::mock(BinaryFileResponse::class);
        $tracy = new Tracy();
        $result = $tracy->renderResponse($response);
        $this->assertSame($result, $response);
    }

    public function test_redirect_reponse()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(true)
            ->mock();

        $tracy = new Tracy();
        $result = $tracy->renderResponse($response);
        $this->assertSame($result, $response);
    }

    public function test_response_with_body()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->andReturn('<body></body>')
            ->shouldReceive('setContent')
            ->mock();

        $tracy = new Tracy();
        $result = $tracy->renderResponse($response);
        $this->assertSame($result, $response);
    }

    public function test_response_without_body()
    {
        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->andReturn('')
            ->shouldReceive('setContent')
            ->mock();

        $tracy = new Tracy();
        $result = $tracy->renderResponse($response);
        $this->assertSame($result, $response);
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

    public function test_ajax_render_panel()
    {
        $config = [
            'showBar' => true,
            'panels'  => [
                'auth'     => true,
                'terminal' => true,
            ],
        ];
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('runningInConsole')->andReturn(false)
            ->shouldReceive('offsetGet')->with('request')->andReturnSelf()
            ->shouldReceive('ajax')->andReturn(true)
            ->mock();

        $tracy = new Tracy($config, $app);
        $tracy->initialize();
        $tracy->renderPanel();
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
}
