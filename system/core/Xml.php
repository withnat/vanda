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
