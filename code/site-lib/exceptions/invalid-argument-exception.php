<?php

namespace Site\Exceptions;

use Throwable;

/**
 * Class Invalid_Argument_Exception
 * @package Site\Exceptions
 */
class Invalid_Argument_Exception extends \InvalidArgumentException
{

	/**
	 * Invalid_Argument_Exception constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param Throwable|null $previous
	 */
	public function __construct( $message = "", $code = 0, Throwable $previous = null )
	{
		parent::__construct( $message, $code, $previous );
	}

}