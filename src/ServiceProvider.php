<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Recca0120\Terminal\ServiceProvider as TerminalServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * boot.
     *
     * @method boot
     *
     * @param \Recca0120\LaravelTracy\Tracy     $tracy
     * @param \Illuminate\Contracts\Http\Kernel $kernel
     */
    public function boot(Tracy $tracy, Kernel $kernel)
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => $this->app->configPath().'/tracy.php',
        ], 'config');

        if ($this->app->runningInConsole() === false && $tracy->enable() === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                return $app->make(Handler::class, [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });
            $kernel->prependMiddleware(Dispatch::class);
            $kernel->pushMiddleware(AppendDebugbar::class);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');

        $this->app->singleton(Tracy::class, function ($app) {
            $config = array_get($app['config'], 'tracy', []);

            return new Tracy($config);
        });

        $this->app->singleton(Debugbar::class, function ($app) {
            $config = array_get($app['config'], 'tracy', []);

            if (array_get($config, 'useLaravelSession', false) === true) {
                $handler = $this->app['session']->driver()->getHandler();
                session_set_save_handler(new SessionHandlerWrapper($handler), true);
            }

            $debugbar = new Debugbar($config, $app['request'], $app);

            return $debugbar;
        });

        $this->app->singleton(BlueScreen::class, BlueScreen::class);

        if ($this->app['config']['tracy.panels.terminal'] === true) {
            $this->app->register(TerminalServiceProvider::class);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ExceptionHandler::class];
    }
}
