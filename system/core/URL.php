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
 * Class URL
 * @package System
 */
final class URL
{
	/**
	 * URL constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string|null $uri
	 * @param  bool|null   $secure
	 * @return string
	 */
	public static function route(string $uri = null, bool $secure = null) : string
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

			if ($secure === true and substr($url, 0, 7) == 'http://')
				$url = substr_replace($url, 'https://', 0, 7);
			elseif ($secure === false and substr($url, 0, 8) == 'https://')
				$url = substr_replace($url, 'http://', 0, 8);
		}
		else
			$url = $uri;

		return $url;
	}

	/**
	 * @param  string $action
	 * @return string
	 */
	public static function routeByAction(string $action) : string
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

		$url = static::route($uri);

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
	 * @return string
	 */
	public static function getContext() : string
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
	 * @param  string $url
	 * @return string
	 */
	public static function getScheme(string $url) : ?string
	{
		return @static::parse($url)['scheme'];
	}

	/**
	 * Parse a URL and return user value.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getUser(string $url) : ?string
	{
		return @static::parse($url)['user'];
	}

	/**
	 * Parse a URL and return password value.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getPass(string $url) : ?string
	{
		return @static::parse($url)['pass'];
	}

	/**
	 * Parse a URL and return host value.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getHost(string $url) : ?string
	{
		return @static::parse($url)['host'];
	}

	/**
	 * Parse a URL and return port value.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getPort(string $url) : ?string
	{
		return @static::parse($url)['port'];
	}

	/**
	 * Parse a URL and return path value.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getPath(string $url) : ?string
	{
		return @static::parse($url)['path'];
	}

	/**
	 * Parse a URL and return query string.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getQuery(string $url) : ?string
	{
		return @static::parse($url)['query'];
	}

	/**
	 * Parse a URL and return fragment value.
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function getFragment(string $url) : ?string
	{
		return @static::parse($url)['fragment'];
	}
}
