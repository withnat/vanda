<?php
/*
 * __      __             _
 * \ \    / /            | |
 *  \ \  / /_ _ _ __   __| | __ _
 *   \ \/ / _` | '_ \ / _` |/ _` |
 *    \  / (_| | | | | (_| | (_| |
 *     \/ \__,_|_| |_|\__,_|\__,_|
 *
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         http://vanda.io
 */

declare(strict_types=1);

namespace System;

use stdClass;

/**
 * Class Auth
 * http://php.net/manual/en/function.password-hash.php
 * http://stackoverflow.com/questions/4795385/how-do-you-use-bcrypt-for-hashing-passwords-in-php
 * http://www.sitepoint.com/hashing-passwords-php-5-5-password-hashing-api/
 * https://gist.github.com/nikic/3707231
 * @package System
 */
final class Auth
{
	protected static $_passthruActions = [];
	protected static $_userdata;

	/**
	 * Auth constructor.
	 */
	private function __construct(){}

	/**
	 * Allow user can access to specifix actions without login.
	 * This method will be called from Controller::preInit().
	 *
	 * @param string $actions
	 */
	public static function setPassthru($actions)
	{
		if (!is_array($actions))
			$actions = explode(',', $actions);

		$actions = array_map('trim', $actions);

		foreach ($actions as $action)
		{
			if ($action)
				static::$_passthruActions[] = $action;
		}
	}

	/**
	 * @return array
	 */
	public static function getPassthru()
	{
		return static::$_passthruActions;
	}

	public static function identity()
	{
		if (!static::$_userdata)
		{
			$data = new stdClass();

			if (isset($_SESSION['__vandaSession']))
			{
				$sessions = $_SESSION['__vandaSession'];

				foreach ($sessions as $key => $value)
				{
					if (substr($key, 0, 11) === '__vandaAuth')
						$data->{lcfirst(substr($key, 11))} = $value;
				}
			}

			static::$_userdata = $data;
		}

		return static::$_userdata;
	}

	public static function genPassword($length = 8)
	{
		return Str::random($length);
	}

	public static function login($username, $password)
	{
		if ($username and $password)
		{
			$user = DB::table('User')
				->where('username', $username)
				->where('status', 1)->load();

			if ($user->id)
			{
				if ($user->status === 1 and Hash::verify($password, $user->password))
				{
					$visited = date('Y-m-d H:i:s');

					$user->visited = $visited;
					$data = ['visited' => $visited];

					DB::table('User')
						->where($user->id)
						->update($data);

					Auth::loadProfile2Session($user);

					return true;
				}
			}
		}

		sleep(2);

		return false;
	}

	public static function loadProfile2Session($user)
	{
		foreach ($user as $key => $value)
		{
			if (in_array($key, ['password', 'tmpKickout']))
				continue;

			$key = '__vandaAuth' . ucfirst($key);
			Session::set($key, $value);
		}
	}

	public static function logout()
	{
		Session::destroy();
	}

	public static function loggedin()
	{
		return (empty(Auth::identity()->id) ? false : true);
	}

	// TODO
	public static function canAccess($data, $redirect=null)
	{
		$canAccess = true;

		if (empty($data->id))
			$canAccess = false;
		else
		{
			if (isset($data->status) and $data->status === -2)
				$canAccess = false;

			if (isset($data->branchId) and UserManager::get('branchId') and UserManager::get('branchId') != $data->branchId)
				$canAccess = false;
		}

		if (!$canAccess and $redirect)
			Response::redirect($redirect);
		elseif (!$canAccess and ACTION === 'printout')
			die('Data does not exist.');
		else
			return $canAccess;
	}
}
