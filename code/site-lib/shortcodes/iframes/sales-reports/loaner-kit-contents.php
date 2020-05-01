<?php

namespace Site\Shortcodes\Iframes\Sales_Reports;

use Site\Shortcodes\Iframes\Sales_Report;

/**
 * Class Loaner_Kit_Contents
 * @package Site\Shortcodes\Iframes\Sales_Reports
 */
class Loaner_Kit_Contents extends Sales_Report
{

	public function shortcode_action( $atts )
	{
		$atts[ 'doc_id' ] = 'ATIMB5FO8f5PjtRAnfw1kEA';

		return parent::shortcode_action( $atts );
	}

}

new Loaner_Kit_Contents();