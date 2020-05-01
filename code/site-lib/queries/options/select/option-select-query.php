<?php

namespace Site\Queries\Options\Select;

use Site\Query;

/**
 * Class FacetWP_Settings_Select_Query
 *
 * Use this to set a wp_options value.
 * 'option_name' => <value>
 *
 * @package Site\Queries\Options\Select
 */
class Option_Select_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$pre = $this->site->prefix;
		$sql = null;

		if ( array_key_exists( 'option_name', $data ) ) {
			$sql = $this->db()->prepare( "
				SELECT option_value, autoload
				FROM {$pre}options 
				WHERE option_name = %s", $data[ 'option_name' ]
			);

		} else if ( array_key_exists( 'option_name_like', $data ) ) {
			$sql = $this->db()->prepare( "
				SELECT option_name, option_value, autoload
				FROM {$pre}options 
				WHERE option_name LIKE %%%s%%",
				$this->db()->esc_like( $data[ 'option_name' ] )
			);

		} else {
			return null;
		}

		return $this->db()->get_row( $sql, ARRAY_A );
	}

}