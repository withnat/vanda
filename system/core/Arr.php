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

use ErrorException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use stdClass;
use System\Exception\InvalidArgumentException;

/**
 * Class Arr
 *
 * Additionally to the rich set of built-in PHP array functions, the Vanda array helper
 * provides extra static methods allowing you to deal with arrays more efficiently.
 *
 * @package System
 */
class Arr
{
	/**
	 * Arr constructor.
	 */
	private function __construct(){}

	/**
	 * Sets a value to the element at the specified position in the given array
	 * using path strings with dots.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'foo' => 'bar'
	 * ];
	 *
	 * $result = Arr::set($array, 'key.subkey', 'value');
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [foo] => bar
	 * //     [key] => Array
	 * //         (
	 * //             [subkey] => value
	 * //         )
	 * //
	 * // )
	 * ```
	 *
	 * @param  array  $array  Array to set a value in.
	 * @param  string $key    The key to set. If the key contains dot, it will set nested array data.
	 * @param  mixed  $value  Value to set.
	 * @return array
	 */
	public static function set(array $array, string $key, $value) : array
	{
		$keys = explode('.', $key);
		$arrayPointer = '$array';

		foreach ($keys as $key)
		{
			$arrayPointer .= static::formatKeySyntax($key);
			$var4If = '';

			// use @ to prevent error in case of key does not exists.
			$syntax = '$var4If = @' . $arrayPointer . ';';
			eval($syntax);

			if (!is_array($var4If))
			{
				$arrayAssigner = $arrayPointer . ' = [];';
				eval($arrayAssigner);
			}
		}

		if (is_string($value))
		{
			// Escape only single quote and backslash.
			$value = addcslashes($value, '\\\'');
		}

		$arrayAssigner = $arrayPointer . ' = \'' . $value . '\';';
		eval($arrayAssigner);

		return $array;
	}

	/**
	 * Returns a specific element from the given array. If the key contains dot,
	 * it will access nested array data.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'foo' => [
	 *         'bar' => 'baz'
	 *     ]
	 * ];
	 *
	 * $result = Arr::get($array, 'foo.bar');
	 * // The $result will be: baz
	 * ```
	 *
	 * @param  array      $array    The input array.
	 * @param  string|int $key      The searched key. If the key contains dot, it will access nested array data.
	 * @param  mixed      $default  Default value.
	 * @return mixed                Depends on what the given array contains.
	 */
	public static function get(array $array, $key, $default = null)
	{
		if (is_string($key))
			$keys = explode('.', $key);
		elseif (is_int($key))
			$keys = [$key];
		else
			throw InvalidArgumentException::typeError(2, ['string', 'int'], $key);

		foreach ($keys as $key)
		{
			if (!is_array($array) or !array_key_exists($key, $array))
				return $default;

			$array = $array[$key];
		}

		return $array;
	}

	/**
	 * Searches the array for a given value and returns the first corresponding key if successful.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe'
	 * ];
	 *
	 * $result = Arr::getKey($array, 'Nat');
	 * // The $result will be: name
	 * ```
	 *
	 * @param  array $array  The input array.
	 * @param  mixed $value  The searched value.
	 * @return mixed         Returns the key for needle if it is found in the given array, null otherwise.
	 */
	public static function getKey(array $array, $value)
	{
		$key = array_search($value, $array, true);

		if ($key === false)
			$key = null;

		return $key;
	}

	/**
	 * Returns the first $length elements from the given array.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'job' => 'Web Developer'
	 * ];
	 *
	 * $result = Arr::first($array);
	 * // The $result will be: Nat
	 *
	 * $result = Arr::first($array, 2);
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [name] => Nat
	 * //     [surname] => Withe
	 * // )
	 * ```
	 *
	 * @param  array    $array   The input array.
	 * @param  int|null $length  The number of elements to return. If $length is 1, returns value depend on what the
	 *                           given array contains. If $length is greater than 1, returns an array contains the first
	 *                           $length elements.
	 * @return mixed             Returns the first $length elements from the given array if the given array is not empty,
	 *                           null otherwise.
	 */
	public static function first(array $array, int $length = null)
	{
		if ($length < 0)
			throw InvalidArgumentException::valueError(2, '$length must be greater than zero', $length);

		if (is_null($length))
		{
			$array = array_values($array);

			// array_shift() returns null if the given array is empty.
			$value = array_shift($array);
		}
		else
			$value = array_slice($array, 0, $length, true);

		return $value;
	}

	/**
	 * Returns the last $length elements from the given array.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'job' => 'Web Developer'
	 * ];
	 *
	 * $result = Arr::last($array);
	 * // The $result will be: Web Developer
	 *
	 * $result = Arr::last($array, 2);
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [surname] => Withe
	 * //     [job] => Web Developer
	 * // )
	 * ```
	 *
	 * @param  array    $array   The input array.
	 * @param  int|null $length  The number of elements to return. If $length is 1, returns value depend on what the
	 *                           given array contains. If $length is greater than 1, returns an array contains the last
	 *                           $length elements.
	 * @return mixed             Returns the last $length elements from the given array if the given array is not empty,
	 *                           null otherwise.
	 */
	public static function last(array $array, int $length = null)
	{
		if ($length < 0)
			throw InvalidArgumentException::valueError(2, '$length must be greater than zero', $length);

		if (is_null($length))
		{
			$value = end($array);

			// The end() function returns FALSE if the array is empty.
			// So, convert it to null to make output same as result of
			// Arr::first() method in case of the given array is empty.
			if ($value === false)
				$value = null;
		}
		else
			$value = array_slice($array, count($array) - $length, null, true);

		return $value;
	}

	/**
	 * Returns the first $length keys from the given array.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'job' => 'Web Developer'
	 * ];
	 *
	 * $result = Arr::firstKey($array);
	 * // The $result will be: name
	 *
	 * $result = Arr::firstKey($array, 2);
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [0] => name
	 * //     [1] => surname
	 * // )
	 * ```
	 *
	 * @param  array    $array   The input array.
	 * @param  int|null $length  The number of keys to return. If $length is 1, returns the first key from the
	 *                           given array. If $length is greater than 1, returns an array contains the first $length
	 *                           keys.
	 * @return mixed             Returns the first $length key from the given array if the given array is not empty,
	 *                           null otherwise.
	 */
	public static function firstKey(array $array, int $length = null)
	{
		if (is_int($length) and $length < 1)
			throw InvalidArgumentException::valueError(2, '$length must be greater than zero', $length);

		if (is_null($length) or $length === 1)
		{
			// PHP 7.3+
			if (function_exists('array_key_first'))
			{
				// @codeCoverageIgnoreStart
				$key = array_key_first($array);
				// @codeCoverageIgnoreEnd
			}
			// PHP 7.2
			else
			{
				// Move the internal pointer to the first of the array.
				reset($array);

				// The key() function returns the index
				// element of the current array position.
				$key = key($array);
			}

			if ($length === 1)
				$key = [$key];
		}
		else
		{
			$keys = [];
			$i = 0;

			foreach ($array as $key => $value)
			{
				$keys[] = $key;

				++$i;

				if ($i === $length)
					break;
			}

			$key = $keys;
		}

		return $key;
	}

	/**
	 * Returns the last $length keys from the given array.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'job' => 'Web Developer'
	 * ];
	 *
	 * $result = Arr::lastKey($array);
	 * // The $result will be: job
	 *
	 * $result = Arr::lastKey($array, 2);
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [0] => surname
	 * //     [1] => job
	 * // )
	 * ```
	 *
	 * @param  array    $array   The input array.
	 * @param  int|null $length  The number of keys to return. If $length is 1, returns the last key from the
	 *                           given array. If $length is greater than 1, returns an array contains the last $length
	 *                           keys.
	 * @return mixed             Returns the last $length key from the given array if the given array is not empty,
	 *                           null otherwise.
	 */
	public static function lastKey(array $array, int $length = null)
	{
		if (is_int($length) and $length < 1)
			throw InvalidArgumentException::valueError(2, '$length must be greater than zero', $length);

		if (is_null($length) or $length === 1)
		{
			// PHP 7.3+
			if (function_exists('array_key_last'))
			{
				// @codeCoverageIgnoreStart
				$key = array_key_last($array);
				// @codeCoverageIgnoreEnd
			}
			// PHP 7.2
			else
			{
				// Move the internal pointer to the end of the array.
				end($array);

				// The key() function returns the index
				// element of the current array position.
				$key = key($array);
			}

			if ($length === 1)
				$key = [$key];
		}
		else
		{
			$array = static::last($array, $length);
			$keys = [];

			foreach ($array as $key => $value)
				$keys[] = $key;

			$key = $keys;
		}

		return $key;
	}

	/**
	 * Returns only the specified key/value pairs from the given array. The given array can be one or multi-dimensional
	 * array.
	 *
	 * The $keys can be 0, '0', '0,1', [0, 1], 'name,job.position', ['name, job.position'].
	 *
	 * Note, for numeric array, an index key 0 (int) is same as '0' (string).
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'job' => [
	 *         'position' => 'Web Developer',
	 *         'salary' => 10000
	 *     ]
	 * ];
	 *
	 * $result = Arr::only($array, 'name,job.salary');
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [name] => Nat
	 * //     [job] => Array
	 * //         (
	 * //             [salary] => 10000
	 * //         )
	 * // )
	 * ```
	 *
	 * @param  array            $array  The input array.
	 * @param  string|int|array $keys   The searched key. If the key contains dot, it will access nested array data
	 *                                  e.g., 0, '0', '0,1', [0, 1], 'name,job.position', ['name, job.position'].
	 * @return array                    Returns a subset of the items from the given array.
	 */
	public static function only(array $array, $keys) : array
	{
		if (is_string($keys))
			$keys = explode(',', $keys);
		elseif (is_int($keys))
			$keys = [$keys];
		elseif (!is_array($keys))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		$result = [];

		if ($array)
		{
			if (static::isMultidimensional($array))
			{
				foreach ($keys as $key)
				{
					$key = static::formatKeySyntax($key);
					$syntax = '$result' . $key . ' = $array' . $key . ';';
					eval($syntax);
				}
			}
			else
			{
				foreach ($keys as $key)
					$result[$key] = $array[$key];
			}
		}

		return $result;
	}

	/**
	 * Returns and removes an element by key from the given array.
	 * Data can be one or multi-dimensional array, but not a recordset.
	 *
	 * The given array can be one or multi-dimensional array.
	 * The $keys can be 0, '0', '0,1', [0, 1], 'name,job.position', ['name, job.position'].
	 *
	 * Note, For numeric array, an index key 0 (int) is same as '0' (string).
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     'name' => 'Nat',
	 *     'surname' => 'Withe',
	 *     'job' => [
	 *         'position' => 'Web Developer',
	 *         'salary' => 10000
	 *     ]
	 * ];
	 *
	 * $result = Arr::pull($array, 'name,job.salary');
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [name] => Nat
	 * //     [job] => Array
	 * //         (
	 * //             [salary] => 10000
	 * //         )
	 * // )
	 * //
	 * // And the $array will be:
	 * // Array
	 * // (
	 * //     [surname] => Withe
	 * //     [job] => Array
	 * //         (
	 * //             [position] => Web Developer
	 * //         )
	 * // )
	 * ```
	 *
	 * @param  array            $array  The input array.
	 * @param  string|int|array $keys   The searched key. If the key contains dot, it will access nested array data
	 *                                  e.g., 0, '0', '0,1', [0, 1], 'name,job.position', ['name, job.position'].
	 * @return mixed                    Depends on what the given array contains.
	 */
	public static function pull(array &$array, $keys)
	{
		if (is_string($keys))
			$keys = explode(',', $keys);
		elseif (is_int($keys))
			$keys = [$keys];
		elseif (!is_array($keys))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		if (count($keys) > 1)
			$result = [];
		else
			$result = '';

		if (static::isMultidimensional($array))
		{
			foreach ($keys as $key)
			{
				$key = static::formatKeySyntax($key);

				if (is_array($result))
				{
					$syntax = '$result' . $key . ' = $array' . $key . ';';
					eval($syntax);
				}
				else
				{
					$syntax = '$result = $array' . $key . ';';
					eval($syntax);
				}

				$syntax = 'unset($array' . $key . ');';
				eval($syntax);
			}
		}
		else
		{
			foreach ($keys as $key)
			{
				if (is_array($result))
					$result[$key] = $array[$key];
				else
					$result = $array[$key];

				unset($array[$key]);
			}
		}

		return $result;
	}

	/**
	 * Return the values from a single column in the given array contains array (dataset) or object (recordset).
	 *
	 * The $columnKey can be 0, '0', 'name', 'job.position'.
	 *
	 * For example,
	 *
	 * ```php
	 * $recordset = [
	 *     [
	 *         'name' => 'Nat',
	 *         'surname' => 'With',
	 *         'job' => [
	 *             'position' => 'Web Developer',
	 *             'salary' => 10000
	 *         ]
	 *     ],
	 *     [
	 *         'name' => 'Angela',
	 *         'surname' => 'SG',
	 *         'job' => [
	 *             'position' => 'Maketing Director',
	 *             'salary' => 10000
	 *         ]
	 *     ]
	 * ];
	 *
	 * $result = Arr::column($recordset, 'job.position');
	 *
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [0] => Web Developer
	 * //     [1] => Maketing Director
	 * // )
	 * //
	 *
	 * $result = Arr::column($recordset, 'job.position', 'name');
	 *
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [Nat] => Web Developer
	 * //     [Angela] => Maketing Director
	 * // )
	 * ```
	 *
	 * @param  array           $data       A multi-dimensional array contains array (dataset) or object (recordset).
	 * @param  string|int      $columnKey  The column key of values to return. If the key contains dot, it will access
	 *                                     nested array data e.g., 0, '0', 'name', 'job.position'.
	 * @param  string|int|null $indexKey   The column key to use as the index/key for the returned array.
	 * @return array                       Returns an array of values representing a single column from the given array.
	 */
	public static function column(array $data, $columnKey, $indexKey = null) : array
	{
		if (!static::isDataset($data) and !static::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		if (!is_string($columnKey) and !is_int($columnKey))
			throw InvalidArgumentException::typeError(2, ['string', 'int'], $columnKey);

		if (!is_null($indexKey) and !is_string($indexKey) and !is_int($indexKey))
			throw InvalidArgumentException::typeError(3, ['string', 'int', 'null'], $indexKey);

		$columnKey = static::formatKeySyntax($columnKey);

		if ($indexKey)
			$indexKey = static::formatKeySyntax($indexKey);

		$result = [];

		foreach ($data as $row)
		{
			$row = static::toArray($row);

			$syntax = '$value = $row' . $columnKey . ';';
			eval($syntax);

			if ($indexKey)
			{
				$syntax = '$key = $row' . $indexKey . ';';
				eval($syntax);

				$syntax = '$result[$key] = $value;';
				eval($syntax);
			}
			else
			{
				$syntax = '$result[] = $value;';
				eval($syntax);
			}
		}

		return $result;
	}

	/**
	 * Returns and removes a column by key from the given array contains array (dataset) or object (recordset).
	 *
	 * The $columnKey can be 0, '0', 'name', 'name,surname', ['name', 'surname']. This method does not support
	 * path strings with dots.
	 *
	 * For example,
	 *
	 * ```php
	 * $dataset = [
	 *     [
	 *         'name' => 'Nat',
	 *         'surname' => 'With',
	 *         'job' => 'Web Developer'
	 *     ],
	 *     [
	 *         'name' => 'Angela',
	 *         'surname' => 'SG',
	 *         'job' => 'Maketing Director'
	 *     ]
	 * ];
	 *
	 * $result = Arr::pullColumn($dataset, 'name,surname');
	 *
	 * // The $result will be:
	 * // Array
	 * // (
	 * //     [0] => Array
	 * //         (
	 * //             [name] => Nat
	 * //             [surname] => With
	 * //         )
	 * //
	 * //     [1] => Array
	 * //         (
	 * //             [name] => Angela
	 * //             [surname] => SG
	 * //         )
	 * // )
	 * //
	 * // And the $dataset will be:
	 * // Array
	 * // (
	 * //     [0] => Array
	 * //         (
	 * //             [job] => Web Developer
	 * //         )
	 * //
	 * //     [1] => Array
	 * //         (
	 * //             [job] => Maketing Director
	 * //         )
	 * // )
	 * ```
	 *
	 * @param  array            $data  A multi-dimensional array contains array (dataset) or object (recordset).
	 * @param  string|int|array $keys  The column of values to return e.g., 0, '0', 'name', 'name,surname',
	 *                                 ['name', 'surname'].
	 * @return array                   Returns an array of values representing a column from the given array.
	 */
	public static function pullColumn(array &$data, $keys) : array
	{
		if (!static::isDataset($data) and !static::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		if (!is_string($keys) and !is_int($keys) and !is_array($keys))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		if (is_string($keys))
			$keys = explode(',', $keys);
		elseif (is_int($keys))
			$keys = [$keys];

		$result = [];

		for ($i = 0, $n = count($data); $i < $n; ++$i)
		{
			if (is_object($data[$i]))
			{
				$row = new stdClass();

				foreach ($keys as $key)
				{
					$row->{$key} = $data[$i]->{$key};
					unset($data[$i]->{$key});
				}

				$result[$i] = $row;
			}
			else
			{
				foreach ($keys as $key)
				{
					$result[$i][$key] = $data[$i][$key];
					unset($data[$i][$key]);
				}
			}

		}

		return $result;
	}

	/**
	 * Get all of the given array except for a specified value.
	 *
	 * @param  array $array          An array to remove an element by value.
	 * @param  mixed $value          The value to remove.
	 * @param  bool  $caseSensitive  Case-sensitive or not.
	 * @param  bool  $recursive      True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function remove(array $array, $value, bool $caseSensitive = true, bool $recursive = true) : array
	{
		if (is_object($value) or is_resource($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'bool', 'array', 'null'], $value);

		if (is_array($value))
			$values = $value;
		else
			$values = [$value];

		foreach ($values as $value)
		{
			foreach ($array as $itemKey => $itemValue)
			{
				if (is_array($itemValue))
				{
					if ($recursive)
						$array[$itemKey] = static::remove($itemValue, $value, $caseSensitive, $recursive);
				}
				else
				{
					if (is_string($itemValue) and is_string($value) and !$caseSensitive)
					{
						if (mb_strtolower($itemValue) === mb_strtolower($value))
							unset($array[$itemKey]);
					}
					else
					{
						if ($itemValue === $value)
							unset($array[$itemKey]);
					}
				}
			}
		}

		return $array;
	}

	/**
	 * Get all of the given array except for a specified key/index.
	 *
	 * @param  array            $array      An array to remove an element by key.
	 * @param  string|int|array $keys       The key name or index to remove.
	 * @param  bool             $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function removeKey(array $array, $keys, bool $recursive = true) : array
	{
		if (is_string($keys))
		{
			if ($keys !== '') // can be '0'.
				$givenKeys = explode(',', $keys);
			else
				$givenKeys = [];
		}
		elseif (is_array($keys))
			$givenKeys = $keys;
		elseif (is_int($keys))
			$givenKeys = [$keys];
		else
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		foreach ($givenKeys as $key)
		{
			foreach ($array as $itemKey => $itemValue)
			{
				// Foreach function may fetch $itemKey to integer (0 is not equal '0')
				// e.g., Arr::removeKey(['a'], ['0']); The first index 'a' would be 0
				// and this method will remove array index 'a'. But, in fact, it should not!
				// So use (string) function to convert and compare it as string.
				if ((string)$itemKey === (string)$key)
					unset($array[$itemKey]);
				elseif ($recursive and is_array($itemValue))
					$array[$itemKey] = static::removeKey($itemValue, $keys, $recursive);
			}
		}

		return $array;
	}

	/**
	 * @param  array        $array      An array to remove an element by data type.
	 * @param  string|array $dataTypes  The data type to remove.
	 * @param  bool         $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function removeType(array $array, $dataTypes, bool $recursive = true) : array
	{
		if (!is_string($dataTypes) and !is_array($dataTypes))
			throw InvalidArgumentException::typeError(2, ['string', 'array'], $dataTypes);

		if (is_string($dataTypes))
			$dataTypes = explode(',', $dataTypes);

		foreach ($dataTypes as $dataType)
		{
			$dataType = strtolower($dataType);

			// gettype($itemValue) will returns
			// 'interger', no 'int'
			// 'double', no 'float'
			// 'boolean', no 'bool'
			// so change it to what we can compare below.
			switch ($dataType)
			{
				case 'int':
					$dataType = 'integer';
					break;

				case 'float':
					$dataType = 'double';
					break;

				case 'bool':
					$dataType = 'boolean';
					break;
			}

			foreach ($array as $itemKey => $itemValue)
			{
				if ($dataType === strtolower(gettype($itemValue)))
					unset($array[$itemKey]);
				elseif ($recursive and is_array($itemValue))
					$array[$itemKey] = static::removeType($itemValue, $dataTypes, $recursive);
			}
		}

		return $array;
	}

	/**
	 * @param  array $array      An array to remove an element by blank value.
	 * @param  bool  $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function removeBlank(array $array, bool $recursive = true) : array
	{
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				if ($recursive)
					$array[$key] = static::removeBlank($value, $recursive);
			}
			elseif (!strlen(trim((string)$value)))
				unset($array[$key]);
		}

		return $array;
	}

	/**
	 * Remove a column from an array of arrays (dataset) or objects (recordset).
	 *
	 * @param  array            $array  An array to remove an element by key (dataset or recordset).
	 * @param  string|int|array $keys   The key name to remove.
	 * @return array
	 */
	public static function removeColumn(array $array, $keys) : array
	{
		if (!static::isDataset($array) and !static::isRecordset($array))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $array);

		if (!is_string($keys) and !is_int($keys) and !is_array($keys))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		if (is_string($keys))
			$keys = explode(',', $keys);
		elseif (is_int($keys))
			$keys = [$keys];

		for ($i = 0, $n = count($array); $i < $n; ++$i)
		{
			if (is_object($array[$i]))
			{
				foreach ($keys as $key)
					unset($array[$i]->{$key});
			}
			else
			{
				foreach ($keys as $key)
					unset($array[$i][$key]);
			}
		}

		return $array;
	}

	/**
	 * Builds a map (key-value pairs) from a multi-dimensional array (dataset) or an array of objects (recordset).
	 *
	 * The `$from` and `$to` parameters specify the key names or property names to set up the map.
	 * Optionally, one can further group the map according to a grouping field $group.
	 *
	 * For example,
	 *
	 * ```php
	 * $array = [
	 *     ['id' => '101', 'name' => 'Nat', 'class' => 'A'],
	 *     ['id' => '102', 'name' => 'Ann', 'class' => 'A'],
	 *     ['id' => '103', 'name' => 'May', 'class' => 'B'],
	 * ];
	 *
	 * $result = Arr::map($array, 'id', 'name');
	 * // The $result will be:
	 * // [
	 * //     '101' => 'Nat',
	 * //     '102' => 'Ann',
	 * //     '103' => 'May',
	 * // ]
	 *
	 * $result = Arr::map($array, 'id', 'name', 'class');
	 * // The $result will be:
	 * // [
	 * //     'A' => [
	 * //         '101' => 'Nat',
	 * //         '102' => 'Ann',
	 * //     ],
	 * //     'B' => [
	 * //         '103' => 'May',
	 * //     ],
	 * // ]
	 * ```
	 *
	 * @param  array       $data
	 * @param  string      $from
	 * @param  string      $to
	 * @param  string|null $group
	 * @return array
	 */
	public static function map(array $data, string $from, string $to, string $group = null) : array
	{
		if (!static::isDataset($data) and !static::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		if (static::isRecordset($data))
			$data = static::toArray($data);

		$result = [];

		foreach ($data as $item)
		{
			$key = static::get($item, $from);
			$value = static::get($item, $to);

			if ($group)
			{
				$groupKey = static::get($item, $group);
				$result[$groupKey][$key] = $value;
			}
			else
				$result[$key] = $value;
		}

		return $result;
	}

	/**
	 * Insert an item onto the beginning of an array.
	 *
	 * @param  array       $array
	 * @param  mixed       $value
	 * @param  string|null $key
	 * @return array
	 */
	public static function insert(array $array, $value, string $key = null) : array
	{
		if (is_null($key))
			array_unshift($array, $value);
		else
			$array = [$key => $value] + $array;

		return $array;
	}

	/**
	 * Because in_array() returns true if $search is 0.
	 *
	 * Casting any string that doesn't start with a digit to a number results in 0 in PHP.
	 * And this is exactly what happens when comparing 0 with some string.
	 * See the PHP docs for details about how comparisons between various types are done.
	 *
	 * Use the third argument (set it to true) of in_array to avoid loose type comparison.
	 *
	 * @param  array $array          The array to search.
	 * @param  mixed $search         The searched value.
	 * @param  bool  $caseSensitive
	 * @return bool
	 */
	public static function has(array $array, $search, bool $caseSensitive = true) : bool
	{
		$searchDataType = gettype($search);

		if ($searchDataType === 'string')
		{
			if (!$caseSensitive)
			{
				// Remove data types that not compatible with mb_strtolower().
				$array = static::removeType($array, 'array,object,resource');

				return in_array(mb_strtolower($search), array_map('mb_strtolower', $array), true);
			}
			else
				return in_array($search, $array, true);
		}
		else
		{
			// Each object and resource has its own object/resource#id.
			// Comparison in strict mode will always return false.
			if (in_array($searchDataType, ['object', 'resource']))
				return in_array($search, $array);
			// If the searched value is an array that contains string
			// and compare value in case-sensitive mode.
			elseif ($searchDataType === 'array' and !$caseSensitive)
				return in_array($search, $array);
			// If the searched value is an array and other.
			else
				return in_array($search, $array, true);
		}
	}

	public static function hasAny(array $array, array $searches, bool $caseSensitive = true) : bool
	{
		foreach ($searches as $search)
		{
			if (static::has($array, $search, $caseSensitive))
				return true;
		}

		return false;
	}

	public static function hasAll(array $array, array $searches, bool $caseSensitive = true) : bool
	{
		foreach ($searches as $search)
		{
			if (!static::has($array, $search, $caseSensitive))
				return false;
		}

		return true;
	}

	/**
	 * Checks if the given key or index exists in the array.
	 *
	 * For example,
	 *
	 * ```php
	 * $employee = [
	 *     'name' => 'Nat',
	 *     'age' => '38',
	 *     'job' => [
	 *         'position' => 'Web Developer',
	 *         'saraly' => 100000
	 *     ]
	 * ];
	 *
	 * $result = Arr::hasKey($employee, 'job');
	 * // The $result will be: True
	 *
	 * $result = Arr::hasKey($employee, 'job.position');
	 * // The $result will be: True

	 * $result = Arr::hasKey($employee, 'job.startdate');
	 * // The $result will be: False
	 * ```
	 *
	 * For an indexed array, numerice key and string key are not difference.
	 *
	 * ```php
	 * $array = ['Nat'];
	 *
	 * $result = Arr::hasKey($array, '0');
	 * // The $result will be: True
	 *
	 * $result = Arr::hasKey($array, 0);
	 * // The $result will be: True
	 * ```
	 *
	 * @param  array      $array  An array with keys to check.
	 * @param  string|int $key    Value to check.
	 * @return bool
	 */
	public static function hasKey(array $array, $key) : bool
	{
		if (!is_string($key) and !is_int($key))
			throw InvalidArgumentException::typeError(2, ['string', 'int'], $key);

		// Is key in first dimension of array?
		if (array_key_exists($key, $array))
			return true;

		// Numeric key is not in base array.
		if (is_int($key))
			return false;

		// If key is string contains dot and is not in first dimension of array.
		if (strpos($key, '.') and static::isMultidimensional($array))
		{
			$pos = strrpos($key, '.');

			$searchedKey = substr($key, 0, $pos);
			$searchedKey = static::formatKeySyntax($searchedKey);

			$exist = false;

			$syntax = '$exist = isset($array' . $searchedKey . ');';
			eval($syntax);

			if ($exist)
			{
				$value = '';

				$syntax = '$value = $array' . $searchedKey . ';';
				eval($syntax);

				if (is_array($value))
				{
					$key = substr($key, $pos + 1);

					return array_key_exists($key, $value);
				}
			}
		}

		return false;
	}

	/**
	 * @param  array            $array
	 * @param  string|int|array $keys
	 * @return bool
	 */
	public static function hasAnyKey(array $array, $keys) : bool
	{
		if (is_string($keys))
			$keys = explode(',', $keys);
		elseif (is_int($keys))
			$keys = [$keys];
		elseif (!is_array($keys))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		foreach ($keys as $key)
		{
			if (static::hasKey($array, $key))
				return true;
		}

		return false;
	}

	/**
	 * @param  array            $array
	 * @param  string|int|array $keys
	 * @return bool
	 */
	public static function hasAllKeys(array $array, $keys) : bool
	{
		if (is_string($keys))
			$keys = explode(',', $keys);
		elseif (is_int($keys))
			$keys = [$keys];
		elseif (!is_array($keys))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'array'], $keys);

		foreach ($keys as $key)
		{
			if (!static::hasKey($array, $key))
				return false;
		}

		return true;
	}

	/**
	 * Random Element Value - Takes an array as input and returns a random element value.
	 *
	 * @param  array $array  The array to random.
	 * @return mixed         Depends on what the array contains.
	 */
	public static function random(array $array)
	{
		if (empty($array))
			return null;
		else
			return $array[array_rand($array)];
	}

	/**
	 * Random Element Key - Takes an array as input and returns a random element key.
	 *
	 * @param  array      $array  The array to random key.
	 * @return int|string         Depends on type of index.
	 */
	public static function randomKey(array $array)
	{
		if (empty($array))
			return null;
		else
			return array_rand($array);
	}

	/**
	 * This function shuffles (randomizes the order of the elements in) an array.
	 *
	 * @param  array $array
	 * @return array
	 */
	public static function shuffle(array $array) : array
	{
		shuffle($array);

		return $array;
	}

	/**
	 * Sort an array by values.
	 *
	 * @param  array  $array
	 * @param  string $direction  'asc' or 'desc'
	 * @param  bool   $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function sort(array $array, string $direction = 'asc', bool $recursive = true) : array
	{
		if ($direction === 'asc')
		{
			if (static::isAssociative($array))
				asort($array);
			else
				sort($array);
		}
		else
		{
			if (static::isAssociative($array))
				arsort($array);
			else
				rsort($array);
		}

		foreach ($array as $key => $value)
		{
			if ($recursive and is_array($value))
				$array[$key] = static::sort($value, $direction, $recursive);
		}

		return $array;
	}

	/**
	 * Sort an array by keys.
	 *
	 * @param  array  $array
	 * @param  string $direction  'asc' or 'desc'
	 * @param  bool   $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function sortKey(array $array, string $direction = 'asc', bool $recursive = true) : array
	{
		if ($direction === 'asc')
			ksort($array);
		else
			krsort($array);

		foreach ($array as $key => $value)
		{
			if ($recursive and is_array($value))
				$array[$key] = static::sortKey($value, $direction, $recursive);
		}

		return $array;
	}

	/**
	 * Utility function to sort an array of arrays (dataset) on a given field.
	 *
	 * @param  array  $dataset    An array of arrays (dataset).
	 * @param  string $key        The key to sort on.
	 * @param  string $direction  Direction to sort in [asc = Ascending] [desc = Descending].
	 * @return array              The sorted array of arrays (dataset).
	 */
	public static function sortDataset(array $dataset, string $key, string $direction = 'asc') : array
	{
		if (!static::isDataset($dataset))
			throw InvalidArgumentException::typeError(1, ['dataset'], $dataset);

		if (strtolower($direction) === 'desc')
			$direction = -1;
		else
			$direction = 1;

		$GLOBALS['System\Arr::sortDataset'] = ['key' => $key, 'direction' => $direction];

		usort($dataset, function($a, $b)
		{
			$params = $GLOBALS['System\Arr::sortDataset'];

			if (strcmp($a[$params['key']], $b[$params['key']]) > 0)
				return $params['direction'];
			elseif (strcmp($a[$params['key']], $b[$params['key']]) < 0)
				return $params['direction'] * -1;
			else
				return 0;
		});

		unset($GLOBALS['System\Arr::sortDataset']);

		return $dataset;
	}

	/**
	 * Utility function to sort an array of objects (recordset) on a given field.
	 *
	 * @param  array  $recordset  An array of objects (recordset).
	 * @param  string $key        The key to sort on.
	 * @param  string $direction  Direction to sort in [asc = Ascending] [desc = Descending].
	 * @return array              The sorted array of objects (recordset).
	 */
	public static function sortRecordset(array $recordset, string $key, string $direction = 'asc') : array
	{
		if (!static::isRecordset($recordset))
			throw InvalidArgumentException::typeError(1, ['recordset'], $recordset);

		if (strtolower($direction) === 'desc')
			$direction = -1;
		else
			$direction = 1;

		$GLOBALS['System\Arr::sortRecordset'] = ['key' => $key, 'direction' => $direction];

		usort($recordset, function($a, $b)
		{
			$params = $GLOBALS['System\Arr::sortRecordset'];

			if (strcmp($a->{$params['key']}, $b->{$params['key']}) > 0)
				return $params['direction'];
			elseif (strcmp($a->{$params['key']}, $b->{$params['key']}) < 0)
				return $params['direction'] * -1;
			else
				return 0;
		});

		unset($GLOBALS['System\Arr::sortRecordset']);

		return $recordset;
	}

	/**
	 * Convert a multi-dimensional array into a single-dimensional array.
	 *
	 * @param  array $array  A multi-dimensional array.
	 * @return array         Returns an array of values representing a single column from the input array.
	 */
	public static function flatten(array $array) : array
	{
		$result = [];

		$item = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

		foreach ($item as $value)
			$result[] = $value;

		return $result;
	}

	/**
	 * The Arr::dot method flattens a multi-dimensional array into
	 * a single level array that uses "dot" notation to indicate depth.
	 *
	 * @param  array  $array
	 * @param  string $prepend
	 * @return array
	 */
	public static function dot(array $array, string $prepend = '') : array
	{
		$result = [];

		foreach ($array as $key => $value)
		{
			if (is_array($value) and !empty($value))
				$result = array_merge($result, static::dot($value, $prepend . $key . '.'));
			else
				$result[$prepend . $key] = $value;
		}

		return $result;
	}

	/**
	 * Method to determine if the input data is a dataset or not.
	 *
	 * @param  mixed $data  The data to check.
	 * @return bool         True if the data is a recordset.
	 */
	public static function isDataset($data) : bool
	{
		if (!is_array($data) or
			!isset($data[0]) or
			!is_array($data[0]))
		{
			return false;
		}

		$masterKeys = array_keys($data[0]);
		$masterKeyCount = count($masterKeys);

		for ($i = 1, $n = count($data); $i < $n; ++$i)
		{
			if (!is_array($data[$i]))
				return false;

			$rowKeys = array_keys($data[$i]);
			$rowKeyCount = count($rowKeys);

			if ($masterKeyCount != $rowKeyCount)
				return false;

			foreach ($masterKeys as $k => $masterKey)
			{
				if ($masterKey != $rowKeys[$k])
					return false;
			}
		}

		return true;
	}

	/**
	 * Method to determine if the input data is a recordset or not.
	 *
	 * @param  mixed $data  The data to check.
	 * @return bool         True if the data is a recordset.
	 */
	public static function isRecordset($data) : bool
	{
		if (!is_array($data) or !isset($data[0]) or !is_object($data[0]))
			return false;

		$data[0] = (array)$data[0];
		$masterKeys = array_keys($data[0]);
		$masterKeyCount = count($masterKeys);

		for ($i = 1, $n = count($data); $i < $n; ++$i)
		{
			if (!is_object($data[$i]))
				return false;

			$row = (array)$data[$i];
			$rowKeys = array_keys($row);
			$rowKeyCount = count($rowKeys);

			if ($masterKeyCount != $rowKeyCount)
				return false;

			foreach ($masterKeys as $k => $masterKey)
			{
				if ($masterKey != $rowKeys[$k])
					return false;
			}
		}

		return true;
	}

	/**
	 * Method to determine if the input data is an associative array or not.
	 *
	 * @param  mixed $data  The data to check.
	 * @return bool         True if the data is an associative array.
	 */
	public static function isAssociative($data) : bool
	{
		if (!is_array($data))
			return false;

		if (empty($data)) // empty array [].
			return false;

		$result = (array_keys($data) !== range(0, count($data) - 1));

		return $result;
	}

	/**
	 * Method to determine if the input data is a multi-dimensional array or not.
	 *
	 * @param  mixed $data  The data to check.
	 * @return bool         True if the data is multi-dimensional array.
	 */
	public static function isMultidimensional($data) : bool
	{
		if (!is_array($data))
			return false;

		if (static::isAssociative($data))
			$data = static::toSequential($data);

		// rsort() sorts all the sub-arrays towards the beginning
		// of the parent array, and re-indexes the array.
		// This ensures that if there are one or more sub-arrays
		// inside the parent array, the first element of parent
		// array (at index 0) will always be an array.
		// Checking for the element at index 0, we can tell
		// whether the array is multi-dimensional or not.
		rsort($data);

		$result = (isset($data[0]) and is_array($data[0]));

		return $result;
	}

	/**
	 * Utility function to map an object to an array.
	 *
	 * @param  object             $data       The source object.
	 * @param  bool               $recursive  True to recurve through multi-level objects.
	 * @param  string|array|null  $keys       An optional field names. Only be used in top level elements.
	 * @return array                          The array mapped from the given object.
	 */
	public static function fromObject(object $data, bool $recursive = true, $keys = null) : array
	{
		if (is_null($keys))
			$givenKeys = [];
		elseif (is_string($keys))
		{
			if ($keys !== '') // can be '0'.
				$givenKeys = explode(',', $keys);
			else
				$givenKeys = [];
		}
		elseif (is_array($keys))
			$givenKeys = $keys;
		else
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $keys);

		$result = [];

		foreach ($data as $key => $value)
		{
			// If the $key is 0 (interger) and the $givenKeys contains only string
			// e.g., in_array(0, ['a', 'b', 'c'] it always return TRUE.
			// But numeric key of an object is always string. Don't need to convert it.
			//if ($key === 0)
			//	$key = (string)$key;

			if (!$givenKeys or in_array($key, $givenKeys))
			{
				if ($recursive)
				{
					// Data under this level may be is an object.
					// e.g., object > array (this level) > object > ...
					// So convert array to object to ensure it will
					// go to next level recursively.
					if (is_array($value))
						$value = (object)$value;

					// Go to next level (recursive).
					if (is_object($value))
						$result[$key] = static::fromObject($value, $recursive);
					else
						$result[$key] = $value;
				}
				else
				{
					if (is_object($value) or is_array($value))
						$result[$key] = [];
					else
						$result[$key] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Parses str as if it were the query string passed via a URL and sets variables in the current scope.
	 *
	 * @param  string $string  The input string.
	 * @return array           The array parsed from the given string.
	 */
	public static function fromString(string $string) : array
	{
		parse_str($string, $array);

		return $array;
	}

	/**
	 * Utility function to convert the given data to an array.
	 *
	 * @param  mixed                 $data       The source data.
	 * @param  bool                  $recursive  True to recurve through multi-level arrays or objects.
	 * @param  string|int|array|null $keys       An optional field names. Only be used in top level elements.
	 * @return array                             The array mapped from the given object.
	 */
	public static function toArray($data, bool $recursive = true, $keys = null) : array
	{
		if (is_array($data) or is_object($data))
		{
			if (is_null($keys))
				$givenKeys = [];
			elseif (is_string($keys))
			{
				if ($keys !== '') // can be '0'.
					$givenKeys = explode(',', $keys);
				else
					$givenKeys = [];
			}
			elseif (is_int($keys))
				$givenKeys = [$keys];
			elseif (is_array($keys))
				$givenKeys = $keys;
			else
				throw InvalidArgumentException::typeError(3, ['string', 'int', 'array', 'null'], $keys);

			$result = [];

			foreach ($data as $key => $value)
			{
				// If the $key is 0 (interger) and the $givenKeys contains only string
				// e.g., in_array(0, ['a', 'b', 'c'] it always return TRUE.
				if ($key === 0)
					$key = (string)$key;

				if (!$givenKeys or in_array($key, $givenKeys))
				{
					if (is_array($value) or is_object($value))
					{
						if ($recursive)
							$result[$key] = static::toArray($value, $recursive);
						else
							$result[$key] = [];
					}
					else
						$result[$key] = $value;
				}
			}
		}
		else
			$result = [$data];

		return $result;
	}

	/**
	 * Utility function to map an array to a stdClass object.
	 *
	 * @param  array             $array      The array to map.
	 * @param  string            $class      Name of the class to create.
	 * @param  bool              $recursive  True to recurve through multi-level arrays.
	 * @param  string|array|null $keys       An optional field names. Only be used in top level elements.
	 * @return object                        The object mapped from the given array.
	 */
	public static function toObject(array $array, string $class = 'stdClass', bool $recursive = true, $keys = null) : object
	{
		if (is_null($keys))
			$givenKeys = [];
		elseif (is_string($keys))
		{
			if ($keys !== '') // can be '0'.
				$givenKeys = explode(',', $keys);
			else
				$givenKeys = [];
		}
		elseif (is_int($keys))
			$givenKeys = [$keys];
		elseif (is_array($keys))
			$givenKeys = $keys;
		else
			throw InvalidArgumentException::typeError(4, ['string', 'int', 'array', 'null'], $keys);

		$obj = new $class;

		foreach ($array as $key => $value)
		{
			// If the $key is 0 (interger) and the $givenKeys contains only string
			// e.g., in_array(0, ['a', 'b', 'c'] it always return TRUE.
			if ($key === 0)
				$key = (string)$key;

			if (!$givenKeys or in_array($key, $givenKeys))
			{
				if ($recursive)
				{
					// Data under this level may be is an array.
					// e.g., array > object (this level) > array > ...
					// So convert object to array to ensure it will
					// go to next level recursively.
					if (is_object($value))
						$value = (array)$value;

					// Go to next level (recursive).
					if (is_array($value))
						$obj->{$key} = static::toObject($value, $class, $recursive);
					else
						$obj->{$key} = $value;
				}
				else
				{
					if (is_array($value) or is_object($value))
						$obj->{$key} = new $class;
					else
						$obj->{$key} = $value;
				}
			}
		}

		return $obj;
	}

	/**
	 * Utility function to map an array to a string.
	 *
	 * @param  array                 $array           The array to map.
	 * @param  string                $innerGlue       The glue (optional, defaults to '=') between the key and the value.
	 * @param  string                $outerGlue       The glue (optional, defaults to ' ') between array elements.
	 * @param  string                $valueDelimiter  Value delimiter.
	 * @param  bool                  $recursive       True to recurve through multi-level arrays.
	 * @param  string|int|array|null $keys            An optional field names. Only be used in top level elements.
	 * @return string                                 The string mapped from the given array.
	 */
	public static function toString(array $array, string $innerGlue = '=', string $outerGlue = ' ', string $valueDelimiter = '"', bool $recursive = true, $keys = null) : string
	{
		if (is_null($keys))
			$givenKeys = [];
		elseif (is_string($keys))
		{
			if ($keys !== '') // can be '0'.
				$givenKeys = explode(',', $keys);
			else
				$givenKeys = [];
		}
		elseif (is_int($keys))
			$givenKeys = [$keys];
		elseif (is_array($keys))
			$givenKeys = $keys;
		else
			throw InvalidArgumentException::typeError(6, ['string', 'int', 'array', 'null'], $keys);

		$output = [];

		foreach ($array as $key => $value)
		{
			// If the $key is 0 (interger) and the $givenKeys contains only string
			// e.g., in_array(0, ['a', 'b', 'c'] it always return TRUE.
			if ($key === 0)
				$key = (string)$key;

			if (!$givenKeys or in_array($key, $givenKeys))
			{
				if (is_array($value))
				{
					if ($recursive)
						$output[] = static::toString($value, $innerGlue, $outerGlue, $valueDelimiter, $recursive);
				}
				else
					$output[] = $key . $innerGlue . $valueDelimiter . $value . $valueDelimiter;
			}
		}

		return implode($outerGlue, $output);
	}

	/**
	 * Convert the array into a query string.
	 *
	 * @param  array  $array
	 * @return string
	 */
	public static function toQueryString(array $array) : string
	{
		// Set enc_type to PHP_QUERY_RFC3986, then encoding is performed according to » RFC 3986,
		// and spaces will be percent encoded (%20).
		// https://www.php.net/manual/en/function.http-build-query.php
		$queryString = http_build_query($array, '', '&', PHP_QUERY_RFC3986);

		return $queryString;
	}

	/**
	 * @param  mixed $data
	 * @return array
	 */
	public static function toDataset($data) : array
	{
		$data = static::toArray($data);

		if (!static::isMultidimensional($data))
			$data = [$data];

		$dataset = [];

		foreach ($data as $item)
		{
			$item = (array)$item;
			$dataset[] = $item;
		}

		return $dataset;
	}

	/**
	 * @param  mixed $data
	 * @return array
	 */
	public static function toRecordset($data) : array
	{
		$data = static::toArray($data);

		if (!static::isMultidimensional($data))
			$data = [$data];

		$recordset = [];

		foreach ($data as $item)
		{
			$item = (object)$item;
			$recordset[] = $item;
		}

		return $recordset;
	}

	/**
	 * @param  mixed $data
	 * @return array
	 */
	public static function toMultidimensional($data) : array
	{
		if (static::isMultidimensional($data))
			return $data;

		$data = [static::toArray($data)];

		return $data;
	}

	/**
	 * @param  mixed $data
	 * @return array        Return an array with a numeric index. Values are stored and accessed in linear fashion.
	 */
	public static function toSequential($data) : array
	{
		if (is_array($data) or is_object($data))
		{
			if (is_object($data))
				$data = (array)$data;

			$data = array_values($data);

			foreach ($data as $i => $value)
			{
				if (is_object($value))
					$value = (array)$value;

				if (is_array($value))
					$data[$i] = static::toSequential($value);
			}
		}
		else
			$data = [$data];

		return $data;
	}

	/**
	 * Encodes an array to JSON.
	 *
	 * @param  array  $array  An array to encode.
	 * @return string
	 * @throws ErrorException
	 * @codeCoverageIgnore
	 */
	public static function toJson(array $array) : string
	{
		return Json::encode($array);
	}

	/**
	 * Extract a slice of the array.
	 * An alias for built-in PHP function array_slice() with preserve_keys parameter default to TRUE.
	 *
	 * @param  array    $array
	 * @param  int      $offset
	 * @param  int|null $length
	 * @return array
	 */
	public static function slice(array $array, int $offset, int $length = null) : array
	{
		return array_slice($array, $offset, $length, true); // 'preserve_keys' parameter default to TRUE.
	}

	/**
	 * Removes duplicate values from an array.
	 *
	 * @param  array $array
	 * @param  bool  $recursive  True to recurve through multi-level arrays.
	 * @param  bool  $reindex    In case of $array is not an associative.
	 * @return array
	 */
	public static function unique(array $array, bool $recursive = true, bool $reindex = true) : array
	{
		// Checks if the given array is an associative
		// before removing duplicate values. If checking
		// after removing duplicate values, it will always
		// return true. See more at Arr::isAssociative().
		$isAssociative = static::isAssociative($array);

		$array = array_unique($array, SORT_REGULAR);

		if ($recursive)
		{
			foreach ($array as $key => $value)
			{
				if (is_array($value))
					$array[$key] = static::unique($value, $recursive, $recursive);
			}
		}

		if ($reindex and !$isAssociative)
			$array = array_values($array);

		return $array;
	}

	/**
	 * @param  array  $array
	 * @param  string $glue
	 * @param  bool   $recursive
	 * @return string
	 */
	public static function implode(array $array, string $glue = '', bool $recursive = true) : string
	{
		$string = '';

		foreach ($array as $piece)
		{
			if (is_array($piece))
			{
				if ($recursive)
					$string .= static::implode($piece, $glue) . $glue;
			}
			else
				$string .= $piece . $glue;
		}

		$string = rtrim($string, $glue);

		return $string;
	}

	/**
	 * Wraps the given value in an array format.
	 *
	 * e.g.,
	 *
	 * 0 to [0]
	 * '0' to ['0']
	 * 'name' to ['name']
	 * 'job.position' to ['job']['position']
	 *
	 * @param  string|int $key
	 * @return string
	 */
	public static function formatKeySyntax($key) : string
	{
		if (!is_string($key) and !is_int($key))
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $key);

		if (is_string($key))
		{
			// $key can be '0'.
			if ($key === '')
				return '';

			$key = str_replace('.', '\'][\'', $key);
		}

		$key = "['$key']";

		return $key;
	}
}
