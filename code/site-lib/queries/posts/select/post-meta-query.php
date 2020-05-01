<?php

namespace Site\Queries\Posts\Select;

use Site\Multisite\Destination_Mapper;
use Site\Multisite\Network;
use Site\Multisite\Site;
use Site\Query;

/**
 * Class Post_Meta_Query
 * @package Site\Queries
 */
class Post_Meta_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function execute( $data = array() )
	{
		$pre  = $this->site->get_prefix();
		$sort = false;
		$srtd = array();

		if ( ! array_key_exists( 'post_id', $data ) ) {
			return null;
		}

		if ( in_array( 'sorted', $data ) ) {
			$sort = true;
		}

		$id = $data[ 'post_id' ];

		$sql = $this->db()->prepare( "
			SELECT meta_key, meta_value FROM {$pre}postmeta 
			WHERE post_id = %d
			AND meta_key != %s",
			array( $id, Destination_Mapper::META_KEY )
		);

		$results = $this->db()->get_results( $sql, ARRAY_A );

		if ( ! $sort ) {
			return $results;
		}

		foreach ( $results as $row ) {
			$srtd[ $row[ 'meta_key' ] ] = $row[ 'meta_value' ];
		}

		return $srtd;
	}

}