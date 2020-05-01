<?php

namespace Site\Queries\Terms\Select;

use Site\Query;

/**
 * Class Tags_For_Taxonomy_Query
 *
 * Returns an array of slugs grouped by a taxonomy name.
 * the key value params taken are 'taxonomy', and optional 'sort_by', which can be either
 * 'term_taxonomy_id' or 'slug', and optional 'slugs_only'.
 *
 * @package Site\Queries\Terms\Select
 */
class Tags_For_Taxonomy_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array|false
	 */
	public function execute( $data = array() )
	{
		$pre  = $this->site->get_prefix();
		$tax  = null;
		$key  = 'slug';
		$srtd = array();

		if ( ! array_key_exists( 'taxonomy', $data ) ) {
			return false;
		}

		$tax = $data[ 'taxonomy' ];

		if ( array_key_exists( 'sort_by', $data ) ) {
			switch ( $data[ 'sort_by' ] ) {
				case 'term_taxonomy_id':
					$key = 'term_taxonomy_id';
					break;
				case 'slug':
					$key = 'slug';
					break;
			}
		}

		$sql = $this->db()->prepare( "
			SELECT t.slug, t.name, tt.term_taxonomy_id
			FROM {$pre}terms t
			INNER JOIN {$pre}term_taxonomy tt ON tt.term_id = t.term_id
			WHERE tt.taxonomy = %s",
			$tax
		);

		$results = $this->db()->get_results( $sql, ARRAY_A );

		if ( in_array( 'slugs_only', $data ) ) {
			foreach ( $results as $row ) {
				$srtd[ (int) $row[ $key ] ] = $row[ 'slug' ];
			}

			return $srtd;
		}

		foreach ( $results as $row ) {
			$srtd[ $row[ $key ] ] = $row;
		}

		return $srtd;
	}

}