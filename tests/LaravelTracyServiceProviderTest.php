<?php

namespace Recca0120\LaravelTracy\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Recca0120\LaravelTracy\LaravelTracyServiceProvider;

class LaravelTracyServiceProviderTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $container = Container::getInstance();
        $container->bind('path.config', function () {
            return __DIR__;
        });
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testRegister()
    {
        $serviceProvider = new LaravelTracyServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $app->shouldReceive('offsetGet')->twice()->with('config')->andReturn(
            $config = m::mock('Illuminate\Contracts\Config\Repository, ArrayAccess')
        );
        $config->shouldReceive('get')->once()->with('tracy', [])->andReturn([]);
        $config->shouldReceive('set')->once()->with('tracy', m::type('array'));

        $app->shouldReceive('offsetGet')->once()->with('config')->andReturn($config = [
            'tracy' => [
                'panels' => [
                    'terminal' => true,
                ],
            ],
        ]);

        $app->shouldReceive('register')->once()->with('Recca0120\Terminal\TerminalServiceProvider');
        $app->shouldReceive('singleton')->once()->with('Tracy\BlueScreen', m::on(function ($closure) use ($app) {
            return $closure($app) instanceof \Tracy\BlueScreen;
        }));
        $app->shouldReceive('singleton')->once()->with('Tracy\Bar', m::on(function ($closure) use ($app) {
            $app->shouldReceive('offsetGet')->once()->with('request')->andReturn(
                $request = m::mock('Illuminate\Http\Request')
            );
            $request->shouldReceive('ajax')->once()->andReturn(false);
            $bar = $closure($app);
            $this->assertInstanceOf(\Tracy\Bar::class, $bar);

            return $bar instanceof \Tracy\Bar;
        }));
        $app->shouldReceive('singleton')->once()->with('Recca0120\LaravelTracy\DebuggerManager', m::on(function ($closure) use ($app) {
            $app->shouldReceive('make')->once()->with('Tracy\Bar')->andReturn(
                $bar = m::mock('Tracy\Bar')
            );
            $app->shouldReceive('make')->once()->with('Tracy\BlueScreen')->andReturn(
                $bar = m::mock('Tracy\BlueScreen')
            );
            $debugbarManager = $closure($app);
            $this->assertInstanceOf(\Recca0120\LaravelTracy\DebuggerManager::class, $debugbarManager);

            return $debugbarManager instanceof \Recca0120\LaravelTracy\DebuggerManager;
        }));

        $serviceProvider->register();
    }

    public function testBoot()
    {
        $serviceProvider = new LaravelTracyServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $app->shouldReceive('runningInConsole')->once()->andReturn(false);

        $view = m::mock('Illuminate\Contracts\View\Factory');
        $view
            ->shouldReceive('getEngineResolver')->once()->andReturnSelf()
            ->shouldReceive('resolve')->once()->with('blade')->andReturnSelf()
            ->shouldReceive('getCompiler')->once()->andReturnSelf()
            ->shouldReceive('directive')->once()->with('bdump', m::on(function ($closure) {
                $expression = '$foo';

                return $closure($expression) === "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            }));

        $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager');
        $debuggerManager->shouldReceive('enabled')->once()->andReturn(true);

        $app->shouldReceive('extend')->once()->with('Illuminate\Contracts\Debug\ExceptionHandler', m::on(function ($closure) use ($app) {
            $handler = $closure(
                $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
                $app
            );
            $this->assertInstanceOf(\Recca0120\LaravelTracy\Exceptions\Handler::class, $handler);

            return $handler instanceof \Recca0120\LaravelTracy\Exceptions\Handler;
        }));

        $kernel = m::mock('Illuminate\Contracts\Http\Kernel');
        $kernel->shouldReceive('prependMiddleware')->once()->with('Recca0120\LaravelTracy\Middleware\RenderBar');

        $serviceProvider->boot(
            $debuggerManager,
            $kernel,
            $view
        );
    }

    public function testBootRunningInConsole()
    {
        $serviceProvider = new LaravelTracyServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);

        $view = m::mock('Illuminate\Contracts\View\Factory');
        $view
            ->shouldReceive('getEngineResolver')->once()->andReturnSelf()
            ->shouldReceive('resolve')->once()->with('blade')->andReturnSelf()
            ->shouldReceive('getCompiler')->once()->andReturnSelf()
            ->shouldReceive('directive')->once()->with('bdump', m::on(function ($closure) {
                $expression = '$foo';
                $compiled = $closure($expression);
                $this->assertSame($compiled, "<?php \Tracy\Debugger::barDump({$expression}); ?>");

                return $compiled === "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            }));

        $serviceProvider->boot(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $kernel = m::mock('Illuminate\Contracts\Http\Kernel'),
            $view
        );
    }

    public function testProviders()
    {
        $serviceProvider = new LaravelTracyServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $this->assertSame([
            'Illuminate\Contracts\Debug\ExceptionHandler',
        ], $serviceProvider->provides());
    }
}
