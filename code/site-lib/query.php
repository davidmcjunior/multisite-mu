<?php

namespace Site;

use Site\Multisite\Site;

/**
 * Class Query
 *
 * Subclasses of this class represent SQL queries that are (or may) be used frequently.
 * Generally, an array of key value pairs containing the query params are passed into the
 * execute method. In the case of a select query, the query results ar return, otherwise
 * the execute() returns null (although it should really return a bool.)
 *
 * @package Site
 */
abstract class Query extends DB_User
{

	/**
	 * @var Site
	 */
	protected $site;

	/**
	 * Query constructor.
	 *
	 * @param $site Site
	 */
	public function __construct( $site = null )
	{
		$this->site = $site;
	}


	/**
	 * Switches the site the query is run against (really, this changes the table prefix used in the SQL stmt.)
	 *
	 * @param Site $site
	 *
	 * @return void
	 */
	public function change_site( $site )
	{
		$this->site = $site;
	}


	/**
	 * Executes the query implemented within this class...
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public abstract function execute( $data = array() );


	/**
	 * Returns an escaped string that can be used for an SQL "IN" clause.
	 * i.e., "('1','2','3')" or "('cat','dog','fish','sloth')"
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	protected function get_in_string( $data )
	{
		foreach ( $data as &$item ) {
			$item = $this->db()->_escape( $item );
		}

		return "('" . implode( "','", $data ) . "')";
	}


	/**
	 * Determines if the array is of type int[].
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	protected function is_array_of_ints( $array )
	{
		if ( ! is_array( $array ) || count( $array ) < 1  ) {
			return false;
		}

		$all_int = true;

		foreach ( $array as $el ) {
			if ( ! is_numeric( $el ) ) {
				$all_int = false;
			}
		}

		return $all_int;
	}

}