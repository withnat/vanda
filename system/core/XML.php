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
 * Class XML
 * @package System
 */
final class XML
{

	/**
	 * XML constructor.
	 */
	private function __construct(){}

	/**
	 * @param  array  $recordset
	 * @param  string $root
	 * @param  string $element
	 * @param  string $newline
	 * @param  string $tab
	 * @return string
	 */
	public static function fromRecordset(array $recordset, string $root = 'root', string $element = 'element', string $newline = "\n", $tab = "\t") : string
	{
		if (!is_array($recordset))
			$recordset = [$recordset];

		$xml = '<' . $root . '>' . $newline;

		foreach ($recordset as $row)
		{
			$xml .= $tab . '<' . $element . '>' . $newline;

			foreach ($row as $key => $value)
			{
				if (is_string($value))
					$value = static::safe($value);

				$xml .= $tab . $tab . '<' . $key . '>' . $value . '</' . $key . '>' . $newline;
			}

			$xml .= $tab . '</' . $element . '>' . $newline;
		}

		$xml .= '</' . $root . '>' . $newline;

		return $xml;
	}

	/**
	 * @param  string $xml
	 * @return array
	 */
	public static function toArray(string $xml) : array
	{
		$object = static::toObject($xml);
		$array = Arr::fromObject($object);

		return $array;
	}

	/**
	 * @param  string $xml
	 * @return object
	 */
	public static function toObject(string $xml) : object
	{
		$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($xml);

		// preven json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		$object = json_decode($json);

		return $object;
	}

	/**
	 * @param  string $string
	 * @param  bool   $protectAll
	 * @return string
	 */
	public static function safe(string $string, bool $protectAll = false) : string
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

		return $string;
	}
}
