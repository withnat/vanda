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
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System\DB\Platforms;

use System\Config;
use System\DB\AbstractPlatform;
use PDO;
use PDOException;

/**
 * Class SQLite
 * @package System\DB\Platforms
 */
final class Sqlite extends AbstractPlatform
{
	protected static $_delimitIdentifierLeft = '"';
	protected static $_delimitIdentifierRight = '"';

	/**
	 * @return void
	 */
	public static function _connect() : void // ok
	{
		try {
			static::$_connection = new PDO('sqlite:../vand.sqlite');
			static::$_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			static::$_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			static::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}
