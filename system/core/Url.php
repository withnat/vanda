<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package     Vanda
 * @author      Nat Withe <nat@withnat.com>
 * @copyright   Copyright (c) 2010 - 2021, Nat Withe. All rights reserved.
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
class Url
{
	protected static $_baseUrl;
	protected static $_defaultUrl;
	protected static $_scheme;
	protected static $_user;
	protected static $_pass;
	protected static $_host;
	protected static $_port;
	protected static $_path;
	protected static $_query;
	protected static $_fragment;

	/**
	 * Url constructor.
	 */
	private function __construct(){}

	/**
	 * Gets base URL.
	 *
	 * @param  bool|null $secure  If true, force the scheme to be HTTPS. Defaults to null.
	 *                            If the value is null, retrieve the $secure value from
	 *                            the security configuration file.
	 * @return string             Returns the base URL.
	 */
	public static function base(bool $secure = null) : string
	{
		if (!static::$_baseUrl)
			static::$_baseUrl = Request::host() . Request::basePath();

		$baseUrl = static::$_baseUrl;

		if (is_null($secure))
			$secure = (bool)\Config::security('ssl');

		if ($secure and substr($baseUrl, 0, 7) === 'http://')
			$baseUrl = substr_replace($baseUrl, 'https://', 0, 7);
		elseif (!$secure and substr($baseUrl, 0, 8) === 'https://')
			$baseUrl = substr_replace($baseUrl, 'http://', 0, 8);

		return $baseUrl;
	}

	/**
	 * Gets default URL based on the current side (frontend or backend).
	 *
	 * @return string  Returns the default URL.
	 * @codeCoverageIgnore
	 */
	public static function default() : string
	{
		if (!static::$_defaultUrl)
		{
			if (!App::isSpa())
			{
				$side = getenv('APP_SIDE');

				if ($side === 'backend' and Config::app('defaultBackendModule'))
					static::$_defaultUrl = Config::app('defaultBackendModule');
				elseif ($side === 'frontend' and Config::app('defaultFrontendModule'))
					static::$_defaultUrl = Config::app('defaultFrontendModule');
				else
					static::$_defaultUrl = static::create();
			}
			else
				static::$_defaultUrl = '';
		}

		return static::$_defaultUrl;
	}

	/**
	 * Gets URI from the given URL. If the URL is null, it will return the current URI.
	 *
	 * @param string|null $url  The URL to be parsed.
	 * @return string           Returns the current URL.
	 * @codeCoverageIgnore
	 */
	public static function uri(string $url = null) : string
	{
		if (is_null($url))
			$uri = Request::uri();
		else
			$uri = str_replace(static::base(), '/', $url);

		return $uri;
	}

	/**
	 * An alias for getQueryString() method.
	 *
	 * @param string|null $url  The URL to be parsed.
	 * @return string           Returns query string, null if not available.
	 * @codeCoverageIgnore
	 */
	public static function queryString(string $url = null) : ?string
	{
		return static::getQueryString($url);
	}

	/**
	 * Gets current URL with query string (if any).
	 *
	 * @return string  Returns the current URL with query string.
	 * @codeCoverageIgnore
	 */
	public static function current() : string
	{
		$url = static::base() . static::uri();

		return $url;
	}

	/**
	 * Creates full URL from the given path.
	 *
	 * Example url.
	 * http://user:pass@hostname:9090/path?arg=value#anchor
	 *
	 * @param  string|null $path    The path to be appended to the URL.
	 * @param  bool|null   $secure  If true, force the scheme to be HTTPS. Defaults to null.
	 *                              If the value is null, retrieve the $secure value from
	 *                              the security configuration file.
	 * @return string               Returns the full URL.
	 */
	public static function create(string $path = null, bool $secure = null) : string
	{
		if (is_null($path))
			$path = static::$_path;

		// If the $path is still null, convert it to string.
		$path = (string)$path;

		// And remove white-space.
		$path = trim($path);

		if (!static::isValid($path))
		{
			if (static::$_scheme)
				$url = static::$_scheme . '://';
			else
				$url = static::base();

			if (static::$_user and static::$_pass)
				$url .= static::$_user . ':' . static::$_pass . '@';
			elseif (static::$_user)
				$url .= static::$_user . '@';
			elseif (static::$_pass)
				$url .= ':' . static::$_pass . '@';

			$url .= static::$_host;

			if (static::$_port)
				$url .= ':' . static::$_port;
			
			if ($path and substr($path, 0, 1) != '/')
				$path = '/' . $path;

			if ((int)\Setting::get('sef'))
				$prefix = '';
			else
				$prefix = '/index.php';

			$side = getenv('APP_SIDE');
			$lang = getenv('APP_LANG');

			if ($side === 'frontend')
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

		if (static::$_query)
		{
			if (substr(static::$_query, 0, 1) != '?')
				$url .= '?';

			$url .= static::$_query;
		}

		if (static::$_fragment)
		{
			if (substr(static::$_fragment, 0, 1) != '#')
				$url .= '#';

			$url .= static::$_fragment;
		}

		if (is_null($secure))
			$secure = (bool)\Config::security('ssl');

		if ($secure and substr($url, 0, 7) === 'http://')
			$url = substr_replace($url, 'https://', 0, 7);
		elseif (!$secure and substr($url, 0, 8) === 'https://')
			$url = substr_replace($url, 'http://', 0, 8);

		static::_reset();

		return $url;
	}

	/**
	 * Creates full URL from the given action.
	 *
	 * @param  string $action  The action to be appended to the URL.
	 * @return string          Returns the full URL.
	 */
	public static function createFromAction(string $action) : string
	{
		static::_reset();

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
				$module = getenv('APP_MODULE');
				$controller = $arr[0];
				$action = $arr[1];
			}

			$uri = $module . '/' . $controller . '/' . $action;
		}
		else
		{
			if (getenv('APP_MODULE') === getenv('APP_CONTROLLER'))
				$uri = getenv('APP_MODULE') . '/' . $action;
			else
				$uri = getenv('APP_MODULE') . '/' . getenv('APP_CONTROLLER') . '/' . $action;
		}

		$url = static::create($uri);

		return $url;
	}

	/**
	 * Determines if the given path is a valid URL.
	 *
	 * @param  string $path  The path to be validated.
	 * @return bool          Returns true if the given path is a valid URL.
	 */
	public static function isValid(string $path) : bool
	{
		if (!preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path))
			return (filter_var($path, FILTER_VALIDATE_URL) !== false);

		return true;
	}

	/**
	 * Generate a uri fragment with a hash mark (#)for singgle-page application mode.
	 *
	 * @param  string $uri  The uri to be hashed.
	 * @return string       Returns the uri fragment.
	 */
	public static function hashSpa(string $uri) : string
	{
		$idPos = strpos($uri, '?id=');
		$amPos = strpos($uri, '&', (int)$idPos);

		if ($idPos and $amPos === false)
			$uri = str_replace('?id=', ':', $uri);

		$hash = '#' . $uri;

		return $hash;
	}

	/**
	 * Converts the given URL into a context string used for variable suffix names, such as cookie names.
	 *
	 * @param  string|null $url  The URL to be converted.
	 * @return string            Returns the context string.
	 */
	public static function toContext(string $url = null) : string
	{
		if (!$url)
		{
			$url = Request::url();

			$arr = explode('?', $url);
			$url = $arr[0];

			$module = getenv('APP_MODULE');
			$controller = getenv('APP_CONTROLLER');

			// Replace same module and controller name with module name.
			// e.g., http://localhost/vanda/admin/user/user
			// ----> http://localhost/vanda/admin/user
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
	 * @param  string $url  The URL to be parsed.
	 * @return array        Returns an associative array containing any of the various components of the URL.
	 */
	public static function parse(string $url) : array
	{
		return parse_url($url);
	}

	/**
	 * Parse a URL and return scheme value, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns scheme value.
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
	 * @param  string $scheme  The scheme to be set.
	 * @return void
	 */
	public static function setScheme(string $scheme) : void
	{
		static::$_scheme = $scheme;
	}

	/**
	 * Parse a URL and return user value, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns user value.
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
	 * @param  string $user  The user to be set.
	 * @return void
	 */
	public static function setUser(string $user) : void
	{
		static::$_user = $user;
	}

	/**
	 * Parse a URL and return password value, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns password value.
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
	 * @param  string $pass  The password to be set.
	 * @return void
	 */
	public static function setPass(string $pass) : void
	{
		static::$_pass = $pass;
	}

	/**
	 * Parse a URL and return host value, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns host value.
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
	 * @param  string $host  The host to be set.
	 * @return void
	 */
	public static function setHost(string $host) : void
	{
		static::$_host = $host;
	}

	/**
	 * Parse a URL and return port value.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return int|null          Returns port value.
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
	 * @param  int  $port  The port to be set.
	 * @return void
	 */
	public static function setPort(int $port) : void
	{
		static::$_port = $port;
	}

	/**
	 * Parse a URL and return path value, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns path value.
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
	 * @param  string $path  The path to be set.
	 * @return void
	 */
	public static function setPath(string $path) : void
	{
		$path = ltrim($path, '/');
		static::$_path = $path;
	}

	/**
	 * Parse a URL and return query string, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns query string.
	 */
	public static function getQueryString(string $url = null) : ?string
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
	 * Set query string value.
	 *
	 * @param  string $queryString  The query string to be set.
	 * @return void
	 */
	public static function setQueryString(string $queryString) : void
	{
		$query = ltrim($queryString, '?');
		static::$_query = $query;
	}

	/**
	 * Parse a URL and return fragment value, null if not available.
	 *
	 * @param  string|null $url  The URL to be parsed.
	 * @return string|null       Returns fragment value.
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

	/**
	 * Set fragment value.
	 *
	 * @param  string $fragment  The fragment to be set.
	 * @return void
	 */
	public static function setFragment(string $fragment) : void
	{
		$fragment = ltrim($fragment, '#');
		static::$_fragment = $fragment;
	}

	/**
	 * Reset all URL components.
	 *
	 * @codeCoverageIgnore
	 */
	protected static function _reset() : void
	{
		static::$_baseUrl = null;
		static::$_scheme = null;
		static::$_user = null;
		static::$_pass = null;
		static::$_host = null;
		static::$_port = null;
		static::$_path = null;
		static::$_query = null;
		static::$_fragment = null;
	}
}
