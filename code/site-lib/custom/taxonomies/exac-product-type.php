<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Site_Product_Type
 * @package Site\Custom\Taxonomies
 */
class Site_Product_Type extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			"name" => __( 'Site Product Types', '' ),
			"singular_name" => __( 'Site Product Type', '' ),
		);

		$args = array(
			'label' => __( 'Site Product Types', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'site_product_type', 'with_front' => true, ),
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => true,
		);

		register_taxonomy( 'site_product_type', array( 'people', 'product' ), $args );
	}

}

new Site_Product_Type();