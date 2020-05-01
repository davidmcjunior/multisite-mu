<?php

namespace Site\Queries\Posts\Select;

use Site\Query;

/**
 * Class Destination_Tag_For_Site_Query
 *
 * Gets the destination term mapped to a certain site 'id' ('blog_id').
 * takes an array containing 'site_id' => <val> as the parameter.
 *
 * @package Site\Queries
 */
class Destination_Tag_For_Site_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return string|false
	 */
	public function execute( $data = array() )
	{
		$pre = $this->site->get_prefix();

		if ( ! array_key_exists( 'site_id', $data ) ) {
			return false;
		}

		$id = $data[ 'site_id' ];

		$sql = $this->db()->prepare( "
			SELECT t.name, t.slug
			FROM {$pre}terms t
			INNER JOIN {$pre}termmeta tm ON tm.term_id = t.term_id
			WHERE tm.meta_key = 'site_id'
			AND tm.meta_value = %d",
			array( $id )
		);

		$results = $this->db()->get_row( $sql, ARRAY_A );

		if ( $results ) {
			return $results;
		}

		return false;
	}

}