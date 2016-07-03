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
    public function tearDown()
    {
        m::close();
    }

    public function test_init()
    {
        $request = m::mock(Request::class)
            ->shouldReceive('ajax')->andReturn(true)
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('offsetGet')->with('request')->andReturn($request)
            ->mock();

        $tracy = new Tracy();
        $tracy->init([
            'panels' => [
                'user'     => true,
                'terminal' => true,
                'events'   => false,
            ],
        ], $app);
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

    public function test_render_exception()
    {
        $tracy = new Tracy();
        $tracy->renderException(new Exception());
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
