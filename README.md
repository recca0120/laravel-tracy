## [Nette Tracy](https://github.com/nette/tracy.git) for Laravel 5

Better Laravel Exception Handler

[![StyleCI](https://styleci.io/repos/40661503/shield?style=flat)](https://styleci.io/repos/40661503)
[![Build Status](https://travis-ci.org/recca0120/laravel-tracy.svg)](https://travis-ci.org/recca0120/laravel-tracy)
[![Total Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/total.svg)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Latest Stable Version](https://poser.pugx.org/recca0120/laravel-tracy/v/stable.svg)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Latest Unstable Version](https://poser.pugx.org/recca0120/laravel-tracy/v/unstable.svg)](https://packagist.org/packages/recca0120/laravel-tracy)
[![License](https://poser.pugx.org/recca0120/laravel-tracy/license.svg)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Monthly Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/monthly)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Daily Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/daily)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/recca0120/laravel-tracy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/recca0120/laravel-tracy/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/recca0120/laravel-tracy/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/recca0120/laravel-tracy/?branch=master)

![Laravel Tracy](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/tracy.png)

## Features
- Visualization of errors and exceptions
- Debugger Bar (ajax support @v1.5.6)
- Exception stack trace contains values of all method arguments.

## Online Demo
[Demo](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/tracy-exception.html)

## Installing

To get the latest version of Laravel Exceptions, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require recca0120/laravel-tracy
```

Instead, you may of course manually update your require block and run `composer update` if you so choose:

```json
{
    "require": {
        "recca0120/laravel-tracy": "^1.7.13"
    }
}
```

Include the service provider within `config/app.php`. The service povider is needed for the generator artisan command.

```php
'providers' => [
    ...
    Recca0120\LaravelTracy\LaravelTracyServiceProvider::class,
    ...
];
```

publish

```bash
php artisan vendor:publish --provider="Recca0120\LaravelTracy\LaravelTracyServiceProvider"
```

## Config
```php
return [
    'enabled' => env('APP_DEBUG') === true,
    'showBar' => env('APP_ENV') !== 'production',
    'accepts'      => [
        'text/html',
    ],
    // appendTo: body | html
    'appendTo' => 'body',
    'editor' => 'subl://open?url=file://%file&line=%line',
    'maxDepth' => 4,
    'maxLength' => 1000,
    'scream' => true,
    'showLocation' => true,
    'strictMode' => true,
    'panels' => [
        'routing' => true,
        'database' => true,
        'view' => true,
        'event' => false,
        'session' => true,
        'request' => true,
        'auth' => true,
        'html-validator' => true,
        'terminal' => true,
    ],
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
Prefer PhpStorm, you can edit `config/tracy.php`'s key of `editor` like this:
```php
'editor' => 'phpstorm://open?file=%file&line=%line',
```

## Debugger Bar

### Directive bdump
![Ajax](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/bdump.png)

### Ajax Debugger Bar
![Ajax](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/ajax.png)

### SystemInfo
![SystemInfo](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/systeminfo.png)

### Route
![Route](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/route.png)

### View
![View](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/view.png)

### Session
![Session](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/session.png)

### Request
![Request](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/request.png)

### Auth
![Auth](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/login.png)

#### Custom Auth
```
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Recca0120\LaravelTracy\BarManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(BarManager $barManager)
    {
        $barManager->get('auth')->setUserResolver(function() {
            return [
                'id' => 'xxx',
                'username' => 'xxx',
                ...
            ];
        });
    }
}
```


### Html Validator
![Html Validator](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/html-validator.png)

### Web Artisan
web artisan is another package [recca0120/terminal](https://github.com/recca0120/laravel-terminal)
![Terminal](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/terminal.png)

#### notice
if you install terminal before, this panel will throw errors, please remove folder `app/resources/views/vendor/terminal`

## STANDALONE

```php
require __DIR__.'/../vendor/autoload.php';

use Recca0120\LaravelTracy\Tracy;

// before outout
$tracy = Tracy::instance();

$authPanel = $tracy->getPanel('auth');
$authPanel->setUserResolver(function() {
    return [
        'email' => 'recca0120@gmail.com'
    ];
});

function sql($sql)
{
    $tracy = Tracy::instance();
    $databasePanel = $tracy->getPanel('database');
    $databasePanel->logQuery($sql);
}

sql('select * from users');
sql('select * from news');
sql('select * from products');
```

![Standalone](https://cdn.rawgit.com/recca0120/laravel-tracy/master/docs/screenshots/standalone.png)
