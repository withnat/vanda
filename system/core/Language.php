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

/**
 * The Language class is used to get language strings.
 *
 * Class Language
 * @package System
 */
class Language
{
	protected static $_code;
	protected static $_direction;
	protected static $_strings;

	/**
	 * Language constructor.
	 */
	private function __construct(){}

	/**
	 * Gets a language string based on the given string and language ID.
	 *
	 * @param  string   $string  The string to get.
	 * @param  int|null $langId  The language ID to get. Defaults to null.
	 *                           If null, the default language string will be returned.
	 * @return string            Returns the language string.
	 */
	public static function _(string $string, ?int $langId = null) : string
	{
		if (!isset(static::$_strings[$langId]))
			static::_loadLanguage($langId);

		// @codeCoverageIgnoreStart
		if (isset(static::$_strings[$langId][$string]))
			$string = static::$_strings[$langId][$string];
		// @codeCoverageIgnoreEnd

		return $string;
	}

	/**
	 * Gets a language code based on the given language ID.
	 *
	 * @param  int|null $langId  The language ID to get. Defaults to null.
	 *                           If null, the default language code will be returned.
	 * @return string            Returns the language code.
	 * @codeCoverageIgnore
	 */
	public static function code(?int $langId = null) : string
	{
		if (!static::$_code)
			static::_loadLanguage($langId);

		return static::$_code;
	}

	/**
	 * Gets a language direction based on the given language ID.
	 *
	 * @param  int|null $langId  The language ID to get. Defaults to null.
	 *                           If null, the default language direction will be returned.
	 * @return string            Returns the language direction.
	 * @codeCoverageIgnore
	 */
	public static function direction(int $langId = null) : string
	{
		if (!static::$_direction)
			static::_loadLanguage($langId);

		return static::$_direction;
	}

	/**
	 * Loads language strings from the language file based on the given
	 * language ID. First, load from the global language file, then load
	 * from the package language file, and finally, override the global
	 * language strings with those from the package.
	 *
	 * @param  int|null $langId  The language ID to load. Defaults to null.
	 *                           If null, the default language will be loaded.
	 * @return void
	 */
	protected static function _loadLanguage(int $langId = null) : void
	{
		// Load language code.

		if (!static::$_code)
		{
			DB::select('code')->from('Language');

			// @codeCoverageIgnoreStart
			if ($langId)
				$code = DB::where($langId)->loadSingle();
			else
				$code = DB::where('default', 1)->loadSingle();
			// @codeCoverageIgnoreEnd

			static::$_code = $code;
		}

		// Load langauge direction.

		if (!static::$_direction)
		{
			DB::select('direction')->from('Language');

			// @codeCoverageIgnoreStart
			if ($langId)
				$direction = DB::where($langId)->loadSingle();
			else
				$direction = DB::where('default', 1)->loadSingle();
			// @codeCoverageIgnoreEnd

			$direction = strtolower($direction);

			if ($direction !== 'rtl')
				$direction = 'ltr';

			static::$_direction = $direction;
		}

		/*
		 * 1. Load system global language strings.
		 * 2. Load app global language strings and override system global language strings in point 1.
		 * 3. Load system package language strings.
		 * 4. Load app package language strings and override system package language strings in point 3.
		 * 5. Override global language strings in point 2 with package language strings in point 4.
		 *
		 * 2. app global languages          4. app package languages
		 *              |                                |
		 *              | <-------------- 5 ------------ |
		 *              V                                V
		 * 1. system global languages       3. system package languages
		 */

		$code = static::$_code;
		$systemGlobalStrings = [];
		$appGlobalStrings = [];
		$systemPackageStrings = [];
		$appPackageStrings = [];

		// 1. Load system global language strings.

		$path = PATH_LANGUAGE_SYSTEM . DS . $code. '.ini';
		if (is_file($path)) $systemGlobalStrings = parse_ini_file($path);

		// 2. Load app global language strings and override system global language strings in point 1.

		$path = PATH_LANGUAGE . DS . $code. '.ini';
		if (is_file($path)) $appGlobalStrings = parse_ini_file($path);

		$globalStrings = array_merge($systemGlobalStrings, $appGlobalStrings);

		// 3. Load system package language strings.

		$path = PATH_PACKAGE_SYSTEM . DS . PACKAGE . DS . 'languages' . DS . $code . '.ini';
		if ($path) $systemPackageStrings = parse_ini_file($path);

		// 4. Load app package language strings and override system package language strings in point 3.

		$path = PATH_PACKAGE . DS . PACKAGE . DS . 'languages' . DS . $code . '.ini';
		if ($path) $appPackageStrings = parse_ini_file($path);

		$packageStrings = array_merge($systemPackageStrings, $appPackageStrings);

		// 5. Override global language strings in point 2 with package language strings in point 4.

		static::$_strings[$langId] = array_merge($globalStrings, $packageStrings);

		////////////////////////

		/*
		// Load global langauge strings.

		$code = static::$_code;
		$paths = [
			PATH_LANGUAGE . '/' . $code . '.ini',
			PATH_LANGUAGE_SYSTEM . '/' . $code . '.ini'
		];

		$globalStrings = [];

		foreach ($paths as $path)
		{
			// @codeCoverageIgnoreStart
			if (is_file($path))
			{
				$globalStrings = parse_ini_file($path);
				break;
			}
			// @codeCoverageIgnoreEnd
		}

		// Load package langauge strings.

		$paths = [
			PATH_PACKAGE . '/' . PACKAGE . '/languages' . $code. '.ini',
			PATH_PACKAGE_SYSTEM . '/' . PACKAGE . '/languages' . $code. '.ini',
		];

		$packageStrings = [];

		foreach ($paths as $path)
		{
			// @codeCoverageIgnoreStart
			if (is_file($path))
			{
				$packageStrings = parse_ini_file($path);
				break;
			}
			// @codeCoverageIgnoreEnd
		}

		// Override global language strings with package language strings.
		static::$_strings[$langId] = array_merge($globalStrings, $packageStrings);
		*/
	}
}
