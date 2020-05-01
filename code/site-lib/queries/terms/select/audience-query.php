<?php

namespace Site\Queries\Terms\Select;

require_once 'tags-for-taxonomy-query.php';

/**
 * Class Audience_Query
 * @package Site\Queries\Terms\Select
 */
class Audience_Query extends Tags_For_Taxonomy_Query
{

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function execute( $data = array() )
	{
		$data[ 'taxonomy' ] = 'audience';

		return parent::execute( $data );
	}

}