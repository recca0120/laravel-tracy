<?php

namespace Recca0120\LaravelTracy\Tests;

use Mockery as m;
use Recca0120\LaravelTracy\LaravelTracyServiceProvider;

class LaravelTracyServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
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

        $app->shouldReceive('offsetGet')->once()->with('config')->andReturn([
            'tracy.panels.terminal' => true,
        ]);
        $app->shouldReceive('register')->once()->with('Recca0120\Terminal\TerminalServiceProvider');
        $app->shouldReceive('singleton')->once()->with(
            'Recca0120\LaravelTracy\Session\StoreWrapper', 'Recca0120\LaravelTracy\Session\StoreWrapper'
        );
        $app->shouldReceive('singleton')->once()->with(
            'Recca0120\LaravelTracy\BlueScreen', 'Recca0120\LaravelTracy\BlueScreen'
        );
        $app->shouldReceive('singleton')->once()->with(
            'Recca0120\LaravelTracy\Debugbar', m::on(function ($closure) use ($app) {
                $app->shouldReceive('offsetGet')->once()->with('config')->andReturn([]);
                $app->shouldReceive('offsetGet')->once()->with('request')->andReturn(
                    $request = m::mock('Illuminate\Http\Request')
                );
                $request->shouldReceive('ajax')->andReturn(false);
                $closure($app);

                return true;
            })
        );

        $serviceProvider->register();
    }

    public function testBoot()
    {
        $serviceProvider = new LaravelTracyServiceProvider(
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $app->shouldReceive('runningInConsole')->once()->andReturn(true);
        $app->shouldReceive('configPath')->once();

        $app->shouldReceive('offsetGet')->once()->with('config')->andReturn(['tracy.enabled' => true]);
        $app->shouldReceive('extend')->once()->with('Illuminate\Contracts\Debug\ExceptionHandler', m::on(function ($closure) use ($app) {
            $app->shouldReceive('make')->once()->with('Recca0120\LaravelTracy\BlueScreen')->andReturn(
                m::mock('Recca0120\LaravelTracy\BlueScreen')
            );
            $closure(
                $exceptionHandler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler'),
                $app
            );

            return true;
        }));
        $kernel = m::mock('Illuminate\Contracts\Http\Kernel');
        $kernel->shouldReceive('prependMiddleware')->once()->with('Recca0120\LaravelTracy\Middleware\Dispatch');

        $view = m::mock('Illuminate\Contracts\View\Factory');
        $view
            ->shouldReceive('getEngineResolver')->once()->andReturnSelf()
            ->shouldReceive('resolve')->once()->with('blade')->andReturnSelf()
            ->shouldReceive('getCompiler')->once()->andReturnSelf()
            ->shouldReceive('directive')->once()->with('bdump', m::on(function ($closure) {
                $expression = '$foo';

                return $closure($expression) === "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            }));
        $serviceProvider->boot(
            $kernel,
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
