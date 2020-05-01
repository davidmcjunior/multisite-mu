<?php

namespace Site\Shortcodes\Iframes\Sales_Reports;

use Site\Shortcodes\Iframes\Sales_Report;

/**
 * Class Sales_Reports_Dashboard
 * @package Site\Shortcodes\Iframes\Sales_Reports
 */
class Sales_Reports_Dashboard extends Sales_Report
{

	public function shortcode_action( $atts )
	{
		$atts[ 'doc_id' ] = 'AbNjprID6AZLmADBQEqou0E';

		return parent::shortcode_action( $atts );
	}

}

new Sales_Reports_Dashboard();