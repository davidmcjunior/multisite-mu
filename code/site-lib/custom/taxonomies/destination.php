<?php

namespace Site\Custom\Taxonomies;

use Site\Custom\Custom_Type;

/**
 * Class Destination
 * @package Site\Custom\Taxonomies
 */
class Destination extends Custom_Type
{

	/**
	 * @return void
	 */
	public function register()
	{
		$labels = array(
			'name'          => __( 'Destinations', '' ),
			'singular_name' => __( 'Destination', '' ),
		);

		$args = array(
			'label'              => __( 'Destinations', '' ),
			'labels'             => $labels,
			'public'             => true,
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'destination', 'with_front' => true, ),
			'show_admin_column'  => true,
			'show_in_rest'       => false,
			'rest_base'          => '',
			'show_in_quick_edit' => true,
		);

		register_taxonomy( 'destination', array( 'post',
			'item',
			'financials',
			'surgeon_loc',
			'ajde_events',
			'people',
			'product',
			'locations',
			'board_meeting',
			'board_announcement',
			'board_event',
			'board_documents',
			'committee_members'
		), $args );
		// add_action( 'edit_form_after_editor', array( $this, 'disable_board_portal_checkbox' ) );
	}


	/**
	 * @return string
	 */
	public function disable_board_portal_checkbox()
	{
		echo '<script type="text/javascript"> $(document).ready( function() { $(\'li:contains("board.site.com")\').hide(); }); </script>';
	}

}

new Destination();
