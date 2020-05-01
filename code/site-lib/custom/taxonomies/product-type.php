<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Product_Type
 * @package Site\Custom\Taxonomies
 */
class Product_Type extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			"name" => __( 'Product Types', '' ),
			"singular_name" => __( 'Product Type', '' ),
		);

		$args = array(
			'label' => __( 'Product Types', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'product_type', 'with_front' => true, ),
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => true,
		);

		register_taxonomy( 'product_type', array( 'ajde_events', 'item', 'surgeon_loc', 'product' ), $args );
	}

}

new Product_Type();