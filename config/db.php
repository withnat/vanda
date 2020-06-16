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
	'password' => 'none',
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
