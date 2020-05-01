<?php

namespace Site\Https;

/**
 * Class Headers
 * @package Site\Https
 */
class Headers
{

	/**
	 * Headers constructor.
	 */
	public function __construct()
	{
		add_action( 'send_headers', array( $this, 'send_header_action' ) );
	}


	/**
	 * reference: https://benrabicoff.com/adding-secure-http-response-headers-wordpress/
	 * reference: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers
	 *
	 * @return void
	 **/
	public function send_header_action()
	{
		/**
		 * Strict-Transport-Security enforces the use of HTTPS. This is important because it protects
		 * against passive eavesdropper and man-in-the-middle (MITM) attacks.
		 */
		header( "Strict-Transport-Security: max-age=15724800" );

		/**
		 * X-Frame-Options prevents clickjacking attacks and helps ensure your content is not embedded
		 * into other sites via <frame>, <iframe> or <object>.
		 */
		header( "X-Frame-Options: SAMEORIGIN" );

		/**
		 * Content-Security-Policy (and X-Content-Security-Policy for CSP support in IE 10 and IE 11)
		 * tells the browser where resources are allowed to be loaded and if it’s allowed to
		 * parse/run inline styles or Javascript. This is important because it prevents
		 * content injection attacks, such as Cross Site Scripting (XSS).
		 */
		$csp =
			"font-src * data:; " .
			"img-src * data:; " .
			"script-src 'unsafe-inline' 'unsafe-eval' * data:; " .
			"style-src 'unsafe-inline' 'unsafe-eval' * data:;"; // Anything goes...

		// header( "Content-Security-Policy-Report-Only: default-src https:;" );
		header( 'Content-Security-Policy: ' . $csp ); // FF 23+ Chrome 25+ Safari 7+ Opera 19+
		header( 'X-Content-Security-Policy: ' . $csp ); // IE 10+

		/**
		 * X-XSS-Protection sets the configuration for the cross-site scripting filters built into most
		 * browsers. This is important because it tells the browser to block the response if a
		 * malicious script has been inserted from a user input.
		 */
		header( "X-XSS-Protection: 1; mode=block" );

		/**
		 * X-Content-Type-Options stops a browser from trying to MIME-sniff the content type and forces
		 * it to stick with the declared content-type. This is important because the browser will
		 * only load external resources if their content-type matches what is expected, and not
		 * malicious hidden code.
		 */
		header( "X-Content-Type-Options: nosniff" );

		/**
		 * Referrer-Policy allows control/restriction of the amount of information present in the referral
		 * header for links away from your page—the URL path or even if the header is sent at all.
		 */
		header( "Referrer-Policy: no-referrer-when-downgrade" );
	}

}

new Headers();