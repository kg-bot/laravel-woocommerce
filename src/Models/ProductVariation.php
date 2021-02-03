<?php

namespace KgBot\WooCommerce\Models;


use KgBot\WooCommerce\Utils\Model;

class ProductVariation extends Model
{
	protected $entity     = 'products/:product_id/variations';
	protected $primaryKey = 'id';
	protected $modelClass = self::class;
}