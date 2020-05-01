<?php

namespace Site\Queries\Posts\Select;

use Site\Query;


/**
 * Class Posts_For_Country_Query
 * @package Site\Queries
 */
class Posts_For_Terms_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function execute( $data = array() )
	{
		$posts  = array();
		$terms  = '';
		$types  = '()';
		$column = null;
		$prefix = $this->site->get_prefix();

		if ( ! array_key_exists( 'terms', $data ) || ! is_array( $data[ 'terms' ] ) ) {
			return null;
		}

		if ( array_key_exists( 'column_only', $data ) ) {
			$column = $data[ 'column_only' ];
		}

		if ( array_key_exists( 'post_types', $data ) && is_array( $data[ 'post_types' ] ) ) {
			$types = $this->get_in_string( $data[ 'post_types' ] );
		}

		if ( ! in_array( 'all', $data[ 'terms' ] ) ) {
			$terms = $this->get_in_string( $data[ 'terms' ] );

			$sql = "
				SELECT p.*
				FROM {$prefix}posts p
				INNER JOIN {$prefix}term_relationships tr ON tr.object_id = p.ID
				INNER JOIN {$prefix}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				INNER JOIN {$prefix}terms t ON t.term_id = tt.term_id
				WHERE t.slug IN {$terms}
				UNION
				SELECT * FROM {$prefix}posts WHERE post_type IN {$types}";

		} else {
			$sql = "SELECT * FROM {$prefix}posts WHERE post_type IN {$types}";
		}

		$results = $this->db()->get_results( $sql, ARRAY_A );

		if ( ! is_array( $results ) ) {
			return $posts;
		}

		if ( null !== $column ) {
			$posts = array_column( $results, $column );

		} else {
			$posts = $results;
		}

		return $posts;
	}

}