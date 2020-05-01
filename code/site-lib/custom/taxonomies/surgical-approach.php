<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Surgical_Approach
 * @package Site\Custom\Taxonomies
 */
class Surgical_Approach extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name' => __( 'Surgical Approaches', '' ),
			'singular_name' => __( 'Surgical Approach', '' ),
		);

		$args = array(
			'label' => __( 'Surgical Approaches', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'surgical_approach', 'with_front' => true, ),
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => true,
		);

		register_taxonomy( 'surgical_approach', array( 'item', 'surgeon_loc', 'product' ), $args );
	}

}

new Surgical_Approach();