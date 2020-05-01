<?php

namespace Site\Payment_Gateways\Traits;

/**
 * Trait Gets_WC_Shipping_Options_Trait
 * @package Site\Traits
 */
trait Gets_WC_Shipping_Options_Trait
{

	/**
	 * @return array
	 * @throws \Exception
	 */
	protected function get_shipping_options()
	{
		$options    = [ "Can't get shipping options at this time." ];
		$data_store = null;
		$zones      = null;

		// try {
		// 	$zones = \WC_Shipping_Zones::get_zones();
		//
		// 	foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
		// 		$options[ $method->get_method_title() ] = array();
		//
		// 		$options[ $method->get_method_title() ][ $method->id ] =
		// 			sprintf(
		// 				__( 'Any &quot;%1$s&quot; method', 'woocommerce' ),
		// 				$method->get_method_title()
		// 			);
		//
		// 		foreach ( $zones as $zone ) {
		// 			$methods = $zone->get_shipping_methods();
		//
		// 			foreach ( $methods as $id => $method ) {
		// 				if ( $method->id !== $method->id ) {
		// 					continue;
		// 				}
		//
		// 				$option_id = $method->get_rate_id();
		//
		// 				// Translators: %1$s shipping method title, %2$s shipping method id.
		// 				$instance_title =
		// 					sprintf(
		// 						__( '%1$s (#%2$s)', 'woocommerce' ),
		// 						$method->get_title(), $id
		// 					);
		//
		// 				// Translators: %1$s zone name, %2$s shipping method instance name.
		// 				$option_title =
		// 					sprintf(
		// 						__( '%1$s &ndash; %2$s', 'woocommerce' ),
		// 						$zone->get_id() ? $zone->get_zone_name() : __( 'Other locations', 'woocommerce' ),
		// 						$instance_title
		// 					);
		//
		// 				$options[ $method->get_method_title() ][ $option_id ] = $option_title;
		// 			}
		// 		}
		// 	}
		//
		// } catch( \Exception $e ) {
		//
		// }

		return $options;
	}

}
