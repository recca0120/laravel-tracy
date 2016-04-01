var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix
        .browserify([
            'dump.js'
        ], config.get('public.js.outputFolder') + '/dump.js')
        .browserify([
            'ajax-monitor.js'
        ], config.get('public.js.outputFolder') + '/ajax-monitor.js')
        .phpUnit([
            'tests/**/*'
        ]);
});
