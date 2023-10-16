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
 * @link         http://vanda.io
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
	 * from the module language file, and finally, override the global
	 * language strings with those from the module.
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

		// Load global langauge strings.

		$code = static::$_code;
		$paths = [
			PATH_APP . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.ini',
			PATH_SYSTEM . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.ini'
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

		// Load module langauge strings.

		$paths = [
			PATH_APP . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.module.' . MODULE . '.ini',
			PATH_SYSTEM . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.module.' . MODULE . '.ini'
		];

		$moduleStrings = [];

		foreach ($paths as $path)
		{
			// @codeCoverageIgnoreStart
			if (is_file($path))
			{
				$moduleStrings = parse_ini_file($path);
				break;
			}
			// @codeCoverageIgnoreEnd
		}

		// Override global language strings with module language strings.
		static::$_strings[$langId] = array_merge($globalStrings, $moduleStrings);
	}
}
