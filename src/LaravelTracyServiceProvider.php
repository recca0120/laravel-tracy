<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Support\ServiceProvider;
use Tracy\Debugger;
use Tracy\Dumper;

class LaravelTracyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-tracy');
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
                'maxDepth' => Debugger::$maxDepth,
                'maxLen' => Debugger::$maxLen,
                'showLocation' => Debugger::$showLocation,
                'strictMode' => Debugger::$strictMode,
                'editor' => Debugger::$editor,
                'panels' => [
                    'Recca0120\LaravelTracy\Panels\RoutingPanel',
                    'Recca0120\LaravelTracy\Panels\ConnectionPanel',
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

            foreach ($config['panels'] as $panel) {
                Debugger::getBar()->addPanel(new $panel($config), $panel);
            }

            $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
            $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\TracyMiddleware');
        }
    }
}
