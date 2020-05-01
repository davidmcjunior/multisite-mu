<?php

namespace Site\Multisite;

use Site\Auth\Mepr_Group_Mapper;
use Site\DB_User;
use Site\Queries\Posts\Select\Destination_Tag_For_Site_Query;

/**
 * Class Network
 * @package Site\Multisite
 */
class Network extends DB_User
{

	/**
	 * @var Network
	 */
	private static $instance;

	/**
	 * @var Site[]
	 */
	private $sites;

	/**
	 * @var Site
	 */
	public $content_site;

	/**
	 * @var Site
	 */
	public $main_site;

	/**
	 * @var Site
	 */
	public $current_site;


	/**
	 * @return Network
	 */
	public static function get_instance()
	{
		if ( ! isset( static::$instance ) ) {
			static::$instance = new Network();
		}

		return static::$instance;
	}


	/**
	 * @param Site $site
	 * @param $callback
	 *
	 * @return mixed|false
	 */
	public function run_on_site( $site, $callback )
	{
		$return = null;

		$this->switch_site( $site->id );

		if ( is_callable( $callback ) ) {
			$return = call_user_func( $callback );

		} else {
			$return = false;
		}

		$this->restore_previous_site();

		return $return;
	}


	/**
	 * @param int $site_id
	 *
	 * @return bool
	 */
	public function switch_site( $site_id )
	{
		if ( $this->site_exists( $site_id ) ) {
			switch_to_blog( $site_id );
			$this->current_site = $this->sites[ $site_id ];

			return true;
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function restore_previous_site()
	{
		$success = restore_current_blog();

		if ( true === $success ) {
			$this->current_site = $this->sites[ (int) get_current_blog_id() ];
		}

		return $success;
	}


	/**
	 * @param int $id
	 *
	 * @return Site|false
	 */
	public function get_site( $id )
	{
		if ( ! $this->site_exists( $id ) ) {
			return false;
		}

		return $this->sites[ $id ];
	}


	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function site_exists( $id )
	{
		return array_key_exists( $id, $this->sites );
	}


	/**
	 * Create a new 'destination' term corresponding to the domain of
	 * the newly-created site.
	 *
	 * @param int    $blog_id Blog ID.
	 * @param int    $user_id User ID.
	 * @param string $domain  Site domain.
	 */
	public function wpmu_new_blog_action( $blog_id, $user_id, $domain )
	{
		$this->switch_site( Site::CONTENT_ID );

		$domain = str_replace( 'dev.', '.', $domain );

		// Make sure that the 'dev' sites don't end up with 'dev' destination terms...
		$term_arr = wp_insert_term( $domain, 'destination' );

		if ( is_array( $term_arr ) ) {
			add_term_meta( $term_arr[ 'term_id' ], 'site_id', $blog_id );
		}

		$this->restore_previous_site();
	}


	/**
	 * @param int $term_id
	 * @param int $site_id
	 *
	 * @return void
	 */
	public function create_destination_action( $term_id, $site_id = null )
	{
		if ( ! $this->content_is_current_site() ) {
			return;
		}

		$data = array(
			'term_id'    => $term_id,
			'meta_key'   => 'site_id',
			'meta_value' => $site_id
		);

		$this->db()->insert( "{$this->content_site->prefix}termmeta", $data );
	}


	/**
	 * @return bool
	 */
	public function content_is_current_site()
	{
		if ( Site::CONTENT_ID === (int) get_current_blog_id() ) {
			return true;
		}

		return false;
	}


	/**
	 * @param string[] $exclude
	 *
	 * @return Site[]
	 */
	public function get_all_sites( $exclude = array() )
	{
		$return = array();

		foreach ( $this->sites as $site ) {
			if ( ! in_array( $site->id, $exclude ) ) {
				$return[ $site->id ] = $site;
			}
		}

		return $return;
	}


	/**
	 * @param int $id
	 *
	 * @return false|string
	 */
	public function get_destination_tag_for_site( $id )
	{
		$destination = ( new Destination_Tag_For_Site_Query( $this->content_site ) )
			->execute( array( 'site_id' => (int) $id ) );

		return $destination;
	}


	/**
	 * @return void
	 */
	private function init_sites()
	{
		$wp_sites  = get_sites();
		$site_objs = array();

		foreach ( $wp_sites as $wp_site ) {
			$site = new Site( $wp_site->blog_id, $this, $wp_site->domain );

			$site_objs[ $site->id ] = $site;
		}

		$this->sites = $site_objs;
	}


	/**
	 * Network constructor.
	 */
	private function __construct()
	{
		$this->init_sites();

		$this->main_site    = $this->sites[ Site::MAIN_ID ];
		$this->content_site = $this->sites[ Site::CONTENT_ID ];
		$this->current_site = $this->sites[ (int) get_current_blog_id() ];

		$this->add_actions();

		new Plugin_Hider( $this );
		new Warnings( $this );
	}


	/**
	 * @return void
	 */
	private function add_actions()
	{
		add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog_action' ), 10, 3 );
	}

}