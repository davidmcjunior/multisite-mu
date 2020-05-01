<?php

namespace Site\Multisite;

/**
 * Class Locale
 * @package Site\Multisite
 */
class Locale
{

	/**
	 * @var string[]
	 */
	public static $locales = array(
		'en_US' => 'US English',
		'de_DE' => 'German',
		'es_ES' => 'Spanish',
		'fr_FR' => 'French',
		'jp_JP' => 'Japanese'
	);


	/**
	 * @param string $locale
	 *
	 * @return bool
	 */
	public static function is_valid( $locale )
	{
		return in_array( $locale, array_keys( self::$locales ) );
	}

}