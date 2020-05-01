<?php

namespace Site\Custom\Terms\Meta;

use Site\Custom\Custom_Type;
use Site\Multisite\Site;
use Site\Queries\Terms\Select\Destination_Site_Ids_Query;

/**
 * Class Site_Id
 * @package Site\Custom\Terms\Meta
 */
class Site_Id extends Custom_Type
{

	/**
	 * Site_Id constructor.
	 */
	public function __construct()
	{
		parent::__construct( 'init' );

		if ( Site::CONTENT_ID === get_current_blog_id() ) {
			add_action( 'destination_add_form_fields', array( $this, 'destination_add_form_fields_action' ), 10, 2 );
			add_action( 'destination_edit_form_fields', array( $this, 'destination_edit_form_fields_action' ), 10, 2 );
			add_action( 'created_destination', array( $this, 'save_destination_site_id' ), 10, 2 );
			add_action( 'edit_destination', array( $this, 'save_destination_site_id' ), 10, 2 );
		}
	}


	/**
	 * @return void
	 */
	public function register()
	{
		register_meta( 'term', 'site_id', array() );
	}


	/**
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	public function destination_add_form_fields_action( $taxonomy )
	{
		$this->add_form_site_id_field( 'add' );
	}


	/**
	 * @param string $taxonomy
	 *
	 * @return void
	 */
	public function destination_edit_form_fields_action( $term, $taxonomy )
	{
		$this->add_form_site_id_field( 'edit', $term->term_id );
	}


	/**
	 * @param string $action
	 * @param int $term_id
	 *
	 * @return void
	 */
	private function add_form_site_id_field( $action, $term_id = null )
	{
		if ( ! in_array( $action, array( 'add', 'edit' ) ) ) {
			return;
		}

		$site = null;

		$selected = array(
			'site_id' => null,
			'domain'  => null
		);;

		$site_data = $this->get_sites_data();

		if ( isset( $term_id ) ) {
			$selected_id = array_pop( get_term_meta( $term_id, 'site_id' ) );

			if ( isset( $selected_id ) ) {
				$site = get_site( $selected_id );
				$site = $site->domain;
			}

			$selected[ 'site_id' ] = $selected_id;
			$selected[ 'domain' ]  = $site;
		}

		$this->enqueue_jquery();

		include_once "site-id/templates/{$action}-form-field.php";
	}


	/**
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_destination_site_id( $term_id )
	{
		$site_id = $status = null;

		if ( isset( $_POST[ 'site_id' ] ) && is_numeric( $_POST[ 'site_id' ] ) ) {
			if ( Site::CONTENT_ID !== (int) $_POST[ 'site_id' ] ) {
				$site_id = (int) $_POST[ 'site_id' ];
			}
		}

		update_term_meta( $term_id, 'site_id', $site_id );
	}


	/**
	 * @return array
	 */
	private function get_sites_data()
	{
		$site_data = array();

		$dest_query_results = ( new Destination_Site_Ids_Query() )
			->execute( array() );

		foreach ( get_sites() as $site ) {
			$id = (int) $site->blog_id;

			if ( Site::CONTENT_ID === $id ) {
				continue;
			}

			$site_data[ $id ] = array(
				'domain'           => $site->domain,
				'destination_tags' => null
			);
		}

		foreach ( $dest_query_results as $row ) {
			if ( null !== $row[ 'site_id' ] ) {
				$site_data[ (int) $row[ 'site_id' ] ][ 'destination_tags' ] = $row[ 'destinations' ];
			}
		}

		krsort( $site_data );

		return $site_data;
	}


	/**
	 * @return void
	 */
	private function enqueue_jquery()
	{
		$path = get_stylesheet_directory_uri() . '/';

		$scripts = array(
			'jquery-ui-style'           => 'css/jquery-ui/jquery-ui.min.css',
			'jquery-ui-structure-style' => 'css/jquery-ui/jquery-ui.structure.min.css',
			'jquery-ui-theme-style'     => 'css/jquery-ui/jquery-ui.theme.css',
		);

		foreach ( $scripts as $handle => $dir ) {
			wp_register_style( $handle, $path . $dir );
			wp_enqueue_style( $handle );
		}

		wp_register_script( 'jquery-ui', $path . 'js/jquery-ui/jquery-ui.min.js' );
		wp_enqueue_script( 'jquery-ui' );
	}

}

new Site_Id();