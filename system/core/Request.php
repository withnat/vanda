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

declare(strict_types=1);

namespace System;

use System\Exception\InvalidArgumentException;

/**
 * Class Request
 *
 * The Request class represents an HTTP request. It encapsulates the $_SERVER
 * variable and resolves its inconsistency among different Web servers. Also
 * it provides an interface to retrieve request parameters from $_POST, $_GET
 * and REST parameters sent via other HTTP methods like PUT or DELETE.
 *
 * @package System
 */
final class Request
{
	protected static $_getValues;
	protected static $_getValuesXSS;
	protected static $_postValues;
	protected static $_postValuesXSS;
	protected static $_method;
	protected static $_basePath;

	/**
	 * Request constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string           $name
	 * @param  string|int|float $value
	 * @param  string           $method
	 * @return void
	 */
	public static function set(string $name, $value, string $method = 'GET') : void
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string','int','float'], $value);

		$method = trim(strtoupper($method));

		if (!in_array($method, ['GET', 'POST']))
			throw InvalidArgumentException::valueError(3, '$method must be "GET" or "POST"', $method);

		switch ($method)
		{
			case 'GET':
				$_GET[$name] = $value;
				break;

			case 'POST':
				$_POST[$name] = $value;
				break;
		}
	}

	/**
	 * @param  string|null                        $name
	 * @param  string|int|float|array|object|null $default
	 * @param  bool                               $xssClean
	 * @return mixed
	 */
	public static function get(string $name = null, $default = null, ?bool $xssClean = true)
	{
		if (is_resource($default))
			throw InvalidArgumentException::typeError(2, ['string','int','float','array','object','null'], $default);

		return static::_requestByMethod('get', $name, $default, $xssClean);
	}

	/**
	 * @param  string|null                        $name
	 * @param  string|int|float|array|object|null $default
	 * @param  bool                               $xssClean
	 * @return mixed
	 */
	public static function post(string $name = null, $default = null, ?bool $xssClean = true)
	{
		if (is_resource($default))
			throw InvalidArgumentException::typeError(2, ['string','int','float','array','object','null'], $default);

		return static::_requestByMethod('post', $name, $default, $xssClean);
	}

	/**
	 * @param  string                             $method
	 * @param  string|null                        $name
	 * @param  string|int|float|array|object|null $default
	 * @param  bool                               $xssClean
	 * @return mixed
	 */
	private static function _requestByMethod(string $method, string $name = null, $default = null, ?bool $xssClean = true)
	{
		if (is_null(static::${'_' . $method . 'Values'}))
		{
			if ($method == 'get')
				$values = $_GET;
			else
				$values = $_POST;

			$values = Arr::toObject($values);
			static::${'_' . $method . 'Values'} = $values;

			$values = Security::xssClean($values);
			static::${'_' . $method . 'ValuesXSS'} = $values;
		}

		if (is_null($name))
		{
			if ($xssClean)
				return (empty((array)static::${'_' . $method . 'ValuesXSS'}) ? $default : static::${'_' . $method . 'ValuesXSS'});
			else
				return (empty((array)static::${'_' . $method . 'Values'}) ? $default : static::${'_' . $method . 'Values'});
		}
		else
		{
			if ($xssClean)
				return static::${'_' . $method . 'ValuesXSS'}->{$name} ?? $default;
			else
				return static::${'_' . $method . 'Values'}->{$name} ?? $default;
		}
	}

	/**
	 * @param  string      $key
	 * @return mixed
	 */
	public static function server(string $key)
	{
		return $_SERVER[$key] ?? null;
	}

	/**
	 * @return string|null
	 */
	public static function method() : ?string
	{
		if (!static::$_method)
		{
			$method = static::server('REQUEST_METHOD');

			if ($method)
				$method = strtolower($method);

			static::$_method = $method;
		}

		return static::$_method;
	}

	/**
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function referer()
	{
		return static::server('HTTP_REFERER');
	}

	/**
	 * @return string
	 */
	public static function ip() : string
	{
		$ip = (string)static::server('REMOTE_ADDR');

		if (!Data::isValidIP($ip))
			$ip = '0.0.0.0';

		return $ip;
	}

	/**
	 * @return bool
	 */
	public static function isSecure() : bool
	{
		$https = static::server('HTTPS');
		$serverPort = static::server('SERVER_PORT');

		if ($https == '1' or $https == 'on' or $serverPort == 443)
			return true;
		else
			return false;
	}

	/**
	 * @return string
	 */
	public static function host() : string
	{
		if (static::isSecure())
			$protocol = 'https://';
		else
			$protocol = 'http://';

		$host = $protocol . static::server('HTTP_HOST');

		return $host;
	}

	/**
	 * @return string
	 */
	public static function basePath() : string
	{
		if (!static::$_basePath)
			static::$_basePath = rtrim(dirname(static::server('SCRIPT_NAME')), '/\\');

		return static::$_basePath;
	}
}
