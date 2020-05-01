<?php

namespace Site\Queries\Posts\Update;

use Site\Multisite\Destination_Mapper;
use Site\Query;


class Post_Destination_Meta_Update_Query extends Query
{
	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$pre  = $this->site->get_prefix();
		$table = $pre . 'postmeta';

		if ( ! array_key_exists( 'post_id', $data ) ||
		     ! array_key_exists( 'destinations', $data ) ) {
			return null;
		}

		$where = array(
			'post_id'  => $data[ 'post_id' ],
			'meta_key' => Destination_Mapper::META_KEY
		);


		$this->db()->delete( $table, $where );

		$where[ 'meta_value' ] = $data[ 'destinations' ];

		$this->db()->insert( $table, $where );
	}

}