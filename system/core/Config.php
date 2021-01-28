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
