<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Spinal_Location
 * @package Site\Custom\Taxonomies
 */
class Spinal_Location extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name' => __( 'Spinal Locations', '' ),
			'singular_name' => __( 'Spinal Location', '' ),
		);

		$args = array(
			'label' => __( 'Spinal Locations', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'spinal_location', 'with_front' => true, ),
			'show_admin_column' => false,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => false,
		);

		register_taxonomy( 'spinal_location', array( 'library_item' ), $args );
	}

}

new Spinal_Location();