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

namespace System\Mvc;

use System\Arr;
use System\Request;
use System\Response;

abstract class Controller
{
	public function __call($method, $args)
	{
		$view = new View();
		$view->display();
	}

	public function preInit()
	{
	}

	public function init()
	{
//		if (isSPA() and !isSPAExclude())
//			Request::ensureIsAjax();
	}

	public function end()
	{
	}

	public function redirect($url)
	{
		Response::redirect($url);
	}

	public function page_not_found()
	{
		$view = new View(__CLASS__);
		$view->render('404');
	}

	public static function setCache($lifeTime)
	{
		new Cache($lifeTime);

		$cachePath = Cache::$cachePath;
		$cacheFile = Cache::$cacheFile;

		$lifeTime = (int)$lifeTime;

		if ($lifeTime > 0)
		{
			if (is_file($cachePath.$cacheFile))
			{
				$filemtime = filemtime($cachePath.$cacheFile);
				
				if (time()-$filemtime > $lifeTime)
				{
					File::delete($cachePath.$cacheFile);
					return;
				}
			}

			if (is_file($cachePath.$cacheFile))
			{
				$view = new View();
				$view->render($cacheFile);
				exit;
			}
		}
		else
		{
			File::delete($cachePath.$cacheFile);
			return;
		}
	}

	public static function clearCache($package=null, $subPackage=null, $action=null)
	{
		if ($package === null)
			$package = PACKAGE;

		if ($subPackage === null)
			$subPackage = SUBPACKAGE;

		if ($action === null)
			$action = ACTION;

		$cachePath = Cache::$cachePath;
		$cacheFile = md5($package.$subPackage.$action);

		$d = dir($cachePath);
		while (($fileName = $d->read()) !== false)
		{
			if (strpos(strtolower($fileName), EXT) === false)
				continue;

			if (substr($fileName, 0, 32) === $cacheFile)
				File::delete($cachePath.$fileName);
		}
	}
}
