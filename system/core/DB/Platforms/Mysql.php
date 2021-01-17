<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @author		Nat Withe
 * @copyright	Copyright (c) 2010 - 2019, Vanda, Inc. All rights reserved.
 * @license		Proprietary
 * @link		http://vanda.io
 */

declare(strict_types=1);

namespace System\DB\Platforms;

use System\Config;
use System\DB\AbstractPlatform;
use PDO;
use PDOException;

/**
 * Class Mysql
 * @package System\DB\Platforms
 */
final class Mysql extends AbstractPlatform
{
	protected static $_identifierLeft = '`';
	protected static $_identifierRight = '`';

	/**
	 * @return void
	 */
	protected static function _connect() : void
	{
		$host = Config::db('host');
		$port = Config::db('port');
		$dbname = Config::db('database');
		$user = Config::db('username');
		$password = Config::db('password');
		$charset = Config::db('charset');
		$collation = Config::db('collation');

		if (!$host) $host = 'localhost';
		if (!$user) $user = 'root';

		$conn = 'mysql:host=' . $host
			. '; port=' . $port
			. '; dbname=' . $dbname;

		if ($charset)
			$conn .= '; charset=' . $charset;

		try
		{
			static::$_connection = new PDO($conn, $user, $password);
			static::$_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			static::$_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			$encoding = '';

			if ($charset)
				$encoding .= ' SET NAMES ' . $charset;

			if ($collation)
				$encoding .= ' COLLATE ' . $collation;

			if ($encoding)
				static::$_connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, $encoding);

		}
		catch (PDOException $e)
		{
			echo $e->getMessage();
		}
	}
}
