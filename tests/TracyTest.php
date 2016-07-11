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

    public function testNotRunningInConsole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $tracy = new Tracy($config, $app);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(false);
        $tracy->sessionStart();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue($tracy->initialize());
    }

    public function testRunningInConsole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);
        $tracy = new Tracy($config, $app);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(true);
        $tracy->sessionStart();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

       $this->assertFalse($tracy->initialize());
    }

    public function testBlueScreen()
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

    public function testObEnd()
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

        $excepted = 'foo';
        $tracy->obStart();
        echo $excepted;
        $content = ob_get_contents();
        $tracy->obEnd();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $content);
    }

    public function testHiddenBar()
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

    public function testAjaxPanel()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('runningInConsole')->andReturn(false)
            ->shouldReceive('offsetGet')->with('request')->andReturnSelf()
            ->shouldReceive('ajax')->andReturn(true);
        $request->shouldReceive('ajax')->andReturn(true);
        $tracy->initialize();
        $tracy->renderPanel();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
    }

    public function testBinaryfileResponse()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $headers->shouldReceive('get')->with('Content-type')->andReturn('text/html');
        $request->shouldReceive('ajax')->andReturn(false);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testStreamedResponse()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $headers->shouldReceive('get')->with('Content-type')->andReturn('text/html');
        $request->shouldReceive('ajax')->andReturn(false);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testRedirectResponse()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response->shouldReceive('isRedirection')->andReturn(true);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testAcceptContentType()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn('text/html');
        $request->shouldReceive('ajax')->andReturn(false);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testNotAcceptContentType()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn('application/json');
        $request->shouldReceive('ajax')->andReturn(false);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testAcceptContentTypeAndAjax()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn('text/html');
        $request->shouldReceive('ajax')->andReturn(true);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testNotAcceptContentTypeWithAjax()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn('application/json');
        $request->shouldReceive('ajax')->andReturn(true);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testWithoutAccepts()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn('application/json');
        $request->shouldReceive('ajax')->andReturn(false);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testAcceptContentTypeWithBody()
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
        $tracy = new Tracy($config, $app, $request);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->andReturn('<body></body>')
            ->shouldReceive('setContent');
        $headers->shouldReceive('get')->with('Content-type')->andReturn('text/html');
        $request->shouldReceive('ajax')->andReturn(false);
        $excepted = $tracy->renderResponse($response);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($excepted, $response);
    }

    public function testStandalone()
    {
        $tracy = Tracy::enable();
        $tracy->getPanel('request');
        $tracy->getPanel('routing');
        $tracy->getPanel('database');
        $tracy->getPanel('session');
        $tracy->getPanel('request');
    }
}
