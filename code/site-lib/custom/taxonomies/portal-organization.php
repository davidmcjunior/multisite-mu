<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Portal_Organization
 * @package Site\Custom\Taxonomies
 */
class Portal_Organization extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			"name" => __( "Portal Organization", "custom-post-type-ui" ),
			"singular_name" => __( "Portal Organization", "custom-post-type-ui" ),
		);
	
		$args = array(
			"label" => __( "Portal Organization", "custom-post-type-ui" ),
			"labels" => $labels,
			"public" => true,
			"publicly_queryable" => true,
			"hierarchical" => true,
			"show_ui" => true,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"query_var" => true,
			"rewrite" => array( 'slug' => 'portal_organization', 'with_front' => true, ),
			"show_admin_column" => false,
			"show_in_rest" => false,
			"rest_base" => "",
			"show_in_quick_edit" => false,
			);
		register_taxonomy( "portal_organization", array( "product", "people" ), $args );
	}
}

new Portal_Organization();

