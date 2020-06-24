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

use System\Exception\InvalidArgumentException;

/**
 * Class DateTime
 * @package System
 */
final class DateTime extends \DateTime
{
	private static $_formats = [
		'Y-m-d',
		'Y-m-d H:i',
		'Y-m-d H:i:s',
		'Y.m.d',
		'Y.m.d H:i',
		'Y.m.d H:i:s',
		'd.m.Y',
		'd.m.Y H:i',
		'd.m.Y H:i:s',
		'd/m/Y',
		'd/m/Y H:i',
		'd/m/Y H:i:s',
		'Ymd',
		'YmdHi',
		'YmdHis'
	];

	/**
	 * @param  string      $datetime
	 * @param  string|null $format
	 * @return bool
	 */
	public static function isValid(string $datetime, string $format = null) : bool
	{
		$datetime = trim($datetime);

		if ($format)
			$formats = [$format];
		else
			$formats = static::$_formats;

		foreach ($formats as $format)
		{
			$datetimeObject = DateTime::createFromFormat($format, $datetime);

			if ($datetimeObject and $datetimeObject->format($format) === $datetime)
				return true;
		}

		return false;
	}

	/**
	 * The framework default datetime format.
	 *
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function _($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('Y-m-d H:i', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function sortable($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('Y-m-d H:i:s', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function mysql($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('Y-m-d H:i:s', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortDate($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('d/m/Y', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function longDate($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('d F Y', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('H:i', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function longTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('H:i:s', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function fullDateTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('d F Y H:i', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function fullLongDateTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('d F Y H:i:s', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function dayMonth($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('d F', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function monthYear($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('F Y', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function day($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('d', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortDayName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('D', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function fullDayName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('l', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function hour($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('h', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function hour24($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('H', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function minute($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('i', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function second($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('s', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function month($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('m', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortMonthName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('M', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function monthName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('F', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function apm($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('A', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function year($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('Y', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortYear($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return static::_create('y', $datetime);
		else
			throw InvalidArgumentException::type(1, ['string','int'], $datetime);
	}

	/**
	 * @param  string|null $datetime
	 * @return int
	 */
	private static function _toTimeStamp(string $datetime = null) : int
	{
		if ($datetime)
			$timestamp = strtotime($datetime);
		else
			$timestamp = time();

		return $timestamp;
	}

	/**
	 * @param  int|null $timestamp
	 * @return string
	 */
	private static function _toDateTime(int $timestamp = null) : string
	{
		if ($timestamp)
			$datetime = date('Y-m-d H:i:s', $timestamp);
		else
			$datetime = date('Y-m-d H:i:s');

		return $datetime;
	}

	/**
	 * @param  string          $format
	 * @param  string|int|null $datetime
	 * @return string
	 */
	private static function _create(string $format, $datetime = null) : string
	{
		if (is_int($datetime) or is_null($datetime))
			$datetime = static::_toDateTime($datetime);

		if (static::isValid($datetime))
		{
			$timestamp = static::_toTimeStamp($datetime);
			return date($format, $timestamp);
		}
		else
			return '';
	}

	/**
	 * @param  string      $name
	 * @param  string|null $selected
	 * @param  string      $title
	 * @return string
	 */
	public static function timeZoneMenu(string $name, string $selected = null, string $title = '') : string
	{
		$defaultTimeZone = date_default_timezone_get();

		$options = [];
		$timestamp = time();

		foreach (timezone_identifiers_list() as $key => $zone)
		{
			date_default_timezone_set($zone);
			$options[] = [$zone => 'UTC ' . date('P', $timestamp) . ' - ' . $zone];
		}

		date_default_timezone_set($defaultTimeZone);

		return Form::select($name, $options, $selected, $title);
	}

	/**
	 * @param  string      $name
	 * @param  string|null $selected
	 * @param  string      $title
	 * @return string
	 */
	public static function timeZoneRegionMenu(string $name, string $selected = null, string $title = '') : string
	{
		$options = [];

		foreach (timezone_identifiers_list() as $zone)
		{
			if (strpos($zone, '/'))
			{
				$pos = strpos($zone, '/');
				$region = substr($zone, 0, $pos);
			}
			else // UTC
				$region = $zone;

			if ($region and !in_array($region, $options))
				$options[] = $region;
		}

		return Form::select($name, $options, $selected, $title);
	}

	/**
	 * @param string      $name
	 * @param string|null $selected
	 * @param string      $title
	 * @return string
	 */
	public static function timeZoneCityMenu(string $name, string $selected = null, string $title = '') : string
	{
		$options = [];

		foreach (timezone_identifiers_list() as $zone)
		{
			// Except UTC
			if (strpos($zone, '/'))
			{
				$city = substr($zone, strpos($zone, '/') + 1);

				if ($city and !in_array($city, $options))
					$options[] = $city;
			}
		}

		$options = Arr::sort($options);

		return Form::select($name, $options, $selected, $title);
	}
}
