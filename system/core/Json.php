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
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System;

use ErrorException;
use System\Exception\InvalidArgumentException;

/**
 * Class Json
 *
 * Wrapper for the default JSON methods that handles all errors uniformly.
 *
 * @package System
 */
class Json
{
	/**
	 * Json constructor.
	 */
	private function __construct(){}

	/**
	 * Determines if the given string is JSON.
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string is JSON, false otherwise.
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
	 * Encodes the given data to JSON.
	 *
	 * @param  mixed  $data   The input data to encode.
	 * @return string         Returns the JSON string.
	 * @throws ErrorException
	 */
	public static function encode($data) : string
	{
		if (is_resource($data))
			throw InvalidArgumentException::valueError(1, '$data cannot be a resource', $data);

		$result = json_encode($data);
		$error = static::_getError();

		if ($error)
		{
			// @codeCoverageIgnoreStart
			throw new ErrorException('Json Error: ' . $error);
			// @codeCoverageIgnoreEnd
		}

		return $result;
	}

	/**
	 * Decodes the given JSON string.
	 *
	 * @param  string         $json   The input JSON string being decoded.
	 * @param  bool           $assoc  Optionally, when true, returned objects will be converted into associative arrays.
	 *                                Defaults to false.
	 * @return array|object           Returns decoded data representation. Object if $assoc = false or null, array
	 *                                otherwise.
	 * @throws ErrorException
	 */
	public static function decode(string $json, bool $assoc = false)
	{
		$json = stripslashes($json);
		$result = json_decode($json, $assoc);
		$error = static::_getError();

		if ($error)
			throw new ErrorException('Json Error: ' . $error);

		return $result;
	}

	/**
	 * Converts the given dataset (array of arrays) or recordset (array of objects)
	 * to JSON string including with number of records.
	 *
	 * For example,
	 *
	 * ```json
	 * {
	 *   "recordsTotal": 2,
	 *   "recordsFiltered": 2,
	 *   "data": [
	 *     {"name":"Nat","surname":"Withe","work":"Web Developer","salary":10000},
	 *     {"name":"Angela","surname":"SG","work":"Marketing Director","salary":10000}
	 *   ]
	 * }
	 * ```
	 *
	 * @param  array  $data  The input dataset (array of arrays) or recordset (array of objects).
	 * @return string        Returns the JSON string.
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
	 * @return string  Returns the error message.
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
