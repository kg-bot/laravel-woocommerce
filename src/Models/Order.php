<?php

namespace KgBot\WooCommerce\Models;

use KgBot\WooCommerce\Utils\Model;

class Order extends Model
{
	protected $entity     = 'orders';
	protected $primaryKey = 'id';
	protected $modelClass = self::class;
}
