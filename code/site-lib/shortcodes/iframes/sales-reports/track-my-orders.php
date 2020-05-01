<?php

namespace Site\Shortcodes\Iframes\Sales_Reports;

use Site\Shortcodes\Iframes\Sales_Report;

/**
 * Class Track_My_Orders
 * @package Site\Shortcodes\Iframes\Sales_Reports
 */
class Track_My_Orders extends Sales_Report
{

	public function shortcode_action( $atts )
	{
		$atts[ 'doc_id' ] = 'AU6zAY44ZIdKlQZ6iblP288';

		return parent::shortcode_action( $atts );
	}

}

new Track_My_Orders();