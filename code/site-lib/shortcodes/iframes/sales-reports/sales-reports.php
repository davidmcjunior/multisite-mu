<?php

namespace Site\Shortcodes\Iframes\Sales_Reports;

use Site\Shortcodes\Iframes\Sales_Report;

/**
 * Class Sales_Reports
 * @package Site\Shortcodes\Iframes\Sales_Reports
 */
class Sales_Reports extends Sales_Report
{

	public function shortcode_action( $atts )
	{
		$atts[ 'doc_id' ] = 'AZqlC5rJGtVHpLl9.9C8DU0';

		return parent::shortcode_action( $atts );
	}

}

new Sales_Reports();