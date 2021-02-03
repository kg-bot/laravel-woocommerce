<?php

namespace KgBot\WooCommerce\Models;

use KgBot\WooCommerce\Utils\Model;

class Customer extends Model
{
	protected $entity     = 'customers';
	protected $primaryKey = 'id';
	protected $modelClass = self::class;
}
