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
			$formats = DateTime::$_formats;

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
			return DateTime::_create('Y-m-d H:i', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function sortable($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('Y-m-d H:i:s', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function mysql($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('Y-m-d H:i:s', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortDate($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('d/m/Y', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function longDate($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('d F Y', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('H:i', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function longTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('H:i:s', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function fullDateTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('d F Y H:i', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function fullLongDateTime($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('d F Y H:i:s', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function dayMonth($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('d F', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function monthYear($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('F Y', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function day($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('d', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortDayName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('D', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function fullDayName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('l', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function hour($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('h', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function hour24($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('H', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function minute($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('i', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function second($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('s', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function month($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('m', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortMonthName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('M', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function monthName($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('F', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function apm($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('A', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function year($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('Y', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
	}

	/**
	 * @param  string|int $datetime
	 * @return string
	 */
	public static function shortYear($datetime) : string
	{
		if (is_string($datetime) or is_int($datetime))
			return DateTime::_create('y', $datetime);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $datetime);
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
			$datetime = DateTime::_toDateTime($datetime);

		if (DateTime::isValid($datetime))
		{
			$timestamp = DateTime::_toTimeStamp($datetime);
			return date($format, $timestamp);
		}
		else
			return '';
	}
}
