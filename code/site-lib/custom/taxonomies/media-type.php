<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Media_Type
 * @package Site\Custom\Taxonomies
 */
class Media_Type extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name' => __( 'Media Types', '' ),
			'singular_name' => __( 'Media Type', '' ),
		);

		$args = array(
			'label' => __( 'Media Types', '' ),
			'labels' => $labels,
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'media_type', 'with_front' => true, ),
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'show_in_quick_edit' => true,
		);

		register_taxonomy( 'media_type', array( 'item', 'product' ), $args );
	}

}

new Media_Type();