<?php

namespace Site\Queries\Terms\Select;

use Site\Multisite\Site;
use Site\Query;

/**
 * Class Destination_Site_Ids_Query
 * @package Site\Queries\Terms\Select
 */
class Destination_Site_Ids_Query extends Query
{

	/**
	 * @param array $data
	 *
	 * @return array|false
	 */
	public function execute( $data = array() )
	{
		$pre = $this->db()->get_blog_prefix( Site::CONTENT_ID );

		$sql = "
			SELECT CAST(tm.meta_value AS UNSIGNED) AS site_id, GROUP_CONCAT(t.name SEPARATOR ', ') AS destinations
			FROM {$pre}terms t
			INNER JOIN {$pre}term_taxonomy tt ON tt.term_id = t.term_id
			INNER JOIN {$pre}termmeta tm ON tm.term_id = t.term_id
			WHERE tt.taxonomy = 'destination'
			AND tm.meta_key = 'site_id'
			GROUP BY tm.meta_value";

		return $this->db()->get_results( $sql, ARRAY_A );
	}

}