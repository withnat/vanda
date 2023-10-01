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
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array', 'object', 'null'], $value);

		if (is_array($value))
			$value = ['datatype' => 'array', 'value' => $value];
		elseif (is_object($value))
			$value = ['datatype' => 'object', 'value' => $value];
		elseif (is_bool($value))
			$value = ['datatype' => 'bool', 'value' => $value];

		if (is_array($value))
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
		$value = '';

		if (isset($_COOKIE[$name]))
			$value = $_COOKIE[$name];

		if (mb_strlen((string)$value) === 0)
			$value = $default;

		if (Json::isValid($value))
		{
			$value = Json::decode($value, true);

			if (isset($value['datatype']))
			{
				switch ($value['datatype'])
				{
					case 'array':
						$value = $value['value'];
						break;
					case 'object':
						$value = (object)$value['value'];
						break;
					case 'bool':
						$value = (bool)$value['value'];
						break;
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
	 */
	public static function has(string $name) : bool
	{
		return isset($_COOKIE[$name]);
	}

	/**
	 * This method deletes a cookie by setting its expiration date to the past.
	 *
	 * @param string $name  The name of the cookie to delete.
	 * @return void
	 */
	public static function delete(string $name) : void
	{
		setcookie($name, '', time()-3600, '/');
	}

	/**
	 * This method clears all cookies.
	 *
	 * @return void
	 */
	public static function clear() : void
	{
		foreach ($_COOKIE as $name => $value)
			setcookie($name, '', time()-3600, '/');
	}
}
