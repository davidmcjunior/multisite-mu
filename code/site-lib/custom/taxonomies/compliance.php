<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Compliance
 * @package Site\Custom\Taxonomies
 */
class Compliance extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name' => __( 'Compliance', '' ),
			'singular_name' => __( 'Compliance', '' ),
		);

		$args = array(
			'label' => __( 'Compliance', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'compliance', 'with_front' => true, ),
			'show_admin_column' => false,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => false,
		);

		register_taxonomy( 'compliance', array( 'item', 'product' ), $args );
	}

}

new Compliance();