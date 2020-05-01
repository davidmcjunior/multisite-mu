<?php

namespace Site\Queries\Posts\Select;

use Site\Query;

/**
 * Class Post_Destinations_Query
 * @package Site\Queries
 */
class Post_Destinations_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return int[]
	 */
	public function execute( $data = array() )
	{
		$pre = $this->site->get_prefix();

		if ( ! array_key_exists( 'post_id', $data ) ) {
			return null;
		}

		$id = $data[ 'post_id' ];

		$sql = $this->db()->prepare("
			SELECT tm.meta_value as site_id
			FROM {$pre}term_relationships tr
			INNER JOIN {$pre}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$pre}terms t          ON t.term_id = tt.term_id
			INNER JOIN {$pre}termmeta tm      ON tm.term_id = t.term_id
			WHERE tr.object_id = %d
			AND tt.taxonomy = 'destination'
			AND tm.meta_key = 'site_id'",
			array( $id )
		);

		$site_ids = $this->db()->get_results( $sql, ARRAY_A );

		$site_ids = array_map( function( $row ) {
			return (int) $row[ 'site_id' ];
		}, $site_ids );

		return $site_ids;
	}

}