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
 *
 * The Url class provides methods that help you retrieve information from your
 * URL strings. If you use URL routing, you can also retrieve information about
 * the re-routed segments.
 *
 * @package System
 */
final class Url
{
	private static $_baseUrl;
	private static $_defaultUrl;
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
	 * @param  bool|null $secure
	 * @return string
	 * @codeCoverageIgnore
	 */
	public static function base(bool $secure = null) : string
	{
		if (!Url::$_baseUrl)
			Url::$_baseUrl = Request::host() . Request::basePath();

		$baseUrl = Url::$_baseUrl;

		if ($secure === true and substr($baseUrl, 0, 7) == 'http://')
			$baseUrl = substr_replace($baseUrl, 'https://', 0, 7);
		elseif ($secure === false and substr($baseUrl, 0, 8) == 'https://')
			$baseUrl = substr_replace($baseUrl, 'http://', 0, 8);

		return $baseUrl;
	}

	/**
	 * @return string
	 * @codeCoverageIgnore
	 */
	public static function default() : string
	{
		if (!Url::$_defaultUrl)
		{
			if (!isSPA())
			{
				$side = getenv('SIDE');

				if ($side == 'backend' and Config::app('defaultBackendModule'))
					Url::$_defaultUrl = Config::app('defaultBackendModule');
				elseif ($side == 'frontend' and Config::app('defaultFrontendModule'))
					Url::$_defaultUrl = Config::app('defaultFrontendModule');
				else
					Url::$_defaultUrl = Url::create();
			}
			else
				Url::$_defaultUrl = '';
		}

		return Url::$_defaultUrl;
	}

	/**
	 * @return string
	 * @codeCoverageIgnore
	 */
	public static function uri() : string
	{
		return Request::uri();
	}

	/**
	 * @return string
	 * @codeCoverageIgnore
	 */
	public static function current() : string
	{
		$url = Url::base() . Url::uri();

		return $url;
	}

	/**
	 * Example url
	 * http://user:pass@hostname:9090/path?arg=value#anchor
	 *
	 * @param  string|null $path
	 * @param  bool|null   $secure
	 * @return string
	 */
	public static function create(string $path = null, bool $secure = null) : string
	{
		if (is_null($path))
			$path = Url::$_path;

		// If the $path is still null, convert it to string.
		$path = (string)$path;
		// And remove white-space.
		$path = trim($path);

		if (!Url::isValid($path))
		{
			if (Url::$_scheme)
				$url = Url::$_scheme . '://';
			else
				$url = Url::base();

			if (Url::$_user and Url::$_pass)
				$url .= Url::$_user . ':' . Url::$_pass . '@';
			elseif (Url::$_user)
				$url .= Url::$_user . '@';
			elseif (Url::$_pass)
				$url .= ':' . Url::$_pass . '@';

			$url .= Url::$_host;

			if (Url::$_port)
				$url .= ':' . Url::$_port;
			
			if ($path and substr($path, 0, 1) != '/')
				$path = '/' . $path;

			if ((int)\Setting::get('sef'))
				$prefix = '';
			else
				$prefix = '/index.php';

			$side = getenv('SIDE');
			$lang = getenv('LANG');

			if ($side == 'frontend')
			{
				$lang = ($lang ? '/' . $lang : '');
				$url .= $prefix . $lang . $path;
			}
			else
			{
				$backendpath = \Setting::get('backendpath', '/admin');
				$url .= $prefix . $backendpath . $path;
			}
		}
		else
			$url = $path;

		if (Url::$_query)
		{
			if (substr(Url::$_query, 0, 1) != '?')
				$url .= '?';

			$url .= Url::$_query;
		}

		if (Url::$_fragment)
		{
			if (substr(Url::$_fragment, 0, 1) != '#')
				$url .= '#';

			$url .= Url::$_fragment;
		}

		if ($secure === true and substr($url, 0, 7) == 'http://')
			$url = substr_replace($url, 'https://', 0, 7);
		elseif ($secure === false and substr($url, 0, 8) == 'https://')
			$url = substr_replace($url, 'http://', 0, 8);

		Url::reset();

		return $url;
	}

	/**
	 * @param  string $action
	 * @return string
	 */
	public static function createFromAction(string $action) : string
	{
		Url::reset();

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

		$url = Url::create($uri);

		return $url;
	}

	/**
	 * Determine if the given path is a valid URL.
	 *
	 * @param  string $path
	 * @return bool
	 */
	public static function isValid($path) : bool
	{
		if (!preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path))
			return (filter_var($path, FILTER_VALIDATE_URL) !== false);

		return true;
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
			$data = Url::parse($url);
			$value = $data['scheme'] ?? null;
		}
		else
			$value = Url::$_scheme;

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
		Url::$_scheme = $scheme;
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
			$data = Url::parse($url);
			$value = $data['user'] ?? null;
		}
		else
			$value = Url::$_user;

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
		Url::$_user = $user;
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
			$data = Url::parse($url);
			$value = $data['pass'] ?? null;
		}
		else
			$value = Url::$_pass;

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
		Url::$_pass = $pass;
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
			$data = Url::parse($url);
			$value = $data['host'] ?? null;
		}
		else
			$value = Url::$_host;

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
		Url::$_host = $host;
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
			$data = Url::parse($url);
			$value = $data['port'] ?? null;
		}
		else
			$value = Url::$_port;

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
		Url::$_port = $port;
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
			$data = Url::parse($url);

			// Path index is always exist and be a string.
			$value = $data['path'] ?: null;
		}
		else
			$value = Url::$_path;

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
		$path = ltrim($path, '/');
		Url::$_path = $path;
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
			$data = Url::parse($url);
			$value = $data['query'] ?? null;
		}
		else
			$value = Url::$_query;

		return $value;
	}

	/**
	 * Set query value.
	 *
	 * @param  string $query
	 * @return void
	 */
	public static function setQuery(string $query) : void
	{
		$query = ltrim($query, '?');
		Url::$_query = $query;
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
			$data = Url::parse($url);
			$value = $data['fragment'] ?? null;
		}
		else
			$value = Url::$_fragment;

		return $value;
	}

	/**
	 * Set fragment value.
	 *
	 * @param  string $fragment
	 * @return void
	 */
	public static function setFragment(string $fragment) : void
	{
		$fragment = ltrim($fragment, '#');
		Url::$_fragment = $fragment;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function reset() : void
	{
		Url::$_baseUrl = null;
		Url::$_scheme = null;
		Url::$_user = null;
		Url::$_pass = null;
		Url::$_host = null;
		Url::$_port = null;
		Url::$_path = null;
		Url::$_query = null;
		Url::$_fragment = null;
	}
}
