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

/**
 * Class DB
 *
 * @package System
 */
class DB
{
	/**
	 * DB constructor.
	 */
	private function __construct(){}

	/**
	 * Creates a new database connection depending on the database platform.
	 *
	 * @param  string $method  The method to call.
	 * @param  array  $args    The arguments to pass to the method.
	 * @return mixed           Depends on called method.
	 */
	public static function __callStatic(string $method, array $args)
	{
		$platform = Config::db('platform');

		switch ($platform)
		{
			case 'sqlite':
				$obj = new DB\Platforms\Sqlite();
				break;
			case 'pgsql':
				$obj = new DB\Platforms\Pgsql();
				break;
			case 'sqlsrv':
				$obj = new DB\Platforms\Sqlsrv();
				break;
			default:
				$obj = new DB\Platforms\Mysql();
		}

		return call_user_func_array([$obj, $method], $args);
	}
}
