<?php
/*
 * __      __             _
 * \ \    / /            | |
 *  \ \  / /_ _ _ __   __| | __ _
 *   \ \/ / _` | '_ \ / _` |/ _` |
 *    \  / (_| | | | | (_| | (_| |
 *     \/ \__,_|_| |_|\__,_|\__,_|
 *
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         http://vanda.io
 */

declare(strict_types=1);

namespace System\Exception;

use System\Inflector;

/**
 * Class InvalidArgumentException
 *
 * @package System\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException
{
	/**
	 * Raises an exception for an invalid argument type.
	 *
	 * @param  int                      $argument          The argument number.
	 * @param  array|null               $allowedDataTypes  The allowed data types. Defaults to null.
	 * @param  mixed|null               $value             The given value. Defaults to null.
	 * @param  string|null              $customMsg         The custom error message. Defaults to null.
	 * @return InvalidArgumentException                    Returns the exception.
	 */
	public static function typeError(int $argument, ?array $allowedDataTypes = null, $value = null, ?string $customMsg = null) : InvalidArgumentException
	{
		$trace = debug_backtrace(0);

		list($class, $function, $file, $line) = InvalidArgumentException::_list($trace);

		if ($customMsg)
		{
			$msg = 'Argument ' . $argument . ' passed to ' . $class . '::' . $function . '() ' . $customMsg
				. ', called in ' . $file . ' on line ' . $line;
		}
		else
		{
			$given = gettype($value);
			$types = Inflector::sentence($allowedDataTypes, ' or ');

			$msg = 'Argument ' . $argument . ' passed to ' . $class . '::' . $function . '()'
				. ' must be of the type ' . $types . ', ' . $given . ' given, '
				. ' called in ' . $file . ' on line ' . $line;
		}

		return new self($msg);
	}

	/**
	 * Raises an exception for an invalid argument value.
	 *
	 * @param  int                      $argument  The argument number.
	 * @param  string                   $errorMsg  The error message.
	 * @param  mixed|null               $value     The given value. Defaults to null.
	 * @return InvalidArgumentException            Returns the exception.
	 */
	public static function valueError(int $argument, string $errorMsg, $value = null) : InvalidArgumentException
	{
		$trace = debug_backtrace(0);

		list($class, $function, $file, $line) = InvalidArgumentException::_list($trace);

		$msg = 'Argument ' . $argument . ' passed to ' . $class . '::' . $function . '(), ' . $errorMsg;

		if ($value)
			$msg .= ', ' . $value . ' given';

		$msg .= ', called in ' . $file . ' on line ' . $line;

		return new self($msg);
	}

	/**
	 * Gets the class, function, file and line number from the backtrace.
	 *
	 * @param  array $trace  The backtrace.
	 * @return array         Returns the class, function, file and line number.
	 */
	protected static function _list(array $trace) : array
	{
		if (strpos($trace[0]['file'], 'Data.php'))
			$index = 2;
		else
			$index = 1;

		$class = $trace[$index]['class'];
		$function = $trace[$index]['function'];
		$file = $trace[$index]['file'];
		$line = $trace[$index]['line'];

		return [$class, $function, $file, $line];
	}
}