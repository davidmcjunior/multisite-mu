<?php

namespace Site\Utils;

use Site\DB_User;

/**
 * Class Logger
 * @package Site\Utils
 */
class Logger extends DB_User
{

	/**
	 * @var string
	 */
	protected $log_table = 'wp_site_log';

	/**
	 * @param $data
	 */
	public function log( $data )
	{
		$log_data_str = print_r( $data, true );

		$this->db()->insert(
			$this->log_table, array( 'details' => $log_data_str )
		);
	}
}