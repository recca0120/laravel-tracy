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
- Debugger Bar (ajax support @v1.5.6)
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
        "recca0120/laravel-tracy": "^1.5.6"
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
php artisan vendor:publish --provider="Recca0120\LaravelTracy\ServiceProvider"
```

## Config
```php
return [
    'editor'       => 'subl://open?url=file://%file&line=%line',
    'enabled'      => true,
    'showBar'      => true,
    'accepts'      => [
        'text/html',
    ],
    'maxDepth'     => 4,
    'maxLength'    => 1000,
    'scream'       => true,
    'showLocation' => true,
    'strictMode'   => true,
    'panels'       => [
        'routing'  => true,
        'database' => true,
        'view'     => true,
        'event'    => false,
        'session'  => true,
        'request'  => true,
        'auth'     => true,
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

### Ajax Debugger Bar
![Ajax](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/ajax.png)

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

## STANDALONE

```php
require __DIR__.'/../vendor/autoload.php';

use Recca0120\LaravelTracy\Tracy;

// before outout
$tracy = Tracy::instance();

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

![Standalone](https://cdn.rawgit.com/recca0120/laravel-tracy/master/screenshots/standalone.png)
