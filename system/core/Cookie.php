<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package	    Vanda
 * @author	    Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
 * @link		http://vanda.io
 */

declare(strict_types=1);

namespace System;

/**
 * Class Cookie
 *
 * This class contains functions that assist in working with cookies.
 *
 * @package System
 */
class Cookie
{
	/**
	 * Config constructor.
	 */
	private function __construct(){}

	// Expire default to 0, the cookie will expire at the end of the session (when the browser closes)
	public static function set(string $name, $value = null, $expire = 0)
	{
		if ($expire)
			$expire += time();

		// PHP 7.2 : setcookie() function does not have SameSite attribute
		if (version_compare(PHP_VERSION, '7.3', '<'))
		{
		header("Set-Cookie: withnat=value; path=/; HttpOnly; SameSite=Strict");
		}
		else // PHP 7.3+
			setcookie($name, $value, $expire, '/');
	}

	public static function get($name, $default = null)
	{
		$value = '';

		if (isset($_COOKIE[$name]))
			$value = $_COOKIE[$name];

		if ($value == '')
			$value = $default;

		return $value;
	}

	public static function clear($name)
	{
		setcookie($name, '', time()-3600, '/');
	}
}
