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
		$file = str_replace('\\', DS, $file);

		if (substr($file, 0, 7) === 'System' . DS)
			$file = substr_replace($file, PATH_SYSTEM . DS . 'core' . DS, 0, 7);

		$file .= '.php';

		// The second condition is used to avoid including
		// a file from the root directory, such as index.php
		if (!is_file($file) or strpos($file, DS) === false)
		{
			if (substr($class, -6) === 'Helper')
			{
				$file = static::_getFile('helper', $class);

				if (!$file)
					throw new RuntimeException('The requested helper not found: ' . $class);
			}
			elseif (substr($class, -5) === 'model')
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

		throw new RuntimeException('The requested module not found: ' . $controller);
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
			$tempFile = PATH_STORAGE . '/cache/models.php';

			if (!is_file($tempFile) or ENV === 'development')
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
		$tempFile = PATH_STORAGE . '/cache/' . $type . '.php';
		$packagePath = BASEPATH_PACKAGES;
		$packageEntries = scandir($packagePath);

		$content = [];

		foreach ($packageEntries as $packageEntry)
		{
			if (!is_dir($packagePath . '/' . $packageEntry))
				continue;

			$filePath = $packagePath . '/' . $packageEntry . '/' . $type . 's';

			if (is_dir($filePath))
			{
				$fileEntries = scandir($filePath);

				foreach ($fileEntries as $fileEntry)
				{
					if (mb_stripos($fileEntry, '.php') === false)
						continue;

					$content[$fileEntry] = $filePath . '/' . $fileEntry;
				}
			}
		}

		$fp = fopen($tempFile, 'w');
		fwrite($fp, '<?php //'.serialize($content));
	}

	/**
	 * Retrieves the paths of all helpers/models from a temporary file.
	 *
	 * @param  string $type  The type of the file to load.
	 * @return array         Return the model locations.
	 */
	protected static function _loadFileLocationFromTempFile(string $type) : array
	{
		$tempFile = PATH_STORAGE . '/cache/' . $type . 's.php';

		$content = file_get_contents($tempFile);
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
