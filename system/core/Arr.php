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

use ErrorException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use stdClass;
use System\Exception\InvalidArgumentException;

/**
 * Class Arr
 * @package System
 */
final class Arr
{
	/**
	 * Arr constructor.
	 */
	private function __construct(){}

	/**
	 * Return a specific element from the given array.
	 *
	 * Lets you determine whether an array index is set and whether it has a value.
	 * If the element is empty it returns NULL (or whatever you specify as the default value).
	 *
	 * @param  array      $array    The array.
	 * @param  int|string $keys     The searched key.
	 * @param  mixed      $default  Default value.
	 * @return mixed                Depends on what the array contains.
	 */
	public static function get(array $array, $keys, $default = null)
	{
		if (is_string($keys))
			$keys = explode('.', $keys);
		elseif (is_int($keys))
			$keys = [$keys];
		else
			throw InvalidArgumentException::typeError(2, ['int','string'], $keys);

		foreach ($keys as $key)
		{
			if (!is_array($array) or !array_key_exists($key, $array))
				return $default;

			$array = $array[$key];
		}

		return $array;
	}

	/**
	 * @param  array $array  The array.
	 * @param  mixed $value  The searched value.
	 * @return mixed         Returns the key for needle if it is found in the array, NULL otherwise.
	 */
	public static function getKey(array $array, $value)
	{
		$key = array_search($value, $array, true);

		if ($key === false)
			$key = null;

		return $key;
	}

	/**
	 * Return the values from a single column in the input array contains array (dataset) or object (recordset).
	 *
	 * Example
	 *
	 * $recordset = [
	 *     [
	 *         'name' => 'Nat',
	 *         'surname' => 'With',
	 *         'job' => [
	 *             'title' => 'Web Developer',
	 *             'salary' => 10000
	 *         ]
	 *     ],
	 *     [
	 *         'name' => 'Angela',
	 *         'surname' => 'SG',
	 *         'job' => [
	 *             'title' => 'Maketing Director',
	 *             'salary' => 10000
	 *         ]
	 *     ]
	 * ];
	 *
	 * $result = Arr::column($recordset, 'job.title');
	 *
	 * The $result will be:
	 *
	 * Array
	 *     (
	 *         [0] => Web Developer
	 *         [1] => Maketing Director
	 * )
	 *
	 * $result = Arr::column($recordset, 'job.title', 'name');
	 *
	 * The $result will be:
	 *
	 * Array
	 *     (
	 *         [Nat] => Web Developer
	 *         [Angela] => Maketing Director
	 * )
	 *
	 * @param  array       $data       A multi-dimensional array contains array (dataset) or object (recordset)
	 *                                 from which to pull a column of values.
	 * @param  string      $columnKey  The column of values to return.
	 * @param  string|null $indexKey   The column to use as the index/keys for the returned array.
	 * @return array                   Returns an array of values representing a single column from the input array.
	 */
	public static function column(array $data, string $columnKey, string $indexKey = null) : array
	{
		if (!Arr::isDataset($data) and !Arr::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		$columnKey = Arr::formatKeySyntax($columnKey);

		if ($indexKey)
			$indexKey = Arr::formatKeySyntax($indexKey);

		$result = [];

		foreach ($data as $row)
		{
			$row = Arr::toArray($row);

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
	 * Return the first element in the given array.
	 *
	 * @param  array    $array  An array.
	 * @param  int|null $n
	 * @return mixed            Returns the first value of array if the array is not empty; NULL otherwise.
	 */
	public static function first(array $array, int $n = null)
	{
		if (is_null($n))
		{
			$array = array_values($array);

			// array_shift() returns NULL if the array is empty.
			$value = array_shift($array);
		}
		else
			$value = array_slice($array, 0, $n);

		return $value;
	}

	/**
	 * Return the last element in the given array.
	 *
	 * @param  array    $array  An array.
	 * @param  int|null $n
	 * @return mixed            Returns the last value of array if the array is not empty; NULL otherwise.
	 */
	public static function last(array $array, int $n = null)
	{
		if (is_null($n))
		{
			$value = end($array);

			// end() returns FALSE if the array is empty.
			// So, convert it to NULL.
			if ($value === false)
				$value = null;
		}
		else
			$value = array_slice($array, count($array) - $n);

		return $value;
	}

	/**
	 * Gets the first key of an array.
	 *
	 * @param  array $array  An array.
	 * @return mixed         Returns the first key of array if the array is not empty; NULL otherwise.
	 */
	public static function firstKey(array $array)
	{
		// PHP 7.3+
		if (function_exists('array_key_first'))
		{
			// @codeCoverageIgnoreStart
			return array_key_first($array);
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

			return $key;
		}
	}

	/**
	 * Gets the last key of an array.
	 *
	 * @param  array $array  An array.
	 * @return mixed         Returns the last key of array if the array is not empty; NULL otherwise.
	 */
	public static function lastKey(array $array)
	{
		// PHP 7.3+
		if (function_exists('array_key_first'))
		{
			/** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
			// @codeCoverageIgnoreStart
			return array_key_last($array);
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

			return $key;
		}
	}

	/**
	 * Returns only the specified key/value pairs from the given array.
	 * Data can be one or multi-dimensional array, but not a recordset.
	 *
	 * @param  array      $array  Data can be one or multi-dimensional array, but not a recordset.
	 * @param  string|int $keys   The column of values to return. The $keys can be 0, '0', '0,1', 'name,work.position'.
	 * @return array
	 */
	public static function only(array $array, $keys) : array
	{
		if (!is_string($keys) and !is_int($keys))
			throw InvalidArgumentException::typeError(2, ['int','string'], $keys);

		$keys = (string)$keys;
		$keys = explode(',', $keys);
		$result = [];

		if ($array)
		{
			if (Arr::isMultidimensional($array))
			{
				foreach ($keys as $key)
				{
					$key = Arr::formatKeySyntax($key);
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
	 * Returns and removes an element by key from an array.
	 * Data can be one or multi-dimensional array, but not a recordset.
	 *
	 * @param  array      $array  Data can be one or multi-dimensional array, but not a recordset.
	 * @param  string|int $keys   The column of values to return. The $keys can be 0, '0', '0,1', 'name,work.position'.
	 * @return mixed
	 */
	public static function pull(array &$array, $keys)
	{
		if (!is_string($keys) and !is_int($keys))
			throw InvalidArgumentException::typeError(2, ['int','string'], $keys);

		$keys = (string)$keys;
		$keys = explode(',', $keys);

		if (count($keys) > 1)
			$result = [];
		else
			$result = '';

		if (Arr::isMultidimensional($array))
		{
			foreach ($keys as $key)
			{
				$key = Arr::formatKeySyntax($key);

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
	 * // the result is:
	 * // [
	 * //     '101' => 'Nat',
	 * //     '102' => 'Ann',
	 * //     '103' => 'May',
	 * // ]
	 *
	 * $result = Arr::map($array, 'id', 'name', 'class');
	 * // the result is:
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
		if (!Arr::isDataset($data) and !Arr::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		if (Arr::isRecordset($data))
			$data = Arr::toArray($data);

		$result = [];

		foreach ($data as $item)
		{
			$key = Arr::get($item, $from);
			$value = Arr::get($item, $to);

			if ($group)
			{
				$groupKey = Arr::get($item, $group);
				$result[$groupKey][$key] = $value;
			}
			else
				$result[$key] = $value;
		}

		return $result;
	}

	/**
	 * Sets a value to the element at the specified position in the given array.
	 *
	 * @param  array  $array  Array to set a value in.
	 * @param  string $key    Name of the key to set.
	 * @param  mixed  $value  Value to set.
	 * @return array
	 */
	public static function set(array $array, string $key, $value) : array
	{
		$keys = explode('.', $key);
		$arrayPointer = '$array';

		foreach ($keys as $key)
		{
			$arrayPointer .= Arr::formatKeySyntax($key);
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
	 * Wraps the given value in an array format.
	 * ie 'name' to ['name'], 'work.position' to ['work']['position']
	 *
	 * @param  string $key
	 * @return string
	 */
	public static function formatKeySyntax(string $key) : string
	{
		// $key can be '0'.
		if ($key === '')
			return '';

		// Use preg_replace to prevent string injection
		$key = preg_replace('/[^.a-zA-Z0-9_]+/', '', $key);
		$keys = str_replace('.', '\'][\'', $key);
		$keys = "['$keys']";

		return $keys;
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
		// These data types are not compat with mb_strtolower().
		if (in_array(mb_strtolower(gettype($search)), ['array', 'object', 'resource', 'null']))
			$caseSensitive = true;

		if ($caseSensitive)
			return in_array($search, $array, true);
		else
		{
			// Remove data types that not compat with mb_strtolower().
			$array = Arr::removeType($array, 'array,object,resource');

			return in_array(mb_strtolower($search), array_map('mb_strtolower', $array), true);
		}
	}

	public static function hasAny(array $array, array $searches, bool $caseSensitive = true) : bool
	{
		foreach ($searches as $search)
		{
			if (Arr::has($array, $search, $caseSensitive))
				return true;
		}

		return false;
	}

	public static function hasAll(array $array, array $searches, bool $caseSensitive = true) : bool
	{
		foreach ($searches as $search)
		{
			if (!Arr::has($array, $search, $caseSensitive))
				return false;
		}

		return true;
	}

	/**
	 * Checks if the given key or index exists in the array
	 *
	 * For example,
	 *
	 * ```php
	 * $employee = [
	 *     'name' => 'Nat',
	 *     'age' => '38',
	 *     'position' => [
	 *         'title' => 'Web Developer',
	 *         'saraly' => 100000
	 *     ]
	 * ];
	 *
	 * $result = Arr::hasKey($employee, 'position');
	 * // the result is: True
	 *
	 * $result = Arr::hasKey($employee, 'position.title');
	 * // the result is: True

	 * $result = Arr::hasKey($employee, 'position.startdate');
	 * // the result is: False
	 * ```
	 *
	 * @param  array      $array  An array with keys to check.
	 * @param  int|string $key    Value to check.
	 * @return bool
	 */
	public static function hasKey(array $array, $key) : bool
	{
		if (!is_int($key) and !is_string($key))
			throw InvalidArgumentException::typeError(1, ['int','string'], $key);

		// Is in base array?
		if (array_key_exists($key, $array))
			return true;

		if (strpos($key, '.') and Arr::isMultidimensional($array))
		{
			$pos = strrpos($key, '.');

			$keyOfArrayToSearch = substr($key, 0, $pos);
			$keyOfArrayToSearch = Arr::formatKeySyntax($keyOfArrayToSearch);

			$value = '';

			$syntax = '$value = $array' . $keyOfArrayToSearch . ';';
			eval($syntax);

			if (is_array($value))
			{
				$key = substr($key, $pos + 1);

				return array_key_exists($key, $value);
			}
		}

		return false;
	}

	public static function hasAnyKey(array $array, array $keys) : bool
	{
		foreach ($keys as $key)
		{
			if (Arr::hasKey($array, $key))
				return true;
		}

		return false;
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
			if (Arr::isAssociative($array))
				asort($array);
			else
				sort($array);
		}
		else
		{
			if (Arr::isAssociative($array))
				arsort($array);
			else
				rsort($array);
		}

		foreach ($array as $key => $value)
		{
			if ($recursive and is_array($value))
				$array[$key] = Arr::sort($value, $direction, $recursive);
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
				$array[$key] = Arr::sortKey($value, $direction, $recursive);
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
		if (!Arr::isDataset($dataset))
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
		if (!Arr::isRecordset($recordset))
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
					$string .= Arr::implode($piece, $glue).$glue;
			}
			else
				$string .= $piece . $glue;
		}

		$string = rtrim($string, $glue);

		return $string;
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
	 * The Arr::dot method flattens a multi-dimensional array into a single level array that uses "dot" notation to indicate depth:
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
				$result = array_merge($result, Arr::dot($value, $prepend . $key . '.'));
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
		if (!is_array($data) or
			!isset($data[0]) or
			!is_object($data[0]))
		{
			return false;
		}

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

		if (Arr::isAssociative($data))
			$data = Arr::toSequential($data);

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
	 * @param  object       $data       The source object.
	 * @param  bool         $recursive  True to recurve through multi-level objects.
	 * @param  string|null  $keys       An optional field names. Only be used in top level elements.
	 * @return array                    The array mapped from the given object.
	 */
	public static function fromObject(object $data, bool $recursive = true, string $keys = null) : array
	{
		$keys = (string)$keys;

		if ($keys !== '') // can be '0'
			$givenKeys = explode(',', $keys);
		else
			$givenKeys = [];

		$result = [];

		foreach ($data as $key => $value)
		{
			// As $givenKeys are always string and Arr::has() will also
			// check the types of the search value in the given array.
			// So, to support an indexed array also (numeric key), not
			// only an associative, use (string) to ensure $key is string.
			if (!$givenKeys or Arr::has($givenKeys, (string)$key))
			{
				if ($recursive)
				{
					// Data under this level maybe is an object.
					// ie. object > array (this level) > object > ...
					// So convert array to object to ensure it will
					// go to next level recursively.
					if (is_array($value))
						$value = (object)$value;

					// Go to next level (recursive).
					if (is_object($value))
						$result[$key] = Arr::fromObject($value, $recursive);
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
	 * @param  mixed        $data       The source data.
	 * @param  bool         $recursive  True to recurve through multi-level arrays or objects.
	 * @param  string|null  $keys       An optional field names. Only be used in top level elements.
	 * @return array                    The array mapped from the given object.
	 */
	public static function toArray($data, bool $recursive = true, string $keys = null) : array
	{
		if (is_array($data) or is_object($data))
		{
			$keys = (string)$keys;

			if ($keys !== '') // can be '0'
				$givenKeys = explode(',', $keys);
			else
				$givenKeys = [];

			$result = [];

			foreach ($data as $key => $value)
			{
				// As $givenKeys are always string and Arr::has() will also
				// check the types of the search value in the given array.
				// So, to support an indexed array also (numeric key), not
				// only an associative, use (string) to ensure $key is string.
				if (!$givenKeys or Arr::has($givenKeys, (string)$key))
				{
					if (is_array($value) or is_object($value))
					{
						if ($recursive)
							$result[$key] = Arr::toArray($value, $recursive);
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
	 * @param  array       $array      The array to map.
	 * @param  string      $class      Name of the class to create.
	 * @param  bool        $recursive  True to recurve through multi-level arrays.
	 * @param  string|null $keys       An optional field names. Only be used in top level elements.
	 * @return object                  The object mapped from the given array.
	 */
	public static function toObject(array $array, string $class = 'stdClass', bool $recursive = true, string $keys = null) : object
	{
		$keys = (string)$keys;

		if ($keys !== '') // can be '0'
			$givenKeys = explode(',', $keys);
		else
			$givenKeys = [];

		$obj = new $class;

		foreach ($array as $key => $value)
		{
			// As $givenKeys are always string and Arr::has() will also
			// check the types of the search value in the given array.
			// So, to support an indexed array also (numeric key), not
			// only an associative, use (string) to ensure $key is string.
			if (!$givenKeys or Arr::has($givenKeys, (string)$key))
			{
				if ($recursive)
				{
					// Data under this level maybe is an array.
					// ie. array > object (this level) > array > ...
					// So convert object to array to ensure it will
					// go to next level recursively.
					if (is_object($value))
						$value = (array)$value;

					// Go to next level (recursive).
					if (is_array($value))
						$obj->{$key} = Arr::toObject($value, $class, $recursive);
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
	 * @param  array           $array           The array to map.
	 * @param  string          $innerGlue       The glue (optional, defaults to '=') between the key and the value.
	 * @param  string          $outerGlue       The glue (optional, defaults to ' ') between array elements.
	 * @param  string          $valueDelimiter  Value delimiter.
	 * @param  bool            $recursive       True to recurve through multi-level arrays.
	 * @param  string|int|null $keys            An optional field names. Only be used in top level elements.
	 * @return string                           The string mapped from the given array.
	 */
	public static function toString(array $array, string $innerGlue = '=', string $outerGlue = ' ', string $valueDelimiter = '"', bool $recursive = true, $keys = null) : string
	{
		if (!is_null($keys) and !is_string($keys) and !is_int($keys))
			throw InvalidArgumentException::typeError(6, ['int','string','null'], $keys);

		$output = [];

		if (is_array($array))
		{
			$keys = (string)$keys;

			if ($keys !== '') // can be '0'
				$givenKeys = explode(',', $keys);
			else
				$givenKeys = [];

			foreach ($array as $key => $value)
			{
				// As $givenKeys are always string and Arr::has() will also
				// check the types of the search value in the given array.
				// So, to support an indexed array also (numeric key), not
				// only an associative, use (string) to ensure $key is string.
				if (!$givenKeys or Arr::has($givenKeys, (string)$key))
				{
					if (is_array($value))
					{
						if ($recursive)
							$output[] = Arr::toString($value, $innerGlue, $outerGlue, $valueDelimiter, $recursive);
					}
					else
						$output[] = $key . $innerGlue . $valueDelimiter . $value . $valueDelimiter;
				}
			}
		}

		return Arr::implode($output, $outerGlue);
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
		$data = Arr::toArray($data);

		if (!Arr::isMultidimensional($data))
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
		$data = Arr::toArray($data);

		if (!Arr::isMultidimensional($data))
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
		if (Arr::isMultidimensional($data))
			return $data;

		$data = [Arr::toArray($data)];

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
					$data[$i] = Arr::toSequential($value);
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
	 */
	public static function toJSON(array $array) : string
	{
		return JSON::encode($array);
	}

	/**
	 * Get all of the given array except for a specified value.
	 *
	 * @param  array        $array          An array to remove an element by value.
	 * @param  string|array $value          The value to remove.
	 * @param  bool         $caseSensitive  Case-sensitive or not.
	 * @param  bool         $recursive      True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function remove(array $array, $value, bool $caseSensitive = true, bool $recursive = true) : array
	{
		if (is_object($value) and !is_resource($value))
			throw InvalidArgumentException::typeError(2, ['string','int','float','bool','array','null'], $value);

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
						$array[$itemKey] = Arr::remove($itemValue, $value, $caseSensitive, $recursive);
				}
				else
				{
					if ($caseSensitive)
					{
						if ($itemValue === $value)
							unset($array[$itemKey]);
					}
					else
					{
						if (mb_strtolower($itemValue) === mb_strtolower($value))
							unset($array[$itemKey]);
					}
				}
			}
		}

		return $array;
	}

	/**
	 * Get all of the given array except for a specified key.
	 *
	 * @param  array  $array      An array to remove an element by key.
	 * @param  string $keys       The key name to remove.
	 * @param  bool   $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function removeKey(array $array, string $keys, bool $recursive = true) : array
	{
		$givenKeys = explode(',', $keys);

		foreach ($givenKeys as $key)
		{
			foreach ($array as $itemKey => $itemValue)
			{
				// Foreach function may fetch $itemKey to integer (0 is not equal '0')
				// ie. Arr::removeKey(['a'], ['0']); The first index 'a' would be 0
				// and this method will remove array index 'a'. But, in fact, it should not!
				// So use (string) function to convert and compare it as string.
				if ((string)$itemKey === (string)$key)
					unset($array[$itemKey]);
				elseif ($recursive and is_array($itemValue))
					$array[$itemKey] = Arr::removeKey($itemValue, $keys, $recursive);
			}
		}

		return $array;
	}

	/**
	 * @param  array  $array      An array to remove an element by data type.
	 * @param  string $dataTypes  The data type to remove.
	 * @param  bool   $recursive  True to recurve through multi-level arrays.
	 * @return array
	 */
	public static function removeType(array $array, string $dataTypes, bool $recursive = true) : array
	{
		$arrDataTypes = explode(',', $dataTypes);

		foreach ($arrDataTypes as $dataType)
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
					$array[$itemKey] = Arr::removeType($itemValue, $dataTypes, $recursive);
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
					$array[$key] = Arr::removeBlank($value, $recursive);
			}
			elseif (!strlen(trim((string)$value)))
				unset($array[$key]);
		}

		return $array;
	}

	/**
	 * Returns and removes a column by key from a dataset or recordset .
	 *
	 * @param  array  $data  A multi-dimensional array contains array (dataset) or object (recordset)
	 *                       from which to pull a column of values.
	 * @param  string $keys  The column of values to return. The $keys can be 'name,work.position'
	 * @return array
	 */
	public static function pullColumns(array &$data, string $keys) : array
	{
		if (!Arr::isDataset($data) and !Arr::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		$keys = explode(',', $keys);
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
	 * Remove a column from an array of arrays (dataset) or objects (recordset).
	 *
	 * @param  array  $array  An array to remove an element by key (dataset or recordset).
	 * @param  string $keys   The key name to remove.
	 * @return array
	 */
	public static function removeColumn(array $array, string $keys) : array
	{
		if (!Arr::isDataset($array) and !Arr::isRecordset($array))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $array);

		$keys = explode(',', $keys);

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
		// return true. See more at Arr::isAssociative()
		$isAssociative = Arr::isAssociative($array);

		$array = array_unique($array, SORT_REGULAR);

		if ($recursive)
		{
			foreach ($array as $key => $value)
			{
				if (is_array($value))
					$array[$key] = Arr::unique($value, $recursive, $recursive);
			}
		}

		if ($reindex and !$isAssociative)
			$array = array_values($array);

		return $array;
	}
}
