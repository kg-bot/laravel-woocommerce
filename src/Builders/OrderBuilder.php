<?php

namespace KgBot\WooCommerce\Builders;


use KgBot\WooCommerce\Models\Order;

class OrderBuilder extends Builder
{
	protected $entity = 'orders';
	protected $model  = Order::class;
}