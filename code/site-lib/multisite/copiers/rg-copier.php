<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 4/27/17
 * Time: 3:36 PM
 */

namespace Site\Multisite\Copiers;

use Site\Multisite\Copier;
use Site\Utils\Schema_Copier;

/**
 * Class RG_Copier
 * @package Site\Multisite\Copiers
 */
class RG_Copier extends Copier
{

	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function copy_all( $data )
	{
		$source      = $this->source_site;
		$target      = $this->target_site;
		$content_pre = $this->source_prefix;
		$target_pre  = $this->target_prefix;

		( new Schema_Copier() )
			->copy_plugin_schema( $source, $target, 'gf_' );

		// keep the primary key synced with content's
		$this->db()->query( "ALTER TABLE {$target_pre}gf_form CHANGE id id INT(11)" );

		foreach ( array( 'gf_form', 'gf_form_meta' ) as $table ) {
			$fields = $this->db()->get_results( "DESCRIBE {$content_pre}{$table}", ARRAY_A );
			$fields = implode( ', ', array_column( $fields, 'Field' ) );

			$this->db()->query( "
				TRUNCATE TABLE {$target_pre}{$table}"
			);

			$this->db()->query( "
				INSERT IGNORE INTO {$target_pre}{$table} ({$fields})
				SELECT {$fields} FROM {$content_pre}{$table}"
			);
		}
	}


	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function copy_single( $data )
	{
		// TODO: Implement copy_single() method.
	}

}