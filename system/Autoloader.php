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
	protected static $_loader;
	protected static $_helperLocations = [];
	protected static $_modelLocations = [];

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
		$file = str_replace('\\', '/', $file);

		if ($file === 'BaseController')
		{
			$file = BASEPATH . '/packages/base/' . SIDE . 'modules/controllers/BaseController.php';

			if (!is_file($file))
				$file = BASEPATH . '/system/packages/base/' . SIDE . '/modules/controllers/BaseController.php';
		}
		else if (substr($file, 0, 7) === 'System/')
			$file = substr_replace($file, BASEPATH . '/system/core/', 0, 7) . '.php';

		// The second condition is used to avoid including
		// a file from the root directory, such as index.php
		if (!is_file($file) or strpos($file, '/') === false)
		{
			if (substr($class, -6) === 'Helper')
			{
				$file = static::_getFile('helper', $class);

				if (!$file)
					throw new RuntimeException('The requested helper not found: ' . $class);
			}
			else
			{
				$file = static::_getFile('model', $class);

				if (!$file)
					throw new RuntimeException('The requested model not found: ' . $class);
			}
		}

		if (is_file($file))
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
		$file = BASEPATH . '/packages/' . $module . '/' . SIDE . '/modules/controllers/' . $controller . '.php';

		if (!is_file($file))
			$file = BASEPATH . '/system/packages/' . $module . '/' . SIDE . '/modules/controllers/' . $controller . '.php';

		if (!is_file($file))
			throw new RuntimeException('The requested module not found: ' . $controller);

		include_once $file;
	}

	/**
	 * Gets the location of the model.
	 *
	 * @param  string $type   The type of
	 * @param  string $class  The name of the model.
	 * @return string         Return the model location.
	 */
	protected static function _getFile(string $type, string $class) : string
	{
		if (!isset(static::${'_' . $type . 'Locations'}[$class]))
		{
			$tempFile = BASEPATH . '/storage/cache/models.php';

			if (!is_file($tempFile) or ENVIRONMENT === 'development')
				static::_loadFileLocationToTempFile($type);

			static::${'_' . $type . 'Locations'} = static::_loadFileLocationFromTempFile($type);
		}

		return static::${'_' . $type . 'Locations'}[$class];
	}

	/**
	 * Retrieve the paths of all helpers/models and save their locations to a temporary file.
	 *
	 * @param  string $type  The type of the file to load.
	 * @return void
	 */
	protected static function _loadFileLocationToTempFile(string $type) : void
	{
		$packagePaths = [
			BASEPATH . '/system/packages', // Scan system packages first.
			BASEPATH . '/packages' // Overwrite system packages with user packages.
		];

		$content = [];

		foreach ($packagePaths as $packagePath)
		{
			$packageEntries = scandir($packagePath);

			foreach ($packageEntries as $packageEntry)
			{
				if (in_array($packageEntry, ['.', '..']) or !is_dir($packagePath . '/' . $packageEntry))
					continue;

				$filePath = $packagePath . '/' . $packageEntry . '/' . $type . 's';

				if (is_dir($filePath))
				{
					$fileEntries = scandir($filePath);

					foreach ($fileEntries as $fileEntry)
					{
						if (strpos($fileEntry, '.php'))
						{
							$key = str_replace('.php', '', $fileEntry);
							$value = $filePath . '/' . $fileEntry;

							$content[$key] = $value;
						}
					}
				}
			}
		}

		$file = BASEPATH . '/storage/cache/' . $type . 's.php';
		$content = '<?php //' . serialize($content);
		file_put_contents($file, $content);
	}

	/**
	 * Retrieves the paths of all helpers/models from a temporary file.
	 *
	 * @param  string $type  The type of the file to load.
	 * @return array         Return the model locations.
	 */
	protected static function _loadFileLocationFromTempFile(string $type) : array
	{
		$file = BASEPATH . '/storage/cache/' . $type . 's.php';

		$content = file_get_contents($file);
		$content = substr($content, 8); // Remove '<?php //'
		$content = @unserialize($content);

		if ($content === false)
		{
			static::_loadFileLocationToTempFile($type);
			$content = static::_loadFileLocationFromTempFile($type);
		}

		return $content;
	}
}
