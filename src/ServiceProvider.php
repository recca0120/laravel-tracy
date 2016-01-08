<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;

class ServiceProvider extends BaseServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->handlePublishes();
        if ($this->isEnabled() === false) {
            return;
        }

        $this->app->instance('tracy.debugger', $this->app->make('tracy.debugger'));
    }

    public function register()
    {
        $this->app->singleton('tracy.debugger', function ($app) {
            return $app->make(Debugger::class);
        });
        $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
            return new Handler($exceptionHandler);
        });
    }

    protected function handlePublishes()
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
    }

    protected function isEnabled()
    {
        return $this->app['config']['app.debug'] == true && $this->app->runningInConsole() === false;
    }

    public function provides()
    {
        return [ExceptionHandler::class];
    }
}
