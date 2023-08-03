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

namespace System;

use System\Mvc\Helper;
use System\Mvc\Model;

/**
 * Decorate Composer ClassLoader
 */
class Autoloader
{
	private static $_loader;

	/**
	 * Constructor
	 *
	 * @param object $loader  Composer autoloader
	 */
	public function __construct(object $loader)
	{
		static::$_loader = $loader;
	}

	/**
	 * @param  string $class
	 * @return void
	 */
	public static function loadClass(string $class) : void
	{
		$file = $class;
		$file = str_replace('\\', DS, $file);

		if (substr($file, 0, 7) === 'System' . DS)
			$file = substr_replace($file, PATH_SYSTEM . DS . 'core' . DS, 0, 7);

		$file .= '.php';

		// Second condition is used to avoid file at root directory ie index.php
		if (!is_file($file) or (is_file($file) and strpos($file, DS) === false))
		{
			if (substr($class, -6) === 'Helper')
				$file = Helper::getHelperLocation($class);
			else
				$file = Model::getModelLocation($class);
		}

		// Use is_string() for PHP 8.1 because Helper::getHelperLocation() and
		// Model::getModelLocation() may return null, which is not allowed in is_file().
		if (is_string($file) and is_file($file))
			include_once $file;
		else
			static::$_loader->loadClass($class);
	}

	/**
	 * @param  string $module
	 * @param  string $controller
	 * @return void
	 */
	public static function importModule(string $module, string $controller) : void
	{
		$paths = [
			PATH_APP . DS . 'modules' . DS . SIDE . DS . $module . DS . 'controllers' . DS,
			PATH_SYSTEM . DS . 'modules' . DS . SIDE . DS . $module . DS . 'controllers' . DS
		];

		foreach ($paths as $path)
		{
			$path .= $controller . '.php';

			if (is_file($path))
			{
				include_once $path;
				break;
			}
		}
	}
}
