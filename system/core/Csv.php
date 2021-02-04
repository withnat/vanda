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
 * Class Csv
 *
 * Class to handle parsing of CSV files, where the column headers are in the
 * first row.
 *
 * @package System
 */
final class Csv
{

	/**
	 * Csv constructor.
	 */
	private function __construct(){}

	/**
	 * Generates CSV string from the given dataset (array of arrays).
	 *
	 * @param  array  $dataset    The input dataset (array of arrays).
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $newline    Optionally, newline character. Default to \n
	 * @param  string $enclosure  Optionally, enclosure. Default to " (double quote).
	 * @return string             Returns the well-formed CSV.
	 */
	public static function fromDataset(array  $dataset, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : string
	{
		if (!Arr::isDataset($dataset))
			throw InvalidArgumentException::typeError(1, ['dataset'], $dataset);

		$csv = Csv::_fromDatasetOrRecordset($dataset, $delimiter, $newline, $enclosure);

		return $csv;
	}

	/**
	 * Generates CSV string from the given recordset (array of objects).
	 *
	 * @param  array  $recordset  The input recordset (array of objects).
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $newline    Optionally, newline character. Default to \n
	 * @param  string $enclosure  Optionally, enclosure. Default to " (double quote).
	 * @return string             Returns the well-formed CSV.
	 */
	public static function fromRecordset(array  $recordset, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : string
	{
		if (!Arr::isRecordset($recordset))
			throw InvalidArgumentException::typeError(1, ['recordset'], $recordset);

		$csv = Csv::_fromDatasetOrRecordset($recordset, $delimiter, $newline, $enclosure);

		return $csv;
	}

	/**
	 * Converts the given CSV string to an array.
	 *
	 * @param  string $csv        The well-formed CSV string.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $newline    Optionally, new line character. Defaults to \n
	 * @param  string $enclosure  Optionally, enclosure. Defaults to " (double quote).
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
				$columns = Csv::_parseLine($line, $delimiter, $enclosure);
				$data[] = $columns;
			}
		}

		return $data;
	}

	/**
	 * Converts the given CSV string to dataset (array of arrays).
	 *
	 * @param  string $csv        The well-formed CSV string.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $newline    Optionally, new line character. Defaults to \n
	 * @param  string $enclosure  Optionally, enclosure. Defaults to " (double quote).
	 * @return array              Returns dataset (array of arrays).
	 */
	public static function toDataset(string $csv, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : array
	{
		$lines = explode($newline, $csv);
		$header = null;
		$data = [];

		foreach ($lines as $line)
		{
			if ($line)
			{
				$columns = Csv::_parseLine($line, $delimiter, $enclosure);

				if (is_null($header))
					$header = $columns;
				else
					$data[] = array_combine($header, $columns);
			}
		}

		return $data;
	}

	/**
	 * Converts the given CSV string to recordset (array of objects).
	 *
	 * @param  string $csv        The well-formed CSV string.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $newline    Optionally, new line character. Defaults to \n
	 * @param  string $enclosure  Optionally, enclosure. Defaults to " (double quote).
	 * @return object             Returns recordset (array of objects).
	 */
	public static function toRecordset(string $csv, string $delimiter = ',', string $newline = "\n", string $enclosure = '"') : object
	{
		$array = Csv::toDataset($csv, $delimiter, $newline, $enclosure);
		$object = Arr::toObject($array);

		return $object;
	}

	/**
	 * Escapes enclosure character from the given string.
	 *
	 * @param  mixed  $string     The input string.
	 * @param  string $enclosure  The enclosure character.
	 * @return string             Returns the string without enclosure character.
	 */
	public static function safe($string, string $enclosure) : string
	{
		if (is_string($string))
			$string = str_replace($enclosure, $enclosure . $enclosure, $string);

		return (string)$string;
	}

	/**
	 * Parses the given CSV string and returns in array.
	 *
	 * @param  string $line       The CSV string.
	 * @param  string $delimiter  The delimiter character.
	 * @param  string $enclosure  The enclosure character.
	 * @return array              Returns an array.
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

	/**
	 * Generates CSV from the given dataset (array of arrays) or recordset (array of objects).
	 *
	 * @param array  $datasetOrRecordset  The input dataset (array of arrays) or recordset (array of objects).
	 * @param string $delimiter           Delimiter.
	 * @param string $newline             Newline character.
	 * @param string $enclosure           Enclosure.
	 * @return string                     Returns the well-formed CSV.
	 */
	private static function _fromDatasetOrRecordset(array  $datasetOrRecordset, string $delimiter, string $newline, string $enclosure) : string
	{
		$header = '';
		$csv = '';

		foreach ($datasetOrRecordset as $i => $row)
		{
			foreach ($row as $key => $value)
			{
				if ($i === 0)
					$header .= $enclosure . Csv::safe($key, $enclosure) . $enclosure . $delimiter;

				$csv .= $enclosure . Csv::safe($value, $enclosure) . $enclosure . $delimiter;
			}

			$csv = substr($csv, 0, (0 - mb_strlen($delimiter))) . $newline;
		}

		$header = substr($header, 0, (0 - mb_strlen($delimiter))) . $newline;

		return $header . $csv;
	}
}
