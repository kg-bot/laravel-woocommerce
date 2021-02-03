<?php


namespace KgBot\WooCommerce\Builders;

use Illuminate\Support\Collection;
use KgBot\WooCommerce\Exceptions\WooCommerceClientException;
use KgBot\WooCommerce\Exceptions\WooCommerceRequestException;
use KgBot\WooCommerce\Traits\ApiFiltering;
use KgBot\WooCommerce\Utils\Model;
use KgBot\WooCommerce\Utils\Request;
use Psr\Http\Message\ResponseInterface;


class Builder
{
	use ApiFiltering;

	protected $entity;
	/** @var Model */
	protected $model;
	protected $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}


	/**
	 * Get only first page of resources, you can also set query parameters, default limit is 1000
	 *
	 * @param array $filters
	 *
	 * @return mixed
	 * @throws WooCommerceClientException
	 * @throws WooCommerceRequestException
	 */
	public function get( $filters = [] ) {

		$urlFilters = $this->parseFilters( $filters );

		return $this->request->handleWithExceptions( function () use ( $urlFilters ) {

			$response     = $this->request->client->get( "{$this->entity}{$urlFilters}" );
			$fetchedItems = $this->getResponse( $response );

			return $this->populateModelsFromResponse( $fetchedItems );
		} );
	}

	protected function getResponse( ResponseInterface $response ): Collection {

		return collect( json_decode( $this->fixInvalidKeys( $response ), false ) );
	}

	private function fixInvalidKeys( ResponseInterface $response ): string {
		$body = (string) $response->getBody();

		// Remove invalid characters
		return str_replace( [ "\0", "\u0000", "\0*", "\0*\0", '\u0000', '\0', '\0*', '\0*\0' ], '', $body );
	}

	/**
	 * @param $response
	 *
	 * @return Collection|Model
	 */
	protected function populateModelsFromResponse( $response ) {
		$items = collect();
		if ( is_iterable( $response ) ) {
			foreach ( $response as $index => $item ) {
				/** @var Model $model */
				$modelClass = $this->getModelClass();
				$model      = new $modelClass( $this->request, $item );

				$items->push( $model );
			}
		} else {
			$modelClass = $this->getModelClass();

			return new $modelClass( $this->request, $response );
		}


		return $items;

	}

	protected function getModelClass(): string {
		return $this->model;
	}

	/**
	 * It will iterate over all pages until it does not receive empty response, you can also set query parameters,
	 * default limit per page is 1000
	 *
	 * @param array $filters
	 *
	 * @return mixed
	 * @throws WooCommerceClientException
	 * @throws WooCommerceRequestException
	 */
	public function all( $filters = [] ) {
		$page = 1;
		$this->limit();

		$items = collect();

		$response = function ( $filters, $page ) {
			$this->page( $page );

			$urlFilters = $this->parseFilters( $filters );

			return $this->request->handleWithExceptions( function () use ( $urlFilters ) {

				$response = $this->request->client->get( "{$this->entity}{$urlFilters}" );

				$responseData = json_decode( $this->fixInvalidKeys( $response ), false );
				$fetchedItems = $this->getResponse( $response );
				$pages        = $responseData->pages;
				$items        = $this->populateModelsFromResponse( $fetchedItems );

				return (object) [

					'items' => $items,
					'pages' => $pages,
				];
			} );
		};

		do {

			$resp = $response( $filters, $page );

			$items = $items->merge( $resp->items );
			$page++;
			sleep( 2 );

		} while ( $page <= $resp->pages );


		return $items;

	}

	/**
	 * Find single resource by its id filed, it also accepts query parameters
	 *
	 * @param       $id
	 * @param array $filters
	 *
	 * @return mixed
	 * @throws WooCommerceClientException
	 * @throws WooCommerceRequestException
	 */
	public function find( $id, $filters = [] ) {
		unset( $this->wheres['per_page'], $this->wheres['page'] );

		$urlFilters = $this->parseFilters( $filters );
		$id         = rawurlencode( rawurlencode( $id ) );

		return $this->request->handleWithExceptions( function () use ( $id, $urlFilters ) {
			$response     = $this->request->client->get( "{$this->entity}/{$id}{$urlFilters}" );
			$responseData = $this->getResponse( $response );

			return $this->populateModelsFromResponse( (object) $responseData->all() );
		} );
	}

	/**
	 * Create new resource and return created model
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @throws WooCommerceClientException
	 * @throws WooCommerceRequestException
	 */
	public function create( $data ) {
		return $this->request->handleWithExceptions( function () use ( $data ) {

			$response     = $this->request->client->post( $this->entity, [
				'json' => $data,
			] );
			$responseData = $this->getResponse( $response );

			return $this->populateModelsFromResponse( $responseData );
		} );
	}

	public function getEntity() {
		return $this->entity;
	}

	public function setEntity( $new_entity ) {
		$this->entity = $new_entity;

		return $this->entity;
	}
}
