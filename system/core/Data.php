<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license     MIT
 * @link        http://vanda.io
 */

declare(strict_types=1);

namespace System;

use Exception;
use System\Exception\InvalidArgumentException;

/**
 * Class Data
 * @package System
 */
final class Data
{
	/**
	 * Data constructor.
	 */
	private function __construct(){}

	/**
	 * Return a specific element from the given array or object.
	 * A $keys variable supports both array and object.
	 * If the array element / object property is empty it returns NULL (or whatever you specify as the default value).
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
	 * $data['job']->title = 'Web Developer';
	 * $data['job']->salary = '10000';
	 *
	 * $result = Data::get($data, 'job.title');
	 * // the result is: Web Developer
	 * ```
	 *
	 * @param  array|object $data     The data, array or object.
	 * @param  int|string   $keys     The searched key.
	 * @param  mixed        $default  Default value.
	 * @return mixed                  Depends on what the array contains.
	 */
	public static function get($data, $keys, $default = null)
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::type(1, ['array','object'], $data);

		if (is_string($keys))
			$keys = Str::explode($keys, '.');
		elseif (is_int($keys))
			$keys = [$keys];
		else
			throw InvalidArgumentException::type(2, ['int','string'], $keys);

		foreach ($keys as $key)
		{
			if (is_object($data))
			{
				if (isset($data->{$key}))
					$data = $data->{$key};
				else
					$data = $default;
			}
			elseif (is_array($data))
			{
				if (array_key_exists($key, $data))
					$data = $data[$key];
				else
					$data = $default;
			}
			else
				$data = $default;
		}

		return $data;
	}

	/**
	 * @param  array|object $data
	 * @param  string       $key
	 * @param  mixed        $value
	 * @return array|object
	 */
	public static function set($data, string $key, $value)
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::type(1, ['array','object'], $data);

		if (is_object($data))
		{
			$keys = explode('.', $key);
			$dataPointer = '$data';

			foreach ($keys as $key)
			{
				$dataPointer .= "->{'$key'}";
				$var4If = '';

				// use @ to prevent error in case of key does not exists.
				$syntax = '$var4If = @' . $dataPointer . ';';
				eval($syntax);

				if (!is_object($var4If))
				{
					$dataAssigner = $dataPointer . ' = new \stdClass();';;
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
			$data = Arr::set($data, $key, $value);

		return $data;
	}

	/**
	 * Converts a value to boolean type.
	 *
	 * @param  mixed $value  The value to be converted.
	 * @return bool
	 */
	public static function ensureBoolean($value) : bool
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
	public static function ensureInteger($value) : int
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
	 * Converts a value to array type.
	 *
	 * If the value is a string and it is in the form (a,b,c) then an array
	 * consisting of each of the elements will be returned. If the value is a string
	 * and it is not in this form then an array consisting of just the string will be returned,
	 * if the string is empty an empty array will be returned.
	 * If the value is not a string then it will return an array containing that value or
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
				catch (Exception $e)
				{
					return [];
				}
			}
			elseif ($length >= 2 and $value[0] === '[' and $value[$length - 1] === ']')
			{
				try
				{
					return eval('return ' . $value . ';');
				}
				catch (Exception $e)
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
	 * @param  string $allowedDataTypes
	 * @param  int    $argument
	 * @param  mixed  $data
	 * @return void
	 */
	public static function is(string $allowedDataTypes, int $argument, $data = null) : void
	{
		$dataTypes = strtolower($allowedDataTypes);
		$dataTypes = explode(',', $dataTypes);
		$dataTypes = array_map('trim', $dataTypes);

		$valid = false;

		foreach ($dataTypes as $dataType)
		{
			if ($dataType === 'string' and is_string($data))
			{
				$valid = true;
				break;
			}
			elseif (($dataType === 'int' or $dataType === 'integer') and is_int($data))
			{
				$valid = true;
				break;
			}
			elseif ($dataType === 'float' and is_float($data))
			{
				$valid = true;
				break;
			}
			elseif (($dataType === 'bool' or $dataType === 'boolean') and is_bool($data))
			{
				$valid = true;
				break;
			}
			elseif ($dataType === 'array' and is_array($data))
			{
				$valid = true;
				break;
			}
			elseif ($dataType === 'object' and is_object($data))
			{
				$valid = true;
				break;
			}
			elseif ($dataType === 'null' and is_null($data))
			{
				$valid = true;
				break;
			}
			elseif ($dataType === 'resource' and is_resource($data))
			{
				$valid = true;
				break;
			}
		}

		if (!$valid)
			throw InvalidArgumentException::type($argument, $allowedDataTypes, $data);

//		$dataTypes = strtolower($allowedDataTypes);
//		$dataTypes = explode(',', $dataTypes);
//		$dataTypes = array_map('trim', $dataTypes);
//
//		$dataType = strtolower(gettype($data));
//
//		if (!in_array($dataType, $dataTypes))
//			throw InvalidArgumentException::type($argument, $allowedDataTypes, $data);
	}

	/**
	 * @param  array $allowedDataTypes
	 * @param  int   $argument
	 * @param  mixed $data
	 * @return void
	 */
	public static function expects(array $allowedDataTypes, int $argument, $data = null) : void
	{
		$dataType = strtolower(gettype($data));

		if (!in_array($dataType, $allowedDataTypes))
			throw InvalidArgumentException::type($argument, $allowedDataTypes, $data);
	}

	/**
	 * @param  string $fromType
	 * @param  string $toType
	 * @param  mixed  $data
	 * @return void
	 */
	public static function convert(string $fromType, string $toType, &$data) : void
	{
		$dataType = strtolower(gettype($data));
		$fromType = strtolower($fromType);

		// gettype($data) will returns
		// 'interger', no 'int'
		// 'double', no 'float'
		// 'boolean', no 'bool'
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
					$data = static::ensureString($data);
					break;

				case 'int':
				case 'integer':
					$data = static::ensureInteger($data);
					break;

				case 'float':
				case 'double':
					$data = static::ensureFloat($data);
					break;

				case 'bool':
				case 'boolean':
					$data = static::ensureBoolean($data);
					break;

				case 'object':
					$data = static::ensureObject($data);
					break;

				case 'null':
					$data = null;
					break;
			}
		}
	}
}
