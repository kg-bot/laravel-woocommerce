<?php

namespace KgBot\WooCommerce\Exceptions;


use Exception;

class MethodNotImplemented extends Exception
{

	protected $message = 'This method is not implemented on given resource.';
}