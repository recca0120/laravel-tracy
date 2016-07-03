<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
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
    public function boot(Tracy $tracy, DispatcherContract $events)
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ], 'config');

        if ($this->isEnabled() === false) {
            return;
        }

        $tracy->init($this->app['config']->get('tracy'));
        $tracy->obStart();

        $this->app->extend(ExceptionHandlerContract::class, function ($exceptionHandler, $app) {
            return $app->make(Handler::class, [
                'exceptionHandler' => $exceptionHandler,
            ]);
        });

        $events->listen('kernel.handled', function ($request, $response) use ($tracy) {
            $response = $tracy->renderResponse($response);
            $tracy->obEnd();
        });
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
            return new Tracy($app);
        });

        $config = $this->app['config']->get('tracy');
        if (array_get($config, 'panels.terminal') === true) {
            $serviceProvider = TerminalServiceProvider::class;
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Enable when php isn't cli and debug is true.
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->app['config']->get('app.debug') == true && $this->app->runningInConsole() === false;
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
