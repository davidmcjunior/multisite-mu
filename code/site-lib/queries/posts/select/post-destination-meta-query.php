<?php

namespace Site\Queries\Posts\Select;

use Site\Multisite\Destination_Mapper;
use Site\Query;
use Site\Multisite\Network;

class Post_Destination_Meta_Query extends Query
{
	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$pre  = $this->site->get_prefix();

		if ( ! array_key_exists( 'post_id', $data ) ) {
			return null;
		}

		$id = $data[ 'post_id' ];

		$sql = $this->db()->prepare( "
			SELECT meta_value FROM {$pre}postmeta
			WHERE post_id = %d AND meta_key = %s",
			array( $id, Destination_Mapper::META_KEY )
		);

		$results = $this->db()->get_row( $sql, ARRAY_A );

		if ( $results ) {
			$results = $results[ 'meta_value' ];
		}

		return $results;
	}


}