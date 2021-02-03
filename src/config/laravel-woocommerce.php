<?php

return [

	'consumer_secret' => env( 'WOOCOMMERCE_CONSUMER_SECRET' ),
	'consumer_key'    => env( 'WOOCOMMERCE_CONSUMER_KEY' ),
	'shop_url'        => env( 'WOOCOMMERCE_SHOP_URL' ),
	'user_agent'      => env( 'WOOCOMMERCE_USER_AGENT', 'kg-bot/laravel-woocommerce' ),
	'api_limit'       => env( 'WOOCOMMERCE_API_LIMIT', 480 ), // Number of allowed requests per minute
];
