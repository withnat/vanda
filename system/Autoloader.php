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
Use \Composer\Autoload\ClassLoader;
use RuntimeException;

/**
 * Decorate Composer ClassLoader
 */
class Autoloader
{
	private static $_loader;

	/**
	 * Constructor
	 *
	 * @param ClassLoader $loader  Composer autoloader
	 */
	public function __construct(ClassLoader $loader)
	{
		static::$_loader = $loader;
	}

	/**
	 * Finds the path to the file where the class is defined and includes it.
	 *
	 * @param  string $class  The name of the class to load.
	 * @return void
	 */
	public static function loadClass(string $class) : void
	{
		$file = $class;
		$file = str_replace('\\', DS, $file);

		if (substr($file, 0, 7) === 'System' . DS)
			$file = substr_replace($file, PATH_SYSTEM . DS . 'core' . DS, 0, 7);

		$file .= '.php';

		// The second condition is used to avoid including
		// a file from the root directory, such as index.php
		if (!is_file($file) or strpos($file, DS) === false)
		{
			if (substr($class, -6) === 'Helper')
				$file = Helper::getHelperLocation($class);
			else
				$file = Model::getModelLocation($class);
		}

		if (is_file((string)$file))
			// Include system file.
			include_once $file;
		else
			// Include composer file.
			static::$_loader->loadClass($class);
	}

	/**
	 * Find and include module controller file.
	 *
	 * @param  string $module      The name of the module.
	 * @param  string $controller  The name of the controller.
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

		throw new RuntimeException('The requested module not found.');
	}
}
