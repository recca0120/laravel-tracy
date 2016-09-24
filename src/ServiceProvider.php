<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
<<<<<<< HEAD
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Recca0120\Terminal\ServiceProvider as TerminalServiceProvider;
=======
>>>>>>> b555fc6590be60f3e0ccfc49e428b448f4e7dc06

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

        if ($tracy->enable() === true) {
<<<<<<< HEAD
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                return $app->make(Handler::class, [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });
            $kernel->prependMiddleware(Dispatch::class);
            $kernel->pushMiddleware(AppendDebugbar::class);
=======
            $this->app->extend('Illuminate\Contracts\Debug\ExceptionHandler', function ($exceptionHandler, $app) {
                return $app->make('Recca0120\LaravelTracy\Exceptions\Handler', [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });
            $kernel->prependMiddleware('Recca0120\LaravelTracy\Middleware\Dispatch');
            $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\AppendDebugbar');
>>>>>>> b555fc6590be60f3e0ccfc49e428b448f4e7dc06
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

        $this->app->singleton('Recca0120\LaravelTracy\Tracy', function ($app) {
            return new Tracy($app['config']->get('tracy', []), $app);
        });

        $this->app->singleton('Recca0120\LaravelTracy\Debugbar', 'Recca0120\LaravelTracy\Debugbar');

        $this->app->singleton('Recca0120\LaravelTracy\BlueScreen', 'Recca0120\LaravelTracy\BlueScreen');

        if ($this->app['config']->get('tracy.panels.terminal') === true) {
            $this->app->register('Recca0120\Terminal\ServiceProvider');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Illuminate\Contracts\Debug\ExceptionHandler'];
    }
}
