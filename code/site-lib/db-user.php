<?php

namespace Site;

use Site\Traits\Accesses_Db_Trait;
use Throwable;

/**
 * Class DB_User
 *
 * Subclasses have access to the $wpdb object as the instance method $this->db().
 *
 * @package Site
 */
abstract class DB_User
{
	use Accesses_Db_Trait;

	/**
	 * @param string $sql
	 * @param bool $execute
	 *
	 * @return array
	 */
	protected function db_delta( $sql, $execute = true )
	{
		return dbDelta( $sql, $execute );
	}

}