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
use System\Exception\InvalidArgumentException;

/**
 * Class Json
 * Wrapper for the default JSON methods that handles all errors uniformly.
 * @package System
 */
final class Json
{
	/**
	 * Json constructor.
	 */
	private function __construct(){}

	/**
	 * Returns true if the string is JSON, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function isValid(string $string) : bool
	{
		if ($string)
		{
			$string = stripslashes($string);
			@json_decode($string);

			return json_last_error() === JSON_ERROR_NONE;
		}

		return false;
	}

	/**
	 * Encodes an object or assoc array to JSON.
	 *
	 * @param  mixed  $data  Data to encode.
	 * @return string
	 * @throws ErrorException
	 */
	public static function encode($data) : string
	{
		if (is_resource($data))
			throw InvalidArgumentException::valueError(1, '$data cannot be a resource', $data);

		// A resource cannot be encoded.
		if (is_array($data))
			$data = Arr::removeType($data, 'resource');

		$result = json_encode($data);
		$error = Json::_getError();

		if ($error)
		{
			// @codeCoverageIgnoreStart
			throw new ErrorException('Json Error: ' . $error);
			// @codeCoverageIgnoreEnd
		}

		return $result;
	}

	/**
	 * Decodes a JSON string to a stdClass or array.
	 *
	 * @param  string         $json   The json string being decoded.
	 * @param  bool           $assoc  When TRUE, returned objects will be converted into associative arrays.
	 * @return array|object           Decoded data representation. Object if $assoc = false or null, array otherwise.
	 * @throws ErrorException
	 */
	public static function decode(string $json, bool $assoc = false)
	{
		$json = stripslashes($json);
		$result = json_decode($json, $assoc);
		$error = Json::_getError();

		if ($error)
			throw new ErrorException('Json Error: ' . $error);

		return $result;
	}

	/**
	 * @param  array  $data  A multi-dimensional array contains array (dataset) or object (recordset).
	 * @return string
	 */
	public static function dataTable(array $data) : string
	{
		if (!Arr::isDataset($data) and !Arr::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['dataset', 'recordset'], $data);

		$recordsTotal = count($data);
		$recordsFiltered = count($data);
		$json = json_encode($data);

		// preven json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		// Below $dataTable is something like this:
		// {
		//   "recordsTotal": 2,
		//   "recordsFiltered": 2,
		//   "data": [
		//     {"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000},
		//     {"name":"Angela","surname":"SG","work":"Marketing Director","salary":10000}
		//   ]
		// }
		$dataTable = '{' . "\n";
		$dataTable .= "\t" . '"recordsTotal": ' . $recordsTotal . ',' . "\n";
		$dataTable .= "\t" . '"recordsFiltered": ' . $recordsFiltered . ',' . "\n";
		$dataTable .= "\t" . '"data": ' . $json . "\n";
		$dataTable .= '}';

		return $dataTable;
	}

	/**
	 * Returns the last error occurred.
	 *
	 * @return string
	 * @codeCoverageIgnore
	 */
	private static function _getError() : string
	{
		switch (json_last_error())
		{
			// error code 1
			case JSON_ERROR_DEPTH:
				$error = 'Maximum stack depth exceeded.';
				break;
			// error code 2
			case JSON_ERROR_STATE_MISMATCH:
				$error = 'Underflow or the modes mismatch.';
				break;
			// error code 3
			case JSON_ERROR_CTRL_CHAR:
				$error = 'Unexpected control character found.';
				break;
			// error code 4
			case JSON_ERROR_SYNTAX:
				$error = 'Syntax error, malformed JSON.';
				break;
			// error code 5
			case JSON_ERROR_UTF8:
				$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
				break;
			// error code 6
			case JSON_ERROR_RECURSION:
				$error = 'One or more recursive references in the value to be encoded.';
				break;
			// error code 7
			case JSON_ERROR_INF_OR_NAN:
				$error = 'One or more NAN or INF values in the value to be encoded.';
				break;
			// error code 8
			case JSON_ERROR_UNSUPPORTED_TYPE:
				$error = 'A value of a type that cannot be encoded was given.';
				break;
			// error code 0
			case JSON_ERROR_NONE:
			default:
				$error = '';
		}

		return $error;
	}
}
