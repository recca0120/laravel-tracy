<?php

use Mockery as m;
use Recca0120\LaravelTracy\Tracy;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Recca0120\LaravelTracy\LaravelTracyServiceProvider;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_service_provider_register()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $config = m::spy('Illuminate\Contracts\Config\Repository, ArrayAccess');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('singleton')->with('Recca0120\LaravelTracy\Debugbar', m::type('Closure'))->andReturnUsing(function ($className, $closure) use ($app) {
                $closure($app);
            });

        $config
            ->shouldReceive('get')->with('tracy', [])->andReturn([])
            ->shouldReceive('offsetGet')->with('tracy.panels.terminal')->andReturn(true);

        $serviceProvider = new LaravelTracyServiceProvider($app);
        $serviceProvider->register();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame(['Illuminate\Contracts\Debug\ExceptionHandler'], $serviceProvider->provides());
        $app->shouldHaveReceived('singleton')->with('Recca0120\LaravelTracy\Session\StoreWrapper', 'Recca0120\LaravelTracy\Session\StoreWrapper')->once();
        $app->shouldHaveReceived('singleton')->with('Recca0120\LaravelTracy\BlueScreen', 'Recca0120\LaravelTracy\BlueScreen')->once();
        $app->shouldHaveReceived('singleton')->with('Recca0120\LaravelTracy\Debugbar', m::type('Closure'))->once();
        $app->shouldHaveReceived('register')->with('Recca0120\Terminal\TerminalServiceProvider')->once();
    }

    public function test_service_provider_boot_when_running_in_console()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $kernel = m::spy('Illuminate\Contracts\Http\Kernel');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(true);

        $serviceProvider = new LaravelTracyServiceProvider($app);
        $serviceProvider->boot($kernel);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $app->shouldHaveReceived('configPath')->once();
    }

    public function test_service_provider_boot()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $kernel = m::spy('Illuminate\Contracts\Http\Kernel');
        $config = ['tracy' => ['enabled' => true]];
        $handler = m::spy('Illuminate\Contracts\Debug\ExceptionHandler');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('extend')->with('Illuminate\Contracts\Debug\ExceptionHandler', m::type('Closure'))->once()->andReturnUsing(function ($className, $closure) use ($handler, $app) {
                return $closure($handler, $app);
            });

        $serviceProvider = new LaravelTracyServiceProvider($app);
        $serviceProvider->boot($kernel);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $app->shouldHaveReceived('make')->with('Recca0120\LaravelTracy\Exceptions\Handler', ['exceptionHandler' => $handler])->once();
        $kernel->shouldHaveReceived('prependMiddleware')->with('Recca0120\LaravelTracy\Middleware\Dispatch')->once();
    }
}

if (function_exists('env') === false) {
    function env($env)
    {
        switch ($env) {
            case 'APP_ENV':
                return 'local';
                break;

            case 'APP_DEBUG':
                return true;
                break;
        }
    }
}
