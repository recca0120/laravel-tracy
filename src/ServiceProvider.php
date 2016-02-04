<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * boot.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole() === true) {
            $this->handlePublishes();
        }

        if ($this->isEnabled() === false) {
            return;
        }

        $this->app->instance('tracy.debugger', $this->app->make('tracy.debugger'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
        $this->app->singleton('tracy.debugger', function ($app) {
            return new Debugger([], $app);
        });
        $this->app->extend(ExceptionHandlerContract::class, function ($exceptionHandler, $app) {
            return new Handler($app->make(ResponseFactoryContract::class), $exceptionHandler);
        });
    }

    /**
     * handle publishes.
     *
     * @return void
     */
    protected function handlePublishes()
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ], 'config');
    }

    /**
     * enable when php isn't cli and debug is true.
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->app['config']['app.debug'] == true && $this->app->runningInConsole() === false;
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
