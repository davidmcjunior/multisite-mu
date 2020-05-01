<?php

namespace Site\Payment_Gateways\Traits;

/**
 * Trait Adds_Payment_Gateway_Trait
 * @package Site\Traits
 */
trait Adds_Payment_Gateway_Trait
{

	/**
	 * @return void
	 */
	protected function add_payment_gateway()
	{
		add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
			$gateways[] = __CLASS__;
			return $gateways;
		} );
	}

}