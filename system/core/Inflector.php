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

/**
 * Class Inflector
 *
 * The Inflector class takes a string and can manipulate it to handle word
 * variations such as pluralizations or camelizing and is normally accessed
 * statically. Example: Inflector::pluralize('example') returns "examples".
 *
 * @package System
 */
class Inflector
{
	/**
	 * Inflector constructor.
	 */
	private function __construct(){}

	/**
	 * Determines if the given word has a plural version.
	 *
	 * @param  string $word  The input Word to check.
	 * @return bool          Returns true if the given word has a plural version.
	 */
	public static function isCountableWord(string $word) : bool
	{
		$word = mb_strtolower($word);
		$unCountableWords = Config::inflector('unCountableWords');
		$unCountableWords = array_map('mb_strtolower', $unCountableWords);

		if (in_array($word, $unCountableWords))
			return false;

		return true;
	}

	/**
	 * Takes a singular word and makes it plural.
	 *
	 * @param  string $string  The input string to pluralize.
	 * @return string          Return a plural noun.
	 * @see https://www.phpliveregex.com/
	 */
	public static function pluralize(string $string) : string
	{
		if (!static::isCountableWord($string))
			return $string;

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
			if (preg_match($rule, $string))
			{
				$string = preg_replace($rule, $replacement, $string);
				break;
			}
		}

		return $string;
	}

	/**
	 * Takes a plural word and makes it singular.
	 *
	 * @param  string $string  The input string to singularize.
	 * @return string          Returns a singular noun.
	 * @see https://www.phpliveregex.com/
	 */
	public static function singularize(string $string) : string
	{
		if (!static::isCountableWord($string))
			return $string;

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
			if (preg_match($rule, $string))
			{
				$string = preg_replace($rule, $replacement, $string);
				break;
			}
		}

		return $string;
	}

	/**
	 * Converts the given word to CamelCased.
	 *
	 * Converts a word like "some_day" or "some day" to "SomeDay". It
	 * will remove non-alphanumeric characters from the word, so
	 * "She's hot" will be converted to "SheSHot".
	 *
	 * @param  string $string  The input string to convert to camel case.
	 * @return string          Returns the UpperCamelCased word.
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
	 * Converts any "CamelCased" or "vanda framework" to a "vanda_framework".
	 *
	 * @param  string $string  The input word to underscore.
	 * @return string          Returns the underscored word.
	 */
	public static function underscore(string $string) : string
	{
		$string = preg_replace('/(\s)+/', '_', $string);
		$string = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $string));

		return $string;
	}

	/**
	 * Converts any "CamelCased" word to an array of strings.
	 *
	 * Returns an array of strings each of which is a substring of string formed
	 * by splitting it at the camelcased letters.
	 *
	 * e.g., "FooBar" becomes ["foo", "bar"]
	 *
	 * @param  string $string  The input word to explode.
	 * @return array           Returns an array of strings.
	 */
	public static function explode(string $string) : array
	{
		$string = explode('_', static::underscore($string));

		return $string;
	}

	/**
	 * Converts the given array of strings to a "CamelCased" word.
	 *
	 * e.g., ["foo", "bar"] becomes "FooBar"
	 *
	 * @param  array  $string  The input qrray to implode.
	 * @return string          Returns UpperCamelCased word.
	 */
	public static function implode(array $string) : string
	{
		$string = static::camelize(implode('_', $string));

		return $string;
	}

	/**
	 * Returns a human-readable string from the given string by replacing the
	 * separator with a space, and by upper-casing the initial character by default.
	 *
	 * e.g., "I had my car fixed_yesTerday" becomes "I Had My Car Fixed Yesterday".
	 *
	 * @param  string $string     The input string.
	 * @param  string $separator  Optionally, the input separator. Defaults to '_''.
	 * @return string             Returns human-readable word.
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
	 * e.g.,
	 *
	 * "Some Day" or "some_day" becomes "someDay"
	 * "She's hot" becomes "sheSHot"
	 *
	 * @param  string $string  The input string being camelBacked.
	 * @return string          Returns camelBacked word.
	 */
	public static function variablize(string $string) : string
	{
		$string   = static::camelize(static::underscore($string));
		$firstChar   = mb_strtolower(mb_substr($string, 0, 1));
		$string = preg_replace('/\\w/', $firstChar, $string, 1);

		return $string;
	}

	/**
	 * Converts the given string (class, model or table name) to a foreign key format.
	 *
	 * e.g., "UserGroup" becomes "userGroupId"
	 *
	 * @param  string $string  The input string (class, model or table name).
	 * @return string          Returns a foreign key.
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
	 * Create URL Title.
	 *
	 * Takes a "title" string as input and creates a human-friendly URL string
	 * with a "separator" string as the word separator.
	 *
	 * e.g., "Url Friendly" becomes "url-friendly"
	 *
	 * @param  string $string     The input string.
	 * @param  string $separator  Optionally, word separator (usually '-' or '_'). Defaults to '-'.
	 * @param  bool   $lowercase  Optionally, Whether to transform the output string to lowercase. Defaults to true.
	 * @return string             Returns a human-friendly URL string.
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

		$string = trim(trim($string, $separator));

		return $string;
	}

	/**
	 * Add order suffix becomes numbers.
	 *
	 * e.g.,
	 *
	 * 1 becomes "1st"
	 * 2 becomes "2nd"
	 * 3 becomes "3rd"
	 * 4 becomes "4th"
	 * 5 becomes "5th"
	 *
	 * @param  int    $number  The input number to get its ordinal value.
	 * @return string          Returns the ordinalized version of $number.
	 */
	public static function ordinalize(int $number) : string
	{
		if (in_array($number % 100, range(11, 13)))
			return $number . 'th';

		switch ($number % 10)
		{
			case 1:
				$string = $number . 'st';
				break;
			case 2:
				$string = $number . 'nd';
				break;
			case 3:
				$string = $number . 'rd';
				break;
			default:
				$string = $number . 'th';
		}

		return $string;
	}

	/**
	 * Converts the given string into a Vanda Controller format.
	 *
	 * e.g.,
	 *
	 * "Some Day" becomes "SomeDayController"
	 * "some_day" becomes "SomeDayController"
	 * "She's hot" becomes "SheSHotController"
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the converted string.
	 */
	public static function controllerize(string $string) : string
	{
		$controller = static::camelize($string) . 'Controller';

		return $controller;
	}

	/**
	 * Converts the given string into a Vanda Action format.
	 *
	 * e.g.,
	 *
	 * "Some Day" becomes "SomeDayAction"
	 * "some_day" becomes "SomeDayAction"
	 * "She's hot" becomes "SheSHotAction"
	 *
	 * @param  string $string  The input string.
	 * @return string          Returns the converted string.
	 */
	public static function actionize(string $string) : string
	{
		$action = static::camelize($string) . 'Action';

		return $action;
	}

	/**
	 * Converts a list of words into a sentence. Special treatment is done for
	 * the last few words.
	 *
	 * For example,
	 *
	 * ```php
	 * $words = ['Nat', 'Angela'];
	 * $result = Inflector::sentence($words);
	 * // The $result will be: Nat and Angela
	 *
	 * $words = ['Nat', 'Angela', 'Vanda'];
	 * $result = Inflector::sentence($words);
	 * // The $result will be: Nat, Angela and Angela
	 *
	 * $words = ['Nat', 'Angela', 'Vanda'];
	 * $result = Inflector::sentence($words, ' & ');
	 * // The $result will be: Nat, Angela & Vanda
	 * ```
	 *
	 * @param  array       $words              The input words to be converted into a string.
	 * @param  string|null $lastWordConnector  Optionally, the string connecting the last two words. Defaults to null.
	 * @param  string      $connector          Optionally, the string connecting words other than those connected by
	 *                                         $lastWordConnector. Default to ', '.
	 * @return string                          Returns the generated sentence.
	 */
	public static function sentence(array $words, ?string $lastWordConnector = null, string $connector = ', ') : string
	{
		if (!$lastWordConnector)
			$lastWordConnector = ' and ';

		switch (count($words))
		{
			case 0:
				$string = '';
				break;
			case 1:
				$string = reset($words);
				break;
			case 2:
				$string = implode($lastWordConnector, $words);
				break;
			default:
				$string = implode($connector, array_slice($words, 0, -1)) . $lastWordConnector . end($words);
		}

		return $string;
	}
}
