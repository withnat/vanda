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

use Exception;
use System\Exception\InvalidArgumentException;

/**
 * Class Str
 * @package System
 */
final class Str
{
	private static $_encoding = null;
	/**
	 * Str constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string|null $encoding
	 * @return string
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
	 * An alias for PHP's mb_strlen() function.
	 *
	 * @param  string      $string    The string being measured for length.
	 * @param  string|null $encoding  The character encoding.
	 * @return int                    The length of the string on success, and 0 if the string is empty.
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
	 * @param  string      $string         The input string.
	 * @param  string      $substring      The substring to search for.
	 * @param  bool        $caseSensitive  Whether or not to enforce case-sensitivity.
	 * @param  string|null $encoding       The character encoding.
	 * @return int                         The number of $substring occurrences.
	 */
	public static function count(string $string, string $substring, bool $caseSensitive = true, string $encoding = null) : int
	{
		$encoding = static::_getEncoding($encoding);

		if (!$caseSensitive)
		{
			$string = mb_strtoupper($string, $encoding);
			$substring = mb_strtoupper($substring, $encoding);
		}

		return mb_substr_count($string, $substring, $encoding);
	}

	/**
	 * Counts words in a string.
	 *
	 * @param  string $string
	 * @return int
	 */
	public static function countWords(string $string) : int
	{
		return count(preg_split('/\s+/u', $string, -1, PREG_SPLIT_NO_EMPTY));
	}

	//

	/**
	 * Returns the first $length (leading) characters of the given string.
	 *
	 * @param  string      $string
	 * @param  int         $length
	 * @param  string|null $encoding
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
	 * @param  string      $string
	 * @param  int         $length
	 * @param  string|null $encoding
	 * @return string
	 */
	public static function right(string $string, int $length = 1, string $encoding = null) : string
	{
		if ($length == 0)
			return '';

		$encoding = static::_getEncoding($encoding);

		$string = mb_substr($string, (0 - $length), null, $encoding);

		return $string;
	}

	/**
	 * Returns the character at $index, with indexes starting at 0.
	 *
	 * @param  string      $string
	 * @param  int         $index
	 * @param  string|null $encoding
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
	 * @param  string      $string
	 * @param  int         $start
	 * @param  int|null    $length
	 * @param  string|null $encoding
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
	 * @param  string      $string    The string to truncate.
	 * @param  int         $length    How many characters from original string to include into truncated string.
	 * @param  string      $suffix    String to append to the end of the truncated string.
	 * @param  string|null $encoding  The character encoding.
	 * @return string                 The truncated string.
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
	 * @param  string      $string    The string to truncate.
	 * @param  int         $words     How many words from original string to include into truncated string.
	 * @param  string      $suffix    String to append to the end of truncated string.
	 * @param  string|null $encoding  The character encoding.
	 * @return string                 The truncated string.
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
	 * Find position of first occurrence of string in a string.
	 * Accepts an optional offset from which to begin the search.
	 *
	 * @param  string      $string    The string being checked.
	 * @param  string      $search    Substring to look for.
	 * @param  int         $offset    Offset from which to search.
	 * @param  string|null $encoding  The character encoding.
	 * @return int|false              The first occurrence's index if found, otherwise false.
	 */
	public static function position(string $string, string $search, int $offset = 0, string $encoding = null)
	{
		$encoding = static::_getEncoding($encoding);
		$pos = mb_strpos($string, $search, $offset, $encoding);

		return $pos;
	}

	/**
	 * Find position of last occurrence of string in a string.
	 * Accepts an optional offset from which to begin the search.
	 *
	 * @param  string      $string    The string being checked.
	 * @param  string      $search    Substring to look for.
	 * @param  int         $offset    Offset from which to search.
	 * @param  string|null $encoding  The character encoding.
	 * @return int|false              The last occurrence's index if found, otherwise false.
	 */
	public static function lastPosition(string $string, string $search, int $offset = 0, string $encoding = null)
	{
		$encoding = static::_getEncoding($encoding);
		$pos = mb_strrpos($string, $search, $offset, $encoding);

		return $pos;
	}

	/**
	 * Returns the substring between $start and $end, if found, or an empty string.
	 *
	 * @param  string      $string    The string being checked.
	 * @param  string      $start     The start of the substring.
	 * @param  string      $end       The end of the substring.
	 * @param  int         $offset    Offset from which to search.
	 * @param  string|null $encoding  The character encoding.
	 * @return string                 A substring between $start and $end.
	 */
	public static function between(string $string, string $start, string $end, int $offset = 0, string $encoding = null) : string
	{
		if ($offset < 0)
			throw InvalidArgumentException::value(4, '$offset must be greater than zero', $offset);

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
	 * Strip whitespace (or other characters) from the beginning and end of a string.
	 *
	 * @param  string          $string
	 * @param  string|int|null $characterMask
	 * @param  string|null     $encoding
	 * @return string
	 */
	public static function trim(string $string, $characterMask = null, string $encoding = null) : string
	{
		if (is_string($characterMask) or is_null($characterMask) or is_int($characterMask))
		{
			$string = static::trimLeft($string, $characterMask, $encoding);
			$string = static::trimRight($string, $characterMask, $encoding);
		}
		else
			throw InvalidArgumentException::type(2, ['string','int','null'], $characterMask);

		return $string;
	}

	/**
	 * Strip whitespace (or other characters) from the beginning of a string.
	 *
	 * @param  string          $string         The input string.
	 * @param  string|int|null $characterMask  Built-in PHP function ltrim() not allow $characterMask to number.
	 * @param  string|null     $encoding       The character encoding.
	 * @return string                          This function returns a string depends on $characterMask data type.
	 *                                         If $characterMask is null : returns a string with whitespace stripped from the beginning of $string.
	 *                                         If $characterMask is string : returns a string with characters stripped from the beginning of $string.
	 *                                         If $characterMask is int : returns a part of string, start at a specified position by $characterMask.
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
			throw InvalidArgumentException::type(2, ['string','int','null'], $characterMask);

		return $string;
	}

	/**
	 * Strip whitespace (or other characters) from the end of a string.
	 *
	 * @param  string          $string         The input string.
	 * @param  string|int|null $characterMask  Built-in PHP function rtrim() not allow $characterMask to number.
	 * @param  string|null     $encoding       The encoding.
	 * @return string                          This function returns a string depends on $characterMask data type.
	 *                                         If $characterMask is null : returns a string with whitespace stripped from the end of $string.
	 *                                         If $characterMask is string : returns a string with characters stripped from the end of $string.
	 *                                         If $characterMask is int : returns a part of string, end at a specified position by $characterMask.
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
			throw InvalidArgumentException::type(2, ['string','int','null'], $characterMask);

		return $string;
	}

	//

	/**
	 * Quote string with slashes.
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function addSlashes(string $string) : string
	{
		$string = addslashes($string);

		return $string;
	}

	/**
	 * Un-quotes a quoted string.
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function stripSlashes(string $string) : string
	{
		$string = stripslashes($string);

		return $string;
	}

	/**
	 * Convert all applicable characters to HTML entities.
	 * An alias of built-in PHP function htmlspecialchars().
	 *
	 * It’s generally recommended to use htmlspecialchars
	 * because htmlentities can cause display problems with
	 * your text depending on what characters are being output.
	 *
	 * @param  string $string
	 * @return string
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
	 * Convert HTML entities to their corresponding characters.
	 * An alias of built-in PHP function htmlspecialchars_decode().
	 *
	 * @param  string   $string
	 * @return string
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
	 * Strip all whitespace characters including tabs, newline characters,
	 * as well as multibyte whitespace such as the thin space and ideographic space.
	 *
	 * 1. " "    (an ordinary space)
	 * 2. "\t"   (a tab)
	 * 3. "\n"   (a new line)
	 * 4. "\r"   (a carriage return)
	 * 5. "\0"   (a null byte)
	 * 6. "\x0B" (a vertical tab)
	 *
	 * @param  string $string
	 * @return string
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
	 * Remove HTML and PHP tags from a string.
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function removeTags(string $string) : string
	{
		$string = strip_tags($string);

		return $string;
	}

	/**
	 * Removes single and double quotes from a string.
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function removeQuotes(string $string) : string
	{
		$string = str_replace(['"', "'"], '', $string);

		return $string;
	}

	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @see    https://www.eso.org/~ndelmott/url_encode.html
	 * @see    http://www.asciitable.com/
	 * @param  string  $string
	 * @param  boolean $urlEncoded
	 * @return string
	 */
	public static function removeInvisibleCharacters(string $string, bool $urlEncoded = true): string
	{
		$patterns = [];

		// remove every control character except horizontal tab (dec 09),
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
	 * @param  string      $string
	 * @param  string      $substring
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string      $string
	 * @param  string      $substring
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string $string  The input string.
	 * @return string          A trimmed string and condensed whitespace.
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
	 * http://www.some-site.com//index.php
	 *
	 * becomes:
	 *
	 * http://www.some-site.com/index.php
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function reduceDoubleSlashes(string $string) : string
	{
		$string = preg_replace('#(^|[^:])//+#', '\\1/', $string);

		return $string;
	}

	//

	/**
	 * Convert the given string to lower-case..
	 *
	 * @param  string      $string
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string      $string
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string      $string
	 * @param  string|null $encoding
	 * @return string
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
	 * Convert the given string to upper-case..
	 *
	 * @param  string      $string
	 * @param  string|null $encoding
	 * @return string
	 */
	public static function upperCase(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_strtoupper($string, $encoding);

		return $string;
	}

	/**
	 * Make a string's first character uppercase.
	 * This method provides a unicode-safe implementation of built-in PHP function `ucfirst()`.
	 *
	 * @param  string      $string
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string      $string
	 * @param  string|null $encoding
	 * @return string
	 */
	public static function upperCaseWords(string $string, string $encoding = null) : string
	{
		$encoding = static::_getEncoding($encoding);
		$string = mb_convert_case($string, MB_CASE_TITLE, $encoding);

		return $string;
	}

	//

	/**
	 * Returns a repeated string given a multiplier. An alias for str_repeat.
	 *
	 * @param  string $string      The string to repeat.
	 * @param  int    $multiplier  The number of times to repeat the string.
	 * @return string
	 */
	public static function repeat(string $string, int $multiplier) : string
	{
		$string = str_repeat($string, $multiplier);

		return $string;
	}

	/**
	 * This method is similar to the php function `str_replace()` except that it will support $limit.
	 *
	 * @param  string       $string
	 * @param  string|array $search
	 * @param  string|array $replace
	 * @param  int|null     $limit
	 * @param  string|null  $encoding
	 * @return string
	 */
	public static function replace(string $string, $search, $replace, int $limit = null, string $encoding = null) : string
	{
		if (!is_string($search) and !is_array($search))
			throw InvalidArgumentException::type(2, ['string','array'], $search);

		if (!is_string($replace) and !is_array($replace))
			throw InvalidArgumentException::type(2, ['string','array'], $replace);

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
	 * Replace the first occurrence of a given value in the string.
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  string      $replace
	 * @param  string|null $encoding
	 * @return string
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
	 * Replace the last occurrence of a given value in the string.
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  string      $replace
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string       $string
	 * @param  string|array $search
	 * @param  string|array $replace
	 * @param  int|null     $limit
	 * @param  string|null  $encoding
	 * @return string
	 */
	public static function ireplace(string $string, $search, $replace, int $limit = null, string $encoding = null) : string
	{
		if (!is_string($search) and !is_array($search))
			throw InvalidArgumentException::type(2, ['string','array'], $search);

		if (!is_string($replace) and !is_array($replace))
			throw InvalidArgumentException::type(2, ['string','array'], $replace);

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
	 * Replace the first occurrence of a given value in the string (case-insensitive version).
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  string      $replace
	 * @param  string|null $encoding
	 * @return string
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
	 * Replace the last occurrence of a given value in the string (case-insensitive version).
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  string      $replace
	 * @param  string|null $encoding
	 * @return string
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
	 * @param  string      $string    The input string.
	 * @param  string      $replace   The replacement string.
	 * @param  int         $start     If start is positive, the replacing will begin at the start'th offset into string.
	 *                                If start is negative, the replacing will begin at the start'th character from the
	 *                                end of string.
	 * @param  int|null    $length    If given and is positive, it represents the length of the portion of string which
	 *                                is to be replaced. If it is negative, it represents the number of characters from
	 *                                the end of string at which to stop replacing. If it is not given, then it will
	 *                                default to strlen( string ); i.e. end the replacing at the end of string.
	 *                                Of course, if length is zero then this function will have the effect of inserting
	 *                                replacement into string at the given start offset.
	 * @param  string|null $encoding  The character encoding.
	 * @return string                 The result string is returned.
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
	 * Replace a given value in the string sequentially with an array.
	 *
	 * For example,
	 *
	 * ```php
	 * // If $replaces is numeric array.
	 * $string = 'My name is ? and ? years old.';
	 * $string = Str::insert($string, '?', ['Nat', 38]);
	 *
	 * // If $replaces is associative array.
	 * $string = 'My name is :name and :age years old.';
	 * $string = Str::insert($string, ':', ['name' => 'Nat', 'age' => 38]);
	 *
	 * // the result is:
	 * // My name is Nat and 38 years old.
	 * ```
	 *
	 * @param  string $string
	 * @param  string $marker
	 * @param  array  $replaces  Numeric or associative array.
	 * @return string
	 */
	public static function insert(string $string, string $marker, array $replaces) : string
	{
		if ($marker === '')
			return $string;

		if (Arr::isAssociative($replaces))
		{
			foreach ($replaces as $key => $value)
				$string = str_replace($marker . $key, $value, $string);
		}
		else
		{
			$blocks = explode($marker, $string);
			$string = '';

			for ($i = 0, $n = count($blocks); $i < $n; ++$i)
			{
				$string .= $blocks[$i];

				if ($i < ($n - 1))
					$string .= ($replaces[$i] ?? $marker);
			}
		}

		return $string;
	}

	/**
	 * Returns a reversed string. A multibyte version of strrev().
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  The character encoding.
	 * @return string                 A reversed string.
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
	 * Determine if a given string starts with a given substring.
	 *
	 * @param  string      $string
	 * @param  string      $prefix
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
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

		return ($search === $prefix);
	}

	/**
	 * Determine if a given string starts with any of given substring.
	 *
	 * @param  string      $string
	 * @param  array       $prefixes
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
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
	 * Determine if a given string ends with a given substring.
	 *
	 * @param  string      $string
	 * @param  string      $suffix
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
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

		return ($search === $suffix);
	}

	/**
	 * Determine if a given string ends with any of given substring.
	 *
	 * @param  string      $string
	 * @param  array       $suffixes
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
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
	 * Adds a single instance of the given value to a string if it does not already start with the value.
	 *
	 * @param  string      $string
	 * @param  string      $prefix
	 * @param  string|null $encoding
	 * @return string
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
	 * Adds a single instance of the given value to a string if it does not already end with the value.
	 *
	 * @param  string      $string
	 * @param  string      $suffix
	 * @param  string|null $encoding
	 * @return string
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
	 * Adds a single instance of the given value to a string if it does not already start and end with the value.
	 *
	 * @param  string      $string
	 * @param  string      $character
	 * @param  string|null $encoding
	 * @return string
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
	 * Return the remainder of a string after the first occurrence of a given value.
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return string
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
	 * Return the remainder of a string after the last occurrence of a given value.
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return string
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
	 * Get the portion of a string before the first occurrence of a given value.
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return string
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
	 * Get the portion of a string before the last occurrence of a given value.
	 *
	 * @param  string      $string
	 * @param  string      $search
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return string
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
	 * Highlights a phrase within a text string.
	 *
	 * @param  string $string    The text string.
	 * @param  string $phrase    The phrase you'd like to highlight.
	 * @param  string $tagOpen   The opening tag to precede the phrase with.
	 * @param  string $tagClose  The closing tag to end the phrase with.
	 * @param  bool   $html      If true, will ignore any HTML tags, ensuring that only the correct text is highlighted.
	 * @return string
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
	 * Add's _1 to a string or increment the ending number to allow _2, _3, etc
	 *
	 * @param  string $string     The string.
	 * @param  string $separator  What should the duplicate number be appended with.
	 * @param  int    $first      Which number should be used for the first dupe increment.
	 * @return string
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
	 * @param  float|int $number  A floating point number or integer.
	 * @return string             The string representation of the number.
	 */
	public static function floatToString($number) : string
	{
		if (!is_float($number) and !is_int($number))
			throw InvalidArgumentException::type(1, ['float','int'], $number);

		$string = str_replace(',', '.', (string)$number);

		return $string;
	}

	/**
	 * Converts each occurrence of some consecutive number of spaces, as defined by $tabLength, to a tab.
	 *
	 * @param  string $string     The input string.
	 * @param  int    $tabLength  Number of spaces to replace with a tab.
	 * @return string
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
	 * @param  string $string     The input string.
	 * @param  int    $tabLength  Number of spaces to replace each tab with.
	 * @return string
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
	 * the given string at the both sides of the string.
	 *
	 * @param  string $string
	 * @param  string $padString
	 * @param  int    $length
	 * @return string
	 */
	public static function pad(string $string, string $padString, int $length) : string
	{
		$string = str_pad($string, $length, $padString, STR_PAD_BOTH);
		return $string;
	}

	/**
	 * Makes a string as long as the first argument by adding
	 * the given string at the beginning of the string.
	 *
	 * @param  string $string
	 * @param  string $padString
	 * @param  int    $length
	 * @return string
	 */
	public static function padLeft(string $string, string $padString, int $length) : string
	{
		$string = str_pad($string, $length, $padString, STR_PAD_LEFT);

		return $string;
	}

	/**
	 * Makes a string as long as the first argument by adding
	 * the given string at the end of the string.
	 *
	 * @param  string $string
	 * @param  string $padString
	 * @param  int    $length
	 * @return string
	 */
	public static function padRight(string $string, string $padString, int $length) : string
	{
		$string = str_pad($string, $length, $padString, STR_PAD_RIGHT);

		return $string;
	}

	//

	/**
	 * Returns true if the string contains only whitespace chars, false otherwise.
	 * Support checking '0' in 'if' statement.
	 *
	 * $input = '0';
	 *
	 * if ($input)
	 * ....do something...
	 *
	 * @param  mixed $string
	 * @return bool
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
	 * Returns true if the string contains only alphabetic chars, false otherwise.
	 *
	 * @param  mixed $string
	 * @return bool
	 */
	public static function isAlpha($string) : bool
	{
		$string = (string)$string;
		$result = mb_ereg_match('^[[:alpha:]]*$', $string);

		return $result;
	}

	/**
	 * Returns true if the string contains only alphabetic and numeric chars, false otherwise.
	 *
	 * @param  mixed $string
	 * @return bool
	 */
	public static function isAlphanumeric($string) : bool
	{
		$string = (string)$string;
		$result = mb_ereg_match('^[[:alnum:]]*$', $string);

		return $result;
	}

	/**
	 * Returns true if the string is base64 encoded, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function isBase64Encoded(string $string) : bool
	{
		$decoded = base64_decode($string);
		$encoded = base64_encode($decoded);

		return ($encoded === $string);
	}

	/**
	 * Returns true if the string contains only hexadecimal chars, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function isHexadecimal(string $string) : bool
	{
		return mb_ereg_match('^[[:xdigit:]]*$', $string);
	}

	/**
	 * Returns true if the string contains only lower case chars, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function isLowerCase(string $string) : bool
	{
		return mb_ereg_match('^[[:lower:]]*$', $string);
	}

	/**
	 * Returns true if the string contains only upper case chars, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function isUpperCase(string $string) : bool
	{
		return mb_ereg_match('^[[:upper:]]*$', $string);
	}

	/**
	 * Returns true if the string is serialized, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
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
	 * Check if the string contain multibyte characters.
	 *
	 * @param  string $string
	 * @return bool
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
	 * Determine if a given string contains a given substring.
	 *
	 * @param  string      $string
	 * @param  string      $needle
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
	 */
	public static function contains(string $string, string $needle, bool $caseSensitive = true, string $encoding = null) : bool
	{
		$encoding = static::_getEncoding($encoding);

		if ($caseSensitive)
			return ($needle !== '' and mb_strpos($string, $needle, 0, $encoding) !== false);
		else
			return ($needle !== '' and mb_stripos($string, $needle, 0, $encoding) !== false);
	}

	/**
	 * Determine if a given string contains some array values.
	 *
	 * @param  string      $string
	 * @param  array       $needles
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
	 */
	public static function containsAny(string $string, array $needles, bool $caseSensitive = true, string $encoding = null) : bool
	{
		foreach ($needles as $needle)
		{
			if (static::contains($string, $needle, $caseSensitive, $encoding))
				return true;
		}

		return false;
	}

	/**
	 * Determine if a given string contains all array values.
	 *
	 * @param  string      $string
	 * @param  array       $needles
	 * @param  bool        $caseSensitive
	 * @param  string|null $encoding
	 * @return bool
	 */
	public static function containsAll(string $string, array $needles, bool $caseSensitive = true, string $encoding = null) : bool
	{
		foreach ($needles as $needle)
		{
			if (!static::contains($string, $needle, $caseSensitive, $encoding))
				return false;
		}

		return true;
	}

	/**
	 * Returns true if the string contains a lower case char, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function hasLowerCase(string $string) : bool
	{
		return mb_ereg_match('.*[[:lower:]]', $string);
	}

	/**
	 * Returns true if the string contains a upper case char, false otherwise.
	 *
	 * @param  string $string
	 * @return bool
	 */
	public static function hasUpperCase(string $string) : bool
	{
		return mb_ereg_match('.*[[:upper:]]', $string);
	}

	//

	/**
	 * Returns an array consisting of the characters in the string.
	 *
	 * @param  string      $string    The input string.
	 * @param  string|null $encoding  The character encoding.
	 * @return array                  An array of string chars
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
	 * @param  string $string
	 * @return array
	 */
	public static function lines(string $string) : array
	{
		$array = mb_split('[\r\n]{1,2}', $string);

		return $array;
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
	 * Create a random string.
	 * For security purposes, use Security::random() instead.
	 *
	 * Type:
	 *	alpha:   A string with lower and uppercase letters only.
	 *	alnum:   Alpha-numeric string with lower and uppercase characters.
	 *	numeric: Numeric string.
	 *	nozero:  Numeric string with no zeros.
	 *
	 * @param  int    $length
	 * @param  string $type    alnum|numeric|alpha|nozero
	 * @return string
	 */
	public static function random($length = 8, $type = 'alnum')
	{
		switch ($type)
		{
			case 'alpha':
				$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			case 'numeric':
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
	 * @param  string $string
	 * @return string
	 */
	public static function base64encode(string $string) : string
	{
		$string = base64_encode($string);

		return $string;
	}

	/**
	 * Decodes data encoded with MIME base64.
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function base64decode(string $string) : string
	{
		$string = base64_decode($string);

		return $string;
	}

	/**
	 * Generate a random UUID version 4
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
	 * Normalise a string replacing foreign characters
	 *
	 * @param  string $string  String to normalise
	 * @return string          Normalised string
	 */
	public static function normalize($string)
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

		return str_replace($from, $to, $string);
	}
}