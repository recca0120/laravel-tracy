## Laravel Tracy Debugbar
Laravel with Nette Tracy Debug Tool

[![Latest Stable Version](https://poser.pugx.org/recca0120/laravel-tracy/v/stable)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Total Downloads](https://poser.pugx.org/recca0120/laravel-tracy/downloads)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Latest Unstable Version](https://poser.pugx.org/recca0120/laravel-tracy/v/unstable)](https://packagist.org/packages/recca0120/laravel-tracy)
[![License](https://poser.pugx.org/recca0120/laravel-tracy/license)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Monthly Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/monthly)](https://packagist.org/packages/recca0120/laravel-tracy)
[![Daily Downloads](https://poser.pugx.org/recca0120/laravel-tracy/d/daily)](https://packagist.org/packages/recca0120/laravel-tracy)

## Installation

Add Presenter to your composer.json file:

```js
"require": {
    "recca0120/laravel-tracy": "~1.0.1"
}
```
Now, run a composer update on the command line from the root of your project:

```
composer update
```

### Registering the Package

Include the service provider within `app/laravel-tracy/app.php`. The service povider is needed for the generator artisan command.

```php
'providers' => [
    ...
    Recca0120\LaravelTracy\ServiceProvider::class,
    ...
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

## ScreenShot
![Panel](http://2.bp.blogspot.com/-gabdqGXuKkk/VnEl-Y6R5UI/AAAAAAAANsc/g3FoEX42ElE/s1600/Image%2B3.png)
![Debug](http://3.bp.blogspot.com/-Y-omvzldG-Q/VnEl_Vv8LhI/AAAAAAAANsk/QBxZfz-7sQk/s1600/Image%2B4.png)

# NEW FEATURE
![Terminal](http://3.bp.blogspot.com/-FTEKX8wtKoo/VotlUs5P_pI/AAAAAAAANvM/85YsBhaaRN4/s1600/Image%2B8.png)

Add Presenter to your composer.json file:

```js
"require": {
    "recca0120/terminal": "~2.0.5"
}
```

Now, run a composer update on the command line from the root of your project:

```
composer update
```

Include the service provider within app/config/app.php. The service povider is needed for the generator artisan command.

```php
'providers' => [
    ...
    Recca0120\Terminal\ServiceProvider::class,
    ...
];
```

Publish assets files

```php
artisan vendor:publish --provider="Recca0120\Terminal\ServiceProvider"
```

## INFO
[about terminal](https://github.com/recca0120/terminal)
