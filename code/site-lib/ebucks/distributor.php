<?php

namespace Site\Ebucks;

use Site\Exceptions\Database_Exception as Site_Database_Exception;
use Site\DB_User;
use Site\Exceptions\Invalid_Argument_Exception as Site_Invalid_Argument_Exception;
use Site\Multisite\Group;
use Site\Multisite\Site;
use Site\Multisite\User;

/**
 * Class Distributor
 * @package Site\Ebucks
 *
 */
class Distributor extends DB_User
{

	/**
	 * @var string
	 */
	private $log_table = 'wp_ebucks_mass_distribution_log';

	/**
	 * @var int
	 */
	private $distributing_user_id;


	/**
	 * Distributor constructor.
	 */
	public function __construct()
	{
		if ( ! is_plugin_active( 'woo-wallet/woo-wallet.php' ) ) {
			return;
		}

		$this->distributing_user_id = get_current_user_id();

		add_action( 'wp_ajax_site_distribute_ebucks', array( $this, 'distribute_ebucks_action' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu_action' ), 50 );
	}


	/**
	 * @return string[]
	 */
	public static function get_sales_portal_groups()
	{
		return Group::get_all_group_names( Site::SALES_PORTAL_ID );
	}


	/**
	 * @return void
	 */
	public function admin_menu_action()
	{
		$title     = 'Distribute eBucks';
		$menu_slug = 'ebucks';
		$function  = array( $this, 'render_admin_page' );
		$icon      = SITE_PLUGIN_ICON_URI;
		$position  = 59;

		add_menu_page( $title, $title, null, $menu_slug, $function, $icon, $position );
	}


	/**
	 * @return void
	 */
	public function render_admin_page()
	{
		include 'templates/distributor.php';
	}


	/**
	 * @return void
	 */
	public function distribute_ebucks_action()
	{
		$type    = $_POST[ 'type' ];
		$amount  = $_POST[ 'amount' ];
		$groups  = $_POST[ 'groups' ];
		$details = $_POST[ 'details' ];
		$status  = null;

		if ( ! in_array( $type, array( 'credit', 'debit' ) ) ) {
			$this->send_status_and_quit(
				false, 'Invalid account adjustment type.'
			);
		}

		if ( ! is_numeric( $amount ) ) {
			$this->send_status_and_quit(
				false, 'Invalid account adjustment amount.'
			);
		}

		if ( ! is_array( $groups ) ) {
			$this->send_status_and_quit(
				false, 'No groups were selected.'
			);
		}

		$status = $this->distribute_to_groups( $type, $amount, $groups, $details );

		$this->send_status_and_quit(
			$status[ 'success' ], $status[ 'message' ]
		);
	}


	/**
	 * @param string $type
	 * @param float $amount
	 * @param string[] $groups
	 * @param string $details
	 *
	 * @return array
	 */
	public function distribute_to_groups( $type, $amount, $groups, $details = '' )
	{
		$user_ids = User::get_user_ids_for_groups( $groups );
		$success  = true;
		$message  = '';
		$db       = $this->db();

		try {
			$db->query( 'START TRANSACTION' );

			foreach ( $user_ids as $user_id ) {
				$this->do_user_transaction( $user_id, $type, $amount, $details );
			}

			$db->query( 'COMMIT' );

			$message .= 'Distribution was successful.';
			$this->log_distribution( $this->distributing_user_id, $type, $amount, $groups );

		} catch ( Site_Database_Exception $e ) {
			$db->query( 'ROLLBACK' );

			$message .= 'An error occurred. Please try again.';
			$data = $e->getMessage();

			if ( isset( $data) ) {
				$db->insert( 'wp_site_log', array( 'details' => $data ) );
			}
		}

		/**
		 * @todo new logging of distribution transactions
		 *
		 */
		return compact( 'success', 'message' );
	}


	/**
	 * @param int $user_id
	 * @param string $type
	 * @param float $amount
	 * @param string $details
	 *
	 * @throws Site_Database_Exception|\InvalidArgumentException
	 */
	public function do_user_transaction( $user_id, $type, $amount, $details = '' )
	{
		$currency = 'USD';
		$balance  = 0.0;
		$blog_id  = Site::SALES_PORTAL_ID;
		$amount   = (float) $amount; // If the value is invalid, it will be cast to 0...
		$pre      = 'wp_'; //Assumption is that one one "wallet" exists and is only prefixed with 'wp_'...
		$db       = $this->db();

		$sql = $db->prepare( "
			SELECT   balance
			FROM     {$pre}woo_wallet_transactions
			WHERE    user_id = %d
			ORDER BY transaction_id DESC
			LIMIT 1",
			array( $user_id )
		);

		$last_transaction = $db->get_row( $sql, ARRAY_A );

		if ( is_array( $last_transaction ) && array_key_exists( 'balance', $last_transaction ) ) {
			$balance = $last_transaction[ 'balance' ];
		}

		if ( 'credit' === $type ) {
			$balance += $amount;

		} else if ( 'debit' === $type ) {
			$balance -= $amount;

		} else {
			throw new Site_Invalid_Argument_Exception( "Type must be either 'credit' or 'debit'." );
		}

		if ( 0.01 > $amount ) {
			throw new Site_Invalid_Argument_Exception( "The value for amount must be a positive integer or decimal." );
		}

		$transaction_data = compact(
			'type', 'amount', 'details', 'balance', 'user_id', 'currency', 'blog_id'
		);

		$success = $db->insert( $pre . 'woo_wallet_transactions', $transaction_data );

		if ( false !== $success ) {
			$transaction_metadata = array(
				'transaction_id' => $db->insert_id,
				'meta_key'       => 'distributing_user_id',
				'meta_value'     => $this->distributing_user_id
			);

			$db->insert( $pre . 'woo_wallet_transaction_meta', $transaction_metadata );

		} else {
			site_log( $transaction_data );

			throw new Site_Database_Exception(
				"Attempt to insert eBucks transaction failed."
			);
		}
	}


	/**
	 * @param int $distributing_user_id
	 * @param string $type
	 * @param float $amount
	 * @param string[] $groups
	 */
	private function log_distribution( $distributing_user_id, $type, $amount, $groups )
	{
		$groups = '"' . implode( '" "', $groups ) . '"';

		$this->db()->insert(
			$this->log_table, compact( 'distributing_user_id', 'type', 'amount', 'groups' )
		);
	}


	/**
	 * @param bool $success
	 * @param string $message
	 */
	private function send_status_and_quit( $success, $message )
	{
		if ( ! $success ) {
			wc_add_notice(  'Please try again.', 'error' );
		}

		echo wp_json_encode( compact( 'success', 'message' ) );

		exit;
	}

}

new Distributor();