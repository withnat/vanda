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

/**
 * Class Url
 * @package System
 */
final class Url
{
	private static $_scheme;
	private static $_user;
	private static $_pass;
	private static $_host;
	private static $_port;
	private static $_path;
	private static $_query;
	private static $_fragment;

	/**
	 * Url constructor.
	 */
	private function __construct(){}

	/**
	 * @return string
	 */
	public static function base() : string
	{

	}

	/**
	 * @param  string|null $side
	 * @return string
	 */
	public static function default($side = null) : string
	{

	}

	/**
	 * @return string
	 */
	public static function uri() : string
	{

	}

	/**
	 * @return string
	 */
	public static function current() : string
	{

	}

	/**
	 * @param  string|null $uri
	 * @param  bool|null   $secure
	 * @return string
	 */
	public static function create(string $uri = null, bool $secure = null) : string
	{
		// If the given $string is null, convert to string first.
		$uri = (string)$uri;
		$uri = trim($uri);

		if (stripos($uri, 'http://') === false and stripos($uri, 'https://') === false)
		{
			if (substr($uri, 0, 1) != '/')
				$uri = '/' . $uri;

			if ((int)\Setting::get('sef'))
				$prefix = '';
			else
				$prefix = '/index.php';

			$side = getenv('SIDE');
			$lang = getenv('LANG');

			if ($side == 'frontend')
			{
				$lang = ($lang ? '/' . $lang : '');
				$url = Request::baseUrl() . $prefix . $lang . $uri;
			}
			else
			{
				$backendpath = \Setting::get('backendpath', '/admin');
				$url = Request::baseUrl() . $prefix . $backendpath . $uri;
			}
		}
		else
			$url = $uri;

		if ($secure === true and substr($url, 0, 7) == 'http://')
			$url = substr_replace($url, 'https://', 0, 7);
		elseif ($secure === false and substr($url, 0, 8) == 'https://')
			$url = substr_replace($url, 'http://', 0, 8);

		return $url;
	}

	/**
	 * @param  string $action
	 * @return string
	 */
	public static function createFromAction(string $action) : string
	{
		if (strpos($action, '.'))
		{
			$arr = explode('.', $action);

			if (count($arr) > 2)
			{
				$module = $arr[0];
				$controller = $arr[1];
				$action = $arr[2];
			}
			else
			{
				$module = getenv('MODULE');
				$controller = $arr[0];
				$action = $arr[1];
			}

			$uri = $module . '/' . $controller . '/' . $action;
		}
		else
		{
			if (getenv('MODULE') === getenv('CONTROLLER'))
				$uri = getenv('MODULE') . '/' . $action;
			else
				$uri = getenv('MODULE') . '/' . getenv('CONTROLLER') . '/' . $action;
		}

		$url = static::create($uri);

		return $url;
	}

	/**
	 * @param  string $uri
	 * @return string
	 */
	public static function hashSPA(string $uri) : string
	{
		$idPos = strpos($uri, '?id=');
		$amPos = strpos($uri, '&', (int)$idPos);

		if ($idPos and $amPos === false)
			$uri = str_replace('?id=', ':', $uri);

		$hash = '#' . $uri;

		return $hash;
	}

	/**
	 * @param  string|null $url
	 * @return string
	 */
	public static function toContext($url = null) : string
	{
		if (!$url)
		{
			$url = Request::url();

			$arr = explode('?', $url);
			$url = $arr[0];

			$module = getenv('MODULE');
			$controller = getenv('CONTROLLER');

			// Replace same module and controller name with module name.
			// ie. http://localhost/vanda/admin/user/user
			// --> http://localhost/vanda/admin/user
			// Because you can access index action of module user using
			// 2 above urls. But Paginator will detect these urls in
			// different context (user and user/user) and will return
			// different 'pagesize' value.
			if ($module === $controller and stripos($url, $module . '/' . $controller))
				$url = str_replace($module . '/' . $controller, $module, $url);
		}
		else
		{
			$arr = explode('?', $url);
			$url = $arr[0];
		}

		$context = preg_replace('/[^a-z0-9]+/i', '', $url);

		return $context;
	}

	/**
	 * Encodes string into "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
	 *
	 * > Note: Base 64 padding `=` may be at the end of the returned string.
	 * > `=` is not transparent to URL encoding.
	 *
	 * @see https://tools.ietf.org/html/rfc4648#page-7
	 *
	 * @param  string $string  The string to encode.
	 * @return string          Encoded string.
	 */
	public static function encode(string $string) : string
	{
		$string = base64_encode($string);
		$string = str_replace(['+', '/'], ['-', '_'], $string);

		return $string;
	}

	/**
	 * Decodes "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
	 *
	 * @see https://tools.ietf.org/html/rfc4648#page-7
	 *
	 * @param  string $string  Encoded string.
	 * @return string          Decoded string.
	 */
	public static function decode(string $string) : string
	{
		$string = str_replace(['-', '_'], ['+', '/'], $string);
		$string = base64_decode($string);

		return $string;
	}

	/**
	 * Parse a URL and return its components.
	 * This is just an alias for PHP's native parse_url().
	 *
	 * @param  string $url
	 * @return array
	 */
	public static function parse(string $url) : array
	{
		return parse_url($url);
	}

	/**
	 * Parse a URL and return scheme value.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getScheme(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['scheme'] ?? null;
		}
		else
			$value = static::$_scheme;

		return $value;
	}

	/**
	 * Set scheme value.
	 *
	 * @param  string $scheme
	 * @return void
	 */
	public static function setScheme(string $scheme) : void
	{
		static::$_scheme = $scheme;
	}

	/**
	 * Parse a URL and return user value.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getUser(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['user'] ?? null;
		}
		else
			$value = static::$_user;

		return $value;
	}

	/**
	 * Set user value.
	 *
	 * @param  string $user
	 * @return void
	 */
	public static function setUser(string $user) : void
	{
		static::$_user = $user;
	}

	/**
	 * Parse a URL and return password value.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getPass(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['pass'] ?? null;
		}
		else
			$value = static::$_pass;

		return $value;
	}

	/**
	 * Set password value.
	 *
	 * @param  string $pass
	 * @return void
	 */
	public static function setPass(string $pass) : void
	{
		static::$_pass = $pass;
	}

	/**
	 * Parse a URL and return host value.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getHost(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['host'] ?? null;
		}
		else
			$value = static::$_host;

		return $value;
	}

	/**
	 * Set host value.
	 *
	 * @param  string $host
	 * @return void
	 */
	public static function setHost(string $host) : void
	{
		static::$_host = $host;
	}

	/**
	 * Parse a URL and return port value.
	 *
	 * @param  string|null $url
	 * @return int|null
	 */
	public static function getPort(string $url = null) : ?int
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['port'] ?? null;
		}
		else
			$value = static::$_port;

		return $value;
	}

	/**
	 * Set port value.
	 *
	 * @param  int  $port
	 * @return void
	 */
	public static function setPort(int $port) : void
	{
		static::$_port = $port;
	}

	/**
	 * Parse a URL and return path value.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getPath(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);

			// Path index is always exist and be a string.
			$value = $data['path'] ?: null;
		}
		else
			$value = static::$_path;

		return $value;
	}

	/**
	 * Set path value.
	 *
	 * @param  string $path
	 * @return void
	 */
	public static function setPath(string $path) : void
	{
		static::$_path = $path;
	}

	/**
	 * Parse a URL and return query string.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getQuery(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['query'] ?? null;
		}
		else
			$value = static::$_query;

		return $value;
	}

	/**
	 * Parse a URL and return fragment value.
	 *
	 * @param  string|null $url
	 * @return string|null
	 */
	public static function getFragment(string $url = null) : ?string
	{
		if ($url)
		{
			$data = static::parse($url);
			$value = $data['fragment'] ?? null;
		}
		else
			$value = static::$_fragment;

		return $value;
	}
}
