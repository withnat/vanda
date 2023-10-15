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

use System\Arr;
use System\Request;
use System\Language;

/**
 * Miscellaneous helper functions.
 */

if (!function_exists('t'))
{
	/**
	 * @param  string $string
	 * @return string
	 */
	function t(string $string) : string
	{
		return Language::_($string, @System\Auth::identity()->languageId);
	}
}

if (!function_exists('pr'))
{
	/**
	 * Method to print human-readable information
	 * about a variable for debugging purposes.
	 *
	 * @example pr($something, $another);
	 * @param   mixed[] ...,  A variable.
	 * @return  void
	 */
	function pr() : void
	{
		$args = func_get_args();

		foreach ($args as $data)
		{
			if (Request::isCli())
				echo print_r($data, true);
			else
				echo "<pre>\n" . htmlspecialchars(print_r($data, true)) . '</pre>';
		}
	}
}

if (!function_exists('prx'))
{
	/**
	 * Method to print human-readable information
	 * about a variable for debugging purposes
	 * and terminate the current script.
	 *
	 * @example pr($something, $another);
	 * @param   mixed[] ...,  A variable.
	 * @return  void
	 */
	function prx() : void
	{
		$args = func_get_args();

		foreach ($args as $data)
			pr($data);

		exit;
	}
}

if (!function_exists('c'))
{
	/**
	 * @param  array $array
	 * @return void
	 */
	function c(array $array) : void
	{
		echo Arr::count($array);
	}
}

if (!function_exists('cx'))
{
	/**
	 * @param  array $array
	 * @return void
	 */
	function cx(array $array) : void
	{
		c($array);
		exit;
	}
}

if (!function_exists('e'))
{
	/**
	 * @param  mixed $input
	 * @return void
	 */
	function e($input) : void
	{
		echo $input;
	}
}

if (!function_exists('er'))
{
	/**
	 * @param  mixed $input
	 * @return void
	 */
	function er($input) : void
	{
		echo $input . '<br />';
	}
}

if (!function_exists('ex'))
{
	/**
	 * @param  mixed $input
	 * @return void
	 */
	function ex($input) : void
	{
		echo $input;
		exit;
	}
}

if (!function_exists('erx'))
{
	/**
	 * @param  mixed $input
	 * @return void
	 */
	function erx($input) : void
	{
		echo $input . '<br />';
		exit;
	}
}

if (!function_exists('swap'))
{
	/**
	 * @param  mixed $var1
	 * @param  mixed $var2
	 * @return void
	 */
	function swap(&$var1, &$var2) : void
	{
		list($var1, $var2) = [$var2, $var1];
	}
}

if (!function_exists('setValueIfEmpty'))
{
	/**
	 * @param  mixed $var1
	 * @param  mixed $var2
	 * @param  mixed|null  $defaultVal1
	 * @param  mixed|null  $defaultVal2
	 * @return void
	 */
	function setValueIfEmpty(&$var1, &$var2, $defaultVal1 = null, $defaultVal2 = null) : void
	{
		if (!$var1 and !$var2)
		{
			$var1 = $defaultVal1;
			$var2 = $defaultVal2;
		}
		elseif ($var1)
			$var2 = $var1;
		elseif ($var2)
			$var1 = $var2;
	}
}

if (!function_exists('isSPAExclude'))
{
	/**
	 * @return bool
	 */
	function isSPAExclude() : bool
	{
		$search = MODULE . '.' . CONTROLLER . '.' . ACTION;
		$result = false;

		if (SIDE === 'frontend' and FRONTEND_SPA_MODE)
		{
			$excludes = explode(',', FRONTEND_SPA_MODE_EXCLUDES);
			$excludes = array_map('trim', $excludes);

			if (Arr::has($excludes, $search, true))
				$result = true;
		}
		elseif (SIDE === 'backend' and BACKEND_SPA_MODE)
		{
			$excludes = explode(',', BACKEND_SPA_MODE_EXCLUDES);
			$excludes = array_map('trim', $excludes);

			if (Arr::has($excludes, $search, true))
				$result = true;
		}

		return $result;
	}
}
