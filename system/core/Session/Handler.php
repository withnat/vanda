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

namespace System\Session;

use System\Auth;
use System\DB;

/**
 * Class Handler
 * @package System\Session
 */
class Handler
{
	/**
	 * Handler constructor.
	 */
	public function __construct()
	{
		// Cannot setup session.use_trans_sid after session start. Fail on deleting
		// temp file in DEV_MODE will start session via Flash::danger() method.
		// Setting up session.use_trans_sid after that will display warning message.
		// Warning: ini_set(): A session is active. You cannot change ...

		// When use_trans_sid is enabled, PHP will pass the session ID via the
		// URL. This makes it far easier for a malicious party to obtain an
		// active session ID and hijack the session. Defaults to 0.
		// (Only when cookies are not working. Plus, if both session.use_trans_sid
		// and session.use_cookies are 1, then session.use_only_cookies decides: 1
		// will disable URL-rewriting.)
		//ini_set('session.use_trans_sid', 0);

		session_set_save_handler
		(
			['System\Session\Handler', '_open'],
			['System\Session\Handler', '_close'],
			['System\Session\Handler', '_read'],
			['System\Session\Handler', '_write'],
			['System\Session\Handler', '_destroy'],
			['System\Session\Handler', '_gc']
		);

		session_write_close();
		session_start();
	}

	/**
	 * @return bool
	 */
	public static function _open() : bool
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public static function _close() : bool
	{
		return true;
	}

	/**
	 * @param  string $id
	 * @return string
	 */
	public static function _read(string $id) : string
	{
		return DB::select('data')
				->from('Session')
				->where('id', $id)
				->loadSingle();
	}

	/**
	 * @param  string $id
	 * @param  string $data
	 * @return bool
	 */
	public static function _write(string $id, string $data) : bool
	{
		$userId = @Auth::identity()->id;

		// Don't leave $userId empty.
		// Otherwise, sql query will fail.
		// So, ensure it is an integer.
		$userId = (int)$userId;

		DB::raw('REPLACE INTO #_Session VALUES (' .
				DB::escape($id) . ', ' .
				time() . ', ' .
				DB::escape($data) . ', ' .
				$userId . ')')
				->execute();

		return true;
	}

	/**
	 * @param  string $id
	 * @return bool
	 */
	public static function _destroy(string $id) : bool
	{
		if (DB::table('Session')->where('id', $id)->delete())
			return true;
		else
			return false;
	}

	/**
	 * @param  int  $max
	 * @return bool
	 */
	public static function _gc(int $max) : bool
	{
		$old = time() - $max;

		if (DB::table('Session')->where('access', '<', $old)->delete())
			return true;
		else
			return false;
	}
}
