<?php

namespace KgBot\WooCommerce\Models;


use KgBot\WooCommerce\Builders\ProductVariationBuilder;
use KgBot\WooCommerce\Utils\Model;

class Product extends Model
{
	protected $entity     = 'products';
	protected $primaryKey = 'id';
	protected $modelClass = self::class;

	/**
	 * @return ProductVariationBuilder
	 */
	public function variations(): ProductVariationBuilder {
		$builder = new ProductVariationBuilder( $this->request );
		$builder->setEntity( str_replace( ':product_id', $this->url_friendly_id, $builder->getEntity() ) );

		return $builder;
	}
}