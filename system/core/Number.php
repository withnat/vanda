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
 * Class Number
 *
 * The Number Class handles various operations and manipulations related to
 * numeric values. It includes functionalities to format byte sizes, determine
 * appropriate size units for file sizes, convert file sizes between different
 * units, and check if a number falls within a specified range.
 *
 * @package System
 */
class Number
{
	/**
	 * Formats the bytes to the desired form. Possible unit options are Byte (B),
	 * Kilobyte (KB), Megabyte (MB), Gigabyte (GB), Terabyte (TB).
	 *
	 * For example, formatting 12345 byes results in "12.1 K" and 1234567 results in "1.18 MB".
	 *
	 * @param  int         $size     The input byte sizes.
	 * @param  int         $decimal  Optionally, sets the number of decimal digits. If 0, the decimal separator is
	 *                               omitted from the return value. Defaults to 1.
	 * @param  string|null $unit     Optionally, specifies the unit. Defaults to null (auto).
	 * @return string                Returns a formatted string representing the given bytes in more human-readable form.
	 */
	public static function byteFormat(int $size, int $decimal = 1, string $unit = null) : string
	{
		// Trim first
		if ($unit)
			$unit = strtoupper(trim($unit));

		// After trim
		if (!$unit)
			$unit = static::getUnitByFileSize($size);

		$size = static::getFileSizeByUnit($size, $unit);
		$size = number_format($size, $decimal);

		// Remove trailing zero after decimal point
		// and keep thousand separator (comma).

		// e.g., 140,176.0 becomes 140,176.
		$size = rtrim($size, '0');

		// e.g., 140,176. becomes 140,176
		$size = rtrim($size, '.');

		$size .= ' ' . $unit;

		return $size;
	}

	/**
	 * Returns a unit of file size by the given bytes.
	 *
	 * @param  int    $size  The input byte sizes.
	 * @return string        Returns file size unit.
	 */
	public static function getUnitByFileSize(int $size) : string
	{
		if ($size < 1024)
			$unit = 'B';
		elseif ($size < 1048576)
			$unit = 'KB';
		elseif ($size < 1073741824)
			$unit = 'MB';
		elseif ($size < 1099511627776)
			$unit = 'GB';
		else
			$unit = 'TB';

		return $unit;
	}

	/**
	 * Returns a human readable file size.
	 *
	 * @param  int    $size  The input byte sizes.
	 * @param  string $unit  Specifies the unit.
	 * @return float         Returns the formatted byte sizes based on the given unit.
	 */
	public static function getFileSizeByUnit(int $size, string $unit) : float
	{
		$unit = strtoupper(trim($unit));

		switch ($unit)
		{
			case 'KB':
				$size = $size / 1024;
				break;
			case 'MB':
				$size = $size / 1048576;
				break;
			case 'GB':
				$size = $size / 1073741824;
				break;
			case 'TB':
				$size = $size / 1099511627776;
				break;
		}

		return $size;
	}

	/**
	 * Enture the number of the given value is in the specific range.
	 *
	 * @param  int|float $value  The input value.
	 * @param  int|float $min    Specifies the sequence's minimum value.
	 * @param  int|float $max    Specifies the sequence's maximum value.
	 * @return int|float         Returns the value between the minimum value and the maximum Value.
	 */
	public static function inrange($value, $min, $max)
	{
		if (!is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(1, ['int', 'float'], $value);

		if (!is_int($min) and !is_float($min))
			throw InvalidArgumentException::typeError(2, ['int', 'float'], $min);

		if (!is_int($max) and !is_float($max))
			throw InvalidArgumentException::typeError(3, ['int', 'float'], $max);

		if ($value > $max)
			$value = $max;
		elseif ($value < $min)
			$value = $min;

		return $value;
	}
}
