<?php

namespace Recca0120\LaravelTracy\Tests\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\DebuggerManager;
use Recca0120\LaravelTracy\Http\Controllers\LaravelTracyController;
use Symfony\Component\HttpFoundation\Response;

class LaravelTracyControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @runInSeparateProcess
     */
    public function testBar()
    {
        $controller = new LaravelTracyController();

        $request = m::spy(Request::class);

        $request->allows('get')->andReturns('foo');

        $debuggerManager = m::spy(DebuggerManager::class);
        $debuggerManager->expects('dispatchAssets')->andReturns([
            ['foo' => 'bar'], $content = 'foo',
        ]);

        $responseFactory = m::spy(ResponseFactory::class);
        $responseFactory->allows('stream')->with(m::on(function ($callback) use ($content) {
            ob_start();
            $callback();
            $output = ob_get_clean();

            if (function_exists('xdebug_get_headers') === true && in_array('foo: bar', xdebug_get_headers(), true) === false) {
                return false;
            }

            return $content === $output;
        }), 200)->andReturns($response = m::spy(Response::class));

        $this->assertSame($response, $controller->bar($debuggerManager, $request, $responseFactory));
    }
}
