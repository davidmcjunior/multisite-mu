<?php

namespace Site\Multisite\Copiers;


use Site\Multisite\Copier;
use Site\Queries\Options\Select\Option_Select_Query;
use Site\Queries\Options\Update\Option_Update_Query;
use Site\Utils\Schema_Copier;

/**
 * Class FWP_Settings_Copier
 * @package Site\Multisite\Copiers
 */
class FWP_Settings_Copier extends Copier
{

	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public function copy_all( $data )
	{
		$opt_name = 'facetwp_settings';
		$source   = $this->source_site;
		$target   = $this->target_site;

		( new Schema_Copier() )
			->copy_plugin_schema( $source, $target, 'facetwp_' );

		$settings = ( new Option_Select_Query( $source ) )
			->execute( array( 'option_name' => $opt_name ) );

		$settings[ 'option_name' ] = $opt_name;

		( new Option_Update_Query( $this->target_site ) )->execute( $settings );

		if ( function_exists( '\FWP()' ) ) {
			$target->make_current_site();
			\FWP()->indexer->index();
			$target->restore_previous_site();
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