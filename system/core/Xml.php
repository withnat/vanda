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
 * Class Xml
 *
 * The Xml class allows you to transform arrays or objects into SimpleXMLElement
 * or DOMDocument objects, and back into arrays or objects again.
 *
 * @package System
 */
final class Xml
{
	/**
	 * Xml constructor.
	 */
	private function __construct(){}

	/**
	 * @param  array  $dataset
	 * @param  string $root
	 * @param  string $element
	 * @param  string $newline
	 * @param  string $tab
	 * @return string
	 */
	public static function fromDataset(
		array  $dataset,
		string $root = 'root',
		string $element = 'element',
		string $newline = "\n",
		$tab = "\t"
	) : string
	{
		if (!Arr::isDataset($dataset))
			throw InvalidArgumentException::typeError(1, ['dataset'], $dataset);

		$xml = Xml::_fromDatasetOrRecordset($dataset, $root, $element, $newline, $tab);

		return $xml;
	}

	/**
	 * @param  array  $recordset
	 * @param  string $root
	 * @param  string $element
	 * @param  string $newline
	 * @param  string $tab
	 * @return string
	 */
	public static function fromRecordset(
		array  $recordset,
		string $root = 'root',
		string $element = 'element',
		string $newline = "\n",
		$tab = "\t"
	) : string
	{
		if (!Arr::isRecordset($recordset))
			throw InvalidArgumentException::typeError(1, ['recordset'], $recordset);

		$xml = Xml::_fromDatasetOrRecordset($recordset, $root, $element, $newline, $tab);

		return $xml;
	}

	/**
	 * @param  string $xml
	 * @return array
	 */
	public static function toArray(
		string $xml
	) : array
	{
		$object = Xml::toObject($xml);
		$array = Arr::fromObject($object);

		return $array;
	}

	/**
	 * @param  string       $xml  A well-formed XML string.
	 * @return object|false       Returns an object of class SimpleXMLElement
	 *                            with properties containing the data held within
	 *                            the xml document, or FALSE on failure.
	 */
	public static function toObject(
		string $xml
	)
	{
		$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($xml);

		// preven json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		$object = json_decode($json);

		return $object;
	}

	/**
	 * @param  mixed  $string
	 * @param  bool   $protectAll
	 * @return string
	 */
	public static function safe(
		$string,
		bool   $protectAll = false
	) : string
	{
		if (is_string($string))
		{
			$temp = '__TEMP_AMPERSANDS__';

			// Replace entities to temporary markers so that
			// ampersands won't get messed up
			$string = preg_replace('/&#(\d+);/', $temp . '\\1;', $string);

			if ($protectAll === true)
				$string = preg_replace('/&(\w+);/', $temp . '\\1;', $string);

			$search = ['&', '<', '>', '"', "'", '-'];
			$replace = ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'];

			$string = str_replace($search, $replace, $string);

			// Decode the temp markers back to entities
			$string = preg_replace('/' . $temp . '(\d+);/', '&#\\1;', $string);

			if ($protectAll === true)
				return preg_replace('/' . $temp . '(\w+);/', '&\\1;', $string);
		}

		return (string)$string;
	}

	/**
	 * @param  array  $datasetOrRecordset
	 * @param  string $root
	 * @param  string $element
	 * @param  string $newline
	 * @param  string $tab
	 * @return string
	 */
	private static function _fromDatasetOrRecordset(
		array  $datasetOrRecordset,
		string $root = 'root',
		string $element = 'element',
		string $newline = "\n",
		string $tab = "\t"
	) : string
	{
		$xml = '<' . $root . '>' . $newline;

		foreach ($datasetOrRecordset as $row)
		{
			$xml .= $tab . '<' . $element . '>' . $newline;

			foreach ($row as $key => $value)
			{
				if (is_string($value))
					$value = Xml::safe($value);

				$xml .= $tab . $tab . '<' . $key . '>' . $value . '</' . $key . '>' . $newline;
			}

			$xml .= $tab . '</' . $element . '>' . $newline;
		}

		$xml .= '</' . $root . '>' . $newline;

		return $xml;
	}
}
