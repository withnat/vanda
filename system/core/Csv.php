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
 * A lightweight & flexible PHP web framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System;

use SplFileObject;
use SplTempFileObject;
use System\Exception\InvalidArgumentException;

/**
 * Class Csv
 *
 * This class provides a set of methods for working with CSV data, including
 * reading and writing CSV files, converting between arrays and well-formed
 * CSV strings, and sanitizing CSV strings to ensure proper structure. It also
 * supports working with dataset (array of arrays) and recordset (array of
 * objects) structures.
 *
 * @package System
 */
class Csv
{
	/**
	 * Csv constructor.
	 */
	private function __construct(){}

	/**
	 * Reads the given CSV file and returns an array.
	 *
	 * @param string $file          The CSV file.
	 * @param string $delimiter     Optionally, delimiter. Defaults to , (comma).
	 * @param string $enclosure     Optionally, enclosure. Default to " (double quote).
	 * @return array                Returns an array.
	 */
	public static function read(string $file, string $delimiter = ',', string $enclosure = '"') : array
	{
		$file = new SplFileObject($file);
		$file->setFlags(SplFileObject::READ_CSV);
		$file->setCsvControl($delimiter, $enclosure);

		$data = [];

		foreach ($file as $line)
		{
			// Skip empty line.
			if (count($line) == 1 and empty($line[0]))
				continue;

			$data[] = $line;
		}

		return $data;
	}

	/**
	 * Writes data to a CSV file using SplFileObject.
	 *
	 * This method creates or updates a CSV file with the specified data, allowing
	 * customization of the delimiter and enclosure characters for the CSV format.
	 *
	 * @param  string $file       The CSV file to write to.
	 * @param  array  $data       The data to write to the CSV file.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $newline    Optionally, newline character. Default to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @param  string $enclosure  Optionally, enclosure. Default to " (double quote).
	 * @return void
	 */
	public static function write(string $file, array $data, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : void
	{
		if (Arr::isDataset($data) or Arr::isRecordset($data))
			$csvString = static::_fromDatasetOrRecordset($data, $delimiter, $enclosure, $newline);
		else
			$csvString = static::fromArray($data, $delimiter, $enclosure, $newline);

		file_put_contents($file, $csvString);
	}

	/**
	 * Generates CSV string from the given array.
	 *
	 * @param  array  $array      The input array.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $enclosure  Optionally, enclosure. Default to " (double quote).
	 * @param  string $newline    Optionally, newline character. Default to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @return string             Returns the well-formed CSV.
	 */
	public static function fromArray(array $array, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : string
	{
		$array = Arr::toMultidimensional($array);
		$csvString = '';

		foreach ($array as $row)
		{
			foreach ($row as $column)
				$csvString .= $enclosure . static::safe($column, $enclosure) . $enclosure . $delimiter;

			$csvString = substr($csvString, 0, (0 - mb_strlen($delimiter))) . $newline;
		}

		$csvString = rtrim($csvString, $newline);

		return $csvString;
	}

	/**
	 * Generates CSV string from the given dataset (array of arrays).
	 *
	 * @param  array  $dataset    The input dataset (array of arrays).
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $enclosure  Optionally, enclosure. Default to " (double quote).
	 * @param  string $newline    Optionally, newline character. Default to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @return string             Returns the well-formed CSV.
	 */
	public static function fromDataset(array $dataset, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : string
	{
		if (!Arr::isDataset($dataset))
			throw InvalidArgumentException::typeError(1, ['dataset'], $dataset);

		$csvString = static::_fromDatasetOrRecordset($dataset, $delimiter, $enclosure, $newline);
		$csvString = rtrim($csvString, $newline);

		return $csvString;
	}

	/**
	 * Generates CSV string from the given recordset (array of objects).
	 *
	 * @param  array  $recordset  The input recordset (array of objects).
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $enclosure  Optionally, enclosure. Default to " (double quote).
	 * @param  string $newline    Optionally, newline character. Default to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @return string             Returns the well-formed CSV.
	 */
	public static function fromRecordset(array $recordset, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : string
	{
		if (!Arr::isRecordset($recordset))
			throw InvalidArgumentException::typeError(1, ['recordset'], $recordset);

		$csvString = static::_fromDatasetOrRecordset($recordset, $delimiter, $enclosure, $newline);
		$csvString = rtrim($csvString, $newline);

		return $csvString;
	}

	/**
	 * Converts the given CSV string to an array.
	 *
	 * @param  string $csvString  The well-formed CSV string.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $enclosure  Optionally, enclosure. Defaults to " (double quote).
	 * @param  string $newline    Optionally, new line character. Defaults to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @return array              Returns an array.
	 */
	public static function toArray(string $csvString, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : array
	{
		$csvString = static::sanitize($csvString, $delimiter, $enclosure);
		$lines = explode($newline, $csvString);
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
	 * Converts the given CSV string to dataset (array of arrays).
	 *
	 * @param  string $csvString  The well-formed CSV string.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $enclosure  Optionally, enclosure. Defaults to " (double quote).
	 * @param  string $newline    Optionally, new line character. Defaults to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @return array              Returns dataset (array of arrays).
	 */
	public static function toDataset(string $csvString, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : array
	{
		$csvString = static::sanitize($csvString, $delimiter, $enclosure);
		$lines = explode($newline, $csvString);
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
	 * Converts the given CSV string to recordset (array of objects).
	 *
	 * @param  string $csvString  The well-formed CSV string.
	 * @param  string $delimiter  Optionally, delimiter. Defaults to , (comma).
	 * @param  string $enclosure  Optionally, enclosure. Defaults to " (double quote).
	 * @param  string $newline    Optionally, new line character. Defaults to PHP_EOL, new line character based on the
	 *                            current OS.
	 * @return array              Returns recordset (array of objects).
	 */
	public static function toRecordset(string $csvString, string $delimiter = ',', string $enclosure = '"', string $newline = PHP_EOL) : array
	{
		$array = static::toDataset($csvString, $delimiter, $enclosure, $newline);
		$object = Arr::toRecordset($array);

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
	 * Sanitizes a CSV string to ensure it is well-formed and properly structured.
	 *
	 * This method takes an input CSV string and performs operations to clean, validate,
	 * and structure the content, ensuring that it adheres to the standard CSV format.
	 * It can help in handling CSV strings that may be messy or inconsistent, making
	 * them suitable for further processing or import/export tasks.
	 *
	 * @param  string $csvString  The input CSV string to sanitize.
	 * @return string             Returns the well-formed CSV string.
	 */
	protected static function sanitize(string $csvString, string $delimiter = ',', string $enclosure = '"') : string
	{
		// Create an SplTempFileObject from the CSV string
		$tempFile = new SplTempFileObject();
		$tempFile->fwrite($csvString);

		// Set the flags to read CSV data
		$tempFile->setFlags(SplFileObject::READ_CSV);
		$tempFile->setCsvControl($delimiter, $enclosure);

		$csvString = '';

		foreach ($tempFile as $row)
		{
			foreach ($row as $column)
				$csvString .= $enclosure . static::safe($column, $enclosure) . $enclosure . $delimiter;

			$csvString = substr($csvString, 0, (0 - mb_strlen($delimiter))) . PHP_EOL;
		}

		return $csvString;
	}

	/**
	 * Parses the given CSV string and returns in array.
	 *
	 * @param  string $line       The CSV string.
	 * @param  string $delimiter  The delimiter character.
	 * @param  string $enclosure  The enclosure character.
	 * @return array              Returns an array.
	 */
	protected static function _parseLine(string $line, string $delimiter, string $enclosure) : array
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
	 * @param string $enclosure           Enclosure.
	 * @param string $newline             Newline character.
	 * @return string                     Returns the well-formed CSV.
	 */
	protected static function _fromDatasetOrRecordset(array $datasetOrRecordset, string $delimiter, string $enclosure, string $newline) : string
	{
		$header = '';
		$csvString = '';

		foreach ($datasetOrRecordset as $i => $row)
		{
			foreach ($row as $key => $value)
			{
				if ($i === 0)
					$header .= $enclosure . static::safe($key, $enclosure) . $enclosure . $delimiter;

				$csvString .= $enclosure . static::safe($value, $enclosure) . $enclosure . $delimiter;
			}

			$csvString = substr($csvString, 0, (0 - mb_strlen($delimiter))) . $newline;
		}

		$header = substr($header, 0, (0 - mb_strlen($delimiter))) . $newline;
		$csvString = rtrim($csvString, $newline);

		if (Arr::isRecordset($datasetOrRecordset) or Arr::isAssociative($datasetOrRecordset[0]))
			$csvString = $header . $csvString;

		return $csvString;
	}
}
