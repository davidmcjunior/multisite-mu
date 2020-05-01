<?php

namespace Site\Multisite;

use Site\DB_User;

/**
 * Class Copier
 * @package Site\Multisite
 */
abstract class Copier extends DB_User
{

	/**
	 * @var Site
	 */
	protected $source_site;

	/**
	 * @var Site
	 */
	protected $target_site;

	/**
	 * @var string
	 */
	protected $source_prefix;

	/**
	 * @var string
	 */
	protected $target_prefix;

	/**
	 * @var string
	 */
	protected $table;


	/**
	 * Copier constructor.
	 *
	 * @param Site $source_site
	 * @param Site $target_site
	 */
	public function __construct( $source_site, $target_site = null )
	{
		$this->source_site   = $source_site;
		$this->target_site   = $target_site;
		$this->source_prefix = $source_site->prefix;

		if ( isset( $target_site ) ) {
			$this->target_prefix = $target_site->prefix;
		}
	}


	/**
	 * @param Site $site
	 */
	public function set_target_site( $site )
	{
		$this->target_site    = $site;
		$this->target_prefix  = $site->prefix;
	}


	/**
	 * @param array $data
	 *
	 * @return void
	 */
	public abstract function copy_all( $data );

	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public abstract function copy_single( $data );

}