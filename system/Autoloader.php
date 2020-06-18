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

		if (is_file($file))
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
