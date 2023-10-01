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
 *
 * This class is used to get and set configuration values.
 *
 * @package System
 * @codeCoverageIgnore
 */
class Config
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
	 * Returns a value from the config array.
	 *
	 * @param  string      $key      The key name to get.
	 * @param  string|null $default  Optionally, the default value if the config value is empty.
	 * @return mixed                 Returns the config value.
	 */
	public static function get(string $key, ?string $default = null)
	{
		$keys = explode('.', $key);
		$file = current($keys);

		if (!array_key_exists($file, static::$configs))
		{
			$path = PATH_BASE . DS . 'config' . DS . ENV . DS . $file . '.php';

			if (!is_file($path) or !is_readable(($path)))
				$path = PATH_BASE . DS . 'config' . DS . $file . '.php';

			// Don't use include_once because /System/requirements.php need to include config file too!
			// However, above IF array_key_exists condition will include config file once.
			static::$configs[$file] = include($path);
		}

		return Arr::get(static::$configs, $key, $default);
	}

	/**
	 * Updates a value in the config array for runtime modification,
	 * without affecting the underlying configuration file.
	 *
	 * @param  string $key    The key to set.
	 * @param  mixed  $value  The value to set.
	 * @return void
	 */
	public static function set(string $key, $value) : void
	{
		Arr::set(static::$configs, $key, $value);
	}

	/**
	 * Removes value in the config array.
	 *
	 * @param  string $key  The key name to remove.
	 * @return void
	 */
	public static function remove(string $key) : void
	{
		Arr::removeKey(static::$configs, $key);
	}

	/**
	 * Returns a value from the config array using the
	 * method call as the file reference.
	 *
	 * For example,
	 *
	 * ```php
	 * echo Config::app('env');
	 * echo Config::security('ssl');
	 * ```
	 *
	 * @param   string $method  Original method name.
	 * @param   array  $args    Arguments passed to the method.
	 * @return  mixed           Returns result of the get method.
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

		return static::get($key, $default);
	}
}
