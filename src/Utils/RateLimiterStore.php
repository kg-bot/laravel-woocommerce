<?php


namespace KgBot\WooCommerce\Utils;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\GuzzleRateLimiterMiddleware\Store;

class RateLimiterStore implements Store
{
	public function push( int $timestamp ) {
		Cache::put( 'laravel-woocommerce-rate-limiter', array_merge( $this->get(), [ $timestamp ] ), Carbon::now()->addMinute() );
	}

	public function get(): array {
		return Cache::get( 'laravel-woocommerce-rate-limiter', [] );
	}
}