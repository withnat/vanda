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
	protected static $_ip;
	protected static $_basePath;
	protected static $_isAjax;

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
	 * @param  string $key
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
		return $_SERVER['HTTP_REFERER'] ?? null;
	}

	/**
	 * Get the current visitor's IP address
	 *
	 * @return string
	 */
	public static function ip() : string
	{
		if (!Request::$_ip)
		{
			// ref : https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php/3003233
			//
			// There are different types of users behind the Internet, so we want
			// to catch the IP address from different portions. Those are:
			//
			// 1. $_SERVER['REMOTE_ADDR'] - Normal, non-proxied server or server
			// behind a transparent proxy. This contains the real IP address of
			// the client. That is the most reliable value you can find from the user.
			//
			// 2. $_SERVER['HTTP_CLIENT_IP'] - This will fetch the IP address when
			// the user is from shared Internet services (e.g. non-transparent proxy).
			//
			// 3. $_SERVER['HTTP_X_FORWARDED_FOR'] - This will fetch the IP address
			// from the user when he/she is behind the proxy (e.g. NginX).
			//
			// So we can use this following combined function to get the real IP
			// address from users who are viewing in diffrent positions,

			$ip = '';

			// Normally the $_SERVER superglobal is set
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			elseif (isset($_SERVER['HTTP_CLIENT_IP']))
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			elseif(isset($_SERVER['REMOTE_ADDR']))
				$ip = $_SERVER['REMOTE_ADDR'];

			// This part is executed on PHP running as CGI, or on SAPIs which do
			// not set the $_SERVER superglobal
			if (!$ip and function_exists('getenv'))
			{
				if (getenv('HTTP_X_FORWARDED_FOR'))
					$ip = getenv('HTTP_X_FORWARDED_FOR');
				elseif (getenv('HTTP_CLIENT_IP'))
					$ip = getenv('HTTP_CLIENT_IP');
				elseif (getenv('REMOTE_ADDR'))
					$ip = getenv('REMOTE_ADDR');
			}

			// ***
			// (In the future, check more information from this sources to improve above code.
			// https://www.ipqualityscore.com/articles/view/1/how-to-detect-proxies-with-php
			// and
			// https://stackoverflow.com/questions/7623187/will-the-value-of-a-set-serverhttp-client-ip-be-an-empty-string)
			// ***

			// Some proxies typically list the whole chain of IP
			// addresses through which the client has reached us.
			// e.g. Format: "X-Forwarded-For: client_ip, proxy_ip1, proxy_ip2, ..." etc.
			// ref : https://stackoverflow.com/questions/2422395/why-is-request-envremote-addr-returning-two-ips
			if ($ip)
			{
				$arr = explode(',', $ip);
				$ip = $arr[0];
				$ip = trim($ip);
			}

			if (!Validator::isValidIp($ip))
				$ip = '0.0.0.0';

			Request::$_ip = $ip;
		}

		return Request::$_ip;
	}

	/**
	 * Get the server protocol.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function protocol() : ?string
	{
		return $_SERVER['SERVER_PROTOCOL'] ?? null;
	}

	/**
	 * Get the scheme.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function scheme() : ?string
	{
		return $_SERVER['REQUEST_SCHEME'] ?? null;
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
			static::$_basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

		return static::$_basePath;
	}

	/**
	 * @return string
	 */
	public static function uri() : string
	{
		// IIS not recognizing ‘REQUEST_URI’
		if (strpos((string)static::server('SERVER_SOFTWARE'), 'IIS') !== false)
			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 0);

		// Remove static::getBasePath() string only first occurrence of a string match.
		// If not, it will remove all matches ie remove 'foo' from /foo/home/foobar.
		return preg_replace('/' . str_replace('/', '\/', static::basePath()) . '/i', '', $_SERVER['REQUEST_URI'], 1);
	}

	/**
	 * Get the query string.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function queryString() : ?string
	{
		return $_SERVER['QUERY_STRING'] ?? null;
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
	 * @return bool
	 */
	public static function isGet() : bool
	{
		return static::method() === 'get';
	}

	/**
	 * @return bool
	 */
	public static function isPost() : bool
	{
		return static::method() === 'post';
	}

	/**
	 * Checks if the current request was sent
	 * with a XMLHttpRequest header as sent by javascript.
	 *
	 * @return bool
	 */
	public static function isAjax() : bool
	{
		if (is_null(static::$_isAjax))
		{
			if ((string)static::server('HTTP_X_REQUESTED_WITH') == 'xmlhttprequest')
				static::$_isAjax = true;
			else
				static::$_isAjax = false;
		}

		return static::$_isAjax;
	}

	/**
	 * Checks if the current request was sent
	 * via the command line.
	 *
	 * @return bool
	 */
	public static function isCli() : bool
	{
		return PHP_SAPI === 'cli' or defined('STDIN');
	}

	/**
	 * @param  string|null $redirect
	 * @return void
	 */
	public static function ensureIsGet(string $redirect = null) : void
	{
		if (!static::isGet())
		{
			if (!trim((string)$redirect))
				$redirect = Url::default();

			Response::redirect($redirect);
		}
	}

	/**
	 * @param  string|null $redirect
	 * @return void
	 */
	public static function ensureIsPost(string $redirect = null) : void
	{
		if (!static::isPost())
		{
			if (!trim((string)$redirect))
				$redirect = Url::default();

			Response::redirect($redirect);
		}
	}

	/**
	 * @param  string|null $redirect
	 * @return void
	 */
	public static function ensureIsAjax(string $redirect = null) : void
	{
		if (!static::isAjax())
		{
			if (!trim((string)$redirect))
				$redirect = Url::default();

			Response::redirect($redirect);
		}
	}

	/**
	 * The built-in CLI web server does not support getallheaders().
	 * So don't test this method by adding 'codeCoverageIgnore' annotation.
	 *
	 * @param  string      $name
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function header(string $name)
	{
		// Don't add "ext-apache" to composer.json.
		// It will make updating composer fail.
		foreach (getallheaders() as $key => $value)
		{
			if (strtolower($key) === strtolower($name))
				return $value;
		}
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
}
