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

return [
	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	| All database work in Vanda is done through the PHP PDO facilities.
	*/
	'platform' => 'mysql',
	'host' => 'localhost',
	'port' => 3306,
	'database' => 'vanda',
	'prefix' => 'vd_',
	'username' => 'root',
	'password' => '!none',
	'charset' => 'utf8mb4',
	'collation' => 'utf8mb4_unicode_ci',

	/*
	|--------------------------------------------------------------------------
	| Examples of configuring each database platform
	|--------------------------------------------------------------------------
	|
	|	/sqlite/
	|
	|	'platform' => 'sqlite',
	|	'database' => 'vanda.sqlite',
	|	'prefix' => 'vd_',
	|
	|	/pgsql/
	|
	|	'platform' => 'pgsql',
	|	'host' => 'localhost',
	|	'port' => 5432,
	|	'database' => 'vanda',
	|	'prefix' => 'vd_',
	|	'username' => 'root',
	|	'password' => '',
	|	'charset' => 'utf8',
	|	'schema' => 'public',
	|	'sslmode' => 'prefer',
	|
	|	/sqlsrv/
	|
	|	'platform' => 'sqlsrv',
	|	'host' => 'localhost',
	|	'port' => 1433,
	|	'database' => 'vanda',
	|	'prefix' => 'vd_',
	|	'username' => 'root',
	|	'password' => '',
	|	'charset' => 'utf8',
	*/
];
