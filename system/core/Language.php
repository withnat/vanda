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

		static::$_code = $code;
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

		static::$_direction = $direction;
	}

	/**
	 * @param  int|null $langId
	 * @return void
	 */
	private static function _getLanguage(int $langId = null) : void
	{
		static::_getCode($langId);
		static::_getDirection($langId);

		$code = static::$_code;

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

		static::$_strings[$langId] = array_merge($globalStrings, $moduleStrings);
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

		if (!isset(static::$_strings[$langId]))
			static::_getLanguage($langId);

		if (isset(static::$_strings[$langId][$string]))
			$string = static::$_strings[$langId][$string];

		return $string;
	}

	/**
	 * @param  int|null $langId
	 * @return string
	 */
	public static function code(int $langId = null) : string
	{
		if (!static::$_code)
			static::_getCode($langId);

		return static::$_code;
	}

	/**
	 * @param  int|null $langId
	 * @return string
	 */
	public static function direction(int $langId = null) : string
	{
		if (!static::$_direction)
			static::_getDirection($langId);

		return static::$_direction;
	}
}
