<?php

namespace Site\Multisite;

use Site\Exceptions\Invalid_Argument_Exception;


/**
 * Class User
 * @package Site\Multisite
 */
class User
{

	/**
	 *
	 */
	const GROUP_META_KEY = 'sitetech_groups';


	/**
	 * Static class.
	 *
	 * User constructor.
	 */
	private function __construct() {}


	/**
	 * @param int $user_id
	 *
	 * @return string[]
	 */
	public static function get_groups( $user_id )
	{
		if ( isset( $_SESSION[ 'groups' ] ) ) {
			return $_SESSION[ 'groups' ];
		}

		$groups = get_user_meta( $user_id, User::GROUP_META_KEY );

		if ( ! $groups ) {
			return [];
		}

		/**
		 * Groups are stored as a string with the group names delineated by double quotes and commas...
		 */
		$groups = str_replace( '"', '', $groups );

		return explode( ',', $groups );
	}


	/**
	 * @param string[] $groups
	 *
	 * @return int[]|Invalid_Argument_Exception
	 */
	public static function get_user_ids_for_groups( $groups )
	{
		if ( ! is_array( $groups ) ) {
			throw new Invalid_Argument_Exception( "Expected an array of strings." );
		}

		$user_ids = [];

		// Make sure users in multiple groups aren't included multiple times...
		foreach ( $groups as $group ) {
			$user_ids = array_unique(
				array_merge(
					$user_ids, self::get_user_ids_for_group( $group )
				)
			);
		}

		return $user_ids;
	}


	/**
	 * @param string $group
	 *
	 * @return int[]
	 */
	public static function get_user_ids_for_group( $group )
	{
		global $wpdb;

		/**
		 * Query for the group name, delineated by double quotes...
		 */
		$sql = sprintf( "
			SELECT   user_id
			FROM     wp_usermeta
			WHERE    meta_key = '%s'
			AND      meta_value LIKE '%s'
			GROUP BY user_id",
			self::GROUP_META_KEY,
			'"' . $group . '"'
		);

		/**
		 * @legacy MiniOrange SSO
		 *
		 * Stabby
		 */
		if ( is_plugin_active( LEGACY_SSO_PLUGIN ) ) {
			$code  = '%"countrycode";a:1:{i:0;s:2:"' . substr( $group, 0, 2 ) . '";%';
			$group = '%' . substr( $group, 3 ) . '%';

			$sql = sprintf( "
				SELECT   user_id
				FROM     wp_usermeta
				WHERE    meta_key = 'mo_saml_user_attributes'
				AND      meta_value LIKE '%s'
				AND      meta_value LIKE '%s'
				GROUP BY user_id",
				$code, $group
			);
		}

		$user_ids = $wpdb->get_results( $sql, ARRAY_A );

		if ( ! is_array( $user_ids ) ) {
			return [];
		}

		return array_column( $user_ids, 'user_id' );
	}


	/**
	 * @return int[]
	 */
	public static function get_all_user_ids()
	{
		global $wpdb;

		$results = $wpdb->get_results( "SELECT ID FROM wp_users", ARRAY_A );

		if ( ! is_array( $results ) ) {
			return [];
		}

		return array_column( $results, 'ID' );
	}

}