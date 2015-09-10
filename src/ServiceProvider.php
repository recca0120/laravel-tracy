<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Support\ServiceProvider as baseServiceProvider;
use Tracy\Debugger;
use Tracy\Dumper;

class ServiceProvider extends baseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ]);
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        if (config('app.debug') === true and $this->app->runningInConsole() === false) {
            $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
            $config = array_merge([
                'strictMode' => true,
                'maxDepth' => 4,
                'maxLen' => 1000,
                'showLocation' => true,
                'editor' => 'subl://open?url=file://%file&line=%line',
                'panels' => [
                    'Recca0120\LaravelTracy\Panels\RoutingPanel',
                    'Recca0120\LaravelTracy\Panels\DatabasePanel',
                    'Recca0120\LaravelTracy\Panels\SessionPanel',
                    'Recca0120\LaravelTracy\Panels\RequestPanel',
                    'Recca0120\LaravelTracy\Panels\EventPanel',
                    'Recca0120\LaravelTracy\Panels\UserPanel',
                ],
                'dumpOption' => [
                    Dumper::COLLAPSE => false,
                    'live' => true,
                ],
            ], config('tracy'));

            Debugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
            Debugger::$maxDepth = array_get($config, 'maxDepth');
            Debugger::$maxLen = array_get($config, 'maxLen');
            Debugger::$showLocation = array_get($config, 'showLocation');
            Debugger::$strictMode = array_get($config, 'strictMode');
            Debugger::$editor = array_get($config, 'editor');
            $this->app->singleton(
                'Illuminate\Contracts\Debug\ExceptionHandler',
                'Recca0120\LaravelTracy\Exceptions\Handler'
            );
            $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
            $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\Tracy');

            foreach ($config['panels'] as $panel) {
                Debugger::getBar()->addPanel(new $panel($config, $this->app), $panel);
            }
        }
    }

    public function provides()
    {
        return [\Illuminate\Contracts\Debug\ExceptionHandler::class];
    }
}
