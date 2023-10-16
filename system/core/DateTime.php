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

use DateTimeZone;
use Exception;
use System\Exception\InvalidArgumentException;

/**
 * Class Time
 * @package System
 */
class DateTime extends \DateTime
{
	/**
	 * A list of datetime formats.
	 *
	 * @var array
	 */
	protected static $_formats = [
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
	 * The format string to be applied when using the __toString() magic method.
	 *
	 * @var string
	 */
	public static $format = 'Y-m-d H:i:s';

	/**
	 * Placeholder for a \DateTimeZone object with GMT as the time zone.
	 *
	 * @var object
	 */
	protected static $_utc;

	/**
	 * Placeholder for a \DateTimeZone object with the default server
	 * time zone as the time zone.
	 *
	 * @var string
	 */
	protected static $_serverTimezone;

	/**
	 * The \DateTimeZone object for usage in rending dates as strings.
	 *
	 * @var DateTimeZone
	 */
	protected $_timezone;

	protected static $_datetime;
	protected static $_timestamp;

	const DAY_SHORT = "\x021\x03";
	const DAY_LONG = "\x022\x03";
	const MONTH_SHORT = "\x023\x03";
	const MONTH_LONG = "\x024\x03";

	/**
	 * Time constructor.
	 *
	 * @param  string                   $time      String in a format accepted by strtotime(). Defaults to "now".
	 * @param  DateTimeZone|string|null $timezone  Time zone to be used for the date. Might be a string or a DateTimeZone
	 *                                             object. Defaults to null.
	 * @throws Exception
	 */
	public function __construct($time = 'now', DateTimeZone $timezone = null)
	{
		if (!static::$_utc or !static::$_serverTimezone)
		{
			static::$_utc = new \DateTimeZone('UTC');
			static::$_serverTimezone = @date_default_timezone_get();
		}

		if (!($timezone instanceof \DateTimeZone))
		{
			if (is_null($timezone))
				$timezone = static::$_gmt;
			elseif (is_string($timezone))
				$timezone = new \DateTimeZone($timezone);
		}

		$timezone       = ! empty($timezone) ? $timezone : date_default_timezone_get();
		$this->_timezone = $timezone instanceof DateTimeZone ? $timezone : new \DateTimeZone($timezone);

		date_default_timezone_set('UTC');

		// If the date is numeric assume a unix timestamp and convert it.
		if (is_numeric($time))
			$time = date('c', $time); // e.g., 2011-01-14T23:43:42+07:00

		parent::__construct($time, $timezone);

		// Reset the timezone for 3rd party libraries/extension that does not use Vada DateTime.
		date_default_timezone_set(static::$_serverTimezone);

		static::$_datetime = $time;
		$this->_timezone = $timezone;
		$this->_locale = $locale;
	}

	public function setTimestamp($timestamp) : void
	{
		static::$_datetime = date('Y-m-d H:i:s', $timestamp);

		parent::setTimestamp($timestamp);
	}

	/**
	 * Determines if the given string is a datetime.
	 *
	 * @param  string      $time  The input string.
	 * @param  string|null $format    Optional, the datetime format to check. Defaults to null.
	 * @return bool                   Returns true if the given string is a datetime, false otherwise.
	 */
	public static function isValid(string $time, string $format = null) : bool
	{
		$time = trim($time);

		if ($format)
			$formats = [$format];
		else
			$formats = static::$_formats;

		foreach ($formats as $format)
		{
			$timeObject = parent::createFromFormat($format, $time);

			if ($timeObject and $timeObject->format($format) === $time)
				return true;
		}

		return false;
	}

	/**
	 * Converts the given datetime into the default framework datetime format.
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function _($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('Y-m-d H:i', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a sortable format.
	 *
	 * e.g., 2011-01-14 13:25:38
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function sortable($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('Y-m-d H:i:s', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a MySQL format.
	 *
	 * e.g., 2011-01-14 13:25:38
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function mysql($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('Y-m-d H:i:s', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime a short date format.
	 *
	 * e.g., 14/01/2011
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function shortDate($time) : string
	{
		if (is_string($time) or is_int($time))
		{
			$format = Config::app('shortDate', 'd/m/Y');

			return static::_create($format, $time);
		}
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime a medium date format.
	 *
	 * e.g., 14 Jan 96
	 *
	 * @param  string|int|null $time  The input datetime.
	 * @return string                     Returns the formated datetime.
	 * @throws Exception
	 */
	public static function mediumDate($time = null) : string
	{
		if (is_string($time) or is_int($time) or is_null($time))
		{
			$format = Config::app('mediumDate', 'j-M-y');

			if (is_int($time))
			{
				$date = new \DateTime();
				$date->setTimestamp($time);

				$time = $date->format($format);
			}
			else
			{
				if (is_null($time))
					$time = static::$_datetime;

				$date = new \DateTime();

				$time = $date->format($format);
			}

			//$time = static::_translate($time, 'short');

			return $time;
		}
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int', 'null'], $time);
	}

	/**
	 * Converts the given datetime to a long date format.
	 *
	 * e.g., Friday, January 14, 2011
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function longDate($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('l, F j, Y', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a short time format (without seconds).
	 *
	 * e.g., 09:15
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function shortTime($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('H:i', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a long time format (with seconds).
	 *
	 * e.g., 09:15:38
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function longTime($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('H:i:s', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a long datetime format.
	 *
	 * e.g., Friday, January 14, 2011 14:23
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function longDateTime($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('l, F j, Y H:i', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	public static function fullDateTime($time) : string
	{

	}

	/**
	 * Converts the given datetime to a day of the month and full month name.
	 *
	 * e.g., 14 January
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function dayMonth($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('d F', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to full month name and full year.
	 *
	 * e.g., January 2015
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function monthYear($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('F Y', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a day of the month, 2 digits with leading zeros.
	 *
	 * e.g., 01 to 31
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function day($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('d', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a textual representation of a day, three letters.
	 *
	 * e.g., Mon to Sun
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function shortDayName($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('D', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a full textual representation of the day of the week.
	 *
	 * e.g., Monday to Sunday
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function fullDayName($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('l', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to 24-hour format of an hour with leading zeros.
	 *
	 * e.g., 00 to 23
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function hour($time) : string
	{
		return static::hour24($time);
	}

	/**
	 * Converts the given datetime to 12-hour format of an hour with leading zeros.
	 *
	 * e.g., 01 to 12
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function hour12($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('h', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to 24-hour format of an hour with leading zeros.
	 *
	 * e.g., 00 to 23
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function hour24($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('H', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to minutes with leading zeros.
	 *
	 * e.g., 00 to 59
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function minute($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('i', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to seconds with leading zeros.
	 *
	 * e.g., 00 to 59
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function second($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('s', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a numeric representation of a month, with leading zeros.
	 *
	 * e.g., 01 to 12
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function month($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('m', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a short textual representation of a month, 2 letters.
	 *
	 * e.g., Jan to Dec
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function shortMonthName($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('M', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a full textual representation of a month,
	 * such as January or March.
	 *
	 * e.g., January to December
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function monthName($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('F', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to an uppercase Ante meridiem and Post meridiem.
	 *
	 * e.g., AM or PM
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function apm($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('A', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to a full numeric representation of a year, 4 digits.
	 *
	 * e.g., 1981 or 2015
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function year($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('Y', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

	/**
	 * Converts the given datetime to two digit representation of a year.
	 *
	 * e.g., 81 (1981) or 96 (1996)
	 *
	 * @param  string|int $time  The input datetime.
	 * @return string                Returns the formated datetime.
	 */
	public static function shortYear($time) : string
	{
		if (is_string($time) or is_int($time))
			return static::_create('y', $time);
		else
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $time);
	}

//	/**
//	 * @param  string|null $time The input datetime.
//	 * @return int
//	 */
//	private static function _toTimeStamp(string $time = null) : int
//	{
//		if ($time)
//			$timestamp = strtotime($time);
//		else
//			$timestamp = time();
//
//		return $timestamp;
//	}

//	/**
//	 * @param  int|null $timestamp The input datetime.
//	 * @return string
//	 */
//	private static function _toDateTime(int $timestamp = null) : string
//	{
//		if ($timestamp)
//			$time = date('Y-m-d H:i:s', $timestamp);
//		else
//			$time = date('Y-m-d H:i:s');
//
//		return $time;
//	}

	/**
	 * Returns the date as a formatted string.
	 *
	 * @param  string $format            The date format specification string (see {@link PHP_MANUAL#date}).
	 * @param  string|int|null $time The input datetime.
	 * @param  bool $translate           True to translate localised strings.
	 * @return string
	 * @throws Exception
	 */

	 public function format($format, bool $local = false, $translate = true) : string
	{
//		if ($time)
//			parent::__construct($time);

		if ($translate)
		{
			$format = preg_replace('/(^|[^\\\])D/', "\\1" . static::DAY_SHORT, $format);
			$format = preg_replace('/(^|[^\\\])l/', "\\1" . static::DAY_LONG, $format);
			$format = preg_replace('/(^|[^\\\])M/', "\\1" . static::MONTH_SHORT, $format);
			$format = preg_replace('/(^|[^\\\])F/', "\\1" . static::MONTH_LONG, $format);
		}

		$time = parent::format($format);

		//$time = date($format, $timestamp);

		if ($translate)
		{
			if (strpos($time, static::DAY_SHORT) !== false)
				$time = str_replace(static::DAY_SHORT, $this->dayToString((int)parent::format('w'), true), $time);

			if (strpos($time, static::DAY_LONG) !== false)
				$time = str_replace(static::DAY_LONG, $this->dayToString((int)parent::format('w')), $time);

			if (strpos($time, static::MONTH_SHORT) !== false)
				$time = str_replace(static::MONTH_SHORT, $this->monthToString((int)parent::format('n'), true), $time);

			if (strpos($time, static::MONTH_LONG) !== false)
				$time = str_replace(static::MONTH_LONG, $this->monthToString((int)parent::format('n')), $time);
		}

		return $time;
	}

	/**
	 * Translates day of week number to a string.
	 *
	 * @param  int    $day   The numeric day of the week.
	 * @param  bool   $abbr  If true, return the abbreviated day string. Defaults to false.
	 * @return string        Returns the day of the week.
	 */
	public static function dayToString(int $day, bool $abbr = false) : string
	{
		switch ($day)
		{
			case 0:
				$output = ($abbr ? t('SUN_SHORT') : t('SUN_LONG'));
				break;

			case 1:
				$output = ($abbr ? t('MON_SHORT') : t('MON_LONG'));
				break;

			case 2:
				$output = ($abbr ? t('TUE_SHORT') : t('TUE_LONG'));
				break;

			case 3:
				$output = ($abbr ? t('WED_SHORT') : t('WED_LONG'));
				break;

			case 4:
				$output = ($abbr ? t('THU_SHORT') : t('THU_LONG'));
				break;

			case 5:
				$output = ($abbr ? t('FRI_SHORT') : t('FRI_LONG'));
				break;

			case 6:
				$output = ($abbr ? t('SAT_SHORT') : t('SAT_LONG'));
				break;

			default:
				$output = '';
		}

		return $output;
	}

	/**
	 * Translates month number to a string.
	 *
	 * @param  int   $month  The numeric month of the year.
	 * @param  bool   $abbr  If true, return the abbreviated month string. Defaults to false.
	 * @return string        Returns the month of the year.
	 */
	public static function monthToString(int $month, bool $abbr = false) : string
	{
		switch ($month)
		{
			case 1:
				$output = ($abbr ? t('JAN_SHORT') : t('JAN_LONG'));
				break;

			case 2:
				$output = ($abbr ? t('FEB_SHORT') : t('FEB_LONG'));
				break;

			case 3:
				$output = ($abbr ? t('MAR_SHORT') : t('MAR_LONG'));
				break;

			case 4:
				$output = ($abbr ? t('APR_SHORT') : t('APR_LONG'));
				break;

			case 5:
				$output = ($abbr ? t('MAY_SHORT') : t('MAY_LONG'));
				break;

			case 6:
				$output = ($abbr ? t('JUN_SHORT') : t('JUN_LONG'));
				break;

			case 7:
				$output = ($abbr ? t('JUL_SHORT') : t('JUL_LONG'));
				break;

			case 8:
				$output = ($abbr ? t('AUG_SHORT') : t('AUG_LONG'));
				break;

			case 9:
				$output = ($abbr ? t('SEP_SHORT') : t('SEP_LONG'));
				break;

			case 10:
				$output = ($abbr ? t('OCT_SHORT') : t('OCT_LONG'));
				break;

			case 11:
				$output = ($abbr ? t('NOV_SHORT') : t('NOV_LONG'));
				break;

			case 12:
				$output = ($abbr ? t('DEC_SHORT') : t('DEC_LONG'));
				break;

			default:
				$output = '';
		}

		return $output;
	}

	protected static function _translate(string $time, string $version = 'short') : string
	{
		if ($version === 'short')
		{
			$searches = [
				'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
				'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug',
				'Sep', 'Oct', 'Nov', 'Dec'
			];

			$replaces = [
				t('MON'), t('TUE'), t('WED'), t('THU'), t('FRI'), t('SAT'), t('SUN'),
				t('JAN'), t('FEB'), t('MAR'), t('APR'), t('MAY'), t('JUN'), t('JUL'), t('AUG'),
				t('SEP'), t('OCT'), t('NOV'), t('DEC')
			];
		}
		else
		{
			$searches = [
				'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
				'September', 'October', 'November', 'December'
			];

			$replaces = [
				t('MONDAY'), t('TUESDAY'), t('WEDNESDAY'), t('THURSDAY'), t('FRIDAY'), t('SATURDAY'), t('SUNDAY'),
				t('JANUARY'), t('FEBRUARY'), t('MARCH'), t('APRIL'), t('MAY'), t('JUNE'), t('JULY'), t('AUGUST'),
				t('SEPTEMBER'), t('OCTOBER'), t('NOVEMBER'), t('DECEMBER')
			];
		}

		$time = str_replace($searches, $replaces, $time);

		return $time;
	}
}
