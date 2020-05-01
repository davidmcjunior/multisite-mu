<?php

namespace Site\Custom\Shortcodes;

use Site\Custom\Custom_Type;

/**
 * Class Site_Resource_Library
 * @package Site\Custom\Shortcodes
 */
class Site_Resource_Library extends Custom_Type
{

	/**
	 * Site_Resource_Library constructor.
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

		$languages = array(
			'US English' => 'en_US',
			'German'     => 'de_DE',
			'Spanish'    => 'es_ES',
			'French'     => 'fr_FR',
			'Japanese'   => 'jp_JP'
		);

		$fwp_settings = json_decode( get_option( 'facetwp_settings' ), true );
		$libraries = array();

		foreach ( $fwp_settings[ 'templates' ] as $template ) {
			$libraries[ $template[ 'label' ] ] = $template[ 'name' ];
		}

		$vc_map_settings = array(
			'name'        => __( 'Resource Library' ),
			'base'        => 'site_library',
			'category'    => __( 'Site' ),
			'description' => __( 'Choose a Resource Library.', 'sitetech' ),
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => __( 'Select Library' ),
					'param_name'  => 'template',
					'value'       => $libraries, // array of template labels and ids from content site
					'description' => __( 'Which library to use?' )
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Display Options' ),
					'param_name'  => 'display_options',
					'holder'      => 'div',
					'admin_label' => true,
					'value'       => array(
						'Hide Title'       => 'hide_title',
						'Hide Image'       => 'hide_image',
						'Hide Lit #'       => 'hide_literaturenumber',
						'Hide Description' => 'hide_description',
					), //value
					'std'         => ' ',
					'description' => __( 'Choose any elements to hide from your resource library list' )
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

new Site_Resource_Library();