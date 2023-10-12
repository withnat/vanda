<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2024, Nat Withe. All rights reserved.
 * @link        https://vanda.io
 */

declare(strict_types=1);

namespace System;

/**
 * Class App
 *
 * @package System
 */
class App
{
	protected static $_isSpa;

	/**
	 * App constructor.
	 */
	private function __construct(){}

	/**
	 * Whether the system is running in single-page application mode.
	 *
	 * @return bool  Returns true if the system is running in single-page application mode, false otherwise.
	 */
	public static function isSpa() : bool
	{
		if (is_null(static::$_isSpa))
		{
			$side = getenv('APP_SIDE');
			$frontendSpaMode = getenv('APP_FRONTEND_SPA_MODE');
			$backendSpaMode = getenv('APP_BACKEND_SPA_MODE');

			if (($side === 'frontend' and $frontendSpaMode) or ($side === 'backend' and $backendSpaMode))
				static::$_isSpa = true;
			else
				static::$_isSpa = false;
		}

		return static::$_isSpa;
	}
}
