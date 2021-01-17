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
