<?php

namespace Site\Queries\Terms\Select;

use Site\Query;

/**
 * Class Taxonomy_Ids_From_Slugs_Query
 *
 * Gets all term_taxonomy_ids associated with a term slug or slugs.
 * pass the key 'slugs' with an an array of slugs, or the key 'slug' with a single value.
 *
 * @package Site\Queries
 */
class Taxonomy_Ids_From_Slugs_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function execute( $data = array() )
	{
		$pre    = $this->site->get_prefix();
		$ids    = array();
		$slugs  = null;
		$single = false;

		if ( array_key_exists( 'slugs', $data ) && is_array( $data[ 'slugs' ] ) ) {
			$slugs = implode( "','", $data[ 'slugs' ] );

			foreach ( $slugs as $slug ) {
				if ( true === $this->check_for_whitespace( $slug ) ) {
					return null;
				}
			}

		} else if ( array_key_exists( 'slug', $data ) ) {
			$slugs  = $data[ 'slug' ];

			if ( true === $this->check_for_whitespace( $slugs ) ) {
				return null;
			}

			$single = true;

		} else {
			return null;
		}

		$sql = "
			SELECT GROUP_CONCAT(tt.term_taxonomy_id) AS ids
			FROM {$pre}term_taxonomy tt
			INNER JOIN {$pre}terms t ON t.term_id = tt.term_id
			WHERE t.slug IN ('{$slugs}')";

		$results = $this->db()->get_row( $sql, ARRAY_A );

		if ( $results ) {
			$ids = explode( ',', $results[ 'ids' ] );

			$ids = array_map( function( $id ) {
				return (int) $id;
			}, $ids );

			if ( true === $single ) {
				$ids = array_shift( $ids );
			}
		}

		return $ids;
	}


	/**
	 * @param string $str
	 *
	 * @return bool
	 */
	private function check_for_whitespace( $str )
	{
		if ( preg_match( '/\s/', $str ) ) {
			return true;

		} else {
			return false;
		}
	}

}