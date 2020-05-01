<?php

namespace Site\Shortcodes;

/**
 * Class Iframe
 *
 * Original plugin: https://wordpress.org/plugins/iframe/
 *
 * @package Site\Shortcodes
 */
abstract class Iframe extends Shortcode
{


	/**
	 * Iframe constructor.
	 *
	 * @param string $shortcode
	 */
	public function __construct( $shortcode = 'iframe' )
	{
		parent::__construct( $shortcode );

		add_action( 'vc_after_init', [ $this, 'register_vc_component_action' ] );
	}


	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function shortcode_action( $atts )
	{
		$defaults = array(
			'id'          => str_replace( '_', '-', $this->shortcode ),
			'width'       => '100%',
			'height'      => '500',
			'scrolling'   => 'yes',
			'class'       => 'sales-dashboard',
			'frameborder' => '0'
		);

		foreach ( $defaults as $default => $value ) {
			if ( ! @array_key_exists( $default, $atts ) ) {
				$atts[ $default ] = $value;
			}
		}

		$html = '<iframe';

		foreach( $atts as $attr => $value ) {
			$attr = strtolower( esc_attr( $attr ) );

			if ( in_array( $attr, [ 'same_height_as', 'onload', 'onpageshow', 'onclick' ] ) ) continue;

			if ( $value != '' ) {
				$html .= ' ' . $attr . '="' . $value . '"';

			} else {
				$html .= ' ' . $attr;
			}
		}

		$html .= '></iframe>' . "\n";

		if ( isset( $atts[ 'same_height_as' ] ) ) {
			$html .= '
				<script>
				document.addEventListener("DOMContentLoaded", function(){
					var target_element, iframe_element;
					iframe_element = document.querySelector("iframe.' . esc_attr( $atts[ 'class' ] ) . '");
					target_element = document.querySelector("' . esc_attr( $atts[ 'same_height_as' ] ) . '");
					iframe_element.style.height = target_element.offsetHeight + "px";
				});
				</script>
			';
		}

		return $html;
	}


	/**
	 * @return void
	 */
	public function register_vc_component_action()
	{
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$vc_map_settings = array(
			'name'        => ucwords( str_replace( '_', ' ', $this->shortcode ) ),
			'base'        => $this->shortcode,
			'category'    => __( 'Site' ),
			'params' => array(
				array(
					'type'       => 'textfield',
					'value'      => '100%',
					'heading'    => 'Width',
					'param_name' => 'width',
				),
				array(
					'type'       => 'textfield',
					'value'      => '500px',
					'heading'    => 'Height',
					'param_name' => 'height',
				)
			)
		);

		vc_map( $vc_map_settings );
	}

}