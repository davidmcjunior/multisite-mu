<?php

namespace Site\Custom\Shortcodes;

use Site\Custom\Custom_Type;
use Site\Multisite\Locale;

/**
 * Class Site_Facet
 * @package Site\Custom\Shortcodes
 */
class Site_Facet extends Custom_Type
{

	/**
	 * Site_Facet constructor.
	 */
	public function __construct()
	{
		parent::__construct( 'vc_after_init' );
	}


	/**
	 * @return void
	 */
	public function register()
	{
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$fwp_settings = json_decode( get_option( 'facetwp_settings' ), true );
		$languages    = array_flip( Locale::$locales );
		$facets       = array();

		foreach ( $fwp_settings[ 'facets' ] as $facet ) {
			$facets[ $facet[ 'label' ] ] = $facet[ 'name' ];
		}

		$vc_map_settings = array(
			'name'        => __( 'Library Facet' ),
			'base'        => 'site_facet',
			'category'    => __( 'Site' ),
			'description' => __( 'Fun Filtering Facets.', 'sitetech' ),
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Select Facet' ),
					'param_name'  => 'facet',
					'value'       => $facets, // array of facet labels and ids from content site
					'description' => __( 'Which facet to use?' )
				),
				array(
					'type'        => 'dropdown',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Select Language' ),
					'param_name'  => 'lang',
					'value'       => $languages, // array of facet labels and ids from content site
					'description' => __( 'Which language to use?' )
				)
			)
		);

		vc_map( $vc_map_settings );
	}

}

new Site_Facet();