<?php

namespace Site\Auth;

use Site\Multisite\Network;
use Site\Multisite\Site;
use Site\Multisite\User;
use MeprUtils;
use MeprTransaction;

/**
 * Class Mepr_Group_Mapper
 * @package Site\Auth
 */
class Mepr_Group_Mapper extends Group_Mapper
{

	/**
	 * Mepr_Group_Mapper constructor.
	 * @param Network
	 */
	public function __construct( $network )
	{
		/**
		 * @todo find a better action to hook into for this method.
		 * Note: This action is running at the end of each round of execution to keep the groups
		 * continuously synchronized. My attempts to add this action into other any other hooks
		 * or filters has been unsuccessful, because the DB values immediately get overwritten
		 * but there's got to be a better way to do this...
		 */
		parent::__construct( $network, 'wp_loaded' );
	}


	/**
	 * @param array ...$args
	 *
	 * @return bool
	 */
	public function map_user_groups( ...$args )
	{
		/**
		 * Don't run unless user's logged in...
		 */
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();
		$groups  = User::get_groups( $user_id );

		/**
		 * @legacy MiniOrange
		 *
		 * Make go away when MO is retired...
		 */
		if ( is_plugin_active( LEGACY_SSO_PLUGIN ) ) {
			$sso_data = $this->get_legacy_sso_data( $user_id );
			$groups   = $sso_data[ 'groups' ];
			// $role     = strtolower( $sso_data[ 'role' ] );

			// if ( ! empty( $role ) && ( null !== get_role( $role ) ) ) {
			// 	wp_update_user( (object) [ 'ID' => $user_id, 'role' => $role ] );
			// }
		}

		/**
		 * Don't run unless MemberPress is activated...
		 */
		if ( ! is_plugin_active( 'memberpress/memberpress.php' ) ) {
			return false;
		}

		$db       = $this->db();
		$db_pre   = 'wp_26_'; //$this->db()->prefix;
		$mepr_ids = null;

		if ( ! empty( $groups ) ) {
			/**
			 * Prep the SQL statement that will get the IDs of the group (membership)
			 * names stored in the array above and make a placeholder string that
			 * matches the array's length...
			 */
			$placeholders = implode( ', ', array_fill( 0, count( $groups ), '%s' ) );

			// this gets all the matching names of the groups after all the work done above, and returns the ids
			$sql = $db->prepare( "
				SELECT ID FROM {$db_pre}posts
				WHERE  post_title IN ({$placeholders})
				AND    post_type = 'memberpressproduct'",
				$groups
			);

			$mepr_ids = $db->get_results( $sql, ARRAY_A );
		}

		// this is the id's of all the memberpress memberships that okta says the user should have
		if ( is_array( $mepr_ids ) && ! empty( $mepr_ids ) ) {
			$mepr_ids = array_column( $mepr_ids, 'ID' ); // singles out just the ids

		} else {
			$mepr_ids = [];
		}

		$current_user_active_membership_ids = [];

		// Get active membership id's for current user...
		if ( class_exists( 'MeprUtils' ) ) {
			$user = MeprUtils::get_currentuserinfo();

			if ( false !== $user  && isset( $user->ID ) ) {
				// Returns an array of Membership ID's that the current user is active on
				$current_user_active_membership_ids = $user->active_product_subscriptions( 'ids' );

				if ( ! $current_user_active_membership_ids ) {
					$current_user_active_membership_ids = [];
				}
			}
		}

		// Compare current active memberships to OKTA memberships and figure out which ones to add
		$memberships_to_add = array_diff( $mepr_ids, $current_user_active_membership_ids ) ;

		// Add - Add new transactions that give this user access to memberships...
		foreach ( $memberships_to_add as $membership ) {
			$this->generate_mepr_transaction( $membership, 0, 'add', $user_id );
		}

		// REMOVE MEMBERSHIPS

		// Compare current active memberships to OKTA memberships and figure out which ones to remove
		$memberships_to_remove = array_diff( $current_user_active_membership_ids, $mepr_ids );

		if ( ! empty( $memberships_to_remove ) ) {
			$placeholder_to_remove = implode( ',', $memberships_to_remove );

			// get a list of transactions that are giving the user access to the memberships
			// that they should not have access to
			$sql = $db->prepare( "
				SELECT id, product_id 
				FROM {$db_pre}mepr_transactions
				WHERE  product_id IN ({$placeholder_to_remove})
				AND    user_id = %d",
				$user_id
			);

			$transactions_to_expire = $db->get_results( $sql, ARRAY_A );

			// Remove - Expire the transactions that give this user memberships...
			foreach ( $transactions_to_expire as $transaction ) {
				$this->generate_mepr_transaction(
					$transaction[ 'product_id' ], $transaction[ 'id' ], 'remove', $user_id
				);
			}
		}

		return true;
	}


	/**
	 *  Adds or removes Memberpress memberships to a user by creating or expiring transactions
	 *
	 *  @param int $membership_id  post id of post type memberpress product
	 *  @param int $transaction_id ID field of wp_mepr_transactions
	 *  @param string $addremove either 'add' or 'remove'
	 *  @param int $user_id
	 *
	 *  @return int transaction id that was created or updated from wp_mepr_transactions
	 *
	 */
	public function generate_mepr_transaction( $membership_id, $transaction_id, $addremove, $user_id )
	{
		$txn = new MeprTransaction();

		$txn->product_id = $membership_id;
		$txn->amount     = 0;
		$txn->total      = 0;
		$txn->user_id    = $user_id;
		$txn->status     = MeprTransaction::$complete_str;
		$txn->txn_type   = MeprTransaction::$payment_str;
		$txn->gateway    = 'manual';

		// Deactivate membership by setting the transaction expiration to 1 day in the past
		if ( $addremove == 'remove' ) {
			$txn->id         = $transaction_id;
			$txn->expires_at = gmdate('Y-m-d 23:59:59', (time() - MeprUtils::days(1)));
		}

		// Add new transaction with lifetime duration
		if ( $addremove == 'add' ) {
			$txn->created_at = MeprUtils::ts_to_mysql_date(time());
			$txn->expires_at = 0; // 0 means never expire
		}

		// either create or update transaction depending on existence of $txn->id
		return $txn->store();  // return created or updated transaction id
	}


	/**
	 *
	 * @legacy MiniOrange SSO
	 *
	 * @param int $user_id
	 *
	 * @return array|bool
	 */
	private function get_legacy_sso_data( $user_id )
	{
		/**
		 * Get the MiniOrange SAML user data...
		 */
		$meta = get_user_meta( $user_id, 'mo_saml_user_attributes', true );

		if ( ! is_array( $meta ) ) {
			return false;
		}

		$return  = [
			'groups' => [],
			'role'   => 'subscriber'
		];

		$groups  = $meta[ 'groups' ];
		$country = $meta[ 'countrycode' ][0];

		foreach ( $groups as $group ) {
			if ( 0 === strpos( $group, 'SM ' ) ) {
				$groups[] = $country . substr( $group, 2 );
			}
		}

		if ( in_array( 'Site Employees', $groups ) ) {
			$groups[] = $country . ' Employee';
		}

		if ( array_key_exists( 'wordpressrole', $meta ) ) {
			$return[ 'role' ] = $meta[ 'wordpressrole' ][0];
		}

		if ( array_key_exists( 'salesrole', $meta ) ) {
			$sales_role = explode( ',', $meta[ 'salesrole' ][ 0 ] );

			$sales_role = array_map( function( $role ) use ( $country ) {
				return $country . ' ' . $role;
			}, $sales_role );

			$groups = array_merge( $sales_role, $groups );
		}

		if ( array_key_exists( 'specialrole', $meta ) ) {
			$roles = explode( ',', $meta[ 'specialrole' ][ 0 ] );

			$groups = array_merge( $roles, $groups );
		}

		$return[ 'groups' ] = $groups;

		return $return;
	}

}

global $network;

new Mepr_Group_Mapper( $network );