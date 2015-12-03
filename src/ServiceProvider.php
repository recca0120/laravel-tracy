<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Tracy\Debugger;

class ServiceProvider extends BaseServiceProvider
{
    protected $defer = true;

    public function boot(Kernel $kernel)
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ], 'config');

        if (config('app.debug') === true and $this->app->runningInConsole() === false) {
            $this->app->singleton(
                'Illuminate\Contracts\Debug\ExceptionHandler',
                'Recca0120\LaravelTracy\Exceptions\Handler'
            );

            $kernel->pushMiddleware('Recca0120\LaravelTracy\Middleware\TracyMiddleware');

            $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
            $config = config('tracy');

            Debugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
            Debugger::$maxDepth = array_get($config, 'maxDepth');
            Debugger::$maxLen = array_get($config, 'maxLen');
            Debugger::$showLocation = array_get($config, 'showLocation');
            Debugger::$strictMode = array_get($config, 'strictMode');
            Debugger::$editor = array_get($config, 'editor');

            foreach ($config['panels'] as $key => $enabled) {
                if ($enabled === true) {
                    $class = '\Recca0120\LaravelTracy\Panels\\'.ucfirst($key).'Panel';
                } elseif (is_string($enabled) === true) {
                    $class = $enabled;
                }

                if (class_exists($class) === true) {
                    $panel = new $class($config, $this->app);
                    Debugger::getBar()->addPanel($panel, $class);
                }
            }
        }
    }

    public function register()
    {
    }

    public function provides()
    {
        return ['Illuminate\Contracts\Debug\ExceptionHandler', 'config'];
    }
}
