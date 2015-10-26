## Laravel Tracy

## Installation

```
composer require recca0120/laravel-tracy
```

OR

Update composer.json
```
{
    "require": {
        ...
        "recca0120/laravel-tracy": "dev-master"
    },
}
```

Require this package with composer:

```
composer update
```


### Laravel 5.0:

Update config/app.php
```php
'providers' => [
    ...
    'Recca0120\LaravelTracy\ServiceProvider',
];
```

### Laravel 5.1:

Update config/app.php
```php
'providers' => [
    ...
    Recca0120\LaravelTracy\ServiceProvider::class,
];
```

### Editor Link

```
copy <vendor path>/recca0120/laravel-tracy/tools/subl-handler/subl-handler.vbs to any directory where you want to place

double click subl-handler.vbs and select editor (support eclipse, sublime, notepad++, else...)
```
