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

use ErrorException;
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
	 * Prefix for cookie names.
	 *
	 * This property allows for specifying a prefix to be added to the names
	 * of cookies generated within the application. Adding a prefix helps in
	 * organizing and distinguishing cookies associated with this application
	 * from others. It's useful when multiple applications may be using cookies
	 * on the same domain.
	 */
	protected static $_prefix = '__vandaCookie_';

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
	public static function set(string $name, $value, int $expire = 0) : void
	{
		if (is_resource($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array', 'object', 'null'], $value);

		$name = static::$_prefix . $name;

		if (is_array($value))
			$value = ['__vandaCookieDatatype' => 'array', '__vandaCookieValue' => $value];
		elseif (is_object($value))
			$value = ['__vandaCookieDatatype' => 'object', '__vandaCookieValue' => $value];
		elseif (is_bool($value))
			$value = ['__vandaCookieDatatype' => 'bool', '__vandaCookieValue' => $value];

		if (is_array($value))
			$value = json_encode($value);

		// PHP 7.2: The setcookie() function does not have the SameSite attribute.
		// Therefore, we need to use the header() function to ensure the SameSite
		// attribute is set on all PHP versions.

		$expire += time();
		$expireDate = gmdate('D, d M Y H:i:s', $expire) . ' GMT';
		$setCookieString = $name . '=' . $value . '; expires= ' . $expireDate . '; path=/; HttpOnly; SameSite=Strict';

		if (Request::isSecure())
			$setCookieString .= '; Secure';

		header('Set-Cookie: ' . $setCookieString);
	}

	/**
	 * This method provides a friendlier syntax for getting browser cookies.
	 *
	 * @param string $name     The name of the cookie.
	 * @param mixed  $default  The default value to return if the cookie does not exist. Defaults to null.
	 * @return mixed           Returns the value of the cookie if it exists, otherwise returns the default value.
	 * @throws ErrorException
	 */
	public static function get(string $name, $default = null)
	{
		$name = static::$_prefix . $name;
		$value = '';

		if (isset($_COOKIE[$name]))
			$value = $_COOKIE[$name];

		if (mb_strlen((string)$value) === 0)
			$value = $default;

		if (Json::isValid($value))
		{
			$value = Json::decode($value, true);

			if (isset($value['__vandaCookieDatatype']))
			{
				switch ($value['__vandaCookieDatatype'])
				{
					case 'array':
						$value = $value['__vandaCookieValue'];
						break;
					case 'object':
						$value = Arr::toObject($value['__vandaCookieValue']);
						break;
					case 'bool':
						if ($value['__vandaCookieValue'] === 'true')
							$value = true;
						else
							$value = false;
				}
			}
		}

		return $value;
	}

	/**
	 * This method returns true if a cookie exists, false otherwise.
	 *
	 * @param string $name  The name of the cookie to check.
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public static function has(string $name) : bool
	{
		return isset($_COOKIE[static::$_prefix . $name]);
	}

	/**
	 * This method deletes a cookie by setting its expiration date to the past.
	 *
	 * @param string $name  The name of the cookie to delete.
	 * @return void
	 * @codeCoverageIgnore
	 */
	public static function delete(string $name) : void
	{
		setcookie(static::$_prefix . $name, '', time()-3600, '/');
	}

	/**
	 * This method clears all cookies.
	 *
	 * @return void
	 */
	public static function clear() : void
	{
		foreach ($_COOKIE as $name => $value)
		{
			if (strpos($name, static::$_prefix) !== false)
				setcookie(static::$_prefix . $name, '', time()-3600, '/');
		}
	}
}
