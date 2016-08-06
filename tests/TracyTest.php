<?php

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

    public function testNotRunningInConsole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = $this->getConfig();
        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->once()->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app);
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

        $tracy->startBuffering();
        $tracy->stopBuffering();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
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

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->once()->andReturn(false);
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

    public function testAjax()
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
            ->shouldReceive('setContent')->once();

        $tracy = new Tracy($config, $app, $request);

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

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()
            ->shouldReceive('setContent')->once();
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

    public function testEmptyContentType()
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

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $response
            ->shouldReceive('isRedirection')->once()->andReturn(false)
            ->shouldReceive('getContent')->once()->andReturn('<body></body>')
            ->shouldReceive('setContent')->once();
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

    public function testAcceptContentTypeWithoutBody()
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
            ->shouldReceive('setContent')->once();

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

    public function testHtmlValidatorPanel()
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
