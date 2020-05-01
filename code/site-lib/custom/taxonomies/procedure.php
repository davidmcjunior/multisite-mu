<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Procedure
 * @package Site\Custom\Taxonomies
 */
class Procedure extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name' => __( 'Procedures', '' ),
			'singular_name' => __( 'Procedure', '' ),
		);

		$args = array(
			'label' => __( 'Procedures', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'procedure', 'with_front' => true, ),
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => false,
		);

		register_taxonomy( 'procedure', array( 'item', 'surgeon_loc', 'product' ), $args );
	}

}

new Procedure();