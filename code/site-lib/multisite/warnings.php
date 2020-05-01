<?php

namespace Site\Multisite;

/**
 * Class Warnings
 * @package Site\Multisite
 */
class Warnings
{

	/**
	 * @var Network
	 */
	private $network;


	/**
	 * Warnings constructor.
	 *
	 * @param Network $network
	 */
	public function __construct( $network )
	{
		$this->network = $network;
		$this->add_actions();
	}


	/**
	 * @return string
	 */
	private function get_message()
	{
		$message = "WARNING: You are editing a library item on a non-content site. All your changes may eventually be lost.";

		return '<script type="text/javascript">alert("' . $message . '");</script>';
	}


	/**
	 * @return void
	 */
	public function warn_on_edit_action()
	{
		if ( Site::BOARD_PORTAL_ID === (int) get_current_blog_id() ) {
			return;
		}

		global $post;

		$warn_list = array(
			'post',
			'attachment',
			'financials',
			'item',
			'surgeon_loc',
			'revision'
		);

		if ( in_array( $post->post_type, $warn_list ) ) {
			echo $this->get_message();
		}
	}


	/**
	 * @return string[]
	 */
	public function set_row_actions()
	{
		return array( 'Restore', 'Trash', 'Delete Permanently', 'Preview', 'View' );
	}


	/**
	 * @return void
	 */
	private function add_actions()
	{
		$ignore = array( Site::BOARD_PORTAL_ID, Site::CONTENT_ID );

		if ( in_array( (int) get_current_blog_id(), $ignore ) ) {
			return;
		}

		add_action( 'edit_form_after_editor', array( $this, 'warn_on_edit_action' ) );
		add_filter( 'post_row_actions', array( $this, 'set_row_actions' ), 10 );
	}

}