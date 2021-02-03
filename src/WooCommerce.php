<?php

namespace KgBot\WooCommerce;


use KgBot\WooCommerce\Builders\CustomerBuilder;
use KgBot\WooCommerce\Builders\OrderBuilder;
use KgBot\WooCommerce\Builders\ProductBuilder;
use KgBot\WooCommerce\Utils\Request;

class WooCommerce
{
	/**
	 * @var $request Request
	 */
	protected $request;

	/**
	 * @param string|null $consumer_secret
	 * @param string|null $consumer_key
	 * @param string|null $shop_url
	 * @param int         $version
	 * @param array       $options Custom Guzzle options
	 * @param array       $headers Custom Guzzle headers
	 * @param bool        $enable_log
	 * @param null        $log_path
	 */
	public function __construct( string $consumer_secret = null, string $consumer_key = null, string $shop_url = null, int $version = 3, $options = [], $headers = [], $enable_log = false, $log_path = null ) {
		$this->initRequest( $consumer_secret, $consumer_key, $shop_url, $version, $options, $headers, $enable_log, $log_path );
	}

	/**
	 * @param string|null $consumer_secret
	 * @param string|null $consumer_key
	 * @param string|null $shop_url
	 * @param int         $version
	 * @param array       $options
	 * @param array       $headers
	 * @param bool        $enable_log
	 * @param null        $log_path
	 */
	private function initRequest( string $consumer_secret = null, string $consumer_key = null, string $shop_url = null, int $version = 3, $options = [], $headers = [], $enable_log = false, $log_path = null ): void {
		$this->request = new Request( $consumer_secret, $consumer_key, $shop_url, $version, $options, $headers, $enable_log, $log_path );
	}

	/**
	 * @return OrderBuilder
	 */
	public function orders(): OrderBuilder {
		return new OrderBuilder( $this->request );
	}

	/**
	 * @return ProductBuilder
	 */
	public function products(): ProductBuilder {
		return new ProductBuilder( $this->request );
	}

	/**
	 * @return CustomerBuilder
	 */
	public function customers(): CustomerBuilder {
		return new CustomerBuilder( $this->request );
	}

	/**
	 * @return mixed
	 */
	public function self() {
		$response = $this->request->client->get( 'self' );


		return json_decode( (string) $response->getBody() );
	}
}