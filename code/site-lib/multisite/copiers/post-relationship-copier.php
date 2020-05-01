<?php

namespace Site\Multisite\Copiers;

use Site\Multisite\Copier;
use Site\Multisite\Site;

/**
 * Class Post_Relationship_Copier
 * @package Site\Multisite\Copiers
 */
class Post_Relationship_Copier extends Copier
{

	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function copy_all( $data )
	{
		$and        = '';
		$source_id  = $data[ 'source_post_id' ];
		$target_id  = $data[ 'target_post_id' ];
		$target_pre = $this->target_prefix;
		$source_pre = $this->source_prefix;

		if ( Site::CONTENT_ID === (int) $this->source_site->id ) {
			$and .= " AND tt.taxonomy NOT IN ('destination')";
		}

		$this->db()->delete( "{$target_pre}term_relationships", array( 'object_id' => $target_id ) );

		$sql = $this->db()->prepare( "
			INSERT IGNORE INTO {$target_pre}term_relationships (object_id, term_taxonomy_id, term_order)
			SELECT %d AS object_id, tt2.term_taxonomy_id, tr.term_order
			FROM {$source_pre}term_relationships tr
			INNER JOIN {$source_pre}term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$source_pre}terms t          ON t.term_id = tt.term_id
			INNER JOIN {$target_pre}terms t2          ON t2.slug = t.slug
			INNER JOIN {$target_pre}term_taxonomy tt2 ON tt2.term_id = t2.term_id
			WHERE tr.object_id = %d{$and}",
			array( $target_id, $source_id )
		);

		$this->db()->query( $sql );
	}


	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function copy_single( $data )
	{
		// TODO: Implement copy_single() method.
	}

}