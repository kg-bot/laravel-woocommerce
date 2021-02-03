<?php

namespace KgBot\WooCommerce;


use Illuminate\Support\ServiceProvider;

class WooCommerceServiceProvider extends ServiceProvider
{
	/**
	 * Boot.
	 */
	public function boot() {
		$configPath = __DIR__ . '/config/laravel-woocommerce.php';

		$this->mergeConfigFrom( $configPath, 'laravel-woocommerce' );

		$configPath = __DIR__ . '/config/laravel-woocommerce.php';

		if ( function_exists( 'config_path' ) ) {

			$publishPath = config_path( 'laravel-woocommerce.php' );

		} else {

			$publishPath = base_path( 'config/laravel-woocommerce.php' );

		}

		$this->publishes( [ $configPath => $publishPath ], 'config' );
	}

	public function register() {
	}
}