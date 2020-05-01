<?php

namespace Site\Queries\Options\Update;

use Site\Query;

/**
 * Class Option_Update_Query
 * @package Site\Queries\Options\Update
 */
class Option_Update_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$table    = $this->site->get_prefix() . 'options';
		$autoload = 'Yes';

		if ( ! array_key_exists( 'option_name', $data ) ||
		     ! array_key_exists( 'option_value', $data ) ) {
			return null;
		}

		if ( array_key_exists( 'autoload', $data ) ) {
			$autoload = $data[ 'autoload' ];
		}

		$data[ 'autoload' ] = $autoload;

		$this->db()->delete( $table, array( 'option_name' => $data[ 'option_name' ] ) );
		$this->db()->insert( $table, $data );
	}

}