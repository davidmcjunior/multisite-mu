<?php

namespace Site\Traits;

/**
 * Trait Accesses_Db
 *
 * Abstract away the global $wpdb object and migration method.
 *
 * @package Site\Traits
 */
trait Accesses_Db_Trait
{
	/**
	 * No green M&Ms!!!
	 *
	 * @return bool|\hyperdb|\QM_DB|\wpdb
	 */
	protected function db()
	{
		global $wpdb;
		return $wpdb;
	}

}