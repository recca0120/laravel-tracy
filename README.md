# Laravel Tracy
Laravel with Nette Tracy Debug Tool

## Installation

Add Presenter to your composer.json file:

```js
"require": {
    "recca0120/config": "~1.1.1"
}
```
Now, run a composer update on the command line from the root of your project:

    composer update

### Registering the Package

Include the service provider within `app/config/app.php`. The service povider is needed for the generator artisan command.

```php
'providers' => [
    ...
    Recca0120\LaravelTracy\ServiceProvider::class,
    ...
];
```

### Editor Link

```
copy <vendor path>/recca0120/laravel-tracy/tools/subl-handler/subl-handler.vbs to any directory where you want to place

double click subl-handler.vbs and select editor (support eclipse, sublime, notepad++, else...)
```

## ScreenShot
![Panel](http://1.bp.blogspot.com/-7TA7WDb9lGw/VkKl1a-g3iI/AAAAAAAANrs/5LOBp4tBUdk/s1600/Image%2B7.png)
![Debug](http://2.bp.blogspot.com/-gabdqGXuKkk/VnEl-Y6R5UI/AAAAAAAANsc/g3FoEX42ElE/s1600/Image%2B3.png)
