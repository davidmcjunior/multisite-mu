<?php

namespace Site\Auth\Oidc;

/**
 * Class Okta
 * @package Site\Auth\Oidc
 */
class Okta
{

	/**
	 * @param string $domain
	 *
	 * @return array|null
	 */
	public static function get_domain_configs( $domain )
	{
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT base_url, client_id, client_secret, auth_server_id 
			FROM wp_oidc
			WHERE domain = %s",
			$domain
		);

		return $wpdb->get_row( $sql, ARRAY_A );
	}


}