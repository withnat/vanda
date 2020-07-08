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

/**
 * Class Inflector
 *
 * The Inflector class takes a string and can manipulate it to handle word
 * variations such as pluralizations or camelizing and is normally accessed
 * statically. Example: Inflector::pluralize('example') returns "examples".
 *
 * @package System
 */
final class Inflector
{
	/**
	 * Inflector constructor.
	 */
	private function __construct(){}

	/**
	 * Checks if the given word has a plural version.
	 *
	 * @param  string $word  Word to check
	 * @return bool
	 */
	public static function isCountable(string $word) : bool
	{
		// PHP 7.3+
		if (function_exists('is_countable'))
		{
			/** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
			// @codeCoverageIgnoreStart
			return is_countable($word);
			// @codeCoverageIgnoreEnd
		}

		$word = mb_strtolower($word);
		$unCountableWords = Config::inflector('unCountableWords');
		$unCountableWords = array_map('mb_strtolower', $unCountableWords);

		if (in_array($word, $unCountableWords))
			return false;

		return true;
	}

	/**
	 * Takes a singular word and makes it plural
	 *
	 * @param  string $string  Input string to pluralize.
	 * @return string          Plural noun.
	 * @see https://www.phpliveregex.com/
	 */
	public static function pluralize(string $string) : string
	{
		$result = strval($string);

		if (!static::isCountable($result))
			return $result;

		$rules = [
			'/(quiz)$/'                => '\1zes',      // quiz > quizzes
			'/^(ox)$/'                 => '\1\2en',     // ox > oxen
			'/([m|l])ouse$/'           => '\1ice',      // mouse > mice,
			                                            // louse > lice
			'/(matr|vert|ind)ix|ex$/'  => '\1ices',     // matrix > matrices,
			                                            // vertex > vertices,
			                                            // index > indices
			'/(x|ch|ss|sh)$/'          => '\1es',       // search > searches,
			                                            // switch > switches,
			                                            // fix > fixes,
			                                            // box > boxes,
			                                            // process > processes,
			                                            // address > addresses
			'/([^aeiouy]|qu)y$/'       => '\1ies',      // query > queries,
			                                            // ability > abilities,
			                                            // agency > agencies
			'/(hive)$/'                => '\1s',        // archive > archives,
			                                            // hive > hives
			'/(?:([^f])fe|([lr])f)$/'  => '\1\2ves',    // half > halves,
			                                            // safe > saves,
			                                            // wife > wives
			'/sis$/'                   => 'ses',        // basis > bases,
			                                            // diagnosis > diagnoses
			'/([ti])um$/'              => '\1a',        // datum > data,
			                                            // medium > media
			'/(p)erson$/'              => '\1eople',    // person > people,
			                                            // salesperson > salespeople
			'/(m)an$/'                 => '\1en',       // man > men,
			                                            // woman > women,
			                                            // spokesman > spokesmen
			'/(c)hild$/'               => '\1hildren',  // child > children
			'/(buffal|tomat)o$/'       => '\1\2oes',    // buffalo > buffaloes,
			                                            // tomato > tomatoes
			'/(bu|campu)s$/'           => '\1\2ses',    // bus > buses,
			                                            // campus > campuses
			'/(alias|status|virus)$/'  => '\1es',       // alias > aliases
			'/(octop)us$/'             => '\1i',        // octopus > octopi
			'/(ax|cris|test)is$/'      => '\1es',       // axis > axes,
			                                            // crisis > crises
			'/s$/'                     => 's',          // no change (compatibility)
			'/$/'                      => 's',          // no change (compatibility)
		];

		foreach ($rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}

	/**
	 * Takes a plural word and makes it singular
	 *
	 * @param  string $string  Input string to singularize.
	 * @return string          Singular noun.
	 * @see https://www.phpliveregex.com/
	 */
	public static function singularize(string $string) : string
	{
		$result = strval($string);

		if (!static::isCountable($result))
			return $result;

		$rules = [
			'/(matr)ices$/'            => '\1ix',       // matrices > matrix
			'/(vert|ind)ices$/'        => '\1ex',       // vertices > vertex
			                                            // indices > index
			'/^(ox)en/'                => '\1',         // oxen > ox
			'/(alias)es$/'             => '\1',         // aliases > alias
			'/([octop|vir])i$/'        => '\1us',       // octopi > octopus
			                                            // viri > virus
			'/(cris|ax|test)es$/'      => '\1is',       // crises > crisis
			                                            // axes > axis
			                                            // testes > testis
			'/(shoe)s$/'               => '\1',         // shoes > shoe
			'/(o)es$/'                 => '\1',         // oes > o
			'/(bus|campus)es$/'        => '\1',         // buses > bus
			                                            // campuses > campus
			'/([m|l])ice$/'            => '\1ouse',     // mice > mouse
			                                            // lice > louse
			'/(x|ch|ss|sh)es$/'        => '\1',         // xes > x
			                                            // ches > ch
			                                            // sses > ss
			                                            // shes > sh
			'/(m)ovies$/'              => '\1\2ovie',   // movies > movie
			'/(s)eries$/'              => '\1\2eries',  // series > series
			'/([^aeiouy]|qu)ies$/'     => '\1y',
			'/([lr])ves$/'             => '\1f',
			'/(tive)s$/'               => '\1',         // tives > tive
			'/(hive)s$/'               => '\1',         // hives > hive
			'/([^f])ves$/'             => '\1fe',
			'/(^analy)ses$/'           => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'               => '\1um',
			'/(p)eople$/'              => '\1\2erson',  // people > person
			'/(m)en$/'                 => '\1an',       // men > man
			'/(s)tatuses$/'            => '\1\2tatus',  // statuses > status
			'/(c)hildren$/'            => '\1\2hild',   // children > child
			'/(n)ews$/'                => '\1\2ews',    // news > news
			'/(quiz)zes$/'             => '\1',         // quizzes > quiz
			'/([^us])s$/'              => '\1'
		];

		foreach ($rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}

	/**
	 * Returns given word as CamelCased.
	 *
	 * Converts a word like "some_day" or "some day" to "SomeDay". It
	 * will remove non alphanumeric characters from the word, so
	 * "She's hot" will be converted to "SheSHot"
	 *
	 * @param  string $string  Input string to convert to camel case.
	 * @return string          UpperCamelCasedWord
	 */
	public static function camelize(string $string) : string
	{
		$string = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $string);
		$string = str_replace(['_', '-'], ' ', $string);
		$string = mb_strtolower($string);
		$string = ucwords($string);
		$string = str_replace(' ', '', $string);

		return $string;
	}

	/**
	 * Convert any "CamelCased" or "vanda framework" into an "vanda_framework".
	 *
	 * @param  string $string  Word to underscore
	 * @return string          Underscored word
	 */
	public static function underscore(string $string) : string
	{
		$string = preg_replace('/(\s)+/', '_', $string);
		$string = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $string));

		return $string;
	}

	/**
	 * Convert any "CamelCased" word into an array of strings
	 *
	 * Returns an array of strings each of which is a substring of string formed
	 * by splitting it at the camelcased letters.
	 *
	 * ie. "FooBar" to ["foo", "bar"]
	 *
	 * @param  string $string  Word to explode
	 * @return array           Array of strings
	 */
	public static function explode(string $string) : array
	{
		$string = explode('_', static::underscore($string));

		return $string;
	}

	/**
	 * Convert an array of strings into a "CamelCased" word.
	 *
	 * ie. ["foo", "bar"] to "FooBar"
	 *
	 * @param  array  $string  Array to implode
	 * @return string          UpperCamelCasedWord
	 */
	public static function implode(array $string) : string
	{
		$string = static::camelize(implode('_', $string));

		return $string;
	}

	/**
	 * Returns a human-readable string from $word.
	 *
	 * Returns a human-readable string from $word, by replacing
	 * the separator with a space, and by upper-casing the initial
	 * character by default.
	 *
	 * ie. "I had my car fixed_yesTerday" to "I Had My Car Fixed Yesterday"
	 *
	 * @param  string $string     Input string
	 * @param  string $separator  Input separator
	 * @return string             Human-readable word
	 */
	public static function humanize(string $string, string $separator = '_') : string
	{
		$string = preg_replace('/[' . preg_quote($separator) . ']+/', ' ', trim(mb_strtolower($string)));
		$string = ucwords($string);

		return $string;
	}

	/**
	 * Returns camelBacked version of a string. Same as camelize but first char is lowercased.
	 *
	 * ie. "Some Day" or "some_day" ot "someDay", "She's hot" to "sheSHot"
	 *
	 * @param  string $string  String to be camelBacked.
	 * @return string
	 */
	public static function variablize(string $string) : string
	{
		$string   = static::camelize(static::underscore($string));
		$result   = mb_strtolower(mb_substr($string, 0, 1));

		return preg_replace('/\\w/', $result, $string, 1);
	}

	/**
	 * Gets the foreign key for a given class, model or table name.
	 *
	 * @param  string $string  The class, model or table name.
	 * @return string          The foreign key.
	 */
	public static function foreignKey(string $string) : string
	{
		if ($string)
		{
			$string = static::variablize($string);
			$string = Str::ensureEndsWith($string, 'Id');
		}

		return $string;
	}

	/**
	 * Create URL Title
	 *
	 * Takes a "title" string as input and creates a
	 * human-friendly URL string with a "separator" string
	 * as the word separator.
	 *
	 * @param  string $string     Input string
	 * @param  string $separator  Word separator (usually '-' or '_')
	 * @param  bool   $lowercase  Whether to transform the output string to lowercase
	 * @return string
	 */
	public static function slugify(string $string, string $separator = '-', bool $lowercase = true) : string
	{
		$qSeparator = preg_quote($separator, '#');

		$trans = [
			'&.+?;'						=> '',
			'[^\w\d _-]'				=> '',
			'\s+'						=> $separator,
			'(' . $qSeparator . ')+'	=> $separator
		];

		$string = strip_tags($string);

		foreach ($trans as $key => $value)
			$string = preg_replace('#' . $key . '#iu', $value, $string);

		if ($lowercase)
			$string = mb_strtolower($string);

		return trim(trim($string, $separator));
	}

	/**
	 * Add order suffix to numbers ex. 1st 2nd 3rd 4th 5th.
	 *
	 * @param  int    $number  The number to get its ordinal value.
	 * @return string          The ordinalized version of $number.
	 */
	public static function ordinalize(int $number) : string
	{
		if (in_array($number % 100, range(11, 13)))
			return $number . 'th';

		switch ($number % 10)
		{
			case 1:
				return $number . 'st';
			case 2:
				return $number . 'nd';
			case 3:
				return $number . 'rd';
			default:
				return $number . 'th';
		}
	}

	/**
	 * @param  string $string
	 * @return string
	 */
	public static function controllerize(string $string) : string
	{
		$controller = static::camelize($string) . 'Controller';

		return $controller;
	}

	/**
	 * @param  string $string
	 * @return string
	 */
	public static function actionize(string $string) : string
	{
		$action = static::camelize($string) . 'Action';

		return $action;
	}

	/**
	 * Converts a list of words into a sentence.
	 *
	 * Special treatment is done for the last few words. For example,
	 *
	 * $words = ['Nat', 'Angela'];
	 * echo Inflector::sentence($words);
	 * // output: Nat and Angela
	 *
	 * $words = ['Nat', 'Angela', 'Vanda'];
	 * echo Inflector::sentence($words);
	 * // output: Nat, Angela and Angela
	 *
	 * $words = ['Nat', 'Angela', 'Vanda'];
	 * echo Inflector::sentence($words, ' & ');
	 * // output: Nat, Angela & Vanda
	 *
	 * @param  array       $words              The words to be converted into an string.
	 * @param  string|null $lastWordConnector  The string connecting the last two words.
	 * @param  string      $connector          The string connecting words other than those connected by $lastWordConnector.
	 * @return string                          The generated sentence.
	 */
	public static function sentence(array $words, string $lastWordConnector = null, string $connector = ', ') : string
	{
		if (!$lastWordConnector)
			$lastWordConnector = ' and ';

		switch (count($words))
		{
			case 0:
				return '';
			case 1:
				return reset($words);
			case 2:
				return implode($lastWordConnector, $words);
			default:
				return implode($connector, array_slice($words, 0, -1)) . $lastWordConnector . end($words);
		}
	}
}
