<?php

namespace Site\Shortcodes;

/**
 * Class Shortcode
 * @package Site\Shortcodes
 */
abstract class Shortcode
{

	/**
	 * @var string
	 */
	protected $shortcode;

	/**
	 * Shortcode constructor.
	 *
	 * @param string $shortcode
	 */
	public function __construct( $shortcode )
	{
		$this->shortcode = $shortcode;

		add_shortcode( $shortcode, [ $this, 'shortcode_action' ] );
	}


	/**
	 * @param array $atts
	 *
	 * @return mixed
	 */
	public abstract function shortcode_action( $atts );

}