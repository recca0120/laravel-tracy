<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Mockery as m;
use Recca0120\LaravelTracy\ServiceProvider;
use Recca0120\LaravelTracy\Tracy;

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

        $app = m::mock('Illuminate\Contracts\Foundation\Application'.','.'ArrayAccess');
        $config = m::mock('stdClass');
        $session = m::mock('Illuminate\Session\SessionManager');

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
            ->shouldReceive('singleton')->with('Recca0120\LaravelTracy\Tracy', m::type('Closure'))->andReturnUsing(function ($className, $closure) use ($app) {
                return $closure($app);
            })
            ->shouldReceive('singleton')->with('Recca0120\LaravelTracy\Debugbar', 'Recca0120\LaravelTracy\Debugbar')->once()
            ->shouldReceive('singleton')->with('Recca0120\LaravelTracy\BlueScreen', 'Recca0120\LaravelTracy\BlueScreen')->once()
            // ->shouldReceive('register')->with('Recca0120\Terminal\ServiceProvider')->once()
            ->shouldReceive('offsetGet')->with('session')->once()->andReturn($session);

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

        $app = m::mock('Illuminate\Contracts\Foundation\Application'.','.'ArrayAccess');
        $tracy = m::mock('Recca0120\LaravelTracy\Tracy');
        $kernel = m::mock('Illuminate\Contracts\Http\Kernel');
        $handler = m::mock('Illuminate\Contracts\Debug\ExceptionHandler');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('configPath')->andReturn(__DIR__)
            ->shouldReceive('extend')->with('Illuminate\Contracts\Debug\ExceptionHandler', m::type('Closure'))->andReturnUsing(function ($className, $closure) use ($handler, $app) {
                return $closure($handler, $app);
            })
            ->shouldReceive('make')->with('Recca0120\LaravelTracy\Exceptions\Handler', [
                'exceptionHandler' => $handler,
            ]);

        $tracy->shouldReceive('dispatch')->andReturn(true);

        $kernel->shouldReceive('pushMiddleware')->with('Recca0120\LaravelTracy\Middleware\AppendDebugbar')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $serviceProvider = new ServiceProvider($app);
        $serviceProvider->boot($tracy, $kernel);
    }
}
