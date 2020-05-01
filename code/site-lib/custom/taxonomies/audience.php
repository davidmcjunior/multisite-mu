<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Audience
 * @package Site\Custom\Taxonomies
 */
class Audience extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name'          => __( 'Audiences', '' ),
			'singular_name' => __( 'Audience', '' ),
		);

		$args = array(
			'label'              => __( 'Audiences', '' ),
			'labels'             => $labels,
			'public'             => true,
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'audience', 'with_front' => true, ),
			'show_admin_column'  => true,
			'show_in_rest'       => false,
			'rest_base'          => '',
			'show_in_quick_edit' => true,
		);

		register_taxonomy( 'audience', array( 'post',
			'item',
			'financials',
			'surgeon_loc',
			'ajde_events',
			'product',
			'people',
			'locations',
			'page',
		), $args );
	}

}

new Audience();
