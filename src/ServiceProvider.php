<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
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
    public function boot(Tracy $tracy, HttpKernelContract $kernel)
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => $this->app->configPath().'/tracy.php',
        ], 'config');

        if ($tracy->initialize() === true) {
            $this->app->extend(ExceptionHandlerContract::class, function ($exceptionHandler, $app) {
                return $app->make(Handler::class, [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });

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
            return new Tracy($this->app['config']->get('tracy'), $app);
        });

        if ($this->app['config']->get('tracy.panels.terminal') === true) {
            $serviceProvider = TerminalServiceProvider::class;
            $this->app->register($serviceProvider);
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
