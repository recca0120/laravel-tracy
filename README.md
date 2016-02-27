## [Nette Tracy](https://github.com/nette/tracy.git) for Laravel 5

Better Laravel Exception Handler

[![Latest Stable Version](https://poser.pugx.org/recca0120/laravel-tracy/v/stable)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Total Downloads](https://poser.pugx.org/recca0120/laravel-tracy/downloads)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Latest Unstable Version](https://poser.pugx.org/recca0120/laravel-tracy/v/unstable)](https://packagist.org/packages/recca0120/laravel-tracy)
[![License](https://poser.pugx.org/recca0120/laravel-tracy/license)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Monthly Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/monthly)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Daily Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/daily)](https://packagist.org/packages/recca0120/laravel-tracy)

![Laravel Tracy](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/tracy.png)

## Features
- Visualization of errors and exceptions
- Debugger Bar
- Exception stack trace contains values of all method arguments.

## Online Demo
[Demo](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/tracy-exception.html)

## Installing

To get the latest version of Laravel Exceptions, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require recca0120/laravel-tracy
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
    "require": {
        "recca0120/laravel-tracy": "~1.3.5"
    }
}
```

Include the service provider within `config/app.php`. The service povider is needed for the generator artisan command.

```php
'providers' => [
    ...
    Recca0120\LaravelTracy\ServiceProvider::class,
    ...
];
```

publish

```bash
artisan vendor:publish --provider="Recca0120\LaravelTracy\ServiceProvider"
```
## Config
```php
return [
    'ajax'      => [
        'debugbar'           => false, // enable render debugbar when http request is ajax
        'gzCompressLevel'    => 5,    // gzcompress level
        /*
        * http://stackoverflow.com/questions/3326210/can-http-headers-be-too-big-for-browsers/3431476#3431476
        * Lowest limit found in popular browsers:
        *   - 10KB per header
        *   - 256 KB for all headers in one response.
        *   - Test results from MacBook running Mac OS X 10.6.4:
        */
        'maxHeaderSize'   => 102400, // 102400b its => 100 kb
    ],
    'basePath'     => null,
    'strictMode'   => true,
    'maxDepth'     => 4,
    'maxLen'       => 1000,
    'showLocation' => true,
    'editor'       => 'subl://open?url=file://%file&line=%line',
    'panels'       => [
        'routing'  => true,
        'database' => true,
        'view'     => true,
        'session'  => true,
        'request'  => true,
        'event'    => false,
        'user'     => true,
        'terminal' => true,
    ],
    // value: js or tracy
    'panelDumpMethod' => 'js', // tracy dump need more memory
];
```

### Editor Link

windows
```
copy <vendor path>/recca0120/laravel-tracy/tools/subl-handler/subl-handler.vbs to any directory where you want to place

double click subl-handler.vbs and select editor (support eclipse, sublime, notepad++, else...)
```

OSX
```
https://github.com/dhoulb/subl
```

## Debugger Bar

### SystemInfo
![SystemInfo](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/systeminfo.png)

### Route
![Route](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/route.png)

### View
![View](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/view.png)

### Session
![Session](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/session.png)

### Request
![Request](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/request.png)

### Login
![Login](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/login.png)

### Web Artisan
web artisan is another package [recca0120/terminal](https://github.com/recca0120/laravel-terminal)
![Terminal](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/terminal.png)

#### notice
if you install terminal before, this panel will throw errors, please remove folder `app/resources/views/vendor/terminal`

## ISSUE
when ajax debugbar is enabled and debugbar is bigger than 256k, will throw 500 exception, or browser will be no response

so I try to compress debugbar in php, and decompress debugbar in javascript.

It looks like working at chrome 48.0.2564.116 64bit, windows 10

but if you use Laravel-Tracy and it doesn't work correctly

you can try
- disable panel [view , request, event]
- panelDumpMethod change to js
- disable ajax debugbar
