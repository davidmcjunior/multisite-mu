<?php

namespace Site\Queries\Posts\Select;

use Site\Query;

/**
 * Class Post_Query
 * @package Site\Queries
 */
class Post_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array|null
	 */
	public function execute( $data = array() )
	{
		$prefix = $this->site->get_prefix();
		$where  = "WHERE ";
		$val    = null;

		if ( array_key_exists( 'guid', $data ) ) {
			$where .= "guid";
			$val = $data[ 'guid' ];
		}

		if ( array_key_exists( 'post_id', $data ) ) {
			$where .= "ID";
			$val = $data[ 'post_id' ];
		}

		if ( array_key_exists( 'post_type', $data ) ) {
			$where .= "post_type";
			$val = $data[ 'post_type' ];
		}

		if ( array_key_exists( 'post_name', $data ) ) {
			$where .= "post_name";
			$val = $data[ 'post_name' ];
		}

		$where .=  " = %s";

		$sql = $this->db()->prepare( "
			SELECT * FROM {$prefix}posts {$where}",
			array( $val )
		);

		if ( in_array( 'post_type', array_keys( $data ) ) ) {
			return $this->db()->get_results( $sql, ARRAY_A );
		}

		return $this->db()->get_row( $sql, ARRAY_A );
	}

}