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
class Request
{
	protected static $_getValues;
	protected static $_postValues;
	protected static $_method;
	protected static $_ip;
	protected static $_host;
	protected static $_basePath;
	protected static $_uri;
	protected static $_isSecure;
	protected static $_isAjax;

	/**
	 * Request constructor.
	 */
	private function __construct(){}

	/**
	 * Set a request variable.
	 *
	 * @param  string           $name    The variable name.
	 * @param  string|int|float $value   The variable value.
	 * @param  string           $method  The request variable to set (GET or POST). Default to POST.
	 * @return void
	 */
	public static function set(string $name, $value, string $method = 'POST') : void
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string','int','float'], $value);

		$method = trim(strtoupper($method));

		if (!in_array($method, ['GET', 'POST']))
			throw InvalidArgumentException::valueError(3, '$method must be "GET" or "POST"', $method);

		if ($method === 'POST')
			$_POST[$name] = $value;
		else
			$_GET[$name] = $value;

		$_REQUEST[$name] = $value;
	}

	/**
	 * Get a request variable on the GET request method.
	 *
	 * @param  string|null                        $name     The variable name.
	 * @param  string|int|float|array|object|null $default  Default value if the variable does not exist.
	 * @return mixed
	 */
	public static function get(string $name = null, $default = null)
	{
		if (is_resource($default))
			throw InvalidArgumentException::typeError(2, ['string','int','float','array','object','null'], $default);

		return static::_requestByMethod('get', $name, $default);
	}

	/**
	 * Get a request variable on the POST request method.
	 *
	 * @param  string|null                        $name     The variable name.
	 * @param  string|int|float|array|object|null $default  Default value if the variable does not exist.
	 * @return mixed
	 */
	public static function post(string $name = null, $default = null)
	{
		if (is_resource($default))
			throw InvalidArgumentException::typeError(2, ['string','int','float','array','object','null'], $default);

		return static::_requestByMethod('post', $name, $default);
	}

	/**
	 * Get data from a radio/single checkbox form submission and convert to integer.
	 *
	 * @param  string $name    The variable name.
	 * @param  string $method  Where the variable should come from (GET or POST).
	 * @return int
	 */
	public static function switcher(string $name, string $method = 'POST') : int
	{
		$method = trim(strtoupper($method));

		if (!in_array($method, ['GET', 'POST']))
			throw InvalidArgumentException::valueError(2, '$method must be "GET" or "POST"', $method);

		if (static::$method($name))
			return 1;
		else
			return 0;
	}

	/**
	 * Get the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
	 * Default to '' if not available. (e.g. PHP is running from cli).
	 *
	 * @return string
	 */
	public static function method() : string
	{
		if (is_null(static::$_method))
		{
			// Example of X-HTTP-Method-Override.
			// $.ajax({
			//     url: “http://localhost/api/Authors/1”,
			//     type: “POST”,
			//     data: JSON.stringify(authorData),
			//     headers: {
			//         “Content-Type”: “application/json”,
			//         “X-HTTP-Method-Override”: “PUT”
			//     }
			// })
			if (static::hasHeader('X-Http-Method-Override'))
				static::$_method = strtoupper(static::header('X-Http-Method-Override'));
			elseif (isset($_SERVER['REQUEST_METHOD']))
				static::$_method = strtoupper($_SERVER['REQUEST_METHOD']);
			else // if php is running from cli
				static::$_method = '';
		}

		return static::$_method;
	}

	/**
	 * Get the current visitor's IP address
	 *
	 * @return string
	 */
	public static function ip() : string
	{
		if (!static::$_ip)
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
			// (In the future, check more information from the following sources to improve above code.
			// https://www.ipqualityscore.com/articles/view/1/how-to-detect-proxies-with-php
			// https://stackoverflow.com/questions/7623187/will-the-value-of-a-set-serverhttp-client-ip-be-an-empty-string
			//https://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php)
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

			static::$_ip = $ip;
		}

		return static::$_ip;
	}

	/**
	 * Get the host name.
	 *
	 * @return string
	 */
	public static function host() : string
	{
		if (is_null(static::$_host))
		{
			if (isset($_SERVER['HTTP_HOST']))
			{
				if (static::isSecure())
					$protocol = 'https://';
				else
					$protocol = 'http://';

				static::$_host = $protocol . $_SERVER['HTTP_HOST'];
			}
			else // if php is running from cli
				static::$_host = '';
		}

		return static::$_host;
	}

	/**
	 * Returns the root path from which this request is executed.
	 *
	 * http://localhost/index.php         returns an empty string
	 * http://localhost/index.php/page    returns an empty string
	 * http://localhost/web/index.php     returns '/web'
	 * http://localhost/we%20b/index.php  returns '/we%20b'
	 *
	 * @return string
	 */
	public static function basePath() : string
	{
		if (!static::$_basePath)
			static::$_basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

		return static::$_basePath;
	}

	/**
	 * Detect the requested URI from server environment variables.
	 *
	 * @return string
	 */
	public static function uri() : string
	{
		if (is_null(static::$_uri))
		{
			// ‘REQUEST_URI’ variable is not recognized by some versions of IIS.
			// (At least I know IIS 10 recognize 'REQUEST_URI' variable)
			// Use a 'SCRIPT_NAME' variable instead of a 'REQUEST_URI' variable for IIS.
			if (empty($_SERVER['REQUEST_URI']) and strpos((string)static::server('SERVER_SOFTWARE'), 'IIS') !== false)
			{
				$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];

				if (!empty($_SERVER['QUERY_STRING']))
					$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}

			if (!empty($_SERVER['REQUEST_URI']))
			{
				// Remove static::basePath() string only first occurrence of a string match.
				// Otherwise, it will remove all matches ie remove 'foo' from /foo/home/foobar.
				$pattern = '/' . str_replace('/', '\/', static::basePath()) . '/i';
				$replacement = '';
				$subject = $_SERVER['REQUEST_URI'];
				$limit = 1;

				static::$_uri = preg_replace($pattern, $replacement, $subject, $limit);
			}
			else
				static::$_uri = '';
		}

		return static::$_uri;
	}

	/**
	 * Get the query string, null if not available.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function queryString() : ?string
	{
		return $_SERVER['QUERY_STRING'] ?? null;
	}

	/**
	 * Get a server variable from the request, null if not available.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public static function server(string $key)
	{
		return $_SERVER[$key] ?? null;
	}

	/**
	 * Get the URL referrer, null if not available.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function referer() : ?string
	{
		return $_SERVER['HTTP_REFERER'] ?? null;
	}

	/**
	 * Get the server protocol, null if not available.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function protocol() : ?string
	{
		return $_SERVER['SERVER_PROTOCOL'] ?? null;
	}

	/**
	 * Get the scheme, null if not available.
	 *
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function scheme() : ?string
	{
		return $_SERVER['REQUEST_SCHEME'] ?? null;
	}

	/**
	 * Get a header from the request, null if not available.
	 *
	 * The built-in CLI web server does not support getallheaders().
	 * So don't test this method by adding 'codeCoverageIgnore' annotation.
	 *
	 * @param  string      $name     The variable name.
	 * @param  string|null $default  Default value if the variable does not exist.
	 * @return string|null
	 * @codeCoverageIgnore
	 */
	public static function header(string $name, string $default = null) : ?string
	{
		// Don't add "ext-apache" to composer.json.
		// It will make updating composer fail.
		foreach (\getallheaders() as $key => $value)
		{
			if (strtolower($key) === strtolower($name))
				return $value;
		}

		return $default;
	}

	/**
	 * Determine if the request has a given header.
	 *
	 * @param  string $name  The variable name.
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public static function hasHeader(string $name) : bool
	{
		if (is_null(static::header($name)))
			return false;

		return true;
	}

	/**
	 * Determine if the request has any given header.
	 *
	 * @param  array $names  The variable name.
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public static function hasAnyHeader(array $names) : bool
	{
		foreach ($names as $name)
		{
			if (!is_null(static::header($name)))
				return true;
		}

		return false;
	}

	/**
	 * Determine if the request has all given headers.
	 *
	 * @param  array $names  The variable name.
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public static function hasAllHeaders(array $names) : bool
	{
		foreach ($names as $name)
		{
			if (is_null(static::header($name)))
				return false;
		}

		return true;
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return bool
	 */
	public static function isSecure() : bool
	{
		if (is_null(static::$_isSecure))
		{
			$https = (string)static::server('HTTPS');
			$serverPort = (string)static::server('SERVER_PORT');

			if ($https === '1' or $https === 'on' or $serverPort === '443')
				static::$_isSecure = true;
			else
				static::$_isSecure = false;
		}

		return static::$_isSecure;
	}

	/**
	 * Returns whether this is a GET request.
	 *
	 * @return bool
	 */
	public static function isGet() : bool
	{
		return static::method() === 'GET';
	}

	/**
	 * Returns whether this is a OPTIONS request.
	 *
	 * @return bool
	 */
	public static function isOptions() : bool
	{
		return static::method() === 'OPTIONS';
	}

	/**
	 * Returns whether this is a HEAD request.
	 *
	 * @return bool
	 */
	public static function isHead() : bool
	{
		return static::method() === 'HEAD';
	}

	/**
	 * Returns whether this is a POST request.
	 *
	 * @return bool
	 */
	public static function isPost() : bool
	{
		return static::method() === 'POST';
	}

	/**
	 * Returns whether this is a DELETE request.
	 *
	 * @return bool
	 */
	public static function isDelete() : bool
	{
		return static::method() === 'DELETE';
	}

	/**
	 * Returns whether this is a PUT request.
	 *
	 * @return bool
	 */
	public static function isPut() : bool
	{
		return static::method() === 'PUT';
	}

	/**
	 * Returns whether this is a PATCH request.
	 *
	 * @return bool
	 */
	public static function isPatch() : bool
	{
		return static::method() === 'PATCH';
	}

	/**
	 * Returns whether this is an AJAX (XMLHttpRequest) request.
	 *
	 * @return bool
	 */
	public static function isAjax() : bool
	{
		if (is_null(static::$_isAjax))
		{
			if ((string)static::server('HTTP_X_REQUESTED_WITH') === 'xmlhttprequest')
				static::$_isAjax = true;
			else
				static::$_isAjax = false;
		}

		return static::$_isAjax;
	}

	/**
	 * Checks if the current request was sent via the command line.
	 *
	 * @return bool
	 */
	public static function isCli() : bool
	{
		return PHP_SAPI === 'cli' or defined('STDIN');
	}

	/**
	 * Redirect to the default URL if this is not a GET request.
	 *
	 * @param  string|null $redirect
	 * @return void
	 * @codeCoverageIgnore
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
	 * Redirect to the default URL if this is not a POST request.
	 *
	 * @param  string|null $redirect
	 * @return void
	 * @codeCoverageIgnore
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
	 * Redirect to the default URL if this is not an AJAX (XMLHttpRequest) request.
	 *
	 * @param  string|null $redirect
	 * @return void
	 * @codeCoverageIgnore
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
	 * Fetches and returns a given variable depending on the request method.
	 *
	 * @param  string                             $method   Where the variable should come from (GET or POST).
	 * @param  string|null                        $name     The variable name.
	 * @param  string|int|float|array|object|null $default  Default value if the variable does not exist.
	 * @return mixed
	 */
	private static function _requestByMethod(string $method, string $name = null, $default = null)
	{
		if (is_null(static::${'_' . $method . 'Values'}))
		{
			if ($method === 'post')
				$values = $_POST;
			else
				$values = $_GET;

			$values = Arr::toObject($values);
			$values = Security::xssClean($values);

			static::${'_' . $method . 'Values'} = $values;
		}

		if (is_string($name))
		{
			if (strpos($name, '.') === false)
			{
				if (empty(static::${'_' . $method . 'Values'}->{$name}))
					$value = $default;
				else
					$value = static::${'_' . $method . 'Values'}->{$name};
			}
			else
			{
				$arrNames = explode('.', $name);
				$name = $arrNames[0];

				if (empty(static::${'_' . $method . 'Values'}->{$name}))
					$value = $default;
				else
					$value = static::${'_' . $method . 'Values'}->{$name};

				array_shift($arrNames);

				foreach ($arrNames as $key)
				{
					if (empty($value->{$key}))
					{
						$value = $default;
						break;
					}

					$value = $value->{$key};
				}
			}
		}
		else // NULL
		{
			if (empty((array)static::${'_' . $method . 'Values'}))
				$value = $default;
			else
				$value = static::${'_' . $method . 'Values'};
		}

		return $value;
	}
}
