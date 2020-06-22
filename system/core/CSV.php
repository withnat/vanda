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

/**
 * Class CSV
 * @package System
 */
final class CSV
{

	/**
	 * CSV constructor.
	 */
	private function __construct(){}

	/**
	 * @param  array  $data
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return string
	 */
	public static function fromRecordset(array $data, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : string
	{
		$csv = '';

		foreach ($data as $row)
		{
			foreach ($row as $key => $value)
				$csv .= $enclosure . static::safe($value, $enclosure) . $enclosure . $delimiter;

			$csv = substr($csv, 0, (0 - mb_strlen($delimiter))) . $newline;
		}

		return $csv;
	}

	/**
	 * @param  string $csv
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return array
	 */
	public static function toArray(string $csv, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : array
	{
		$lines = explode($newline, $csv);
		$data = [];

		foreach ($lines as $line)
		{
			if ($line)
			{
				$columns = static::_parseLine($line, $delimiter, $enclosure);
				$data[] = $columns;
			}
		}

		return $data;
	}

	/**
	 * @param  string $csv
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return array
	 */
	public static function toAssociative(string $csv, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : array
	{
		$lines = explode($newline, $csv);
		$header = null;
		$data = [];

		foreach ($lines as $line)
		{
			if ($line)
			{
				$columns = static::_parseLine($line, $delimiter, $enclosure);

				if (is_null($header))
					$header = $columns;
				else
					$data[] = array_combine($header, $columns);
			}
		}

		return $data;
	}

	/**
	 * @param  string $csv
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return object
	 */
	public static function toObject(string $csv, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : object
	{
		$array = static::toAssociative($csv, $delimiter, $newline, $enclosure);
		$object = Arr::toObject($array);

		return $object;
	}

	/**
	 * @param  string $string
	 * @param  string $enclosure
	 * @return string
	 */
	public static function safe(string $string, string $enclosure) : string
	{
		$string = str_replace($enclosure, $enclosure . $enclosure, $string);

		return $string;
	}

	/**
	 * @param  string $line
	 * @param  string $delimiter
	 * @param  string $enclosure
	 * @return array
	 */
	private static function _parseLine(string $line, string $delimiter, string $enclosure) : array
	{
		$columns = explode($delimiter, $line);

		foreach ($columns as $key => $value)
		{
			$value = mb_substr($value, mb_strlen($enclosure));
			$value = mb_substr($value, 0, (0 - mb_strlen($enclosure)));
			$value = str_replace($enclosure . $enclosure, $enclosure, $value);

			$columns[$key] = $value;
		}

		return $columns;
	}
}
