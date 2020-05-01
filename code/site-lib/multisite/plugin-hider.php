<?php

namespace Site\Multisite;

/**
 * Class Plugin_Hider
 * @package Site\Multisite
 */
class Plugin_Hider
{

	/**
	 * @var string[]
	 */
	public $content_only_plugins = array(
		'site-object-pusher',
		'site-content-initializer'
	);


	/**
	 * Plugin_Hider constructor.
	 *
	 * @param \Site\Multisite\Network $network
	 */
	public function __construct( $network )
	{
		if ( ! $network->content_is_current_site() ) {
			add_action( 'pre_current_active_plugins', array( $this, 'pre_activate_action' ) );
		}
	}


	/**
	 * @return void
	 */
	public function pre_activate_action()
	{
		global $wp_list_table;

		$plugin_list = &$wp_list_table->items;

		$hide_list   = array_map( function( $name ) {
			return $name . '/index.php';
		}, $this->content_only_plugins );

		foreach ( $plugin_list as $key => $val ) {
			if ( in_array( $key, $hide_list ) ) {
				unset( $plugin_list[ $key ] );
			}
		}
	}

}