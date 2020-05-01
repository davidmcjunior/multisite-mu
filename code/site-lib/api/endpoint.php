<?php

namespace Site\Api;


/**
 * Class Endpoint
 *
 * Extend to create a custom API endpoint.
 *
 * @package Site
 */
abstract class Endpoint
{

	/**
	 * Endpoint constructor.
	 *
	 * @param string $namespace
	 * @param string $route
	 * @param string $methods
	 */
	public function __construct( $namespace, $route, $methods = 'POST' )
	{
		add_action(
			'rest_api_init',
			function () use ( $namespace, $route, $methods ) {
				register_rest_route(
					$namespace,
					$route,
					array(
						'methods'  => $methods,
						'callback' => array( $this, 'handle' ),
					)
				);
			}
		);
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	public abstract function handle( $request );

}