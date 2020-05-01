<?php

namespace Site\Queries\Terms\Insert;

use Site\Query;


/**
 * Class Term_Meta_And_Taxonomy_Insert_All_Query
 * @package Site\Queries\Terms\Insert
 */
class Term_Meta_And_Taxonomy_Insert_All_From_Content_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function execute( $data = array() )
	{
		$content_pre = $this->site->network->content_site->get_prefix();
		$target_pre  = $this->site->get_prefix();
		$tables      = array( 'terms', 'term_taxonomy', 'termmeta' );
		$db          = $this->db();

		$synced_term_ids = $db->get_results( "
			SELECT t.term_id
			FROM {$target_pre}terms t
			INNER JOIN {$target_pre}termmeta tm ON tm.term_id = t.term_id
			WHERE tm.meta_key = 'synced'
			", ARRAY_A
		);

		$synced_term_ids = $this->get_in_string( array_column( $synced_term_ids, 'term_id' ) );

		foreach ( $tables as $table ) {
			$db->query( "
				DELETE FROM {$target_pre}{$table} WHERE term_id IN {$synced_term_ids}
			");
		}

		$db->query( "
			INSERT IGNORE INTO {$target_pre}terms (name, slug, term_group)
			SELECT name, slug, term_group FROM {$content_pre}terms
			WHERE slug NOT IN (SELECT slug FROM {$target_pre}terms)
			AND NOT slug = 'uncategorized'"
		);

		$db->query( "
			INSERT IGNORE INTO {$target_pre}termmeta (term_id, meta_key, meta_value)
			SELECT trg.term_id, 'synced', ''
			FROM {$target_pre}terms trg
			LEFT JOIN {$content_pre}terms cnt ON cnt.slug = trg.slug
			WHERE cnt.term_id IS NOT NULL
			AND NOT trg.term_id IN (
				SELECT term_id FROM {$target_pre}terms WHERE slug = 'uncategorized'
			)"
		);

		$db->query( "
			INSERT IGNORE INTO {$target_pre}term_taxonomy (term_id, taxonomy, description, parent, count)
			SELECT t.term_id, ctt.taxonomy, ctt.description, ctt.parent, ctt.count
			FROM   {$content_pre}term_taxonomy ctt
			INNER JOIN {$content_pre}terms ct  ON ct.term_id = ctt.term_id
			INNER JOIN {$target_pre}terms t    ON t.slug = ct.slug
			WHERE t.slug NOT IN (
				SELECT t.slug FROM {$target_pre}terms t
				INNER JOIN {$target_pre}term_taxonomy tt
				ON tt.term_id = t.term_id
			)
			AND NOT t.term_id IN (
				SELECT term_id FROM {$target_pre}terms WHERE slug = 'uncategorized'
			)"
		);
	}

}