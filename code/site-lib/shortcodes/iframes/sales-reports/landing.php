<?php

namespace Site\Shortcodes\Iframes\Sales_Reports;

use Site\Shortcodes\Iframes\Sales_Report;

/**
 * Class Landing
 * @package Site\Shortcodes\Iframes\Sales_Reports
 */
class Landing extends Sales_Report
{

	public function shortcode_action( $atts )
	{
		$atts[ 'doc_id' ] = 'Afwjj01gVGhOv2hXxOatClQ';

		return parent::shortcode_action( $atts );
	}

}

new Landing();