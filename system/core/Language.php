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

/**
 * Class Language
 * @package System
 */
final class Language
{
	protected static $_direction;
	protected static $_strings;
	protected static $_code;

	/**
	 * Language constructor.
	 */
	private function __construct(){}

	/**
	 * @param  int|null $langId
	 * @return void
	 */
	private static function _getCode(int $langId = null) : void
	{
		DB::select('code')->from('Language');

		if ($langId)
			$code = DB::where($langId)->loadSingle();
		else
			$code = DB::where('default', 1)->loadSingle();

		Language::$_code = $code;
	}

	/**
	 * @param  int|null $langId
	 * @return void
	 */
	private static function _getDirection(int $langId = null) : void
	{
		DB::select('direction')->from('Language');

		if ($langId)
			$direction = DB::where($langId)->loadSingle();
		else
			$direction = DB::where('default', 1)->loadSingle();

		$direction = strtolower($direction);

		if ($direction != 'rtl')
			$direction = 'ltr';

		Language::$_direction = $direction;
	}

	/**
	 * @param  int|null $langId
	 * @return void
	 */
	private static function _getLanguage(int $langId = null) : void
	{
		Language::_getCode($langId);
		Language::_getDirection($langId);

		$code = Language::$_code;

		$langPaths = [
			PATH_APP . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.ini',
			PATH_SYSTEM . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.ini'
			];

		$globalStrings = [];

		foreach ($langPaths as $path)
		{
			if (is_file($path))
			{
				$globalStrings = parse_ini_file($path);
				break;
			}
		}

		$langPaths = [
			PATH_APP . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.module.' . MODULE . '.ini',
			PATH_SYSTEM . DS . 'languages' . DS . SIDE . DS . $code . DS . $code . '.module.' . MODULE . '.ini'
		];

		$moduleStrings = [];

		foreach ($langPaths as $path)
		{
			if (is_file($path))
			{
				$moduleStrings = parse_ini_file($path);
				break;
			}
		}

		Language::$_strings[$langId] = array_merge($globalStrings, $moduleStrings);
	}

	/**
	 * @param  string   $string
	 * @param  int|null $langId
	 * @return string
	 */
	public static function _(string $string, int $langId = null) : string
	{
		if (!$string)
			return '';

		if (!isset(Language::$_strings[$langId]))
			Language::_getLanguage($langId);

		if (isset(Language::$_strings[$langId][$string]))
			$string = Language::$_strings[$langId][$string];

		return $string;
	}

	/**
	 * @param  int|null $langId
	 * @return string
	 */
	public static function code(int $langId = null) : string
	{
		if (!Language::$_code)
			Language::_getCode($langId);

		return Language::$_code;
	}

	/**
	 * @param  int|null $langId
	 * @return string
	 */
	public static function direction(int $langId = null) : string
	{
		if (!Language::$_direction)
			Language::_getDirection($langId);

		return Language::$_direction;
	}
}
