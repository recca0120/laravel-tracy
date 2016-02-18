<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\Terminal\ServiceProvider as TerminalServiceProvider;

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
        $this->handlePublishes();

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
        $this->registerTerminal();

        $this->app->singleton('tracy.debugger', function ($app) {
            $config = $app['config']->get('tracy');
            $debugger = new Debugger($config, $app);
            $debugger->setBasePath(array_get($config, 'base_path'));

            $this->app['events']->listen('kernel.handled', function ($request, $response) use ($debugger) {
                return $debugger->appendDebugbar($request, $response);
            });

            return $debugger;
        });

        $this->app->extend(ExceptionHandlerContract::class, function ($exceptionHandler, $app) {
            return new Handler($app->make(ResponseFactoryContract::class), $exceptionHandler);
        });
    }

    /**
     * register terminal.
     *
     * @return void
     */
    protected function registerTerminal()
    {
        $config = $this->app['config']->get('tracy');
        if (array_get($config, 'panels.terminal') === true) {
            $serviceProvider = TerminalServiceProvider::class;
            $this->app->register($serviceProvider);
        }
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
