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

use System\Exception\InvalidArgumentException;

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
	 * @param  string $key      The key name to get.
	 * @param  mixed  $default  Optionally, the default value if the config value is empty.
	 * @return mixed            Returns the config value.
	 */
	public static function get(string $key, $default = null)
	{
		if (is_array($default) or is_object($default) or is_resource($default))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'boll', 'null'], $default);

		$keys = explode('.', $key);
		$file = current($keys);

		//if (!array_key_exists($file, static::$configs) and !array_key_exists('config', static::$configs))
		//{
			$path = PATH_CONFIG . DS . ENVIRONMENT . DS . $file . '.php';

			if (!is_file($path) or !is_readable(($path)))
			{
				$file = 'config';
				$path = PATH_CONFIG . DS . $file . '.php';
			}

			// Don't use include_once because /System/requirements.php need to include config file too!
			// However, above IF array_key_exists condition will include config file once.
			static::$configs[$file] = include($path);
		//}

		// The main config value can be overridden by the environment config value.
		// So, try to get value from the environment config file first.
		$value = Arr::get(static::$configs, $key, $default);

		// If the value is still empty, try to get value from the main config file.
		if (!$value)
			$value = Arr::get(static::$configs, 'config.' . $key, $default);

		return $value;
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
