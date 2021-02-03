<?php

namespace KgBot\WooCommerce\Utils;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Config;
use KgBot\WooCommerce\Exceptions\WooCommerceClientException;
use KgBot\WooCommerce\Exceptions\WooCommerceRequestException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;

class Request
{
	/**
	 * @var Client
	 */
	public $client;

	/**
	 * Request constructor.
	 *
	 * @param string|null $consumer_secret
	 * @param string|null $consumer_key
	 * @param string|null $shop_url
	 * @param int         $version
	 * @param array       $options
	 * @param array       $headers
	 * @param bool        $enable_log
	 * @param null        $log_path
	 */
	public function __construct( string $consumer_secret = null, string $consumer_key = null, string $shop_url = null, int $version = 3, $options = [], $headers = [], $enable_log = false, $log_path = null ) {
		$consumer_secret = $consumer_secret ?? Config::get( 'laravel-woocommerce.consumer_secret' );
		$consumer_key    = $consumer_key ?? Config::get( 'laravel-woocommerce.consumer_key' );
		$shop_url        = $shop_url ?? Config::get( 'laravel-woocommerce.shop_url' );
		$headers         = array_merge( $headers, [

			'User-Agent'    => Config::get( 'laravel-woocommerce.user_agent' ),
			'Accept'        => 'application/json; charset=utf8',
			'Content-Type'  => 'application/json; charset=utf8',
			'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret )
		] );

		$options = $this->addCustomMiddlewares( $options, $enable_log, $log_path );

		$options = array_merge( $options, [

			'base_uri' => $shop_url . "/wp-json/wc/v{$version}/",
			'headers'  => $headers,
		] );

		$this->client = new Client( $options );
	}

	public function addCustomMiddlewares( array $options, $log, $log_path ) {
		if ( ! empty( $options['handler'] ) ) {
			$options['handler']->push( $this->createThrottleMiddleware() );
		} else {
			$stack = HandlerStack::create();

			$stack->push( $this->createThrottleMiddleware() );
			$options['handler'] = $stack;
		}

		if ( $log ) {

			$options['handler']->push( $this->createLoggerMiddleware( $log_path ) );
		}

		return $options;
	}

	public function createThrottleMiddleware() {
		return RateLimiterMiddleware::perMinute( Config::get( 'laravel-woocommerce.api_limit', 480 ), new RateLimiterStore() );
	}

	public function createLoggerMiddleware( $log_path = null ) {
		$log_path = $log_path ?? 'guzzle-logger.log';

		$logger   = new Logger( 'GuzzleCustomLogger' );
		$location = storage_path( 'logs/' . $log_path );
		$logger->pushHandler( new StreamHandler( $location, Logger::DEBUG ) );

		$format =
			'{method} {uri} - {target} - {hostname} HTTP/{version} .......... ' .
			'REQUEST HEADERS: {req_headers} ....... REQUEST: {req_body} ' .
			'......... RESPONSE HEADERS: {res_headers} ........... RESPONSE: {code} - {res_body}';

		return Middleware::log(
			$logger,
			new MessageFormatter( $format )
		);
	}

	/**
	 * @param $callback
	 *
	 * @return mixed
	 * @throws WooCommerceClientException
	 * @throws WooCommerceRequestException
	 */
	public function handleWithExceptions( $callback ) {
		try {

			return $callback();

		} catch ( ClientException | ServerException $exception ) {

			$message = $exception->getMessage();
			$code    = $exception->getCode();

			if ( $exception->hasResponse() ) {

				$message = (string) $exception->getResponse()->getBody();
				$code    = $exception->getResponse()->getStatusCode();
			}

			throw new WooCommerceRequestException( $message, $code );

		} catch ( Exception $exception ) {

			$message = $exception->getMessage();
			$code    = $exception->getCode();

			throw new WooCommerceClientException( $message, $code );
		}
	}
}
