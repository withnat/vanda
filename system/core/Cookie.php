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

use System\Exception\InvalidArgumentException;

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

	/**
	 * This method provides a friendlier syntax for setting browser cookies.
	 *
	 * @param string                             $name    The name of the cookie.
	 * @param string|int|float|array|object|null $value   The value of the cookie.
	 * @param int                                $expire  The number of seconds until expiration.
	 * @return void
	 */
	public static function set(string $name, $value = null, int $expire = 0) : void
	{
		if (is_resource($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array', 'object', 'null'], $default);

		if (is_array($value) or is_object($value))
			$value = json_encode($value);

		// PHP 7.2 : setcookie() function does not have SameSite attribute.
		if (version_compare(PHP_VERSION, '7.3', '<'))
		{
			$expire += time();
			$expireDate = gmdate('D, d M Y H:i:s', $expire) . ' GMT';
			$setCookieString = $name . '=' . $value . '; expires= ' . $expireDate . '; path=/; HttpOnly; SameSite=Strict';

			if (Request::isSecure())
				$setCookieString .= '; Secure';

			header('Set-Cookie: ' . $setCookieString);
		}
		else // PHP 7.3+
		{
			$setCookieOptions = [
				'expires' => time() + $expire,
				'path' => '/',
				'httponly' => true,
				'samesite' => 'Strict',
			];

			if (Request::isSecure())
				$setCookieOptions['secure'] = true;

			setcookie($name, $value, $setCookieOptions);
		}
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

	public static function has($name) : bool
	{
		return isset($_COOKIE[$name]);
	}

	public static function clear($name)
	{
		setcookie($name, '', time()-3600, '/');
	}
}
