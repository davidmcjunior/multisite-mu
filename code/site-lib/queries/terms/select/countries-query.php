<?php

namespace Site\Queries\Terms\Select;

require_once 'tags-for-taxonomy-query.php';

/**
 * Class Countries_Query
 *
 * @package Site\Queries\Terms\Select
 * @deprecated
 */
class Countries_Query extends Tags_For_Taxonomy_Query
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function execute( $data = array() )
	{
		$data[ 'taxonomy' ] = 'countries';

		return parent::execute( $data );
	}

}