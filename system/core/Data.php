<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace System;

use Countable;
use Error;
use Exception;
use ResourceBundle;
use SimpleXmlElement;
use System\Exception\InvalidArgumentException;

/**
 * Class Data
 *
 * Utility class for handling array and object.
 *
 * @package System
 */
class Data
{
	/**
	 * Data constructor.
	 */
	private function __construct(){}

	/**
	 * Returns a specific element from the given array or object. The given
	 * key supports both array and object. Returns null if the array element/
	 * object property is empty (or whatever you specify as the
	 * default value).
	 *
	 * For example,
	 *
	 * ```php
	 * $data = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'age' => 38,
	 *     'job' => new stdClass()
	 * ];
	 *
	 * $data['job']->position = 'Web Developer';
	 * $data['job']->salary = '10000';
	 *
	 * $result = Data::get($data, 'job.position');
	 * // The $result will be: Web Developer
	 * ```
	 *
	 * @param  array|object $data     The input data, array or object.
	 * @param  int|string   $keys     The searched key.
	 * @param  mixed        $default  Optionally, default value. Defaults to null.
	 * @return mixed                  Depends on what the array or object contains.
	 */
	public static function get($data, $keys, $default = null)
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::typeError(1, ['array', 'object'], $data);

		if (is_string($keys))
			$keys = explode('.', $keys);
		elseif (is_int($keys))
			$keys = [$keys];
		else
			throw InvalidArgumentException::typeError(2, ['int', 'string'], $keys);

		foreach ($keys as $key)
		{
			if (is_array($data))
			{
				if (array_key_exists($key, $data))
					$data = $data[$key];
				else
					$data = $default;
			}
			elseif (is_object($data))
				$data = $data->{$key} ?? $default;
			else
				$data = $default;
		}

		return $data;
	}

	/**
	 * Sets the given value to the given array or object using path strings with dots.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'foo' => 'bar'
	 * ];
	 *
	 * $result = Data::set($array, 'key.subkey', 'value');
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [foo] => bar
	 * //     [key] => Array
	 * //         (
	 * //             [subkey] => value
	 * //         )
	 * // )
	 *
	 * $object = new stdClass();
	 * $object->foo = 'bar';
	 *
	 * $result = Data::set($data, 'key.subkey', 'value');
	 * // The $result will be:
	 * // stdClass Object
	 * // (
	 * //     [foo] => bar
	 * //     [key] => stdClass Object
	 * //         (
	 * //             [subkey] => value
	 * //         )
	 * // )
	 * ```
	 * 
	 * @param  array|object $data   The data to set a value in.
	 * @param  string       $key    The key to set. If the key contains dot, it will set nested array/object.
	 * @param  mixed        $value  Value to set.
	 * @return array|object
	 */
	public static function set($data, string $key, $value)
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::typeError(1, ['array', 'object'], $data);

		if (is_object($data))
		{
			$keys = explode('.', $key);
			$dataPointer = '$data';

			foreach ($keys as $key)
			{
				$dataPointer .= "->{'$key'}";
				$var4If = '';

				// Use @ to prevent error in case of key does not exist.
				$syntax = '$var4If = @' . $dataPointer . ';';
				eval($syntax);

				if (!is_object($var4If))
				{
					$dataAssigner = $dataPointer . ' = new \stdClass();';
					eval($dataAssigner);
				}
			}

			if (is_string($value))
			{
				// Escape only single quote and backslash.
				$value = addcslashes($value, '\\\'');
			}

			$dataAssigner = $dataPointer . ' = \'' . $value . '\';';
			eval($dataAssigner);
		}
		else
			// @codeCoverageIgnoreStart
			$data = Arr::set($data, $key, $value);
			// @codeCoverageIgnoreEnd

		return $data;
	}

	/**
	 * Converts a value to string type.
	 *
	 * Note, a boolean value will be converted to 'true' if it is true
	 * and 'false' if it is false.
	 *
	 * @param  mixed  $value The value to be converted.
	 * @return string
	 */
	public static function ensureString($value) : string
	{
		if (is_bool($value))
			return ($value ? 'true' : 'false');
		else
			return (string)$value;
	}

	/**
	 * Converts a value to integer type.
	 *
	 * @param  mixed $value  The value to be converted.
	 * @return int
	 */
	public static function ensureInt($value) : int
	{
		return (int)$value;
	}

	/**
	 * Converts a value to float type.
	 *
	 * @param  mixed $value  The value to be converted.
	 * @return float
	 */
	public static function ensureFloat($value) : float
	{
		return (float)$value;
	}

	/**
	 * Converts a value to boolean type.
	 *
	 * @param  mixed $value  The value to be converted.
	 * @return bool
	 */
	public static function ensureBool($value) : bool
	{
		if (is_string($value))
		{
			$value = strtolower($value);

			if (in_array($value, ['true', '1', 'on', 'yes']))
				return true;
			else
				return false;
		}
		elseif (is_int($value) or is_float($value))
		{
			$value = (int)$value;

			return ($value > 0);
		}
		elseif (is_bool($value))
			return $value;
		else
			return false;
	}

	/**
	 * Converts a value to array type.
	 *
	 * If the given value is a string, and it is in the form (a,b,c) then an array
	 * consisting of each of the elements will be returned. If the given value is a string,
	 * and it is not in this form then an array consisting of just the string will be returned,
	 * if the given string is empty then an empty array will be returned.
	 *
	 * If the given value is not a string then it will return an array containing that value or
	 * the same value in case it is already an array.
	 *
	 * @param  mixed $value  The value to be converted.
	 * @return array
	 */
	public static function ensureArray($value) : array
	{
		if (is_string($value))
		{
			$value = trim($value);
			$length = mb_strlen($value);

			if ($length >= 2 and $value[0] === '(' and $value[$length - 1] === ')')
			{
				try
				{
					return eval('return array' . $value . ';');
				}
				// Use Error for PHP 7.2+ and Exception for PHP 8
				catch (Error | Exception $e)
				{
					return [];
				}
			}
			elseif ($length >= 2 and $value[0] === '[' and $value[$length - 1] === ']')
			{
				try
				{
					$syntax = 'return ' . $value . ';';
					return eval($syntax);
				}
				// Use Error for PHP 7.2+ and Exception for PHP 8
				catch (Error | Exception $e)
				{
					return [];
				}
			}
			else
				return ($length > 0 ? [$value] : []);
		}
		else
			return (array)$value;
	}

	/**
	 * Converts a value to object type.
	 *
	 * @param  mixed  $value  The value to be converted.
	 * @return object
	 */
	public static function ensureObject($value) : object
	{
		return (object)$value;
	}

	/**
	 * Verifies that the content of a variable is an array or an object
	 * implementing Countable.
	 *
	 * @param  mixed $data  The input data to check.
	 * @return bool         Returns true if data is countable, false otherwise.
	 */
	public static function isCountable($data) : bool
	{
		// PHP 7.3+
		if (function_exists('is_countable'))
		{
			// Ignore for PHPUnit 9+ on PHP 7.3+
			// @codeCoverageIgnoreStart
			return is_countable($data);
			// @codeCoverageIgnoreEnd
		}
		else
		{
			// Ignore for PHPUnit 8 on PHP 7.2
			// @codeCoverageIgnoreStart
			return is_array($data) or
				$data instanceof Countable or
				$data instanceof ResourceBundle or
				$data instanceof SimpleXmlElement;
			// @codeCoverageIgnoreEnd
		}
	}

	/**
	 * Throws an InvalidArgumentException if the given data is not the allowed data type.
	 *
	 * @param  string|array $allowedDataTypes  The allowed data types. Multiple values can be separated by comma.
	 * @param  int         $argument           The position of argument.
	 * @param  mixed       $data               The data to check.
	 * @return void
	 */
	public static function expects($allowedDataTypes, int $argument, $data) : void
	{
		if (!is_array($allowedDataTypes))
			$allowedDataTypes = explode(',', $allowedDataTypes);

		$allowedDataTypes = array_map('strtolower', $allowedDataTypes);
		$dataType = strtolower(gettype($data)); // NULL becames null.

		// gettype($data) will returns
		// 'interger', not 'int'
		// 'double', not 'float'
		// 'boolean', not 'bool'
		// so change it to what we can compare below.
		for ($i = 0, $n = count($allowedDataTypes); $i < $n; ++$i)
		{
			switch ($allowedDataTypes[$i])
			{
				case 'int':
					$allowedDataTypes[$i] = 'integer';
					break;

				case 'float':
					$allowedDataTypes[$i] = 'double';
					break;

				case 'bool':
					$allowedDataTypes[$i] = 'boolean';
					break;
			}
		}

		if (!in_array($dataType, $allowedDataTypes))
			throw InvalidArgumentException::typeError($argument, $allowedDataTypes, $data);
	}

	/**
	 * Converts the given data variable to new data type ($toType)
	 * if the original data type is as specified in $fromType.
	 *
	 * @param  string $fromType  The original data type.
	 * @param  string $toType    The new data type.
	 * @param  mixed  $var       The variable.
	 * @return void
	 */
	public static function convert(string $fromType, string $toType, &$var) : void
	{
		$dataType = strtolower(gettype($var));
		$fromType = strtolower($fromType);

		// gettype($var) will returns
		// 'interger', not 'int'
		// 'double', not 'float'
		// 'boolean', not 'bool'
		// so change it to what we can compare below.
		switch ($fromType)
		{
			case 'int':
				$fromType = 'integer';
				break;

			case 'float':
				$fromType = 'double';
				break;

			case 'bool':
				$fromType = 'boolean';
				break;
		}

		if ($dataType === $fromType)
		{
			switch ($toType)
			{
				case 'string':
					$var = static::ensureString($var);
					break;

				case 'int':
				case 'integer':
					$var = static::ensureInt($var);
					break;

				case 'float':
				case 'double':
					$var = static::ensureFloat($var);
					break;

				case 'bool':
				case 'boolean':
					$var = static::ensureBool($var);
					break;

				case 'array':
					$var = static::ensureArray($var);
					break;

				case 'object':
					$var = static::ensureObject($var);
					break;

				case 'null':
					$var = null;
					break;
			}
		}
	}
}
