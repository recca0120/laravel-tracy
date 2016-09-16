<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Mockery as m;
use Recca0120\LaravelTracy\BlueScreen;
use Recca0120\LaravelTracy\Debugbar;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Recca0120\LaravelTracy\ServiceProvider;
use Recca0120\LaravelTracy\Tracy;
use Recca0120\Terminal\ServiceProvider as TerminalServiceProvider;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_register()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = m::mock(Application::class.','.ArrayAccess::class);
        $config = m::mock(stdClass::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $config
            ->shouldReceive('get')->with('tracy', [])->andReturn([])
            ->shouldReceive('set')
            ->shouldReceive('get')->with('tracy.panels.terminal')->andReturn(true);

        $app
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('singleton')->with(Tracy::class, m::type(Closure::class))->andReturnUsing(function ($className, $closure) use ($app) {
                return $closure($app);
            })
            ->shouldReceive('singleton')->with(Debugbar::class, Debugbar::class)->once()
            ->shouldReceive('singleton')->with(BlueScreen::class, BlueScreen::class)->once()
            ->shouldReceive('register')->with(TerminalServiceProvider::class)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $serviceProvider = new ServiceProvider($app);
        $serviceProvider->register();
        $serviceProvider->provides();
    }

    public function test_boot()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = m::mock(Application::class.','.ArrayAccess::class);
        $tracy = m::mock(Tracy::class);
        $kernel = m::mock(Kernel::class);
        $handler = m::mock(ExceptionHandler::class);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('configPath')->andReturn(__DIR__)
            ->shouldReceive('extend')->with(ExceptionHandler::class, m::type(Closure::class))->andReturnUsing(function ($className, $closure) use ($handler, $app) {
                return $closure($handler, $app);
            })
            ->shouldReceive('make')->with(Handler::class, [
                'exceptionHandler' => $handler,
            ]);

        $tracy->shouldReceive('enable')->andReturn(true);

        $kernel
            ->shouldReceive('prependMiddleware')->with(Dispatch::class)->once()
            ->shouldReceive('pushMiddleware')->with(AppendDebugbar::class)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $serviceProvider = new ServiceProvider($app);
        $serviceProvider->boot($tracy, $kernel);
    }
}
