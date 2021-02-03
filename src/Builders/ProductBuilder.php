<?php

namespace KgBot\WooCommerce\Builders;


use KgBot\WooCommerce\Models\Product;

class ProductBuilder extends Builder
{

	protected $entity = 'products';
	protected $model  = Product::class;
}