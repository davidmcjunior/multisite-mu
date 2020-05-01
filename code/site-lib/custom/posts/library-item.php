<?php

namespace Site\Custom\Posts;

use Site\Custom\Custom_Type;

/**
 * Class Library_Item
 * @package Site\Custom\Post
 */
class Library_Item extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name' => __( 'Library Items', '' ),
			'singular_name' => __( 'Library Item', '' ),
		);

		$args = array(
			'label' => __( 'Library Items', '' ),
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_rest' => false,
			'rest_base' => '',
			'has_archive' => false,
			'show_in_menu' => true,
			'exclude_from_search' => false,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'rewrite' => array( 'slug' => 'item', 'with_front' => true ),
			'query_var' => true,
			'menu_icon' => 'dashicons-book-alt',
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies' => array( 'product_type', 'audience', 'destination', 'surgical_approach', 'procedure', 'media_type', 'internal_tags', 'spinal_location' ),
		);

		register_post_type( 'item', $args );
	}

}

new Library_Item();