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
 * @package System
 */
final class Number
{
	/**
	 * @param  int         $size
	 * @param  int         $precision
	 * @param  string|null $unit
	 * @return string
	 */
	public static function byteFormat(int $size, int $precision = 1, string $unit = null) : string
	{
		// Trim first
		if ($unit)
			$unit = strtoupper(trim($unit));

		// After trim
		if (!$unit)
			$unit = Number::getUnitByFileSize($size);

		$size = Number::getFileSizeByUnit($size, $unit);
		$size = number_format($size, $precision);

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
	 * @param  int    $size
	 * @return string
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
	 * @param  int    $size
	 * @param  string $unit
	 * @return float
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
	 * Enture the number of $value is in the specific range.
	 *
	 * @param  int|float $value
	 * @param  int|float $min
	 * @param  int|float $max
	 * @return int|float
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
