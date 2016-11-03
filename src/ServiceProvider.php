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
     * @param \Illuminate\Contracts\Http\Kernel $kernel
     */
    public function boot(Kernel $kernel)
    {
        if ($this->app['config']['tracy']['enabled'] === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                return $app->make(Handler::class, [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });
            $kernel->prependMiddleware(Dispatch::class);
            $kernel->pushMiddleware(AppendDebugbar::class);
        }

        if ($this->app->runningInConsole() === true) {
            $this->publishes([
                __DIR__.'/../config/tracy.php' => $this->app->configPath().'/tracy.php',
            ], 'config');
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

        $this->registerDebugbar();

        $this->app->singleton(BlueScreen::class, BlueScreen::class);

        if ($this->app['config']['tracy.panels.terminal'] === true) {
            $this->app->register(TerminalServiceProvider::class);
        }
    }

    /**
     * registerDebugbar.
     */
    protected function registerDebugbar()
    {
        $this->app->singleton(Debugbar::class, function ($app) {
            $config = array_get($app['config'], 'tracy', []);

            if (array_get($config, 'useLaravelSession', false) === true) {
                $handler = $this->app['session']->driver()->getHandler();
                session_set_save_handler(new SessionHandlerWrapper($handler), true);
            }

            $debugbar = new Debugbar($config, $app['request'], $app);

            return $debugbar;
        });
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
