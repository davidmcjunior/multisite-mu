<?php

namespace Site\Exceptions;

/**
 * Class Database_Exception
 * @package Site
 */
class Database_Exception extends \Exception
{

	/**
	 * Database_Exception constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param \Throwable|null $previous
	 */
	public function __construct( $message = "", $code = 0, \Throwable $previous = null )
	{
		parent::__construct( $message, $code, $previous );
	}

}