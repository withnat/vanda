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

use Exception;
use System\Exception\InvalidArgumentException;

/**
 * Class Str
 *
 * The Str class is a set of methods to help with the manipulation of strings
 * and is normally accessed statically. Example: Str::uuid().
 *
 * The class also contains multibyte agnostic versions of PHP's multibyte-aware
 * functions. For example, you can replace both strlen() and mb_strlen() with
 * Str::length(), which will return a multi-byte aware result based on whether
 * or not PHP's mbstring extension is loaded.
 *
 * @package System
 */
class Str
{
	private static $_encoding = null;
	/**
	 * Str constructor.
	 */
	private function __construct(){}

	/**
	 * Gets the character encoding. If it is omitted or null, the internal
	 * character encoding value will be used.
	 *
	 * @param  string|null $encoding  Optionally, the character encoding can be overwritten. Defaults to null.
	 * @return string                 Returns the character encoding.
	 */
	private static function _getEncoding(string $encoding = null) : string
	{
		if (!$encoding and !static::$_encoding)
		{
			if (Config::app('charset'))
				$encoding = Config::app('charset');
			else
				$encoding = mb_internal_encoding();

			static::$_encoding = $encoding;
		}
		else
			$encoding = static::$_encoding;

		return $encoding;
	}

	/**
	 * Returns the length of the given string.
	 *
	 * An alias for PHP's mb_strlen() function.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 * $result = Str::length($string);
	 * // The $result will be: 30
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return int                    Returns the length of the string on success, and 0 if the string is empty.
	 */
	public static function length(string $string, string $encoding = null) : int
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($string, $encoding);

		return $length;
	}

	/**
	 * Returns the number of occurrences of $substring in the given string.
	 * By default, the comparison is case-sensitive, but can be made insensitive
	 * by setting $caseSensitive to false.
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 * $result = Str::count($string, 'A');
	 * // The $result will be: 1
	 *
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 * $result = Str::count($string, 'A', false);
	 * // The $result will be: 4
	 * ```
	 *
	 * @param  string      $string         The string being counted on.
	 * @param  string      $substring      The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return int                         Returns the number of $substring occurrences.
	 */
	public static function count(string $string, string $substring, bool $caseSensitive = true, string $encoding = null) : int
	{
		$encoding = static::_getEncoding($encoding);

		if (!$caseSensitive)
		{
			$string = mb_strtoupper($string, $encoding);
			$substring = mb_strtoupper($substring, $encoding);
		}

		$count = mb_substr_count($string, $substring, $encoding);

		return $count;
	}

	/**
	 * Counts words in the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'Nat Withe';
	 * $result = Str::countWords($string);
	 * // The $result will be: 2
	 * ```
	 *
	 * @param  string $string  The string being counted on.
	 * @return int             Returns the number of words in the given string.
	 */
	public static function countWords(string $string) : int
	{
		$count = count(preg_split('/\s+/u', $string, -1, PREG_SPLIT_NO_EMPTY));

		return $count;
	}

	//

	/**
	 * Returns the first $length (leading) characters of the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'Nat Withe';
	 *
	 * $result = Str::left($string);
	 * // The $result will be: N
	 *
	 * * $result = Str::left($string, 2);
	 * // The $result will be: Na
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  int         $length    Optionally, the length of character to return. Defaults to 1.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string
	 */
	public static function left(string $string, int $length = 1, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_substr($string, 0, $length, $encoding);

		return $string;
	}

	/**
	 * Returns the last $length (trailing) characters of the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'Nat Withe';
	 *
	 * $result = Str::right($string);
	 * // The $result will be: e
	 *
	 * $result = Str::right($string, 2);
	 * // The $result will be: he
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  int         $length    Optionally, the length of character to return. Defaults to 1.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string
	 */
	public static function right(string $string, int $length = 1, string $encoding = null) : string
	{
		if ($length === 0)
			return '';

		$encoding = static::_getEncoding($encoding);

		$string = mb_substr($string, (0 - $length), null, $encoding);

		return $string;
	}

	/**
	 * Returns the character at $index, with indexes starting at 0.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::at($string, 5);
	 * // The $result will be: F
	 *
	 * $result = Str::at($string, -5);
	 * // The $result will be: B
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  int         $index     The location of a character in the given string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string
	 */
	public static function at(string $string, int $index, string $encoding = null) : string
	{
		if ($index < 0 and abs($index) > static::length($string))
			return '';

		$encoding = static::_getEncoding($encoding);
		$string = mb_substr($string, $index, 1, $encoding);

		return $string;
	}

	/**
	 * Returns the portion of string specified by the start and length parameters.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::slice($string, 0, 3);
	 * // The $result will be: ABC
	 *
	 * $result = Str::at($string, 0, -13);
	 * // The $result will be: ABCDEF:eFMNRZa:/f
	 *
	 * $result = Str::at($string, -8, 2);
	 * // The $result will be: fa
	 *
	 * $result = Str::at($string, -8, -3);
	 * // The $result will be: fa:Bm
	 * ```
	 *
	 * @param  string      $string    The string to slice.
	 * @param  int         $start     Specifies where to start in the string.
	 * @param  int|null    $length    Optionally, specifies the length of the returned string. Default is to the end of
	 *                                the string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string
	 */
	public static function slice(string $string, int $start, int $length = null, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_substr($string, $start, $length, $encoding);

		return $string;
	}

	/**
	 * Limits the string based on the character count. Preserves complete words
	 * so the character count may not be exactly as specified.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'Nat is so tall, and handsome as hell';
	 *
	 * $result = Str::limit($string, 11);
	 * // The $result will be: Nat is so tall,...
	 *
	 * $result = Str::limit($string, 15);
	 * // The $result will be: Nat is so tall,...
	 * ```
	 *
	 * @param  string      $string    The string to truncate.
	 * @param  int         $length    How many characters from original string to include into truncated string.
	 * @param  string      $suffix    Optionally, string to append to the end of the truncated string. Default is '...'.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns the truncated string.
	 * @see    https://en.wikipedia.org/wiki/Escape_character
	 */
	public static function limit(string $string, int $length, string $suffix = '...', string $encoding = null) : string
	{
		if ($length <= 0)
			return $suffix;

		$encoding = static::_getEncoding($encoding);

		// Remove control characters.
		// \x0B is Vertical tab
		// \x0C is Form feed
		$string = str_replace(["\n", "\r", "\t", "\x0B", "\x0C"], ' ', $string);

		// Reduce double spaces and trim.
		$string = preg_replace('!\s+!', ' ', $string);
		$string = trim($string);

		if (mb_strlen($string, $encoding) <= $length)
			return $string;

		$words = explode(' ', $string);
		$result = '';

		foreach ($words as $word)
		{
			$result .= $word . ' ';

			if (mb_strlen($result, $encoding) >= $length)
			{
				$result = rtrim($result);
				break;
			}
		}

		if (mb_strlen($result, $encoding) < mb_strlen($string, $encoding))
			$result .= $suffix;

		return $result;
	}

	/**
	 * Limits a string to the number of words specified.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'Nat is so tall, and handsome as hell';
	 *
	 * $result = Str::limitWords($string, 4);
	 * // The $result will be: Nat is so tall,...
	 *
	 * $result = Str::limitWords($string, 6);
	 * // The $result will be: Nat is so tall, and handsome...
	 * ```
	 *
	 * @param  string      $string    The string to truncate.
	 * @param  int         $words     How many words from original string to include into truncated string.
	 * @param  string      $suffix    Optionally, string to append to the end of truncated string. Default is '...'.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns the truncated string.
	 */
	public static function limitWords(string $string, int $words, string $suffix = '...', string $encoding = null) : string
	{
		if ($words <= 0)
			return $suffix;

		$encoding = static::_getEncoding($encoding);
		$pattern = '/^\s*+(?:\S++\s*+){1,' . $words . '}/u';

		preg_match($pattern, $string, $matches);

		if (!isset($matches[0]) or mb_strlen($string, $encoding) === mb_strlen($matches[0], $encoding))
			return $string;

		$string = rtrim($matches[0]) . $suffix;

		return $string;
	}

	/**
	 * Finds position of first occurrence of string in a string.
	 * Accepts an optional offset from which to begin the search.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::position($string, 'a');
	 * // The $result will be: 13
	 *
	 * $result = Str::position($string, ':', 4);
	 * // The $result will be: 6
	 * ```
	 *
	 * @param  string      $string    The string being checked.
	 * @param  string      $search    Substring to look for.
	 * @param  int         $start     Optionally, specifies where to begin the search. If start is a negative number,
	 *                                it counts from the end of the string. Defaults to 0.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return int|false              Returns the first occurrence's index if found, otherwise false.
	 */
	public static function position(string $string, string $search, int $start = 0, string $encoding = null)
	{
		$encoding = static::_getEncoding($encoding);
		$pos = mb_strpos($string, $search, $start, $encoding);

		return $pos;
	}

	/**
	 * Finds position of last occurrence of string in a string.
	 * Accepts an optional offset from which to begin the search.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::lastPosition($string, ':');
	 * // The $result will be: 24
	 *
	 * $result = Str::lastPosition($string, ':', -10);
	 * // The $result will be: 14
	 * ```
	 *
	 * @param  string      $string    The string being checked.
	 * @param  string      $search    Substring to look for.
	 * @param  int         $start     Optionally, specifies where to begin the search. If start is a negative number,
	 *                                it counts from the end of the string. Defaults to 0.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return int|false              Returns the last occurrence's index if found, otherwise false.
	 */
	public static function lastPosition(string $string, string $search, int $start = 0, string $encoding = null)
	{
		$encoding = static::_getEncoding($encoding);
		$pos = mb_strrpos($string, $search, $start, $encoding);

		return $pos;
	}

	/**
	 * Returns the substring between $start and $end, if found, or an empty string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::between($string, 'F', 'M');
	 * // The $result will be: :eF
	 *
	 * $result = Str::between($string, ':', ':', 10);
	 * // The $result will be: /fabcdefa
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string      $start     The start of the substring.
	 * @param  string      $end       The end of the substring.
	 * @param  int         $offset    Optionally, offset from which to search. Defaults to 0.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a substring between $start and $end.
	 */
	public static function between(string $string, string $start, string $end, int $offset = 0, string $encoding = null) : string
	{
		if ($offset < 0)
			throw InvalidArgumentException::valueError(4, '$offset must be greater than zero', $offset);

		$encoding = static::_getEncoding($encoding);

		$startPos = mb_strpos($string, $start, $offset, $encoding);

		if ($startPos === false)
			return '';

		$substrPos = $startPos + mb_strlen($start, $encoding);
		$endPos = mb_strpos($string, $end, $substrPos, $encoding);

		if ($endPos === false)
			return '';

		$length = $endPos - $substrPos;
		$substr = mb_substr($string, $substrPos, $length, $encoding);

		return $substr;
	}

	/**
	 * Strips whitespace (or other characters) from the beginning and end of a string.
	 *
	 * (Built-in PHP function trim() does not allow $characterMask to number.)
	 *
	 * If $characterMask is null, returns a string with whitespace stripped from the beginning of $string.
	 * If $characterMask is string, returns a string with characters stripped from the beginning of $string.
	 * If $characterMask is int, returns a part of string, start at a specified position by $characterMask.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = ' axb,ayb ';
	 *
	 * $result = Str::trim($string);
	 * // The $result will be: 'axb,ayb'
	 *
	 * $result = Str::trim($string, 2);
	 * // The $result will be: 'xb,ay'
	 *
	 * $result = Str::trim($string, 'b');
	 * // The $result will be: 'axb,ay'
	 * ```
	 *
	 * @param  string          $string         The string that will be striped.
	 * @param  string|int|null $characterMask  Optionally, the stripped characters can also be specified.
	 * @param  string|null     $encoding       Optionally, the character encoding. If it is omitted or null, the
	 *                                         internal character encoding value will be used. Defaults to null.
	 * @return string                          Returns a string with whitespace stripped from the beginning and end of
	 *                                         the given string depends on $characterMask data type.
	 */
	public static function trim(string $string, $characterMask = null, string $encoding = null) : string
	{
		if (is_string($characterMask) or is_null($characterMask) or is_int($characterMask))
		{
			$string = static::trimLeft($string, $characterMask, $encoding);
			$string = static::trimRight($string, $characterMask, $encoding);
		}
		else
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'null'], $characterMask);

		return $string;
	}

	/**
	 * Strips whitespace (or other characters) from the beginning of a string.
	 * Built-in PHP function ltrim() not allow $characterMask to number.
	 *
	 * If $characterMask is null, returns a string with whitespace stripped from the beginning of $string.
	 * If $characterMask is string, returns a string with characters stripped from the beginning of $string.
	 * If $characterMask is int, returns a part of string, start at a specified position by $characterMask.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = ' axb,ayb ';
	 *
	 * $result = Str::trimLeft($string);
	 * // The $result will be: 'axb,ayb '
	 *
	 * $result = Str::trimLeft($string, 2);
	 * // The $result will be: 'xb,ayb '
	 *
	 * $result = Str::trimLeft($string, -2);
	 * // The $result will be: 'b '
	 *
	 * $string = 'bbxa,ayb';
	 *
	 * $result = Str::trimLeft($string, 'b');
	 * // The $result will be: 'xa,ayb'
	 * ```
	 *
	 * @param  string          $string         The string that will be striped from the beginning of a string.
	 * @param  string|int|null $characterMask  Optionally, the stripped characters can also be specified.
	 * @param  string|null     $encoding       Optionally, the character encoding. If it is omitted or null, the
	 *                                         internal character encoding value will be used. Defaults to null.
	 * @return string                          Returns a string with whitespace stripped from the beginning of the given
	 *                                         string depends on $characterMask data type.
	 */
	public static function trimLeft(string $string, $characterMask = null, string $encoding = null) : string
	{
		if (is_null($characterMask))
			$string = ltrim($string);
		elseif (is_string($characterMask))
			$string = ltrim($string, $characterMask);
		elseif (is_int($characterMask))
		{
			$start = $characterMask;
			$encoding = static::_getEncoding($encoding);
			$string = mb_substr($string, $start, null, $encoding);
		}
		else
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'null'], $characterMask);

		return $string;
	}

	/**
	 * Strips whitespace (or other characters) from the end of a string.
	 * Built-in PHP function rtrim() not allow $characterMask to number.
	 *
	 * If $characterMask is null, returns a string with whitespace stripped from the end of $string.
	 * If $characterMask is string, returns a string with characters stripped from the end of $string.
	 * If $characterMask is int, returns a part of string, end at a specified position by $characterMask.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = ' axb,ayb ';
	 *
	 * $result = Str::trimRight($string);
	 * // The $result will be: ' axb,ayb'
	 *
	 * $result = Str::trimRight($string, 2);
	 * // The $result will be: ' axb,ay'
	 *
	 * $result = Str::trimRight($string, -2);
	 * // The $result will be: ' a'
	 *
	 * $string = 'bxa,aybb';
	 *
	 * $result = Str::trimRight($string, 'b');
	 * // The $result will be: 'bxa,ay'
	 * ```
	 *
	 * @param  string          $string         The string that will be striped from the end of a string.
	 * @param  string|int|null $characterMask  Optionally, the stripped characters can also be specified.
	 * @param  string|null     $encoding       Optionally, the character encoding. If it is omitted or null, the
	 *                                         internal character encoding value will be used. Defaults to null.
	 * @return string                          Returns a string with whitespace stripped from the end of the given
	 *                                         string depends on $characterMask data type.
	 */
	public static function trimRight(string $string, $characterMask = null, string $encoding = null) : string
	{
		if (is_null($characterMask))
			$string = rtrim($string);
		elseif (is_string($characterMask))
			$string = rtrim($string, $characterMask);
		elseif (is_int($characterMask))
		{
			$encoding = static::_getEncoding($encoding);
			$length = $characterMask;

			if ($length < 0)
				$length = abs($length);
			else
				$length = mb_strlen($string, $encoding) - $length;

			$string = mb_substr($string, 0, $length, $encoding);
		}
		else
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'null'], $characterMask);

		return $string;
	}

	//

	/**
	 * Quotes string with slashes.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::addSlashes("'");
	 * // The $result will be: \'
	 *
	 * $result = Str::addSlashes('"');
	 * // The $result will be: \"
	 *
	 * $result = Str::addSlashes('\\');
	 * // The $result will be: \\\\
	 * ```
	 *
	 * @param  string $string  The string to be escaped.
	 * @return string          Returns the escaped string.
	 */
	public static function addSlashes(string $string) : string
	{
		$string = addslashes($string);

		return $string;
	}

	/**
	 * Un-quotes a quoted string.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::stripSlashes("\'");
	 * // The $result will be: '
	 *
	 * $result = Str::stripSlashes(\"');
	 * // The $result will be: "
	 *
	 * $result = Str::stripSlashes('\\\\');
	 * // The $result will be: \\
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns a string with backslashes stripped off.
	 */
	public static function stripSlashes(string $string) : string
	{
		$string = stripslashes($string);

		return $string;
	}

	/**
	 * Converts all applicable characters to HTML entities.
	 *
	 * An alias for PHP's htmlspecialchars() function.
	 *
	 * It’s generally recommended to use htmlspecialchars
	 * because htmlentities can cause display problems with
	 * your text depending on what characters are being output.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::htmlEncode('<strong>Nat</strong>');
	 * // The $result will be: &lt;strong&gt;Nat&lt;/strong&gt;
	 * ```
	 *
	 * @param  string $string  The string being converted.
	 * @return string          Returns the converted string.
	 */
	public static function htmlEncode(string $string) : string
	{
		// Note for me :P
		// Single pipe or vertical bar is bitwise operators.
		// https://stackoverflow.com/questions/13811922/what-does-using-a-single-pipe-in-a-function-argument-do

		// We take advantage of ENT_SUBSTITUTE flag to correctly deal with invalid UTF-8 sequences.
		$flags = ENT_QUOTES | ENT_SUBSTITUTE;

		$string = htmlspecialchars($string, $flags, mb_internal_encoding());

		return $string;
	}

	/**
	 * Converts HTML entities to their corresponding characters.
	 *
	 * An alias for PHP's htmlspecialchars_decode() function.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::htmlDecode('&lt;strong&gt;Nat&lt;/strong&gt;');
	 * // The $result will be: <strong>Nat</strong>
	 * ```
	 *
	 * @param  string   $string  The string to decode.
	 * @return string            Returns the decoded string.
	 */
	public static function htmlDecode(string $string) : string
	{
		// Note for me :P
		// Single pipe or vertical bar is bitwise operators.
		// https://stackoverflow.com/questions/13811922/what-does-using-a-single-pipe-in-a-function-argument-do

		// We take advantage of ENT_SUBSTITUTE flag to correctly deal with invalid UTF-8 sequences.
		$flags = ENT_QUOTES | ENT_SUBSTITUTE;

		$string = htmlspecialchars_decode($string, $flags);

		return $string;
	}

	//

	/**
	 * Strips all whitespace characters including tabs, newline characters,
	 * as well as multibyte whitespace such as the thin space and ideographic space.
	 *
	 * 1. " "    (an ordinary space)
	 * 2. "\t"   (a tab)
	 * 3. "\n"   (a new line)
	 * 4. "\r"   (a carriage return)
	 * 5. "\0"   (a null byte)
	 * 6. "\x0B" (a vertical tab)
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::removeWhitespace(" a\tb\nc\rd\0e\x0Bf");
	 * // The $result will be: abcdef
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the string without whitespace characters.
	 */
	public static function removeWhitespace(string $string) : string
	{
		// Strip null byte.
		$string = str_replace("\0", "", $string);

		// Strip another whitespaces.
		$string = mb_ereg_replace('[[:space:]]+', '', $string);

		return $string;
	}

	/**
	 * Removes HTML and PHP tags from a string.
	 *
	 * An alias for PHP's strip_tags() function.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::removeTags('<strong>Nat</strong>');
	 * // The $result will be: Nat
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the stripped string.
	 */
	public static function removeTags(string $string) : string
	{
		$string = strip_tags($string);

		return $string;
	}

	/**
	 * Removes single and double quotes from a string.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::removeQuotes('"Nat"');
	 * // The $result will be: Nat
	 *
	 * $result = Str::removeQuotes("'Nat'");
	 * // The $result will be: Nat
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the string without quotes.
	 */
	public static function removeQuotes(string $string) : string
	{
		$string = str_replace(['"', "'"], '', $string);

		return $string;
	}

	/**
	 * Removes Invisible Characters.
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = "http://www.some-site.com/index.php\0";
	 *
	 * $result = Str::removeInvisibleCharacters($string);
	 * // The $result will be: http://www.some-site.com/index.php
	 * ```
	 *
	 * @see    https://www.eso.org/~ndelmott/url_encode.html
	 * @see    http://www.asciitable.com/
	 * @param  string  $string      The input string.
	 * @param  boolean $urlEncoded  Optionally, if set to true, then remove every control character except horizontal
	 *                              tab (dec 09), newline (dec 10) and carriage return (dec 13). Defaults to true.
	 * @return string               Returns the string without invisible characters.
	 */
	public static function removeInvisibleCharacters(string $string, bool $urlEncoded = true): string
	{
		$patterns = [];

		// Remove every control character except horizontal tab (dec 09),
		// newline (dec 10) and carriage return (dec 13).
		// see : https://www.eso.org/~ndelmott/url_encode.html
		if ($urlEncoded)
		{
			$patterns[] = '/%0[0-8bcef]/'; // 00-08, 11, 12, 14, 15
			$patterns[] = '/%1[0-9a-f]/';  // 16-31
		}

		// see : http://www.asciitable.com/
		$patterns[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

		$string = preg_replace($patterns, '', $string);

		return $string;
	}

	//

	/**
	 * Returns a new string with the prefix $substring removed, if present.
	 * A multibyte version of built-in PHP function `ltrim()`.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::removeLeft($string, 'ABCDEF');
	 * // The $result will be: :eFMNRZa:/fabcdefa:Bmnrz
	 * ```
	 *
	 * @param  string      $string     The string that will be striped from the beginning of a string.
	 * @param  string      $substring  The stripped characters.
	 * @param  string|null $encoding   Optionally, the character encoding. If it is omitted or null, the internal
	 *                                 character encoding value will be used. Defaults to null.
	 * @return string                  Returns a string with $substring stripped from the beginning of the given string.
	 */
	public static function removeLeft(string $string, string $substring, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($substring, $encoding);

		if (mb_substr($string, 0, $length, $encoding) === $substring)
		{
			$start = $length;
			$string = mb_substr($string, $start, null, $encoding);
		}

		return $string;
	}

	/**
	 * Returns a new string with the suffix $substring removed, if present.
	 * A multibyte version of built-in PHP function `rtrim()`.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::removeRight($string, 'defa:Bmnrz');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabc
	 * ```
	 *
	 * @param  string      $string     The string that will be striped from the end of a string.
	 * @param  string      $substring  The stripped characters.
	 * @param  string|null $encoding   Optionally, the character encoding. If it is omitted or null, the internal
	 *                                 character encoding value will be used. Defaults to null.
	 * @return string                  Returns a string with $substring stripped from the end of the given string.
	 */
	public static function removeRight(string $string, string $substring, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($substring, $encoding);

		if (mb_substr($string, (0 - $length), null, $encoding) === $substring)
		{
			$length = mb_strlen($string, $encoding) - $length;
			$string = mb_substr($string, 0, $length, $encoding);
		}

		return $string;
	}

	//

	/**
	 * Converts double spaces in a string to a single space.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'A  B      C';
	 *
	 * $result = Str::reduceDoubleSpaces($string);
	 * // The $result will be: A B C
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the trimmed string and condensed whitespace.
	 */
	public static function reduceDoubleSpaces(string $string) : string
	{
		$string = preg_replace('!\s+!', ' ', $string);
		$string = trim($string);

		return $string;
	}

	/**
	 * Converts double slashes in a string to a single slash,
	 * except those found in http://
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'http://www.some-site.com//index.php';
	 *
	 * $result = Str::reduceDoubleSlashes($string);
	 * // The $result will be: http://www.some-site.com/index.php
	 * ```
	 *
	 * @param  string $string    The input string.
	 * @return string            Returns a string without double slashes.
	 */
	public static function reduceDoubleSlashes(string $string) : string
	{
		$string = preg_replace('#(^|[^:])//+#', '\\1/', $string);

		return $string;
	}

	//

	/**
	 * Converts the given string to lower-case.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'I LOVE YOU';
	 *
	 * $result = Str::lowerCase($string);
	 * // The $result will be: i love you
	 * ```
	 *
	 * @param  string      $string    The string being lowercased.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns string with all alphabetic characters converted to lowercase.
	 */
	public static function lowerCase(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_strtolower($string, $encoding);

		return $string;
	}

	/**
	 * Converts the first character of the string to lower case.
	 * This method provides a unicode-safe implementation of built-in PHP function `lcfirst()`.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'I LOVE YOU';
	 *
	 * $result = Str::lowerCaseFirst($string);
	 * // The $result will be: i LOVE YOU
	 * ```
	 *
	 * @param  string      $string    The string being lowercased first alphabetic characters.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns string with first alphabetic character converted to lowercase.
	 */
	public static function lowerCaseFirst(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$rest = mb_substr($string, 1, null, $encoding);

		$string = mb_strtolower($firstChar, $encoding) . $rest;

		return $string;
	}

	/**
	 * Lowercase the first character of each word in a string (unicode-safe).
	 * Note : there is no built-in PHP function 'lcwords()'.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'I LOVE YOU';
	 *
	 * $result = Str::lowerCaseWords($string);
	 * // The $result will be: i lOVE yOU
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns the modified string.
	 */
	public static function lowerCaseWords(string $string, string $encoding = null) : string
	{
		$words = explode(' ', $string);

		for ($i = 0, $n = count($words); $i < $n; ++$i)
			$words[$i] = static::lowerCaseFirst($words[$i], $encoding);

		$string = implode(' ', $words);

		return $string;
	}

	/**
	 * Converts the given string to upper-case.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'i love you';
	 *
	 * $result = Str::upperCase($string);
	 * // The $result will be: I LOVE YOU
	 * ```
	 *
	 * @param  string      $string    The string being uppercased.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns string with all alphabetic characters converted to uppercase.
	 */
	public static function upperCase(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_strtoupper($string, $encoding);

		return $string;
	}

	/**
	 * Makes a string's first character uppercase.
	 * This method provides a unicode-safe implementation of built-in PHP function `ucfirst()`.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'i love you';
	 *
	 * $result = Str::upperCaseFirst($string);
	 * // The $result will be: I love you
	 * ```
	 *
	 * @param  string      $string    The string being uppercased first alphabetic characters.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns string with first alphabetic character converted to uppercase.
	 */
	public static function upperCaseFirst(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$rest = mb_substr($string, 1, null, $encoding);

		$string = mb_strtoupper($firstChar, $encoding) . $rest;

		return $string;
	}

	/**
	 * Uppercase the first character of each word in a string.
	 * This method provides a unicode-safe implementation of built-in PHP function `ucwords()`.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'i love you';
	 *
	 * $result = Str::upperCaseWords($string);
	 * // The $result will be: I Love You
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns the modified string.
	 */
	public static function upperCaseWords(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_convert_case($string, MB_CASE_TITLE, $encoding);

		return $string;
	}

	//

	/**
	 * Returns a repeated string given a multiplier.
	 *
	 * An alias for PHP's str_repeat() function.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'A';
	 *
	 * $result = Str::repeat($string);
	 * // The $result will be: AAA
	 * ```
	 *
	 * @param  string $string      The string to repeat.
	 * @param  int    $multiplier  The number of times to repeat the string.
	 * @return string              Returns a repeated string.
	 */
	public static function repeat(string $string, int $multiplier) : string
	{
		$string = str_repeat($string, $multiplier);

		return $string;
	}

	/**
	 * This method is similar to the php function `str_replace()` except that it will support $limit.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::replace('A', '|');
	 * // The $result will be: |BCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::replace(':', '|', 1);
	 * // The $result will be: ABCDEF|eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::replace([':', '/'], ['|', '-']);
	 * // The $result will be: ABCDEF|eFMNRZa|-fabcdefa|Bmnrz
	 * ```
	 *
	 * @param  string       $string    The string being searched and replaced on.
	 * @param  string|array $search    The value being searched for. An array may be used to designate multiple
	 *                                 searches.
	 * @param  string|array $replace   The replacement value that replaces found search values. An array may be used to
	 *                                 designate multiple replacements.
	 * @param  int|null     $limit     Optionally, the maximum possible replacements for each pattern in each subject
	 *                                 string. Defaults to null (no limit).
	 * @param  string|null  $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                 character encoding value will be used. Defaults to null.
	 * @return string                  Returns a string with the replaced values.
	 */
	public static function replace(string $string, $search, $replace, int $limit = null, string $encoding = null) : string
	{
		if (!is_string($search) and !is_array($search))
			throw InvalidArgumentException::typeError(2, ['string', 'array'], $search);

		if (!is_string($replace) and !is_array($replace))
			throw InvalidArgumentException::typeError(3, ['string', 'array'], $replace);

		if (empty($search))
			return $string;

		if (is_int($limit))
		{
			$encoding = static::_getEncoding($encoding);
			$length = mb_strlen($search, $encoding);

			for ($i = 0; $i < $limit; ++$i)
			{
				$start = mb_strpos($string, $search, 0, $encoding);

				if ($start === false)
					break;

				$string = static::subreplace($string, $replace, $start, $length, $encoding);
			}
		}
		else
			$string = str_replace($search, $replace, $string);

		return $string;
	}

	/**
	 * Replaces the first occurrence of the given value in the string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::replaceFirst(':', '|');
	 * // The $result will be: ABCDEF|eFMNRZa:/fabcdefa:Bmnrz
	 * ```
	 *
	 * @param  string      $string    The string being searched and replaced on.
	 * @param  string      $search    The value being searched for.
	 * @param  string      $replace   The replacement value that replaces found search value.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a string with the replaced value.
	 */
	public static function replaceFirst(string $string, string $search, string $replace, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		$encoding = static::_getEncoding($encoding);
		$start = mb_strpos($string, $search, 0, $encoding);

		if ($start !== false)
		{
			$length = mb_strlen($search, $encoding);
			$string = static::subreplace($string, $replace, $start, $length, $encoding);
		}

		return $string;
	}

	/**
	 * Replaces the last occurrence of the given value in the string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::replaceLast(':', '|');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa|Bmnrz
	 * ```
	 *
	 * @param  string      $string    The string being searched and replaced on.
	 * @param  string      $search    The value being searched for.
	 * @param  string      $replace   The replacement value that replaces found search value.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a string with the replaced value.
	 */
	public static function replaceLast(string $string, string $search, string $replace, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		$encoding = static::_getEncoding($encoding);
		$start = mb_strrpos($string, $search, 0, $encoding);

		if ($start !== false)
		{
			$length = mb_strlen($search, $encoding);
			$string = static::subreplace($string, $replace, $start, $length, $encoding);
		}

		return $string;
	}

	/**
	 * This method is similar to the php function `str_ireplace()` except that it will support $limit.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::ireplace('a', '|');
	 * // The $result will be: |BCDEF:eFMNRZ|:/f|bcdef|:Bmnrz
	 *
	 * $result = Str::ireplace('a', '|', 1);
	 * // The $result will be: |BCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::ireplace(['a', 'b'], ['4', '8']);
	 * // The $result will be: 48CDEF:eFMNRZ4:/f48cdef4:8mnrz
	 * ```
	 *
	 * @param  string       $string    The string being searched and replaced on.
	 * @param  string|array $search    The value being searched for. An array may be used to designate multiple
	 *                                 searches.
	 * @param  string|array $replace   The replacement value that replaces found search values. An array may be used to
	 *                                 designate multiple replacements.
	 * @param  int|null     $limit     The maximum possible replacements for each pattern in each subject string.
	 *                                 Defaults to null (no limit).
	 * @param  string|null  $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                 character encoding value will be used. Defaults to null.
	 * @return string                  Returns a string with the replaced values.
	 */
	public static function ireplace(string $string, $search, $replace, int $limit = null, string $encoding = null) : string
	{
		if (!is_string($search) and !is_array($search))
			throw InvalidArgumentException::typeError(2, ['string', 'array'], $search);

		if (!is_string($replace) and !is_array($replace))
			throw InvalidArgumentException::typeError(3, ['string', 'array'], $replace);

		if (is_int($limit))
		{
			$encoding = static::_getEncoding($encoding);
			$length = mb_strlen($search, $encoding);

			for ($i = 0; $i < $limit; ++$i)
			{
				$start = mb_stripos($string, $search, 0, $encoding);

				if ($start === false)
					break;

				$string = static::subreplace($string, $replace, $start, $length, $encoding);
			}
		}
		else
			$string = str_ireplace($search, $replace, $string);

		return $string;
	}

	/**
	 * Replaces the first occurrence of the given value in the string (case-insensitive version).
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::ireplaceFirst('a', '|');
	 * // The $result will be: |BCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 * ```
	 *
	 * @param  string      $string    The string being searched and replaced on.
	 * @param  string      $search    The value being searched for.
	 * @param  string      $replace   The replacement value that replaces found search value.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a string with the replaced value.
	 */
	public static function ireplaceFirst(string $string, string $search, string $replace, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		$encoding = static::_getEncoding($encoding);
		$start = mb_stripos($string, $search, 0, $encoding);

		if ($start !== false)
		{
			$length = mb_strlen($search, $encoding);
			$string = static::subreplace($string, $replace, $start, $length, $encoding);
		}

		return $string;
	}

	/**
	 * Replaces the last occurrence of the given value in the string (case-insensitive version).
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::ireplaceLast('a', '|');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdef|:Bmnrz
	 * ```
	 *
	 * @param  string      $string    The string being searched and replaced on.
	 * @param  string      $search    The value being searched for.
	 * @param  string      $replace   The replacement value that replaces found search value.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a string with the replaced value.
	 */
	public static function ireplaceLast(string $string, string $search, string $replace, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		$encoding = static::_getEncoding($encoding);
		$start = mb_strripos($string, $search, 0, $encoding);

		if ($start !== false)
		{
			$length = mb_strlen($search, $encoding);
			$string = static::subreplace($string, $replace, $start, $length, $encoding);
		}

		return $string;
	}

	/**
	 * Replaces a copy of string delimited by the start and
	 * (optionally) length parameters with the string given
	 * in replacement. A multibyte version of substr_replace().
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::subreplace('_____', 5);
	 * // The $result will be: ABCDE_____
	 *
	 * $result = Str::subreplace('_____', 5, 5);
	 * // The $result will be: ABCDE_____NRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::subreplace('_____', 5, -5);
	 * // The $result will be: ABCDE_____Bmnrz
	 *
	 * $result = Str::subreplace('_____', -5);
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:_____
	 *
	 * $result = Str::subreplace('_____', -5, 5);
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:_____
	 *
	 * $result = Str::subreplace('_____', -15, -5);
	 * // The $result will be: ABCDEF:eFMNRZa:_____Bmnrz
	 *
	 * $result = Str::subreplace('_____', 100, 5);
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:Bmnrz_____
	 * ```
	 *
	 * @param  string      $string    The string being searched and replaced on.
	 * @param  string      $replace   The replacement value that replaces found search value.
	 * @param  int         $start     If start is positive, the replacing will begin at the start'th offset into string.
	 *                                If start is negative, the replacing will begin at the start'th character from the
	 *                                end of string.
	 * @param  int|null    $length    Optionally, if given and is positive, it represents the length of the portion of
	 *                                string which is to be replaced. If it is negative, it represents the number of
	 *                                characters from the end of string at which to stop replacing. If it is not given,
	 *                                then it will defaults to strlen( string ); i.e. end the replacing at the end of
	 *                                string. Of course, if length is zero then this function will have the effect of
	 *                                inserting replacement into string at the given start offset.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a string with the replaced value.
	 */
	public static function subreplace(string $string, string $replace, int $start, int $length = null, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$stringLength = mb_strlen($string, $encoding);

		if ($start < 0)
			$start = max(0, $stringLength + $start);
		elseif ($start > $stringLength)
			return $string . $replace;

		if ($length > $stringLength or is_null($length))
			$length = $stringLength;
		elseif ($length < 0)
			$length = max(0, $stringLength - $start + $length);

		$block1 = mb_substr($string, 0, $start, $encoding);
		$block2 = mb_substr($string, ($start + $length), ($stringLength - $start - $length), $encoding);

		$string = $block1 . $replace . $block2;

		return $string;
	}

	/**
	 * Replaces the given value in the string sequentially with an array.
	 *
	 * For example,
	 *
	 * ```php
	 * // If $replaces is numeric array.
	 * $string = 'My name is ? and ? years old.';
	 * $result = Str::insert($string, '?', ['Nat', 38]);
	 *
	 * // The $result will be: My name is Nat and 38 years old.
	 *
	 * // If $replaces is associative array.
	 * $string = 'My name is :name and :age years old.';
	 * $result = Str::insert($string, ':', ['name' => 'Nat', 'age' => 38]);
	 *
	 * // The $result will be: My name is Nat and 38 years old.
	 * ```
	 *
	 * @param  string $string       The string being searched and replaced on.
	 * @param  string $placeholder  The character being searched for.
	 * @param  array  $replaces     The replacement value that replaces found search value (numeric/associative array).
	 * @return string               Returns a string with the replaced value.
	 */
	public static function insert(string $string, string $placeholder, array $replaces) : string
	{
		if ($placeholder === '')
			return $string;

		if (Arr::isAssociative($replaces))
		{
			foreach ($replaces as $key => $value)
				$string = str_replace($placeholder . $key, (string)$value, $string);
		}
		else
		{
			$blocks = explode($placeholder, $string);
			$string = '';

			for ($i = 0, $n = count($blocks); $i < $n; ++$i)
			{
				$string .= $blocks[$i];

				if ($i < ($n - 1))
					$string .= ($replaces[$i] ?? $placeholder);
			}
		}

		return $string;
	}

	/**
	 * Returns a reversed string. A multibyte version of strrev().
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::reverse($string);
	 * // The $result will be: zrnmB:afedcbaf/:aZRNMFe:FEDCBA
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns a reversed string.
	 */
	public static function reverse(string $string, string $encoding = null) : string
	{
		$reversed = '';
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($string, $encoding);

		for ($i = $length - 1; $i >= 0; --$i)
			$reversed .= mb_substr($string, $i, 1, $encoding);

		return $reversed;
	}

	//

	/**
	 * Determines if the given string starts with the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::startsWith($string, 'A');
	 * // The $result will be: true
	 *
	 * * $result = Str::startsWith($string, 'a', false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The string to search in.
	 * @param  string      $prefix         The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if the given string begins with the given substring, false
	 *                                     otherwise.
	 */
	public static function startsWith(string $string, string $prefix, bool $caseSensitive = true, string $encoding = null) : bool
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($prefix, $encoding);
		$search = mb_substr($string, 0, $length, $encoding);

		if (!$caseSensitive)
		{
			$search = mb_strtolower($search, $encoding);
			$prefix = mb_strtolower($prefix, $encoding);
		}

		$result = ($search === $prefix);

		return $result;
	}

	/**
	 * Determines if the given string starts with any of given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::startsWithAny($string, ['A', 'B']);
	 * // The $result will be: true
	 *
	 * * $result = Str::startsWithAny($string, ['a', 'b'], false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The string to search in.
	 * @param  array       $prefixes       The substrings to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if the given string begins with any given substring, false
	 *                                     otherwise.
	 */
	public static function startsWithAny(string $string, array $prefixes, bool $caseSensitive = true, string $encoding = null) : bool
	{
		foreach ($prefixes as $prefix)
		{
			if (static::startsWith($string, $prefix, $caseSensitive, $encoding))
				return true;
		}

		return false;
	}

	/**
	 * Determines if the given string ends with the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::endsWith($string, 'z');
	 * // The $result will be: true
	 *
	 * * $result = Str::endsWith($string, 'Z', false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The string to search in.
	 * @param  string      $suffix         The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if the given string ends with the given substring, false
	 *                                     otherwise.
	 */
	public static function endsWith(string $string, string $suffix, bool $caseSensitive = true, string $encoding = null) : bool
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($suffix, $encoding);
		$search = mb_substr($string, (0 - $length), null, $encoding);

		if (!$caseSensitive)
		{
			$search = mb_strtolower($search, $encoding);
			$suffix = mb_strtolower($suffix, $encoding);
		}

		$result = ($search === $suffix);

		return $result;
	}

	/**
	 * Determines if the given string ends with any of given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::endsWithAny($string, ['z', 'b']);
	 * // The $result will be: true
	 *
	 * * $result = Str::endsWithAny($string, ['Z', 'B'], false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The string to search in.
	 * @param  array       $suffixes       The substrings to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if the given string ends with any given substring, false
	 *                                     otherwise.
	 */
	public static function endsWithAny(string $string, array $suffixes, bool $caseSensitive = true, string $encoding = null) : bool
	{
		foreach ($suffixes as $suffix)
		{
			if (static::endsWith($string, $suffix, $caseSensitive, $encoding))
				return true;
		}

		return false;
	}

	/**
	 * Adds a single instance of the given value to the beginning of
	 * a string if it does not already start with the value.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::ensureStartsWith($string, 'ABC');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::ensureStartsWith($string, '_');
	 * // The $result will be: _ABCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 * ```
	 *
	 * @param  string      $string    The input tstring.
	 * @param  string      $prefix    The value to search for at the beginning of the given string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns string with the given value at the beginning of the string.
	 */
	public static function ensureStartsWith(string $string, string $prefix, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($prefix, $encoding);

		if (mb_substr($string, 0, $length, $encoding) !== $prefix)
			$string = $prefix . $string;

		return $string;
	}

	/**
	 * Adds a single instance of the given value to the end of
	 * a string if it does not already end with the value.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::ensureEndsWith($string, 'nrz');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::ensureEndsWith($string, '_');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:Bmnrz_
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string      $suffix    The value to search for at the end of the given string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return string                 Returns string with the given value at the end of the string.
	 */
	public static function ensureEndsWith(string $string, string $suffix, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$length = mb_strlen($suffix, $encoding);

		if (mb_substr($string, (0 - $length), null, $encoding) !== $suffix)
			$string .= $suffix;

		return $string;
	}

	/**
	 * Adds a single instance of the given value to a string
	 * if it does not already start and end with the value.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::wrap('', '|');
	 * // The $result will be: ||
	 *
	 * $result = Str::wrap('value', '|');
	 * // The $result will be: |value|
	 * ```
	 *
	 * @param  string      $string     The string being wrapped.
	 * @param  string      $character  The character to search for in the given string.
	 * @param  string|null $encoding   Optionally, the character encoding. If it is omitted or null, the internal
	 *                                 character encoding value will be used. Defaults to null.
	 * @return string                  Returns string with the given value at the beginning and end of the string.
	 */
	public static function wrap(string $string, string $character, string $encoding = null) : string
	{
		if (empty($string))
			return $character . $character;

		$string = static::ensureStartsWith($string, $character, $encoding);
		$string = static::ensureEndsWith($string, $character, $encoding);

		return $string;
	}

	//

	/**
	 * Returns the remainder of a string after the first occurrence of the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::after($string, '');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::after($string, 'r');
	 * // The $result will be: z
	 *
	 * $result = Str::after($string, 'r', false);
	 * // The $result will be: Za:/fabcdefa:Bmnrz
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  string      $search         The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return string                      Returns the remainder of the given string.
	 */
	public static function after(string $string, string $search, bool $caseSensitive = true, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		if ($caseSensitive)
			return array_reverse(explode($search, $string, 2))[0];
		else
		{
			$encoding = static::_getEncoding($encoding);
			$start = mb_stripos($string, $search, 0, $encoding);
			$start += mb_strlen($search, $encoding);
			$string = mb_substr($string, $start, null, $encoding);

			return $string;
		}
	}

	/**
	 * Returns the remainder of a string after the last occurrence of the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::afterLast($string, '');
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:Bmnrz
	 *
	 * $result = Str::afterLast($string, 'b');
	 * // The $result will be: cdefa:Bmnrz
	 *
	 * $result = Str::afterLast($string, 'b', false);
	 * // The $result will be: mnrz
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  string      $search         The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return string                      Returns the remainder of the given string.
	 */
	public static function afterLast(string $string, string $search, bool $caseSensitive = true, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		if ($caseSensitive)
			return array_reverse(explode($search, $string))[0];
		else
		{
			$encoding = static::_getEncoding($encoding);
			$start = mb_strripos($string, $search, 0, $encoding);
			$start += mb_strlen($search, $encoding);
			$string = mb_substr($string, $start, null, $encoding);

			return $string;
		}
	}

	/**
	 * Returns the portion of a string before the first occurrence of the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::before($string, 'e');
	 * // The $result will be: ABCDEF
	 *
	 * $result = Str::before($string, 'e', false);
	 * // The $result will be: ABCD
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  string      $search         The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return string                      Returns the portion of the given string.
	 */
	public static function before(string $string, string $search, bool $caseSensitive = true, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		if ($caseSensitive)
			return explode($search, $string)[0];
		else
		{
			$encoding = static::_getEncoding($encoding);
			$pos = mb_stripos($string, $search, 0, $encoding);
			$string = mb_substr($string, 0, $pos, $encoding);

			return $string;
		}
	}

	/**
	 * Returns the portion of a string before the last occurrence of the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::beforeLast($string, 'b');
	 * // The $result will be: ABCDEF:eFMNRZa:/fa
	 *
	 * $result = Str::beforeLast($string, 'b', false);
	 * // The $result will be: ABCDEF:eFMNRZa:/fabcdefa:
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  string      $search         The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return string                      Returns the portion of the given string.
	 */
	public static function beforeLast(string $string, string $search, bool $caseSensitive = true, string $encoding = null) : string
	{
		if ($search === '')
			return $string;

		$encoding = static::_getEncoding($encoding);

		if ($caseSensitive)
			$pos = mb_strrpos($string, $search, 0, $encoding);
		else
			$pos = mb_strripos($string, $search, 0, $encoding);

		if ($pos === false)
			return $string;

		$string = mb_substr($string, 0, $pos, $encoding);

		return $string;
	}

	//

	/**
	 * Highlights a phrase within the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = '<html>html</html>';
	 *
	 * $result = Str::highlight($string, 'html');
	 * // The $result will be: <html><mark>html</mark></html>
	 *
	 * $result = Str::highlight($string, 'html', '<start>', '</end>', false);
	 * // The $result will be: <<start>html</end>><start>html</end></<start>html</end>>
	 * ```
	 *
	 * @param  string $string    The input string.
	 * @param  string $phrase    The phrase you would like to highlight.
	 * @param  string $tagOpen   Optionally, the opening tag to precede the phrase with. Defaults to <mark>.
	 * @param  string $tagClose  Optionally, the closing tag to end the phrase with. Defaults to </mark>.
	 * @param  bool   $html      Optionally, if true, will ignore any HTML tags, ensuring that only the correct text is
	 *                           highlighted. Defaults to true.
	 * @return string            Returns a highlighted string.
	 */
	public static function highlight(string $string, string $phrase, string $tagOpen = '<mark>', string $tagClose = '</mark>', bool $html = true): string
	{
		if ($string !== '' and $phrase !== '')
		{
			$phrase = '(' . preg_quote($phrase, '/') . ')';

			if ($html)
				$phrase = '(?![^<]+>)' . $phrase . '(?![^<]+>)';

			$phrase = '/' . $phrase . '/iu';

			$string = preg_replace($phrase, $tagOpen . '\\1' . $tagClose, $string);
		}

		return $string;
	}

	/**
	 * Adds _1 to the given string or increment the ending number to allow _2, _3, etc.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::increment('file');
	 * // The $result will be: file_1
	 *
	 * $result = Str::increment('file_1');
	 * // The $result will be: file_2
	 *
	 * $result = Str::increment('file', '-', 100);
	 * // The $result will be: file-100
	 *
	 * $result = Str::increment('file-100', '-', 100);
	 * // The $result will be: file-101
	 * ```
	 *
	 * @param  string $string     The input string.
	 * @param  string $separator  Optionally, what should the duplicate number be appended with. Defaults to '_'.
	 * @param  int    $first      Optionally, which number should be used for the first dupe increment. Defaults to 1.
	 * @return string             Returns a string with number.
	 */
	public static function increment(string $string, string $separator = '_', int $first = 1) : string
	{
		preg_match('/(.+)' . preg_quote($separator, '/') . '([0-9]+)$/', $string, $match);

		if (isset($match[2]))
			$string = $match[1] . $separator . ($match[2] + 1);
		else
			$string = $string . $separator . $first;

		return $string;
	}

	/**
	 * Safely casts a float to string independent of the current locale.
	 *
	 * The decimal separator will always be `.`.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::floatToString(3,14);
	 * // The $result will be: 3.14 (string)
	 * ```
	 *
	 * @param  float|int $number  A floating point number or integer.
	 * @return string             Returns a string representation of the number.
	 */
	public static function floatToString($number) : string
	{
		if (!is_float($number) and !is_int($number))
			throw InvalidArgumentException::typeError(1, ['float', 'int'], $number);

		$string = str_replace(',', '.', (string)$number);

		return $string;
	}

	/**
	 * Converts each occurrence of some consecutive number of spaces, as defined by $tabLength, to a tab.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::spaceToTab('    ');
	 * // The $result will be: \t
	 * ```
	 *
	 * @param  string $string     The input string.
	 * @param  int    $tabLength  Optionally, number of spaces to replace with a tab. Defaults to 4.
	 * @return string             Returns a string with the replaced tabs.
	 */
	public static function spaceToTab(string $string, int $tabLength = 4) : string
	{
		$space = str_repeat(' ', $tabLength);
		$string = str_replace($space, "\t", $string);

		return $string;
	}

	/**
	 * Converts each tab in the string to some number of spaces, as defined by $tabLength.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::tabToSpace("\t");
	 * // The $result will be: '    '
	 * ```
	 *
	 * @param  string $string     The input string.
	 * @param  int    $tabLength  Optionally, number of spaces to replace each tab with. Defaults to 4.
	 * @return string             Returns a string with the replaced spaces.
	 */
	public static function tabToSpace(string $string, int $tabLength = 4) : string
	{
		$space = str_repeat(' ', $tabLength);
		$string = str_replace("\t", $space, $string);

		return $string;
	}

	//

	/**
	 * Makes a string as long as the first argument by adding
	 * the given $padString at the both sides of the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'string';
	 *
	 * $result = Str::pad($string, '_', 5);
	 * // The $result will be: string
	 *
	 * $result = Str::pad($string, '_', 10);
	 * // The $result will be: __string__
	 * ```
	 *
	 * @param  string $string     The input string.
	 * @param  string $padString  The pad string.
	 * @param  int    $length     If the value of length is negative, less than, or equal to the length of the input
	 *                            string, no padding takes place, and string will be returned.
	 * @return string             Returns the padded string.
	 */
	public static function pad(string $string, string $padString, int $length) : string
	{
		$string = str_pad($string, $length, $padString, STR_PAD_BOTH);

		return $string;
	}

	/**
	 * Makes a string as long as the first argument by adding
	 * the given $padString at the beginning of the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'string';
	 *
	 * $result = Str::padLeft($string, '_', 5);
	 * // The $result will be: string
	 *
	 * $result = Str::padLeft($string, '_', 10);
	 * // The $result will be: ____string
	 * ```
	 *
	 * @param  string $string     The input string.
	 * @param  string $padString  The pad string.
	 * @param  int    $length     If the value of length is negative, less than, or equal to the length of the input
	 *                            string, no padding takes place, and string will be returned.
	 * @return string             Returns the padded string.
	 */
	public static function padLeft(string $string, string $padString, int $length) : string
	{
		$string = str_pad($string, $length, $padString, STR_PAD_LEFT);

		return $string;
	}

	/**
	 * Makes a string as long as the first argument by adding
	 * the given $padString at the end of the given string.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'string';
	 *
	 * $result = Str::padRight($string, '_', 5);
	 * // The $result will be: string
	 *
	 * $result = Str::padRight($string, '_', 10);
	 * // The $result will be: string____
	 * ```
	 *
	 * @param  string $string     The input string.
	 * @param  string $padString  The pad string.
	 * @param  int    $length     If the value of length is negative, less than, or equal to the length of the input
	 *                            string, no padding takes place, and string will be returned.
	 * @return string             Returns the padded string.
	 */
	public static function padRight(string $string, string $padString, int $length) : string
	{
		$string = str_pad($string, $length, $padString, STR_PAD_RIGHT);

		return $string;
	}

	//

	/**
	 * Determines if the given string contains only whitespace characters.
	 * Support checking '0' in 'if' statement.
	 *
	 * $input = '0';
	 *
	 * if ($input)
	 * ....do something...
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isBlank(0);
	 * // The $result will be: false
	 *
	 * $result = Str::isBlank('0');
	 * // The $result will be: false
	 *
	 * $result = Str::isBlank(null);
	 * // The $result will be: true
	 *
	 * $result = Str::isBlank(' ');
	 * // The $result will be: true
	 *
	 * $result = Str::isBlank(false);
	 * // The $result will be: true
	 *
	 * $result = Str::isBlank(true);
	 * // The $result will be: false
	 * ```
	 *
	 * @param  mixed $string  The input string.
	 * @return bool           Returns true if the string contains only whitespace chars, false otherwise.
	 */
	public static function isBlank($string) : bool
	{
		$string = (string)$string;

		if (!strlen(trim($string)))
			return true;
		else
			return false;
	}

	/**
	 * Determines if the given string contains only alphabetic characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isAlpha(416);
	 * // The $result will be: false
	 *
	 * $result = Str::isAlpha('416');
	 * // The $result will be: false
	 *
	 * $result = Str::isAlpha('Valkyrie416');
	 * // The $result will be: false
	 *
	 * $result = Str::isAlpha('Valkyrie');
	 * // The $result will be: true
	 * ```
	 *
	 * @param  mixed $string  The input string.
	 * @return bool           Returns true if the string contains only alphabetic chars, false otherwise.
	 */
	public static function isAlpha($string) : bool
	{
		$string = (string)$string;
		$result = mb_ereg_match('^[[:alpha:]]*$', $string);

		return $result;
	}

	/**
	 * Determines if the given string contains only alphabetic and numeric characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isAlnum(416);
	 * // The $result will be: true
	 *
	 * $result = Str::isAlnum('416');
	 * // The $result will be: true
	 *
	 * $result = Str::isAlnum('Valkyrie416');
	 * // The $result will be: true
	 *
	 * $result = Str::isAlnum('Valkyrie');
	 * // The $result will be: true
	 *
	 * $result = Str::isAlnum('Valkyrie!');
	 * // The $result will be: false
	 * ```
	 *
	 * @param  mixed $string  The input string.
	 * @return bool           Returns true if the string contains only alphabetic and numeric chars, false otherwise.
	 */
	public static function isAlnum($string) : bool
	{
		$string = (string)$string;
		$result = mb_ereg_match('^[[:alnum:]]*$', $string);

		return $result;
	}

	/**
	 * Determines if the given string is base64 encoded.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isBase64Encoded('TmF0IFdpdGhl');
	 * // The $result will be: true
	 *
	 * $result = Str::isBase64Encoded('I love you');
	 * // The $result will be: false
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string is base64 encoded, false otherwise.
	 */
	public static function isBase64Encoded(string $string) : bool
	{
		$decoded = base64_decode($string);
		$encoded = base64_encode($decoded);

		$result = ($encoded === $string);

		return $result;
	}

	/**
	 * Determines if the given string contains only hexadecimal characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isHexadecimal('D1CE');
	 * // The $result will be: true
	 *
	 * $result = Str::isHexadecimal('D1ZE');
	 * // The $result will be: false
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string contains only hexadecimal chars, false otherwise.
	 */
	public static function isHexadecimal(string $string) : bool
	{
		$result = mb_ereg_match('^[[:xdigit:]]*$', $string);

		return $result;
	}

	/**
	 * Determines if the given string contains only lower case characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isLowerCase('Abc');
	 * // The $result will be: false
	 *
	 * $result = Str::isLowerCase('abc');
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string contains only lower case chars, false otherwise.
	 */
	public static function isLowerCase(string $string) : bool
	{
		$result = mb_ereg_match('^[[:lower:]]*$', $string);

		return $result;
	}

	/**
	 * Determines if the given string contains only upper case characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isUpperCase('Abc');
	 * // The $result will be: false
	 *
	 * $result = Str::isUpperCase('ABC');
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string contains only upper case chars, false otherwise.
	 */
	public static function isUpperCase(string $string) : bool
	{
		$result = mb_ereg_match('^[[:upper:]]*$', $string);

		return $result;
	}

	/**
	 * Determines if the given string is serialized.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isSerialized('b:0;');
	 * // The $result will be: true
	 *
	 * $result = Str::isSerialized('a:0:{}');
	 * // The $result will be: true
	 *
	 * $result = Str::isSerialized('string');
	 * // The $result will be: false
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string is serialized, false otherwise.
	 */
	public static function isSerialized(string $string) : bool
	{
		// Serialized string of boolean false is 'b:0;'.
		// The result of @unserialize('b:0;') is false.
		// So, the below code...
		//
		// if (@unserialize($string) !== false)
		//
		// is
		//
		// if (false !== false)
		//
		// That will always return false.

		if ($string === 'b:0;' or @unserialize($string) !== false)
			return true;
		else
			return false;
	}

	/**
	 * Determines if the given string contains multibyte characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::isMultibyte('NAT WITHE');
	 * // The $result will be: false
	 *
	 * $result = Str::isMultibyte('ŅÀŦ ŴĨŦĤÈ');
	 * // The $result will be: true
	 *
	 * $result = Str::isMultibyte('นัทเองไงจะใครล่ะ');
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string contain multibyte characters, false otherwise.
	 */
	public static function isMultibyte(string $string) : bool
	{
		$length = strlen($string);

		for ($i = 0; $i < $length; ++$i)
		{
			if (ord($string[$i]) > 128)
				return true;
		}

		return false;
	}

	//

	/**
	 * Determines if the given string contains the given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::contains('za');
	 * // The $result will be: false
	 *
	 * $result = Str::contains('za', false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  string      $substring      The substring to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if the given substring is in the given string, false otherwise.
	 */
	public static function contains(string $string, string $substring, bool $caseSensitive = true, string $encoding = null) : bool
	{
		$encoding = static::_getEncoding($encoding);

		if ($caseSensitive)
			$result = ($substring !== '' and mb_strpos($string, $substring, 0, $encoding) !== false);
		else
			$result = ($substring !== '' and mb_stripos($string, $substring, 0, $encoding) !== false);

		return $result;
	}

	/**
	 * Determines if the given string contains any given substring.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::containsAny(['za', 'NoneExistingChar']);
	 * // The $result will be: false
	 *
	 * $result = Str::containsAny(['za', 'NoneExistingChar'], false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  array       $substrings     The substrings to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if any given substring is in the given string, false
	 *                                     otherwise.
	 */
	public static function containsAny(string $string, array $substrings, bool $caseSensitive = true, string $encoding = null) : bool
	{
		foreach ($substrings as $substring)
		{
			if (static::contains($string, $substring, $caseSensitive, $encoding))
				return true;
		}

		return false;
	}

	/**
	 * Determines if the given string contains all given substrings.
	 *
	 * For example,
	 *
	 * ```php
	 * $string = 'ABCDEF:eFMNRZa:/fabcdefa:Bmnrz';
	 *
	 * $result = Str::containsAll(['Za', 'NoneExistingChar']);
	 * // The $result will be: false
	 *
	 * $result = Str::containsAll(['za', 'bm']);
	 * // The $result will be: false
	 *
	 * $result = Str::containsAll(['za', 'bm'], false);
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string      $string         The input string.
	 * @param  array       $substrings     The substrings to search for in the given string.
	 * @param  bool        $caseSensitive  Optionally, whether or not to enforce case-sensitivity. Defaults to true.
	 * @param  string|null $encoding       Optionally, the character encoding. If it is omitted or null, the internal
	 *                                     character encoding value will be used. Defaults to null.
	 * @return bool                        Returns true if all given substrings are in the given string, false
	 *                                     otherwise.
	 */
	public static function containsAll(string $string, array $substrings, bool $caseSensitive = true, string $encoding = null) : bool
	{
		if (empty($substrings))
			return false;

		foreach ($substrings as $substring)
		{
			if (!static::contains($string, $substring, $caseSensitive, $encoding))
				return false;
		}

		return true;
	}

	/**
	 * Determines if the given string contains a lower case character.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::hasLowerCase('ABC');
	 * // The $result will be: false
	 *
	 * $result = Str::hasLowerCase('Abc');
	 * // The $result will be: true
	 *
	 * $result = Str::hasLowerCase('abc');
	 * // The $result will be: true
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string contains a lower case character, false otherwise.
	 */
	public static function hasLowerCase(string $string) : bool
	{
		$result = mb_ereg_match('.*[[:lower:]]', $string);

		return $result;
	}

	/**
	 * Determines if the given string contains an upper case character.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::hasUpperCase('ABC');
	 * // The $result will be: true
	 *
	 * $result = Str::hasUpperCase('Abc');
	 * // The $result will be: true
	 *
	 * $result = Str::hasUpperCase('abc');
	 * // The $result will be: false
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return bool            Returns true if the string contains an upper case character, false otherwise.
	 */
	public static function hasUpperCase(string $string) : bool
	{
		$result = mb_ereg_match('.*[[:upper:]]', $string);

		return $result;
	}

	//

	/**
	 * Returns an array consisting of the characters in the string.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::chars('ABC');
	 * // The $result will be:
	 * // [
	 * //     0 => 'A',
	 * //     1 => 'B',
	 * //     2 => 'C'
	 * // ]
	 * ```
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  Optionally, the character encoding. If it is omitted or null, the internal
	 *                                character encoding value will be used. Defaults to null.
	 * @return array                  Returns an array of string characters.
	 */
	public static function chars(string $string, string $encoding = null) : array
	{
		$encoding = static::_getEncoding($encoding);
		$chars = [];

		for ($i = 0, $n = mb_strlen($string, $encoding); $i < $n; ++$i)
			$chars[] = mb_substr($string, $i, 1, $encoding);

		return $chars;
	}

	/**
	 * Splits on newlines and carriage returns, returning an array.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::lines("ABC\nDEF");
	 * // The $result will be:
	 * // [
	 * //     0 => 'ABC',
	 * //     1 => 'DEF'
	 * // ]
	 *
	 * $result = Str::lines("ABC\rDEF");
	 * // The $result will be:
	 * // [
	 * //     0 => 'ABC',
	 * //     1 => 'DEF'
	 * // ]
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return array           Returns an array.
	 */
	public static function lines(string $string) : array
	{
		$array = mb_split('[\r\n]{1,2}', $string);

		return $array;
	}

	/**
	 * Built-in PHP function explode() not allow $string to null.
	 * This alternative explode function accept null and removes
	 * whitespace and other predefined characters from both sides
	 * of each element of an output array, and skips empty ones.
	 *
	 * The difference between built-in PHP function explode() and
	 * this method is in case of $input = '' (empty string).
	 *
	 * explode(',', $input) will return [''] (array with empty string).
	 *
	 * but
	 *
	 * Str::explode($input, ',') will returns [] (empty array).
	 *
	 * An optional integer $limit will truncate the results.
	 *
	 * @param  string|null $string
	 * @param  string      $delimeter
	 * @param  int|null    $limit
	 * @return array
	 */
	public static function explode(string $string = null, string $delimeter = ' ', int $limit = null) : array
	{
		if ($limit === 0)
			return [];

		$string = (string)$string;

		if (mb_strlen($string)) // $string can be '0'.
		{
			$array = explode($delimeter, $string);
			$array = array_map('trim', $array);

			if (is_int($limit) and $limit != 0)
			{
				if ($limit < 0)
					$output = Arr::last($array, abs($limit));
				else // > 0
					$output = Arr::first($array, $limit);
			}
			else // null or 0
				$output = $array;
		}
		else
			$output = [];

		return $output;
	}

	/**
	 * A multibyte str_shuffle() function. It returns a string with its characters in random order.
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function shuffle(string $string) : string
	{
		$tmp = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
		shuffle($tmp);
		$string = implode('', $tmp);

		return $string;
	}

	//

	/**
	 * Creates a random string.
	 * For security purposes, use Security::random() instead.
	 *
	 * Type:
	 *	alpha:   A string with lower and uppercase letters only.
	 *	alnum:   Alpha-numeric string with lower and uppercase characters.
	 *	num: Numeric string.
	 *	nozero:  Numeric string with no zeros.
	 *
	 * @param  int    $length  The length of string to create.
	 * @param  string $type    Optionally, type of character to ramdom, alnum|alpha|num|nozero. Defaults to alnum.
	 * @return string          Returns a random string.
	 */
	public static function random(int $length = 8, string $type = 'alnum') : string
	{
		switch ($type)
		{
			case 'alpha':
				$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			case 'num':
				$characters = '0123456789';
				break;

			case 'nozero':
				$characters = '123456789';
				break;

			default: // alnum
				// Unfriendly and easily mistaken characters (i o 0 1 l O) are excluded.
				$characters = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
		}

		$multiply = ceil($length / strlen($characters));
		$characters = str_repeat($characters, (int)$multiply);
		$characters = str_shuffle($characters);
		$string = substr($characters, 0, $length);

		return $string;
	}

	/**
	 * Encodes data with MIME base64.
	 *
	 * An alias for PHP's base64_encode() function.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::base64encode('Nat Withe');
	 * // The $result will be: TmF0IFdpdGhl
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the encoded data, as a string.
	 */
	public static function base64encode(string $string) : string
	{
		$string = base64_encode($string);

		return $string;
	}

	/**
	 * Decodes data encoded with MIME base64.
	 *
	 * An alias for PHP's base64_decode() function.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::base64decode('TmF0IFdpdGhl');
	 * // The $result will be: Nat Withe
	 * ```
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the decoded data or false on failure. The returned data may be binary.
	 */
	public static function base64decode(string $string) : string
	{
		$string = base64_decode($string);

		return $string;
	}

	/**
	 * Generates a random UUID version 4.
	 *
	 * Warning: This method should not be used as a random seed for any cryptographic operations.
	 * Instead you should use the openssl or mcrypt extensions.
	 *
	 * It should also not be used to create identifiers that have security implications, such as
	 * 'unguessable' URL identifiers. Instead you should use `Security::randomBytes()` for that.
	 *
	 * @see       https://www.ietf.org/rfc/rfc4122.txt
	 * @return    string RFC 4122 UUID
	 * @copyright Matt Farina MIT License https://github.com/lootils/uuid/blob/master/LICENSE
	 * @throws    Exception
	 */
	public static function uuid(): string
	{
		$uuid = sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			random_int(0, 65535),
			random_int(0, 65535),
			// 16 bits for "time_mid"
			random_int(0, 65535),
			// 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
			random_int(0, 4095) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			random_int(0, 0x3fff) | 0x8000,
			// 48 bits for "node"
			random_int(0, 65535),
			random_int(0, 65535),
			random_int(0, 65535)
		);

		return $uuid;
	}

	/**
	 * Normalise a string replacing foreign characters.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = Str::normalize('ŅÀŦ ŴĨŦĤÈ');
	 * // The $result will be: NAT WITHE
	 * ```
	 *
	 * @param  string $string  The input string to normalise.
	 * @return string          Returns normalised string.
	 */
	public static function normalize(string $string) : string
	{
		$from = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É',
			'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô',
			'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á',
			'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì',
			'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù',
			'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą',
			'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ',
			'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě',
			'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ',
			'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı',
			'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ',
			'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň',
			'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ',
			'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š',
			'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū',
			'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ',
			'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ',
			'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ',
			'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ',
			'ǽ', 'Ǿ', 'ǿ'];

		$to = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E',
			'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O',
			'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a',
			'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i',
			'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u',
			'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a',
			'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
			'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e',
			'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H',
			'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
			'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L',
			'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n',
			'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r',
			'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
			's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u',
			'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y',
			'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O',
			'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u',
			'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE',
			'ae', 'O', 'o'];

		$string = str_replace($from, $to, $string);

		return $string;
	}
}