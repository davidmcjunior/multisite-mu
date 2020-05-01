<?php

namespace Site\Utils;

use Site\Multisite\Site;

require_once ABSPATH . "wp-admin/includes/upgrade.php";

/**
 * Class Schema_Copier
 *
 * This can be used to copy a portion of the database using the tables that have the "source" prefix as
 * the table metadata reference.
 * For example: if the source prefix is 'wp_3_whateverplugin', any tables with a
 * name corresponding to 'wp_3_whateverplugin' + 'rest_of_table_name' will be
 * copied and named with the new prefix (i.e., 'wp_whatevernewpluginnameichoose')
 *
 * @package Site
 */
class Schema_Copier
{

	/**
	 * @var bool|\hyperdb|\QM_DB|\wpdb
	 */
	private $wpdb;

	/**
	 * @var string
	 */
	private $source_pre;

	/**
	 * @var string
	 */
	private $target_pre;

	/**
	 * @var
	 */
	private $plugin_pre;


	/**
	 * Schema_Copier constructor.
	 */
	public function __construct()
	{
		global $wpdb;

		$this->wpdb = $wpdb;
	}


	/**
	 * Copies a subset of the DB schema.
	 *
	 * @param Site $source The source 'Site' object to copy schema from.
	 * @param Site $target The target Site to copy to.
	 * @param string $plugin_pre The plugin's prefix (e.g., 'rg_' or 'facetwp_')
	 */
	public function copy_plugin_schema( $source, $target, $plugin_pre )
	{
		$this->plugin_pre = $plugin_pre;
		$this->source_pre = $source->get_prefix();
		$this->target_pre = $target->get_prefix();

		$tables = $this->get_schema_meta();

		foreach ( $tables as $table => $columns ) {
			$this->copy_table( $table, $columns );
		}
	}


	/**
	 * @return array
	 */
	private function get_schema_meta()
	{
		$schema_meta = array();
		$tables = $this->get_tables();

		foreach ( $tables as $table ) {
			$schema_meta[ $table ] = $this->wpdb->get_results( "DESCRIBE {$table}", ARRAY_A );
		}

		return $schema_meta;
	}


	/**
	 * @param string $table
	 * @param array $columns
	 *
	 * @return array
	 */
	private function copy_table( $table, $columns )
	{
		$sql = $this->build_copy_table_sql( $table, $columns );

		return dbDelta( $sql );
	}


	/**
	 * @param string $table
	 * @param array $columns
	 *
	 * @return string
	 */
	private function build_copy_table_sql( $table, $columns )
	{
		$table   = $this->strip_prefix( $table );
		$charset = $this->get_default_charset();
		$sql     = "\nCREATE TABLE IF NOT EXISTS {$this->target_pre}{$table} (\n";
		$fields  = array();

		foreach ( $columns as $column ) {
			$fields[] = "\t" . $this->build_column_def( $column );
		}

		$fields = implode( ",\n", $fields );

		$sql .= $fields;
		$sql .= $this->build_key_def( $table );
		$sql .= "\n) {$charset};";

		return $sql;
	}


	/**
	 * @param array $column
	 *
	 * @return string
	 */
	private function build_column_def( $column )
	{
		$allow_null = '';
		$default    = '';
		$auto_inc   = '';

		if ( 'auto_increment' === $column[ 'Extra' ] ) {
			$auto_inc .= ' AUTO_INCREMENT';
		}

		if ( 'NO' === $column[ 'Null' ] ) {
			$allow_null .= ' NOT NULL';
		}

		if ( ( null !== $column[ 'Default' ] ) && ( false === strpos( 'int', $column[ 'Type' ] ) ) ) {
			$value = null;

			if ( '' === $column[ 'Default' ] ) {
				$value = "''";
			} else {
				$value = "'{$column[ 'Default' ]}'";
			}

			$default .= " DEFAULT {$value}";
		}

		return "{$column[ 'Field' ]} {$column[ 'Type' ]}{$allow_null}{$default}{$auto_inc}";
	}


	/**
	 * @param string $table
	 *
	 * @return string
	 */
	private function strip_prefix( $table )
	{
		return substr( $table, strlen( $this->source_pre ));
	}


	/**
	 * @return array
	 */
	private function get_tables()
	{
		return $this->wpdb->get_col( "SHOW TABLES LIKE '{$this->source_pre}{$this->plugin_pre}%'" );
	}


	/**
	 * @param string $table
	 *
	 * @return string
	 */
	private function build_key_def( $table )
	{
		$sql      = '';
		$keys     = array();
		$key_data = $this->get_key_description_array( $table );

		foreach ( $key_data as $key => $data ) {
			$sub = '';

			if ( null !== $data[ 'sub' ] ) {
				$sub = "({$data[ 'sub' ]})";
			}

			if ( 'PRIMARY' === $key ) {
				$keys[] = "PRIMARY KEY ({$data[ 'col' ]})";
			} else {
				$keys[] = "KEY {$key} ({$data[ 'col' ]}{$sub})";
			}
		}

		if ( 0 < count( $keys ) ) {
			$sql .= ",\n\t";
			$sql .= implode( ",\n\t", $keys );
		}

		return $sql;
	}


	/**
	 * @param string $table
	 *
	 * @return array
	 */
	private function get_key_description_array( $table )
	{
		$keys    = array();
		$results = $this->wpdb->get_results( "SHOW KEYS FROM {$this->source_pre}{$table}", ARRAY_A );

		foreach ( $results as $row ) {
			$cols = '';

			if ( ! isset( $keys[ $row[ 'Key_name' ] ][ 'col' ] ) ) {
				$cols .= $row[ 'Column_name' ];
			} else {
				$cols .= $keys[ $row[ 'Key_name' ] ][ 'col' ] . ', ' . $row[ 'Column_name' ];
			}

			$keys[ $row[ 'Key_name' ] ][ 'sub' ] = $row[ 'Sub_part' ];
			$keys[ $row[ 'Key_name' ] ][ 'col' ] = $cols;
		}

		return $keys;
	}


	/**
	 * @return string
	 */
	private function get_default_charset()
	{
		$charset_collate = '';

		if ( ! empty( $this->wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$this->wpdb->charset}";
		}
		if ( ! empty( $this->wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$this->wpdb->collate}";
		}

		return $charset_collate;
	}

}