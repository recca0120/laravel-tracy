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
        .coffee([
            'dump.coffee'
        ], config.get('public.js.outputFolder') + '/dump.js')

        .copy([
            'resources/assets/vendor/base64/base64.min.js',
            // 'resources/assets/vendor/zlib.js/bin/zlib.min.js',
            'resources/assets/vendor/pako/dist/pako_inflate.min.js',
        ], config.get('assets.js.folder'))
        .coffee([
            'ajax.coffee'
        ], config.get('assets.js.folder') + '/ajax.js')
        .combine([
            config.get('assets.js.folder') + '/base64.min.js',
            // config.get('assets.js.folder') + '/zlib.min.js',
            config.get('assets.js.folder') + '/pako_inflate.min.js',
            config.get('assets.js.folder') + '/ajax.js',
        ], config.get('public.js.outputFolder') + '/ajax.js')


        .phpUnit([
            'tests/**/*'
        ]);
});
