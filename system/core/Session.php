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

use System\Session\Handler;

/**
 * Class Session
 * @package System
 */
final class Session
{
	protected static $started = false;

	/**
	 * Session constructor.
	 */
	private function __construct(){}

	/**
	 * @return void
	 */
	private static function start() : void
	{
		if (!static::$started)
			static::$started = new Handler();
	}

	/**
	 * @return string
	 */
	private static function _getSessionVar() : string
	{
		return '$_SESSION[\'__vandaSession\']';
	}

	/**
	 * @return string
	 */
	public static function sessionId() : string
	{
		static::start();

		return session_id();
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function set(string $name, $value) : void
	{
		static::start();

		$sessionVar = static::_getSessionVar();
		$arr = explode('.', $name);
		$count = count($arr);

		for ($i = 0; $i < $count; ++$i)
		{
			if (strpos($arr[$i], '[]'))
			{
				$arr[$i] = str_replace('[]', '', $arr[$i]);
				$sessionVar .= '[$arr[' . $i . ']][]';
			}
			else	
				$sessionVar .= '[$arr[' . $i . ']]';
		}

		// Cannot assign null to session.
		if (is_null($value))
			$value = '';

		if (is_string($value))
		{
			// Escape only single quotes and backslash.
			$value = addcslashes($value, '\\\'');
			$value = '\'' . $value . '\'';
		}
		elseif (is_bool($value))
			$value = ($value ? 'true' : 'false');

		$string = $sessionVar . ' = ' . $value . ';';
		eval($string);
	}

	/**
	 * @param  string     $name
	 * @param  mixed|null $default
	 * @return mixed
	 */
	public static function get(string $name, $default = null)
	{
		static::start();

		$sessionVar = static::_getSessionVar();
		$arr = explode('.', $name);
		$count = count($arr);

		for ($i = 0; $i < $count; ++$i)
			$sessionVar .= '[$arr[' . $i . ']]';

		$value = '';

		$string = 'if (isset(' . $sessionVar . ')) $value = ' . $sessionVar . ';';
		eval($string);

		// Don't return NULL as a default value to SQL REPLACE Statement
		// in Session\Handler. In _write() method, Auth::identity()->id
		// with NULL value will make sql query errors.

		if (!$value and !is_null($default))
			$value = $default;

		return $value;
	}

	/**
	 * @param  string $name
	 * @return void
	 */
	public static function clear(string $name) : void
	{
		$sessionVar = static::_getSessionVar();
		$arr = explode('.', $name);
		$count = count($arr);

		for ($i = 0; $i < $count; ++$i)
			$sessionVar .= '[$arr[' . $i . ']]';

		eval('unset(' . $sessionVar . ');');
	}

	/**
	 * @return void
	 */
	public static function destroy() : void
	{
		// Native PHP session_destroy() will 
		// effect after reloading a webpage.
		// So, user still can load webpage twice
		// before they are redirected to login screen.

		$sessionVar = self::_getSessionVar();
		eval('unset(' . $sessionVar . ');');
	}

	/**
	 * @return string
	 */
	public static function getToken() : string
	{
		$token = static::get('__vandaToken');

		if (!$token)
		{
			$userId = @Auth::identity()->id;
			$token = md5($userId . Str::random(32));
			static::set('__vandaToken', $token);
		}

		return $token;
	}

	/**
	 * @param  string      $method
	 * @param  string|null $redirectUrl
	 * @return void
	 */
	public static function checkToken(string $method = 'post', string $redirectUrl = null) : void
	{
		if (!Request::$method(static::getToken()))
		{
			Flash::danger(t('Invalid Token'));

			$redirectUrl = (string)$redirectUrl;

			if (!trim($redirectUrl))
				$redirectUrl = Request::homeUrl();

			Response::redirect($redirectUrl);
		}

		static::clear('__vandaToken');
	}
}
