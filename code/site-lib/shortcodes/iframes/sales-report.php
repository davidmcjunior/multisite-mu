<?php

namespace Site\Shortcodes\Iframes;

use Site\Multisite\User;
use Site\Shortcodes\Iframe;
use ReflectionClass;

/**
 * Class Sales_Report
 *
 * Usage: [sales_dashboard_iframe doc_id="<...>"]
 *
 * @package Site\Shortcodes
 */
abstract class Sales_Report extends Iframe
{

	/**
	 * @var string
	 */
	protected $domain = LAUNCH_REPORT_DOMAIN;

	/**
	 * @var string
	 */
	protected $path = LAUNCH_REPORT_PATH;

	/**
	 * @var string
	 */
	protected $base_url = 'https://' . LAUNCH_REPORT_DOMAIN . '/' . LAUNCH_REPORT_PATH;


	/**
	 * Sales_Dashboard_Iframe constructor.*
	 */
	public function __construct()
	{
		$shortcode = ( new ReflectionClass( $this ) )->getShortName() . '_iframe';

		parent::__construct( strtolower( $shortcode ) );
	}


	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function shortcode_action( $atts )
	{
		$html = '';

		/**
		 * Defaults...
		 */
		$sid_type          = 'CUID';
		$no_details        = 'true';
		$hide_if_no_access = false;

		if ( array_key_exists( 'id_type', $atts ) ) {
			$sid_type = $atts[ 'id_type' ];
		}

		if ( array_key_exists( 'no_details', $atts ) && ! $atts[ 'no_details' ] ) {
			$no_details = 'false';
		}

		if ( array_key_exists( 'hide_if_no_access', $atts ) ) {
			$hide_if_no_access = true;
		}

		$atts[ 'src' ] = $this->base_url . '?sIDType=' . $sid_type . '&iDocID=' .
		                 $atts[ 'doc_id' ] . '&noDetailsPanel=' . $no_details;

		unset( $atts[ 'doc_id' ] );

		/**
		 * Find out if user can access this iframe...
		 */
		$groups = $_SESSION[ 'groups' ];

		/**
		 * @todo Ditch this if() if Memberpress can hide the container itself...
		 */
		if ( $hide_if_no_access && ! in_array( 'SM EBI External', $groups ) ) {
			$html .= '<script type="text/javascript">el = document.getElementById("sales-iframe-container"); el.style.display = "none";</script>';
		}

		$html .= parent::shortcode_action( $atts );

		return $html;
	}

}