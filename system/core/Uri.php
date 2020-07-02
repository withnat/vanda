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
 * @package		Vanda
 * @author		Nat Withe <nat@withnat.com>
 * @copyright	Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @license		MIT
 * @link		http://vanda.io
 */

declare(strict_types=1);

namespace System;

/**
 * Class Uri
 * @package System
 */
final class Uri
{
	protected static $_routeByUris = [];
	protected static $_routeByActions = [];

	/**
	 * Uri constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string|null $uri
	 * @param  bool        $secure
	 * @return string
	 */
	public static function route(string $uri = null, bool $secure = false) : string
	{
		$uri = (string)$uri;
		$uri = trim($uri);
		$key = $uri;

//		if (!isset(static::$_routeByUris[$key]))
//		{
			if (stripos($uri, 'http://') === false and stripos($uri, 'https://') === false)
			{
				if (substr($uri, 0, 1) != '/')
					$uri = '/' . $uri;

				if ((int)\Setting::get('sef'))
					$prefix = '';
				else
					$prefix = '/index.php';

				if (SIDE === 'frontend')
				{
					$lang = (LANG ? '/' . LANG : '');
					$uri = Request::baseUrl() . $prefix . $lang . $uri;
				}
				else
				{
					$backendpath = \Setting::get('backendpath', '/admin');
					$uri = Request::baseUrl() . $prefix . $backendpath . $uri;
				}

				if ($secure)
					$uri = str_replace('http://', 'https://', $uri);

		//		static::$_routeByUris[$key] = $uri;
			}
		//}
return $uri;
		//return static::$_routeByUris[$key];
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
				$module = MODULE;
				$controller = $arr[0];
				$action = $arr[1];
			}

			$uri = $module . '/' . $controller . '/' . $action;
		}
		else
		{
			if (MODULE === CONTROLLER)
				$uri = MODULE . '/' . $action;
			else
				$uri = MODULE . '/' . CONTROLLER . '/' . $action;
		}

		$uri = static::route($uri);

		return $uri;
	}

	/**
	 * @param  string $url
	 * @return string
	 */
	public static function hashSPA(string $url) : string
	{
		$idPos = strpos($url, '?id=');
		$amPos = strpos($url, '&', $idPos);

		if ($idPos and $amPos === false)
			$url = str_replace('?id=', ':', $url);

		$url = '#' . $url;

		return $url;
	}

	/**
	 * @return string
	 */
	public static function getContext() : string
	{
		$url = Request::url();
		$arr = explode('?', $url);
		$url = $arr[0];

		// Replace same module and controller name with module name.
		// ie. http://localhost/vanda/admin/user/user
		// --> http://localhost/vanda/admin/user
		// Because you can access index action of module user using
		// 2 above urls. But Paginator will detect these urls in
		// different context (user and user/user) and will return
		// different 'pagesize' value.
		if (MODULE === CONTROLLER and stripos($url, MODULE . '/' . CONTROLLER))
			$url = str_replace(MODULE . '/' . CONTROLLER, MODULE, $url);

		$context = preg_replace('/[^a-z0-9]+/i', '', $url);

		return $context;
	}

	/**
	 * @param  string $uriSegment
	 * @return string
	 */
	public static function toControllerFormat(string $uriSegment) : string
	{
		$uriSegment = str_replace('_', '-', $uriSegment);
		$arr = explode('-', $uriSegment);

		for ($i = 0, $n = count($arr); $i < $n; ++$i)
			$arr[$i] = ucfirst($arr[$i]);

		$controller = implode('', $arr) . 'Controller';

		return $controller;
	}

	/**
	 * @param  string $uriSegment
	 * @return string
	 */
	public static function toActionFormat(string $uriSegment) : string
	{
		$uriSegment = str_replace('_', '-', $uriSegment);
		$arr = explode('-', $uriSegment);

		for ($i = 1, $n = count($arr); $i < $n; ++$i)
			$arr[$i] = ucfirst($arr[$i]);

		$action = implode('', $arr) . 'Action';

		return $action;
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
		return strtr(base64_encode($string), '+/', '-_');
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
		return base64_decode(strtr($string, '-_', '+/'));
	}

	/**
	 * Parse a URL and return its components.
	 * This is just an alias for PHP's native parse_url().
	 *
	 * @param  string $uri
	 * @return array
	 */
	public static function parse(string $uri) : array
	{
		return parse_url($uri);
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
