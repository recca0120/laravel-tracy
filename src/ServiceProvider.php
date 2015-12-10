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
    protected $defer = true;

    public function boot(Kernel $kernel)
    {
        $this->handlePublishes();

        if ($this->isEnabled() === false) {
            return;
        }

        $this->registerExceptionHandler();
        $this->registerDebugger();
        $kernel->pushMiddleware(AppendDebugbar::class);
    }

    public function register()
    {
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
        $config                 = $this->app['config']['tracy'];
        Debugger::$time         = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        Debugger::$maxDepth     = array_get($config, 'maxDepth');
        Debugger::$maxLen       = array_get($config, 'maxLen');
        Debugger::$showLocation = array_get($config, 'showLocation');
        Debugger::$strictMode   = array_get($config, 'strictMode');
        Debugger::$editor       = array_get($config, 'editor');

        $bar = Debugger::getBar();
        foreach ($config['panels'] as $key => $enabled) {
            if ($enabled === true or $enabled === '1') {
                $class = '\\'.__NAMESPACE__.'\Panels\\'.ucfirst($key).'Panel';
                $bar->addPanel(new $class($config, $this->app), $class);
            } elseif (is_string($enabled) === true) {
                $class = $enabled;
                $bar->addPanel(new $class($config, $this->app), $class);
            }
        }
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
        return $this->app['config']['app.debug'] == true and $this->app->runningInConsole() === false;
    }

    public function provides()
    {
        return [ExceptionHandler::class];
    }
}
