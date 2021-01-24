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
 * Class Config
 * @package System
 */
final class Config
{
	/**
	 * @var array
	 */
	protected static $configs = [];

	/**
	 * Config constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string      $key
	 * @param  string|null $default
	 * @return mixed
	 */
	public static function get(string $key, string $default = null)
	{
		$keys = explode('.', $key);
		$file = current($keys);

		if (!array_key_exists($file, Config::$configs))
		{
			$path = PATH_BASE . DS . 'config' . DS . ENV . DS . $file . '.php';

			if (!is_file($path) or !is_readable(($path)))
				$path = PATH_BASE . DS . 'config' . DS . $file . '.php';

			// Don't use include_once because /System/requirements.php need to include config file too!
			// However, above IF array_key_exists condition will include config file once.
			Config::$configs[$file] = include($path);
		}

		return Arr::get(Config::$configs, $key, $default);
	}

	/**
	 * Sets a value in the config array.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public static function set(string $key, $value) : void
	{
		Arr::set(Config::$configs, $key, $value);
	}

	/**
	 * Removes value in the config array.
	 *
	 * @param  string $key
	 * @return void
	 */
	public static function remove(string $key) : void
	{
		Arr::removeKey(Config::$configs, $key);
	}

	/**
	 * Returns a value from the config array using the
	 * method call as the file reference.
	 *
	 * @example Config::app('url');
	 * @param   string $method
	 * @param   array  $args
	 * @return  mixed
	 */
	public static function __callStatic(string $method, array $args)
	{
		$key = $method;

		if (count($args))
		{
			$key .= '.' . array_shift($args);
			$default = array_shift($args);
		}
		else
			$default = null;

		return Config::get($key, $default);
	}
}
