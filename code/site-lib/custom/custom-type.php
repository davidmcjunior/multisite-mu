<?php

namespace Site\Custom;

/**
 * Class Custom_Type
 * @package Site\Custom
 */
abstract class Custom_Type
{

	/**
	 * Custom_Type constructor.
	 *
	 * @param string $hook
	 */
	public function __construct( $hook = 'init' )
	{
		add_action( $hook, array( $this, 'register' ) );
	}


	/**
	 * @return void
	 *
	 * Override this method to configure the call to WP's register_taxonomy()
	 */
	public abstract function register();

}