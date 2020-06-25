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

use System\Exception\InvalidArgumentException;

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
	 * @param  array  $dataset
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return string
	 */
	public static function fromDataset(
		array  $dataset,
		string $delimiter = ',',
		string $newline = "\n",
		string $enclosure = '"'
	) : string
	{
		if (!Arr::isDataset($dataset))
			throw InvalidArgumentException::type(1, ['dataset'], $dataset);

		$csv = static::_fromDatasetOrRecordset($dataset, $delimiter, $newline, $enclosure);

		return $csv;
	}

	/**
	 * @param  array  $recordset
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return string
	 */
	public static function fromRecordset(
		array  $recordset,
		string $delimiter = ',',
		string $newline = "\n",
		string $enclosure = '"'
	) : string
	{
		if (!Arr::isRecordset($recordset))
			throw InvalidArgumentException::type(1, ['recordset'], $recordset);

		$csv = static::_fromDatasetOrRecordset($recordset, $delimiter, $newline, $enclosure);

		return $csv;
	}

	/**
	 * @param  string $csv
	 * @param  string $delimiter
	 * @param  string $newline
	 * @param  string $enclosure
	 * @return array
	 */
	public static function toArray(
		string $csv,
		string $delimiter = ',',
		string $newline = "\n",
		string $enclosure = '"'
	) : array
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
	public static function toDataset(
		string $csv,
		string $delimiter = ',',
		string $newline = "\n",
		string $enclosure = '"'
	) : array
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
	public static function toRecordset(
		string $csv,
		string $delimiter = ',',
		string $newline = "\n",
		string $enclosure = '"'
	) : object
	{
		$array = static::toDataset($csv, $delimiter, $newline, $enclosure);
		$object = Arr::toObject($array);

		return $object;
	}

	/**
	 * @param  mixed  $string
	 * @param  string $enclosure
	 * @return string
	 */
	public static function safe(
		$string,
		string $enclosure
	) : string
	{
		if (is_string($string))
			$string = str_replace($enclosure, $enclosure . $enclosure, $string);

		return (string)$string;
	}

	/**
	 * @param  string $line
	 * @param  string $delimiter
	 * @param  string $enclosure
	 * @return array
	 */
	private static function _parseLine(
		string $line,
		string $delimiter,
		string $enclosure
	) : array
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

	private static function _fromDatasetOrRecordset(
		array  $datasetOrRecordset,
		string $delimiter = ',',
		string $newline = "\n",
		string $enclosure = '"'
	) : string
	{
		$header = '';
		$csv = '';

		foreach ($datasetOrRecordset as $i => $row)
		{
			foreach ($row as $key => $value)
			{
				if ($i == 0)
					$header .= $enclosure . static::safe($key, $enclosure) . $enclosure . $delimiter;

				$csv .= $enclosure . static::safe($value, $enclosure) . $enclosure . $delimiter;
			}

			$csv = substr($csv, 0, (0 - mb_strlen($delimiter))) . $newline;
		}

		$header = substr($header, 0, (0 - mb_strlen($delimiter))) . $newline;

		return $header . $csv;
	}
}
