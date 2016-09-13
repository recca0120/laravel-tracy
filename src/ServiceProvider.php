<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
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

        if ($tracy->dispatch() === true) {
            $this->app->extend('Illuminate\Contracts\Debug\ExceptionHandler', function ($exceptionHandler, $app) {
                return $app->make('Recca0120\LaravelTracy\Exceptions\Handler', [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });
            $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\AppendDebugbar');
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
            return new Tracy($app['config']->get('tracy', []), $app, $app['session']);
        });

        $this->app->singleton('Recca0120\LaravelTracy\Debugbar', 'Recca0120\LaravelTracy\Debugbar');

        $this->app->singleton('Recca0120\LaravelTracy\BlueScreen', 'Recca0120\LaravelTracy\BlueScreen');

        // if ($this->app['config']->get('tracy.panels.terminal') === true) {
        //     $this->app->register(TerminalServiceProvider::class);
        // }
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
