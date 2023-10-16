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

use System\Exception\InvalidArgumentException;

/**
 * Class Xml
 *
 * The Xml class allows you to transform arrays or objects into SimpleXMLElement
 * or DOMDocument objects, and back into arrays or objects again.
 *
 * @package System
 */
class Xml
{
	/**
	 * Xml constructor.
	 */
	private function __construct(){}

	/**
	 * Converts the given dataset (array of arrays) to XML document.
	 *
	 * @param  array  $dataset  The input dataset (array of arrays).
	 * @param  string $root     Optionally, the name of the root element. Defaults to 'root'.
	 * @param  string $element  Optionally, the name of the elements that represent the array elements. Defaults to
	 *                          'element'.
	 * @param  string $newline  Optionally, newline character. Default to \n
	 * @param  string $tab      Optionally, tab character. Default to \t
	 * @return string           Returns the well-formed XML document.
	 */
	public static function fromDataset(array  $dataset, string $root = 'root', string $element = 'element', string $newline = "\n", string $tab = "\t") : string
	{
		if (!Arr::isDataset($dataset))
			throw InvalidArgumentException::typeError(1, ['dataset'], $dataset);

		$xml = static::_fromDatasetOrRecordset($dataset, $root, $element, $newline, $tab);

		return $xml;
	}

	/**
	 * Converts the given recordset (array of objects) to XML document.
	 *
	 * @param  array  $recordset  The input recordset (array of objects).
	 * @param  string $root       Optionally, the name of the root element. Defaults to 'root'.
	 * @param  string $element    Optionally, the name of the elements that represent the array elements. Defaults to
	 *                            'element'.
	 * @param  string $newline    Optionally, newline character. Default to \n
	 * @param  string $tab        Optionally, tab character. Default to \t
	 * @return string             Returns the well-formed XML document.
	 */
	public static function fromRecordset(array  $recordset, string $root = 'root', string $element = 'element', string $newline = "\n", string $tab = "\t") : string
	{
		if (!Arr::isRecordset($recordset))
			throw InvalidArgumentException::typeError(1, ['recordset'], $recordset);

		$xml = static::_fromDatasetOrRecordset($recordset, $root, $element, $newline, $tab);

		return $xml;
	}

	/**
	 * Converts the given XML string to an array.
	 *
	 * @param  string $xml  The well-formed XML string.
	 * @return array|false  Returns an array of class SimpleXMLElement with elements containing the data held within the
	 *                      XML document, or false on failure.
	 */
	public static function toArray(string $xml)
	{
		$object = static::toObject($xml);

		if ($object === false)
			return false;

		$array = Arr::fromObject($object);

		return $array;
	}

	/**
	 * Converts the given XML document to an object.
	 *
	 * @param  string       $xml  The well-formed XML string.
	 * @return object|false       Returns an object of class SimpleXMLElement with properties containing the data held
	 *                            within the XML document, or false on failure.
	 */
	public static function toObject(string $xml)
	{
		$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($xml);

		// prevent json_encode converts empty value to array.
		$json = str_replace('{}', '""', $json);

		$object = json_decode($json);

		return $object;
	}

	/**
	 * Converts the given XML string to dataset (array of arrays).
	 *
	 * @param  string $xml  The well-formed XML string.
	 * @return array|false  Returns dataset (array of arrays), or false on failure.
	 */
	public static function toDataset(string $xml)
	{
		$dataset = static::toArray($xml);

		return $dataset;
	}

	/**
	 * Converts the given XML string to recordset (array of objects).
	 * This method is an alias of Xml::toObject().
	 *
	 * @param  string $xml  The well-formed XML string.
	 * @return array|false  Returns recordset (array of objects), or false on failure.
	 */
	public static function toRecordset(string $xml)
	{
		$object = static::toObject($xml);

		if ($object === false)
			return false;

		$recordset = (array)$object;

		return $recordset;
	}

	/**
	 * Converts reserved XML characters to entities.
	 *
	 * @param  mixed  $string      The input string.
	 * @param  bool   $protectAll  Optional. Defaults to false.
	 * @return string              Returns the encoded string.
	 */
	public static function safe($string, bool $protectAll = false) : string
	{
		if (is_string($string))
		{
			$temp = '__TEMP_AMPERSANDS__';

			// Replace entities to temporary markers so that
			// ampersands won't get messed up
			$string = preg_replace('/&#(\d+);/', $temp . '\\1;', $string);

			if ($protectAll)
				$string = preg_replace('/&(\w+);/', $temp . '\\1;', $string);

			$search = ['&', '<', '>', '"', "'", '-'];
			$replace = ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'];

			$string = str_replace($search, $replace, $string);

			// Decode the temp markers back to entities
			$string = preg_replace('/' . $temp . '(\d+);/', '&#\\1;', $string);

			if ($protectAll)
				$string = preg_replace('/' . $temp . '(\w+);/', '&\\1;', $string);
		}

		return (string)$string;
	}

	/**
	 * Converts the given dataset (array of arrays) or recordset (array of objects) to XML document.
	 *
	 * @param  array  $datasetOrRecordset  The input dataset (array of arrays) or recordset (array of objects).
	 * @param  string $root                The name of the root element.
	 * @param  string $element             The name of the elements that represent the array elements.
	 * @param  string $newline             Newline character.
	 * @param  string $tab                 Tab character.
	 * @return string                      Returns the well-formed XML document.
	 */
	private static function _fromDatasetOrRecordset(array  $datasetOrRecordset, string $root, string $element, string $newline, string $tab) : string
	{
		$xml = '<' . $root . '>' . $newline;

		foreach ($datasetOrRecordset as $row)
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
}
