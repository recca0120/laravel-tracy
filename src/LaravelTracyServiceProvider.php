<?php namespace Recca0120\LaravelTracy;

use Illuminate\Support\ServiceProvider;

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

        $app = $this->app;
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ]);
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        if (config('app.debug')) {
            $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
            LaravelTracy::register(config('tracy'));
        }
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Illuminate\Contracts\Debug\ExceptionHandler'];
    }
}
