<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Tracy\Debugger;

class ServiceProvider extends BaseServiceProvider
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
        ], 'config');
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        if (config('app.debug') === true and $this->app->runningInConsole() === false) {
            $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
            $config = config('tracy');

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

            foreach ($config['panels'] as $key => $enabled) {
                if ($enabled === true) {
                    $class = '\Recca0120\LaravelTracy\Panels\\'.ucfirst($key).'Panel';
                } elseif (is_string($enabled)) {
                    $class = $enabled;
                }

                if (class_exists($class) === true) {
                    $panel = new $class($config, $this->app);
                    Debugger::getBar()->addPanel($panel, $class);
                }
            }
        }
    }

    public function provides()
    {
        return ['Illuminate\Contracts\Debug\ExceptionHandler'];
    }
}
