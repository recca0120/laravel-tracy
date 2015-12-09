<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Tracy\Debugger;

class ServiceProvider extends BaseServiceProvider
{
    protected $enabled = null;

    public function boot(Kernel $kernel)
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ], 'config');

        if ($this->isEnabled()) {
            $kernel->pushMiddleware(AppendDebugbar::class);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');

        if ($this->isEnabled() === true) {
            $this->registerExceptionHandler();
            $this->registerDebugger();
        }
    }

    protected function isEnabled()
    {
        if ($this->enabled !== null) {
            return $this->enabled;
        }

        return $this->enabled = config('app.debug') == true && $this->app->runningInConsole() === false;
    }

    protected function registerExceptionHandler()
    {
        $exceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->singleton(ExceptionHandler::class, function ($app) use ($exceptionHandler) {
            return new Handler($exceptionHandler);
        });
    }

    protected function registerDebugger()
    {
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

    public function provides()
    {
        return [];
    }
}
