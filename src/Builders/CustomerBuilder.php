<?php

namespace KgBot\WooCommerce\Builders;


use KgBot\WooCommerce\Models\Customer;
use KgBot\WooCommerce\Utils\Model;

class CustomerBuilder extends Builder
{

	protected $entity = 'customers';
	protected $model  = Customer::class;

	/**
	 * @inheritDoc
	 */
	public function first( string $orderBy = 'id' ): ?Model {
		return parent::first( $orderBy );
	}

	/**
	 * @inheritDoc
	 */
	public function last( string $orderBy = 'id' ): ?Model {
		return parent::last( $orderBy );
	}
}