<?php

namespace Recca0120\LaravelTracy\Tests\Http\Controllers;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Http\Controllers\LaravelTracyController;

class LaravelTracyControllerTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @runInSeparateProcess
     */
    public function testIndex()
    {
        $controller = new LaravelTracyController();

        $request = m::mock('Illuminate\Http\Request');

        $request->shouldReceive('session')->once()->andReturn(
            $session = m::mock('Illuminate\Contracts\Session\Session')
        );

        $session->shouldReceive('reflash')->once();

        $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager');
        $debuggerManager->shouldReceive('dispatchAssets')->once()->andReturn([
            $headers = ['foo' => 'bar'],
            $content = 'foo',
        ]);

        $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $responseFactory->shouldReceive('stream')->with(m::on(function ($callback) use ($content) {
            ob_start();
            $callback();
            $output = ob_get_clean();

            if (function_exists('xdebug_get_headers') === true) {
                $this->assertTrue(in_array('foo: bar', xdebug_get_headers(), true));
            }

            return $content === $output;
        }), 200)->andReturn(
            $response = m::mock('Symfony\Component\HttpFoundation\Response')
        );

        $type = 'foo';

        $this->assertSame($response, $controller->index(
            $request,
            $responseFactory,
            $debuggerManager,
            $type
        ));
    }
}
