<?php

namespace Site\Traits;

/**
 * Trait Gives_Status_Trait
 *
 * Provides methods for putting script status output via ajax.
 *
 * @package Site\Traits
 */
trait Gives_Status_Trait
{

	/**
	 * @var string
	 */
	protected $status_transient_key;


	/**
	 * @param int $timeout
	 */
	protected function delete_status( $timeout )
	{
		$start_time = time();

		while ( true ) {
			$time = time() - $start_time;
			if ( $time > $timeout ) {
				break;
			}
		}

		delete_transient( $this->status_transient_key );
	}


	/**
	 * @param string $message
	 */
	protected function set_status( $message )
	{
		set_transient( $this->status_transient_key, $message );
	}


	/**
	 * Link this method to a wp_ajax action hook to get and echo a json-encoded status transient.
	 *
	 * @return string
	 */
	public function get_status_action()
	{
		$status = get_transient( $this->status_transient_key );

		echo json_encode( $status );
		exit;
	}


	/**
	 * @param string $plugin_name
	 */
	protected function init_status_reporting( $plugin_name )
	{
		$this->status_transient_key = 'site_' . $plugin_name . '_status';

		add_action(
			'wp_ajax_get_' . $plugin_name . '_status', array( $this, 'get_status_action' )
		);
	}

}
