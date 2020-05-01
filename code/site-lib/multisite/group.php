<?php

namespace Site\Multisite;


/**
 * Class Group
 * @package Site\Multisite
 */
class Group
{

	private static $groups;

	/**
	 * Static class.
	 * Group constructor.
	 */
	private function __construct() {}


	/**
	 * @param string $name
	 * @param int $site_id
	 *
	 * @return bool
	 */
	public static function is_valid_group( $name, $site_id )
	{
		if ( ! isset( static::$groups ) ) {
			static::$groups = Group::get_all_group_names( $site_id );
		}

		if ( in_array( $name, static::$groups ) ){
			return true;
		}

		return false;
	}


	/**
	 * @param int $site_id
	 * @return string[]
	 */
	public static function get_all_group_names( $site_id )
	{
		global $wpdb;

		$db_pre = $wpdb->get_blog_prefix( $site_id );

		$groups = $wpdb->get_results( "
			SELECT post_title FROM {$db_pre}posts
			WHERE  post_type = 'memberpressproduct'",
			ARRAY_A
		);

		if ( ! is_array( $groups ) ) {
			return array();
		}

		return array_column( $groups, 'post_title' );
	}

}