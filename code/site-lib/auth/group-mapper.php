<?php

namespace Site\Auth;

use Site\DB_User;
use Site\Multisite\Network;

/**
 * Class Group_Mapper
 * @package Site\Auth
 */
abstract class Group_Mapper extends DB_User
{

	/**
	 * @var Network
	 */
	protected $network;


	/**
	 * Group_Mapper constructor.
	 *
	 * @param Network $network
	 * @param string $action
	 * @param int $argc
	 *
	 */
	public function __construct( $network, $action, $argc = 0 )
	{
		$this->network = $network;

		add_action( $action, array( $this, 'map_user_groups' ), 10, $argc );
	}

	/**
	 * @param array ...$args An array containing the argument values
	 *
	 * @return bool
	 */
	public abstract function map_user_groups( ...$args );

}