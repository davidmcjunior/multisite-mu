<?php

namespace Site\Api\Users;

use Site\Api\Endpoint;

/**
 * Class Update_Groups_Endpoint
 * @package Site\Api\Users
 */
class Update_Groups_Endpoint extends Endpoint
{

	/**
	 * Update_Groups_Endpoint constructor.
	 */
	public function __construct()
	{
		parent::__construct( 'v1', 'api/user/groups/update', 'POST' );
	}

	/**
	 * @param $request
	 * @return mixed
	 */
	public function handle( $request )
	{
		// error_log( print_r ( "AUTH: " . $request) );
	}

}