<?php

namespace Site\Multisite;

use Site\DB_User;
use Site\Queries\Posts\Select\Post_Query;

/**
 * Class Site
 *
 * Represents a site within the multisite network.
 *
 * @package Site\Multisite
 */
class Site extends DB_User
{

	const MAIN_ID = 1;

	const US_ID = 1;

	const CONTENT_ID = 3;

	const BOARD_PORTAL_ID = 25;

	const STORE_ID = 26;

	const SALES_PORTAL_ID = 26;

	const NO_SITE = null;

	const MAIN_DOMAIN = 'www.site.com';

	const CONTENT_DOMAIN = 'content.site.com';

	const STAGING_BASE_URI = 'site.staging.wpengine.com';

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var Network
	 */
	public $network;

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $domain;


	/**
	 * @param string $domain
	 *
	 * @return bool
	 */
	public static function is_dev_domain( $domain )
	{
		if ( false !== stripos( $domain, '.local' ) ) {
			return true;

		} else if ( false !== stripos( $domain, 'sitedev' ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Site constructor.
	 *
	 * @param int $id
	 * @param Network $network
	 * @param string $domain
	 */
	public function __construct( $id, $network, $domain )
	{
		$this->id      = (int) $id;
		$this->network = $network;
		$this->domain  = $domain;
		$this->prefix  = $this->db()->get_blog_prefix( $id );
	}


	/**
	 * @return int
	 */
	public function get_id()
	{
		return $this->id;
	}


	/**
	 * @return string
	 */
	public function get_prefix()
	{
		return $this->prefix;
	}


	/**
	 * @return string
	 */
	public function get_domain()
	{
		return $this->domain;
	}


	/**
	 * @param int $user_id
	 * @param string $role
	 */
	public function add_user( $user_id, $role = 'Subscriber' )
	{
		$this->make_current_site();

		( new \WP_User( $user_id ) )->add_role( $role );

		$this->restore_previous_site();
	}


	/**
	 * @return true
	 */
	public function make_current_site()
	{
		return switch_to_blog( $this->id );
	}


	/**
	 * @return void
	 */
	public function restore_previous_site()
	{
		if ( $this->id === get_current_blog_id() ) {
			restore_current_blog();
		}
	}


	/**
	 * @param string $guid
	 *
	 * @return array|false
	 */
	protected function find_post_on_site( $guid )
	{
		$post = ( new Post_Query( $this ) )
			->execute( array( 'guid' => $guid ) );

		if ( $post ) {
			return $post;
		}

		return false;
	}

}