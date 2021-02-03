# WooCommerce REST API PHP SDK For Laravel

[![Latest Stable Version](https://poser.pugx.org/kg-bot/laravel-woocommerce/v/stable)](https://packagist.org/packages/kg-bot/laravel-woocommerce)
[![Total Downloads](https://poser.pugx.org/kg-bot/laravel-woocommerce/downloads)](https://packagist.org/packages/kg-bot/laravel-woocommerce)
[![Latest Unstable Version](https://poser.pugx.org/kg-bot/laravel-woocommerce/v/unstable)](https://packagist.org/packages/kg-bot/laravel-woocommerce)
[![License](https://poser.pugx.org/kg-bot/laravel-woocommerce/license)](https://packagist.org/packages/kg-bot/laravel-woocommerce)
[![Monthly Downloads](https://poser.pugx.org/kg-bot/laravel-woocommerce/d/monthly)](https://packagist.org/packages/kg-bot/laravel-woocommerce)
[![Daily Downloads](https://poser.pugx.org/kg-bot/laravel-woocommerce/d/daily)](https://packagist.org/packages/kg-bot/laravel-woocommerce)

<a href="https://www.buymeacoffee.com/KgBot"><img src="https://img.buymeacoffee.com/button-api/?text=Buy me a beer&emoji=🍺&slug=KgBot&button_colour=5F7FFF&font_colour=ffffff&font_family=Cookie&outline_colour=000000&coffee_colour=FFDD00"></a>

## Installation

1. Require using composer

``` bash
composer require kg-bot/laravel-woocommerce
```

In Laravel 5.5, and above, the package will auto-register the service provider. In Laravel 5.4 you must install this service provider.

2. Add the WooCommerceServiceProvider to your `config/app.php` providers array.

``` php
<?php 
'providers' => [
    // ...
    \KgBot\WooCommerce\WooCommerceServiceProvider::class,
    // ...
]
```

3. Copy the package config to your local config with the publish command:

``` bash
php artisan vendor:publish --provider="KgBot\WooCommerce\WooCommerceServiceProvider"
```