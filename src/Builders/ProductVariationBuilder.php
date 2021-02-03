<?php

namespace KgBot\WooCommerce\Builders;


use KgBot\WooCommerce\Models\ProductVariation;

class ProductVariationBuilder extends Builder
{

	protected $entity = 'products/:product_id/variations';
	protected $model  = ProductVariation::class;
}