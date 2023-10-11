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

namespace System\DB;

use stdClass;
use System\Arr;
use System\Auth;
use System\Config;
use System\CSV;
use System\Data;
use System\File;
use System\Folder;
use System\Paginator;
use System\Str;
use System\Uri;
use System\XML;
use System\Exception\InvalidArgumentException;
use \Closure;
use \ErrorException;
use \PDOException;
use \PDOStatement;

/**
 * ```php
 * private static $_sqlWheres = [
 *  ['operator' => 'AND', 'condition' => 'name=Nat'],
 *  ['operator' => 'OR', 'condition' => '('],
 *  ['operator' => 'AND', 'condition' => 'votes > 100'],
 *  ['operator' => 'AND', 'condition' => 'title <> 'Admin''],
 *  ['operator' => '', 'condition' => ')']
 * ];
 * ```
 *
 * Class AbstractPlatform
 * @package System\DB
 */
abstract class AbstractPlatform
{
	protected static $_instance = null;
	protected static $_connection = null;
	protected static $_tables = [];
	protected static $_tableInfo = [];
	protected static $_sqlRaw = null;
	protected static $_sqlSelects = [];
	protected static $_sqlTable = null;
	protected static $_sqlJoins = [];
	protected static $_sqlWheres = [];
	protected static $_sqlGroups = [];
	protected static $_sqlSorts = [];
	protected static $_sqlTake = null;
	protected static $_sqlSkip = null;
	protected static $_transactionMode = false;
	protected static $_transactionSqls = [];
	protected static $_executedQueries = [];
	protected static $_delimitIdentifierLeft = '`';
	protected static $_delimitIdentifierRight = '`';
	protected static $_affectedRows = null;
	protected static $_dbCachePath = PATH_STORAGE . DS . 'cache' . DS . 'db' . DS;
	protected static $_queryCachePath = PATH_STORAGE . DS . 'cache' . DS . 'queries' . DS;

	/**
	 * AbstractPlatform constructor.
	 *
	 * Nedd to set the visibility of this method to protected as it is
	 * called from the static method getInstance() (new static()).
	 */
	protected function __construct()
	{
		if (is_null(static::$_connection))
		{
			static::_connect();

			if (Config::app('env') === 'development')
			{
				Folder::delete(static::$_dbCachePath);
				Folder::delete(static::$_queryCachePath);
			}

			Folder::create(static::$_dbCachePath);
			Folder::create(static::$_queryCachePath);

			static::$_instance = $this;;
		}
	}

	/**
	 * This is the static method that controls the access to the singleton
	 * instance. On the first run, it creates a singleton object and places it
	 * into the static field. On subsequent runs, it returns the client existing
	 * object stored in the static field.
	 *
	 * This implementation lets you subclass the Singleton class while keeping
	 * just one instance of each subclass around.
	 *
	 * @return AbstractPlatform  Returns the singleton instance.
	 */
	protected static function _getInstance() : AbstractPlatform // ok
	{
		if (is_null(static::$_instance))
			static::$_instance = new static;

		return static::$_instance;
	}

	/**
	 * @return void
	 */
	abstract protected static function _connect() : void; // ok

	// Select

	/**
	 * Sets the list of columns to select.
	 *
	 * For example,
	 *
	 * ```php
	 *  DB::select('*');
	 *  // The result will be: *
	 *
	 *  DB::select('id');
	 *  DB::select('`id`');
	 *  // The result will be: `id`
	 *
	 *  DB::select('`id` AS userId');
	 *  DB::select('`id` AS `userId`');
	 *  // The result will be: `id` AS `userId`
	 *
	 *  DB::select('u.id');
	 *  DB::select('`u`.`id`');
	 *  // The result will be: `u`.`id`
	 *
	 *  DB::select('u.id AS userId');
	 *  DB::select('`u`.`id` AS `userId`');
	 *  // The result will be: `u`.`id` AS `userId`
	 *
	 * DB::select('id, name, email');
	 * // The result will be: `id`, `name`, `email`
	 *  ```
	 *
	 * @param  string           $columns  List of columns separated by comma.
	 * @return AbstractPlatform           Returns the current object.
	 */
	public static function select(string $columns = '*') : AbstractPlatform // ok
	{
		$columns = static::_parseColumn($columns);
		static::$_sqlSelects = array_merge(static::$_sqlSelects, $columns);

		return static::_getInstance();
	}

	/**
	 * Query the database and returns the PDOStatement object.
	 *
	 * @param  string $sql   The raw SQL statement.
	 * @return PDOStatement  Returns the PDOStatement object.
	 */
	public static function query(string $sql) : PDOStatement // ok
	{
		if (Config::app('env') === 'development')
			static::$_executedQueries[] = $sql;

		$result = static::$_connection->query($sql);

		return $result;
	}

	/**
	 * Query the database using AVG() and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function avg(string $columns) // ok
	{
		return static::_queryAggregate('AVG()', $columns);
	}

	/**
	 * Query the database using COUNT() and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function count(string $columns = '*') // ok
	{
		return static::_queryAggregate('COUNT()', $columns);
	}

	/**
	 * Query the database using COUNT(DISTINCT()) and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function countDistinct(string $columns) // ok
	{
		return static::_queryAggregate('COUNT(DISTINCT())', $columns);
	}

	/**
	 * Query the database using MIN() and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function min(string $columns) // ok
	{
		return static::_queryAggregate('MIN()', $columns);
	}

	/**
	 * Query the database using MAX() and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function max(string $columns) // ok
	{
		return static::_queryAggregate('MAX()', $columns);
	}

	/**
	 * Query the database using STD() and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function std(string $columns) // ok
	{
		return static::_queryAggregate('STD()', $columns);
	}

	/**
	 * Query the database using SUM() and returns the query result.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed            Returns the query result.
	 */
	public static function sum(string $columns) // ok
	{
		return static::_queryAggregate('SUM()', $columns);
	}

	/**
	 * Performs a query using DISTINCT and return a query result.
	 *
	 * @param  string      $columns  List of columns separated by comma.
	 * @return array|false           Returns the query result.
	 */
	public static function distinct(string $columns) // ok
	{
		$columns = static::_parseColumn($columns);

		static::$_sqlSelects[] = 'DISTINCT ' . implode(', ', $columns);

		return static::loadAll();
	}

	/**
	 * Performs a query using an aggregate function and return a query result.
	 *
	 * @param  string $function  The function name.
	 * @param  string $columns   List of columns separated by comma.
	 * @return mixed             Returns the query result.
	 */
	protected static function _queryAggregate(string $function, string $columns) // ok
	{
		$columns = static::_parseColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSelects[] = str_replace('()', '(' . $column . ')', $function);

		if (count($columns) > 1)
			return static::load();
		else
			return static::loadSingle();
	}

	// From

	/**
	 * An alias of method static::from().
	 *
	 * @param  string $table
	 * @return AbstractPlatform
	 */
	public static function table(string $table) : AbstractPlatform // ok
	{
		static::from($table);

		return static::_getInstance();
	}

	/**
	 * Sets the table to select from.
	 *
	 * @param  string           $table  The table name.
	 * @return AbstractPlatform         Returns the current object.
	 */
	public static function from(string $table) : AbstractPlatform // ok
	{
		static::$_sqlTable = static::wrapTable($table);

		return static::_getInstance();
	}

	/**
	 * Sets the table for an inner join.
	 *
	 * @param  string           $table      The table name.
	 * @param  string           $condition  The join condition.
	 * @return AbstractPlatform             Returns the current object.
	 */
	public static function innerJoin(string $table, string $condition) : AbstractPlatform // ok
	{
		static::_setJoin('INNER JOIN', $table, $condition);

		return static::_getInstance();
	}

	/**
	 * Sets the table for a left join.
	 *
	 * @param  string           $table      The table name.
	 * @param  string           $condition  The join condition.
	 * @return AbstractPlatform             Returns the current object.
	 */
	public static function leftJoin(string $table, string $condition) : AbstractPlatform // ok
	{
		static::_setJoin('LEFT JOIN', $table, $condition);

		return static::_getInstance();
	}

	/**
	 * Sets the table for a right join.
	 *
	 * @param  string           $table      The table name.
	 * @param  string           $condition  The join condition.
	 * @return AbstractPlatform             Returns the current object.
	 */
	public static function rightJoin(string $table, string $condition) : AbstractPlatform // ok
	{
		static::_setJoin('RIGHT JOIN', $table, $condition);

		return static::_getInstance();
	}

	/**
	 * Sets the table to join.
	 *
	 * @param  string $type       The join type.
	 * @param  string $table      The table name.
	 * @param  string $condition  The join condition.
	 * @return void
	 */
	protected static function _setJoin(string $type, string $table, string $condition) : void // ok
	{
		$table = static::wrapTable($table);

		if (strpos($condition, '='))
		{
			$arr = explode('=', $condition);
			$arr = array_map('static::wrapColumn', $arr);

			$condition = $arr[0] . ' = ' . $arr[1];
		}

		$sql = $type . ' ' . $table . ' ON ' . $condition;

		static::$_sqlJoins[] = $sql;
	}

	// Save

	/**
	 * Executes an 'INSERT' SQL query.
	 *
	 * @param  array|object $data  The provided data can be an array, object, dataset (an array of arrays),
	 *                             or recordset (an array of objects).
	 * @return int                 Returns the number of affected rows.
	 */
	public static function insert($data) : int // ok
	{
		if (!is_array($data) and !is_object($data) and !Arr::isDataset($data) and !Arr::isRecordset($data))
			throw InvalidArgumentException::typeError(1, ['array', 'object', 'dataset', 'recordset'], $data);

		$sql = static::_buildQueryInsert($data);

		if (Arr::isMultidimensional($data) and count($data) > 1)
		{
			static::transaction(function() use ($sql){
				static::raw($sql)->execute();
			});

			$affectedRows = count($data);
		}
		else
		{
			static::raw($sql)->execute();
			$affectedRows = static::getAffectedRows();
		}

		return $affectedRows;
	}

	/**
	 *Executes an 'UPDATE' SQL query.
	 *
	 * @param  array|object $data  The provided data can be an array or object, but not a dataset (an array of arrays)
	 *                             or a recordset (an array of objects).
	 * @return int                 Returns the number of affected rows.
	 */
	public static function update($data) : int // ok
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::typeError(1, ['array', 'object'], $data);

		$sql = static::_buildQueryUpdate($data);

		// If static::_buildQueryUpdate() removes all elements
		// from the given data, the $sql will be empty.
		if ($sql)
		{
			static::raw($sql)->execute();

			return static::getAffectedRows();
		}

		return 0;
	}

	/**
	 * Prepares the data before saving.
	 *
	 * @param  array|object $data  The data to be saved. If it's an array, it can be multidimensional array.
	 * @return array               Returns the data that ready to be saved.
	 */
	protected static function _prepareDataBeforeSave($data) : array // ok
	{
		$data = (array)$data;
		$datas = Arr::toMultidimensional($data);

		for ($i = 0, $n = count($datas); $i < $n; ++$i)
		{
			foreach ($datas[$i] as $key => $value)
			{
				if (!static::columnExists($key))
				{
					unset($datas[$i][$key]);
					continue;
				}

				if (is_array($value))
					$datas[$i][$key] = ',' . implode(',', $value) . ',';

				// If the data from the submitted form is not an array, it is always in string data type.
				elseif (is_string($value) and !mb_strlen($value))
				{
					$default = static::getColumnInfo($key)->default;

					// The default value can be null; convert it to a string for mb_strlen().
					if (mb_strlen((string)$default))
					{
						if ($default === 'CURRENT_TIMESTAMP')
						{
							// The database server will automatically set the default value.
							unset($datas[$i][$key]);
						}
						else
							$datas[$i][$key] = $default;
					}
					elseif (static::getColumnInfo($key)->nullable)
						$datas[$i][$key] = null;
				}

				elseif (is_null($value) and !static::getColumnInfo($key)->nullable)
					unset($datas[$i][$key]);
			}
		}

		return $datas;
	}

	/**
	 * @param $data
	 * @return int  Returns the number of affected rows.
	 */
	public static function save($data) : int
	{
		$where = static::_buildWhere();

		if ($where)
		{
			if (!is_array($data) and !is_object($data))
				throw InvalidArgumentException::typeError(1, ['array', 'object'], $data);

			$sql = static::_buildQuerySave($data);
			static::raw($sql)->execute();

			$affectedRows = static::getAffectedRows();
		}
		else
		{
			$datas = Arr::toMultidimensional($data);
			$autoOrdering = false;

			if (static::columnExists('ordering') and !array_key_exists('ordering', $datas[0]))
			{
				$autoOrdering = true;
				static::lockTables(@static::$_sqlTable);
			}

			$sql = static::_buildQuerySave($data);

			if (count($datas) > 1)
			{
				static::transaction(function() use ($sql){
					static::raw($sql)->execute();
				});

				$affectedRows = count($datas);
			}
			else
			{
				static::raw($sql)->execute();
				$affectedRows = static::getAffectedRows();
			}

			if ($autoOrdering)
				static::unlockTables();
		}

		return $affectedRows;
	}

	/**
	 * Increments the value of the specified column by the specified amount.
	 *
	 * @param  string    $columns  List of columns separated by comma.
	 * @param  int|float $amount   The amount to increment.
	 * @return int                 Returns the number of affected rows.
	 */
	public static function increase(string $columns, $amount = 1) : int // ok
	{
		$where = static::_buildWhere();
		$columns = static::_parseColumn($columns);
		$amount = (float)$amount;

		$sql = 'UPDATE ' . static::$_sqlTable . ' SET ';

		foreach ($columns as $column)
			$sql .= $column . ' = IFNULL(' . $column . ', 0) + ' . $amount . ', ';

		$sql = substr($sql, 0, -2) . $where;

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

	/**
	 * Decrements the value of the specified column by the specified amount.
	 *
	 * @param  string    $columns  List of columns separated by comma.
	 * @param  int|float $amount   The amount to decrement.
	 * @return int                 Returns the number of affected rows.
	 */
	public static function decrease(string $columns, $amount = 1) : int // ok
	{
		$where = static::_buildWhere();
		$columns = static::_parseColumn($columns);
		$amount = (float)$amount;

		$sql = 'UPDATE ' . static::$_sqlTable . ' SET ';

		foreach ($columns as $column)
			$sql .= $column . ' = IFNULL(' . $column . ', 0) - ' . $amount . ', ';

		$sql = substr($sql, 0, -2) . $where;

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

	/**
	 * Sets status to 2 (published).
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function publish() : int // ok
	{
		return static::update(['status' => 2]);
	}

	/**
	 * Set status to 1 (active).
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function activate() : int // ok
	{
		return static::update(['status' => 1]);
	}

	/**
	 * Set status to 0 (inactive).
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function deactivate() : int // ok
	{
		return static::update(['status' => 0]);
	}

	/**
	 * Set status to -1 (archive).
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function archive() : int // ok
	{
		return static::update(['status' => -1]);
	}

	/**
	 * Set status to -2 (trash).
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function trash() : int // ok
	{
		return static::update(['status' => -2]);
	}

	/**
	 * Set status to -3 (discontinued).
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function discontinue() : int // ok
	{
		return static::update(['status' => -3]);
	}

	// Delete

	public static function delete($deleteUploadedFiles = false, $fileBackupPath = null)
	{
		$where = static::_buildWhere();

		if (!$where)
		{
			static::where('id', null);
			$where = static::_buildWhere();
		}

		if ($fileBackupPath)
			static::_backupUploadedFiles($fileBackupPath, $where);

		if ($deleteUploadedFiles)
			static::_deleteUploadedFiles($where);

		$sql = static::_buildQueryDelete($where);
		static::_query($sql);

		return static::getAffectedRows();
	}

	public static function deleteAll($deleteUploadedFiles = false, $fileBackupPath = null)
	{
		if ($fileBackupPath)
			static::_backupUploadedFiles($fileBackupPath);

		if ($deleteUploadedFiles)
			static::_deleteUploadedFiles();

		$sql = static::_buildQueryDelete();
		static::_query($sql);

		return static::getAffectedRows();
	}

	public static function truncate($deleteUploadedFiles = false, $fileBackupPath = null)
	{
		if ($fileBackupPath)
			static::_backupUploadedFiles($fileBackupPath);

		if ($deleteUploadedFiles)
			static::_deleteUploadedFiles();

		$sql = 'TRUNCATE ' . static::$_sqlTable;

		// The static::$_sqlTable will be removed by
		// this method but maybe we need to call method
		// deleteAll() if no DROP privilege to truncate.

		if (static::$_transactionMode)
			$result = static::$_connection->query($sql);
		else
		{
			$result = static::transaction(function () use ($sql){
				static::$_connection->query($sql);
			});
		}

		if ($result and Config::app('env') === 'development')
			static::$_executedQueries[] = $sql;
		else
			static::deleteAll();
	}

	// todo
	private static function _deleteUploadedFiles($where = null)
	{
		// Have to use raw sql because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.

		$sql = 'SELECT * FROM ' . static::$_sqlTable . $where;
		$result = static::$_connection->query($sql);

		if (Config::app('env') === 'development')
			static::$_executedQueries[] = $sql;

		$deleted = 0;
		$assetPath = str_replace(BASEPATH, '', BASEPATH_ASSETS);

		while ($row = $result->fetch())
		{
			foreach ($row as $value)
			{
				// ต้องใช้ mb_stripos มั้ย และจำเป็นต้องเป็น case-insensitive มั้ย
				if (stripos($value, $assetPath) !== false)
				{
					if (File::delete(BASEPATH . $value))
						++$deleted;
				}
			}
		}

		return $deleted;
	}

	// todo
	private static function _backupUploadedFiles($backupPath, $where = null)
	{
		$backupPath = rtrim($backupPath, '/'); // ต้องใช้ DS มั้ย

		Folder::create($backupPath);

		// Have to use raw sql because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.

		$sql = 'SELECT * FROM ' . static::$_sqlTable . $where;
		$result = static::$_connection->query($sql);

		if (Config::app('env') === 'development')
			static::$_executedQueries[] = $sql;

		$backedup = 0;
		$assetPath = str_replace(BASEPATH, '', BASEPATH_ASSETS);

		while ($row = $result->fetch())
		{
			foreach ($row as $value)
			{
				// ต้องใช้ mb_stripos มั้ย และจำเป็นต้องเป็น case-sensitive มั้ย
				if (stripos($value, $assetPath) !== false)
				{
					if (is_file(BASEPATH . $value))
					{
						$filename = File::getName($value);
						copy($value, $backupPath . DS . $filename);

						++$backedup;
					}
				}
			}
		}

		return $backedup;
	}

	// Normal where

	/**
	 * Sets a marking point for starting the SQL group.
	 *
	 * @return AbstractPlatform  Returns the current object.
	 */
	protected static function _groupStart() : AbstractPlatform  // ok
	{
		static::$_sqlWheres[] = ['AND', '('];

		return static::_getInstance();
	}

	/**
	 * Sets a marking point for starting the SQL 'OR' group.
	 *
	 * @return AbstractPlatform  Returns the current object.
	 */
	protected static function _orGroupStart() : AbstractPlatform // ok
	{
		static::$_sqlWheres[] = ['OR', '('];

		return static::_getInstance();
	}

	/**
	 * Sets a marking point for ending the SQL group.
	 *
	 * @return AbstractPlatform  Returns the current object.
	 */
	protected static function _groupEnd() : AbstractPlatform // ok
	{
		static::$_sqlWheres[] = ['', ')'];

		return static::_getInstance();
	}

	/**
	 * Sets the where clause.
	 *
	 * For example,
	 *
	 *  ```php
	 * DB::where(1);
	 * DB::where('id', 1);
	 * DB::where('id = ?', 1);
	 * DB::where('id', '=', 1);
	 * DB::where('id = :id', ['id' => 1]);
	 * DB::where('id = :id', [':id' => 1]);
	 * // The result will be:
	 * // WHERE `id` = 1
	 *
	 * DB::where('name = ? AND surname = ?', 'Nat', 'Withe');
	 * DB::where('name = :name AND surname = :surname', ['name' => 'Nat', 'surname' => 'Withe']);
	 * DB::where('name = :name AND surname = :surname', [':name' => 'Nat', ':surname' => 'Withe']);
	 * // The result will be: WHERE name = 'Nat' AND surname = 'Withe'
	 * ```
	 *
	 * @param  mixed            $where  The where condition.
	 * @return AbstractPlatform         Returns the current object.
	 */
	public static function where($where) : AbstractPlatform // ok
	{
		$args = func_get_args();

		if ($args[0] instanceof Closure)
		{
			static::_groupStart();
			$args[0]();
			static::_groupEnd();
		}
		else
		{
			$where = static::_parseWhere($args);
			static::$_sqlWheres[] = ['AND', $where];
		}

		return static::_getInstance();
	}

	/**
	 * Sets the 'NOT' WHERE clause.
	 *
	 * For example,
	 *
	 *  ```php
	 * DB::whereNot(1);
	 * DB::whereNot('id', 1);
	 * // The result will be:
	 * // WHERE `id` != 1
	 * ```
	 *
	 * @param  mixed            $where  The where condition.
	 * @return AbstractPlatform         Returns the current object.
	 */
	public static function whereNot($where) : AbstractPlatform // ok
	{
		$args = func_get_args();
		$argCount = count($args);

		if ($argCount > 2)
		{
			// raise error in the future.
		}

		if ($argCount === 1)
			$args = ['id', '!=', $args[0]];
		else
			$args = [$args[0], '!=', $args[1]];

		$where = static::_parseWhere($args);
		static::$_sqlWheres[] = ['AND', $where];

		return static::_getInstance();
	}

	/**
	 *  Sets the 'OR' WHERE clause.
	 *
	 *  For example,
	 *
	 *   ```php
	 *  DB::orWhere(1);
	 *  DB::orWhere('id', 1);
	 *  DB::orWhere('id = ?', 1);
	 *  DB::orWhere('id', '=', 1);
	 *  DB::orWhere('id = :id', ['id' => 1]);
	 *  DB::orWhere('id = :id', [':id' => 1]);
	 *  // The result will be:
	 *  // [WHERE ...] or `id` = 1
	 *
	 *  DB::orWhere('name = ? AND surname = ?', 'Nat', 'Withe');
	 *  DB::orWhere('name = :name AND surname = :surname', ['name' => 'Nat', 'surname' => 'Withe']);
	 *  DB::orWhere('name = :name AND surname = :surname', [':name' => 'Nat', ':surname' => 'Withe']);
	 *  // The result will be: [WHERE ...] or name = 'Nat' AND surname = 'Withe'
	 *  ```
	 * @param  mixed            $where  The 'OR' where condition.
	 * @return AbstractPlatform         Returns the current object.
	 */
	public static function orWhere($where) : AbstractPlatform // ok
	{
		$args = func_get_args();

		if (is_callable($args[0]))
		{
			static::_orGroupStart();
			$args[0]();
			static::_groupEnd();
		}
		else
		{
			$where = static::_parseWhere($args);
			static::$_sqlWheres[] = ['OR', $where];
		}

		return static::_getInstance();
	}

	/**
	 * Sets the 'OR NOT' WHERE clause.
	 *
	 * For example,
	 *
	 *  ```php
	 * DB::orWhereNot(1);
	 * DB::orWhereNot('id', 1);
	 * // The result will be:
	 * // [WHERE...] or `id` != 1
	 * ```
	 *
	 * @param  mixed            $where  The where condition.
	 * @return AbstractPlatform         Returns the current object.
	 */
	public static function orWhereNot($where) : AbstractPlatform // ok
	{
		$args = func_get_args();
		$argCount = count($args);

		if ($argCount > 2)
		{
			// raise error in the future.
		}

		if ($argCount === 1)
			$args = ['id', '!=', $args[0]];
		else
			$args = [$args[0], '!=', $args[1]];

		$where = static::_parseWhere($args);
		static::$_sqlWheres[] = ['OR', $where];

		return static::_getInstance();
	}

	/**
	 * Parses the where condition and builds the SQL where clause.
	 *
	 * @param  array   $args  The given arguments.
	 * @return string         Returns the SQL where clause.
	 */
	protected static function _parseWhere(array $args) : string  // ok
	{
		$argCount = count($args);

		// For example,
		// DB::where(1);
		// DB::where('id=1');
		if ($argCount === 1)
		{
			if (is_int($args[0]))
				$where = static::wrapColumn('id') . ' = ' . $args[0];
			else
				$where = $args[0];
		}
		// For example,
		// DB::where('id', 1);
		// DB::where('id = ?', 1);
		// DB::where('id', '=', 1);
		// DB::where('id = :id', ['id' => 1]);
		// DB::where('id = :id', [':id' => 1]);
		else
		{
			$where = $args[0];
			$where = str_replace('{', static::$_delimitIdentifierLeft, $where);
			$where = str_replace('}', static::$_delimitIdentifierRight, $where);

			if (strpos($args[0], '?'))
			{
				$args = Arr::flatten($args);

				for ($i = 1, $n = count($args); $i < $n; ++$i)
					$where = Str::replace($where, '?', static::escape($args[$i]), 1);
			}
			elseif (strpos($args[0], ':'))
			{
				// We need an associative array, so there's no need to flatten it.
				foreach ($args[1] as $key => $value)
				{
					// Supports both :key and key.
					// For example,
					// DB::where('id = :id', ['id' => 1]);
					// DB::where('id = :id', [':id' => 1]);
					if (strpos($key, ':') === false)
						$key = ':' . $key;

					$where = str_replace($key, static::escape($value), $where);
				}
			}
			else
			{
				$args = Arr::flatten($args);
				$column = static::wrapColumn($args[0]);
				$operator = '';
				$value = '';

				if ($argCount === 2)
				{
					$operator = '=';
					$value = $args[1];
				}
				elseif ($argCount > 2)
				{
					$operator = trim($args[1]);
					$value = $args[2];
				}

				$value = static::escape($value);
				$where = $column . ' ' . $operator . ' ' . $value;
			}
		}

		return $where;
	}

	// Where between

	/**
	 * Sets the 'BETWEEN' condition for the WHERE clause.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $start   The start value.
	 * @param  string|int|float $end     The end value.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereBetween(string $column, $start, $end) : AbstractPlatform // ok
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::typeError(3, ['string', 'int', 'float'], $end);

		static::_setWhereBetween('AND', 'BETWEEN', $column, $start, $end);

		return static::_getInstance();
	}

	/**
	 * Sets the 'BETWEEN' condition for the 'OR' clause in the WHERE statement.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $start   The start value.
	 * @param  string|int|float $end     The end value.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereBetween(string $column, $start, $end) : AbstractPlatform // ok
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::typeError(3, ['string', 'int', 'float'], $end);

		static::_setWhereBetween('OR', 'BETWEEN', $column, $start, $end);

		return static::_getInstance();
	}

	/**
	 * Sets the 'NOT BETWEEN' condition for the WHERE clause.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $start   The start value.
	 * @param  string|int|float $end     The end value.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereNotBetween(string $column, $start, $end) : AbstractPlatform // ok
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::typeError(3, ['string', 'int', 'float'], $end);

		static::_setWhereBetween('AND', 'NOT BETWEEN', $column, $start, $end);

		return static::_getInstance();
	}

	/**
	 * Sets the 'NOT BETWEEN' condition for the 'OR' clause in the WHERE statement.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $start   The start value.
	 * @param  string|int|float $end     The end value.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereNotBetween(string $column, $start, $end) : AbstractPlatform // ok
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::typeError(3, ['string', 'int', 'float'], $end);

		static::_setWhereBetween('OR', 'NOT BETWEEN', $column, $start, $end);

		return static::_getInstance();
	}

	/**
	 * Sets the 'BETWEEN' condition for the WHERE clause.
	 *
	 * @param  string           $logicalOperator  The logical operator. Either 'AND' or 'OR'.
	 * @param  string           $sqlOperator      The SQL operator. Either 'BETWEEN' or 'NOT BETWEEN'.
	 * @param  string           $column           The column name.
	 * @param  string|int|float $start            The start value.
	 * @param  string|int|float $end              The end value.
	 * @return void
	 */
	protected static function _setWhereBetween(string $logicalOperator, string $sqlOperator, string $column, $start, $end) : void // ok
	{
		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[] = [$logicalOperator, $column . ' ' . $sqlOperator . ' ' . $start . ' AND ' . $end];
	}

	// Where like

	/**
	 * Sets the 'LIKE' condition for the WHERE clause that contains the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereContain(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' LIKE \'%' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the 'OR' clause in the WHERE statement
	 * that contains the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereContain(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' LIKE \'%' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the WHERE clause that starts with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereStartsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' LIKE \'' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the 'OR' clause in the WHERE statement
	 * that starts with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereStartsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' LIKE \'' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the WHERE clause that ends with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereEndsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' LIKE \'%' . $value . '\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the 'OR' clause in the WHERE statement
	 * that ends with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereEndsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' LIKE \'%' . $value . '\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the WHERE clause that does not contain the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereNotContain(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' NOT LIKE \'%' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 *  Sets the 'LIKE' condition for the 'OR' clause in the WHERE statement
	 *  that does not contain the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereNotContain(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' NOT LIKE \'%' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the WHERE clause that does not start with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereNotStartsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' NOT LIKE \'' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the 'OR' clause in the WHERE statement
	 * that does not start with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereNotStartsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' NOT LIKE \'' . $value . '%\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the WHERE clause that does not end with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereNotEndsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' NOT LIKE \'%' . $value . '\''];

		return static::_getInstance();
	}

	/**
	 * Sets the 'LIKE' condition for the 'OR' clause in the WHERE statement
	 * that does not end with the given value.
	 *
	 * @param  string           $column  The column name.
	 * @param  string|int|float $value   The value to search for.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereNotEndsWith(string $column, $value) : AbstractPlatform // ok
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' NOT LIKE \'%' . $value . '\''];

		return static::_getInstance();
	}

	// Where in

	/**
	 * Sets the 'IN' condition for the WHERE clause.
	 *
	 * @param  string                 $column  The column name.
	 * @param  string|int|float|array $values  The values to search for.
	 * @return AbstractPlatform                Returns the current object.
	 */
	public static function whereIn(string $column, $values) : AbstractPlatform // ok
	{
		if (!is_string($values) and !is_int($values) and !is_float($values) and !is_array($values))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array'], $values);

		static::_setWhereIn('AND', 'IN', $column, $values);

		return static::_getInstance();
	}

	/**
	 * Sets the 'IN' condition for the 'OR' clause in the WHERE statement.
	 *
	 * @param  string                 $column  The column name.
	 * @param  string|int|float|array $values  The values to search for.
	 * @return AbstractPlatform                Returns the current object.
	 */
	public static function orWhereIn(string $column, $values) : AbstractPlatform // ok
	{
		if (!is_string($values) and !is_int($values) and !is_float($values) and !is_array($values))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array'], $values);

		static::_setWhereIn('OR', 'IN', $column, $values);

		return static::_getInstance();
	}

	/**
	 * Sets the 'NOT IN' condition for the WHERE clause.
	 *
	 * @param  string                 $column  The column name.
	 * @param  string|int|float|array $values  The values to search for.
	 * @return AbstractPlatform                Returns the current object.
	 */
	public static function whereNotIn(string $column, $values) : AbstractPlatform // ok
	{
		if (!is_string($values) and !is_int($values) and !is_float($values) and !is_array($values))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array'], $values);
		static::_setWhereIn('AND', 'NOT IN', $column, $values);

		return static::_getInstance();
	}

	/**
	 * Sets the 'NOT IN' condition for the 'OR' clause in the WHERE statement.
	 *
	 * @param  string                 $column  The column name.
	 * @param  string|int|float|array $values  The values to search for.
	 * @return AbstractPlatform                Returns the current object.
	 */
	public static function orWhereNotIn(string $column, $values) : AbstractPlatform // ok
	{
		if (!is_string($values) and !is_int($values) and !is_float($values) and !is_array($values))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'array'], $values);

		static::_setWhereIn('OR', 'NOT IN', $column, $values);

		return static::_getInstance();
	}

	/**
	 * Sets the 'IN' condition for the WHERE clause.
	 *
	 * @param  string                  $logicalOperator  The logical operator. Either 'AND' or 'OR'.
	 * @param  string                  $sqlOperator      The SQL operator. Either 'IN' or 'NOT IN'.
	 * @param  string                  $column           The column name.
	 * @param  string|int|float|array  $values           The values to search for.
	 * @return void
	 */
	protected static function _setWhereIn(string $logicalOperator, string $sqlOperator, string $column, $values) // ok
	{
		$column = static::wrapColumn($column);
		$values = (array)$values;

		// The $args parameter is sent from the model class as
		// a multidimensional array, so we need to flatten it.
		$values = Arr::flatten($values);

		$values = array_map([static::_getInstance(), 'escape'], $values);
		$where = $column . ' ' . $sqlOperator . ' (' . implode(', ', $values) . ')';

		static::$_sqlWheres[] = [$logicalOperator, $where];
	}

	// Where null

	/**
	 * Sets the 'IS NULL' condition for the WHERE clause.
	 *
	 * @param  string           $column  The column name.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereNull(string $column) : AbstractPlatform // ok
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['AND', $column . ' IS NULL'];

		return static::_getInstance();
	}

	/**
	 * Sets the 'IS NULL' condition for the 'OR' clause in the WHERE statement.
	 *
	 * @param  string           $column  The column name.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereNull(string $column) : AbstractPlatform // ok
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['OR', $column . ' IS NULL'];

		return static::_getInstance();
	}

	/**
	 * Sets the 'IS NOT NULL' condition for the WHERE clause.
	 *
	 * @param  string           $column  The column name.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function whereNotNull(string $column) : AbstractPlatform // ok
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['AND', $column . ' IS NOT NULL'];

		return static::_getInstance();
	}

	/**
	 * Sets the 'IS NOT NULL' condition for the 'OR' clause in the WHERE statement.
	 *
	 * @param  string           $column  The column name.
	 * @return AbstractPlatform          Returns the current object.
	 */
	public static function orWhereNotNull(string $column) : AbstractPlatform // ok
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['OR', $column . ' IS NOT NULL'];

		return static::_getInstance();
	}

	// Group

	/**
	 * Sets the grouping columns.
	 *
	 * @param  string           $columns  List of columns separated by comma.
	 * @return AbstractPlatform           Returns the current object.
	 */
	public static function group(string $columns) : AbstractPlatform // ok
	{
		$columns = static::_parseColumn($columns);

		foreach ($columns as $column)
			static::$_sqlGroups[] = $column;

		return static::_getInstance();
	}

	// Order by

	/**
	 * Sets the sorting columns.
	 *
	 * @param  string           $columns    List of columns separated by comma.
	 * @param  string           $direction  The sorting direction. Either 'ASC' or 'DESC'.
	 * @return AbstractPlatform             Returns the current object.
	 */
	public static function sort(string $columns, string $direction = 'ASC') : AbstractPlatform // ok
	{
		$columns = static::_parseColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSorts[] = $column . ' ' . strtoupper($direction);

		return static::_getInstance();
	}

	/**
	 * Sets the sorting columns in ascending order.
	 *
	 * @param  string           $columns  List of columns separated by comma.
	 * @return AbstractPlatform           Returns the current object.
	 */
	public static function sortAsc(string $columns) : AbstractPlatform // ok
	{
		static::sort($columns, 'ASC');

		return static::_getInstance();
	}

	/**
	 * Sets the sorting columns in descending order.
	 *
	 * @param  string           $columns  List of columns separated by comma.
	 * @return AbstractPlatform           Returns the current object.
	 */
	public static function sortDesc(string $columns) : AbstractPlatform // ok
	{
		static::sort($columns, 'DESC');

		return static::_getInstance();
	}

	// Limit

	/**
	 * Sets the number of rows to return.
	 *
	 * @param  int              $num  The number of rows to return.
	 * @return AbstractPlatform       Returns the current object.
	 */
	public static function take(int $num) : AbstractPlatform // ok
	{
		static::$_sqlTake = $num;

		return static::_getInstance();
	}

	/**
	 * Sets the number of rows to skip.
	 *
	 * @param  int              $num  The number of rows to skip.
	 * @return AbstractPlatform       Returns the current object.
	 */
	public static function skip(int $num) : AbstractPlatform // ok
	{
		static::$_sqlSkip = $num;

		return static::_getInstance();
	}

	// Query

	/**
	 * Sets the raw SQL query.
	 *
	 * @param  string           $sql  The raw SQL query.
	 * @return AbstractPlatform
	 */
	public static function raw(string $sql) : AbstractPlatform // ok
	{
		$pos = mb_stripos($sql, ' WHERE ');

		if (!$pos)
			$pos = mb_stripos($sql, ' VALUES ');

		if (!$pos)
			$pos = mb_stripos($sql, ' SET ');

		// Prevent replacing #_ in the WHERE clause.

		if ($pos)
		{
			$block1 = mb_substr($sql, 0, $pos);
			$block2 = mb_substr($sql, $pos);

			$block1 = str_replace('#_', Config::db('prefix'), $block1);

			$sql = $block1 . $block2;
		}
		else
			$sql = str_replace('#_', Config::db('prefix'), $sql);

		static::$_sqlRaw = $sql;

		return static::_getInstance();
	}

	/**
	 * Executes the raw SQL query for insert, update, and delete.
	 *
	 * For retrieving data, use the `load()`, `loadAll()`, `loadSingle()` methods.
	 *
	 * For example,
	 *
	 * ```php
	 * // Without Transaction
	 *
	 * DB::execute($sql);
	 * DB::raw($sql)->execute();
	 *
	 * // With Transaction
	 *
	 * DB::beginTransaction();
	 * DB::execute($sql);
	 *
	 * if (!DB::commit())
	 *    DB::rollBack();
	 * ```
	 *
	 * @param  string|null $sql  The raw SQL query. Defaults to null.
	 * @return int               Returns the number of affected rows.
	 */
	public static function execute(?string $sql = null) : int // ok
	{
		if (!$sql)
			$sql = static::$_sqlRaw;

		static::$_connection->execute($sql);

		return static::getAffectedRows();
	}

	/**
	 * Queries the database and returns the result.
	 *
	 * @param  string      $sql  The raw SQL query.
	 * @return object|bool       Returns a PDOStatement object if transaction mode is off.
	 *                           Returns true if transaction mode is on.
	 */
	protected static function _query(string $sql)
	{
		if (Config::app('env') === 'development')
			static::$_executedQueries[] = $sql;

		if (static::$_transactionMode)
		{
			// The query will be executed after the transaction is committed by the static::commit() method.
			static::$_transactionSqls[] = $sql;

			return true;
		}
		else
		{
			$result = static::$_connection->query($sql);

			if (is_object($result))
				static::$_affectedRows = $result->rowCount();

			elseif (static::$_connection->errorInfo()[2] and Config::app('env') === 'development')
				static::_displayError();

			static::_reset();

			return $result;
		}
	}

	// Load data

	/**
	 * Gets a single item (first column of the first row) from the recordset.
	 *
	 * @return mixed
	 */
	public static function loadSingle() // ok
	{
		$sql = static::_buildQuerySelect();
		$data = static::load($sql);
		$data = (array)$data;
		$data = current($data);

		return $data;
	}

	/**
	 * Retrieves a single row from the recordset.
	 *
	 * For example,
	 *
	 * ```php
	 * DB::table('user')->load();
	 * $result = DB::getLastQuery();
	 * // The $result will be: SELECT * FROM `vd_User` LIMIT 1
	 * ```
	 *
	 * @return stdClass|bool  Returns an object with properties that correspond to the fetched row's columns.
	 *                        Return false if the query string does not contain the word 'SELECT'.
	 */
	public static function load() // ok
	{
		static::take(1);

		$sql = static::_buildQuerySelect();
		$result = static::_query($sql);

		if ($result)
		{
			if ($result->rowCount())
				return $result->fetch();
			else
			{
				$select = substr($sql, 7, stripos($sql, ' FROM ') - 7);

				if (trim($select) === '*')
				{
					$pattern = '/' . Config::db('prefix') . '(.*)\s+/U';
					preg_match_all($pattern, $sql, $tables);

					$columns = [];

					foreach ($tables[0] as $table)
					{
						$table = static::_removeDelimitIdentifier($table);
						$columns = array_merge($columns, array_values(static::getColumns($table)));
					}
				}
				else
				{
					$columns = [];
					$arr = explode(',', $select);

					foreach ($arr as $column)
					{
						$pos = stripos($column, ' AS ');

						if ($pos)
							$column = substr($column, $pos + 4);

						$column = static::_removeDelimitIdentifier($column);
						$columns[] = $column;
					}
				}

				$data = new stdClass();

				foreach ($columns as $column)
					$data->$column = '';

				return $data;
			}
		}
		else
			return false;
	}

	/**
	 * Retrieves all rows from the recordset.
	 *
	 *  For example,
	 *
	 * ```php
	 *  DB::table('user')->loadAll();
	 *  $result = DB::getLastQuery();
	 *  // The $result will be: SELECT * FROM `vd_User`
	 *  ```
	 *
	 * @return array|false  Returns an array of objects with properties that correspond to the fetched rows' columns.
	 */
	public static function loadAll() // ok
	{
		$sql = static::_buildQuerySelect();
		$result = static::_query($sql);

		if ($result)
		{
			$rows = $result->fetchAll();

			return $rows;
		}
		else
			return false;
	}

	/**
	 * @param  int|null       $pagesize  Optionally, the number of rows per page. Defaults to null.
	 * @param  int|null       $page      Optionally, the page number. Defaults to null.
	 * @return array|false               Returns an array of objects with properties that correspond to the fetched rows' columns.
	 * @throws ErrorException
	 */
	public static function paginate(?int $pagesize = null, ?int $page = null) // ok
	{
		if ($pagesize)
			Paginator::setPageSize($pagesize);

		if ($page)
			Paginator::setPage($page);

		$sql = static::_buildQuerySelect();
		$sqlCount = 'SELECT COUNT(*) ' . substr($sql, stripos($sql, ' FROM '));
		$totalrecord = static::raw($sqlCount)->loadSingle();

		Paginator::setTotalRecord($totalrecord);

		$page = Paginator::getPage();
		$pagesize = Paginator::getPageSize();
		$sortcol = Paginator::getSortCol();
		$sortdir = strtoupper(Paginator::getSortDir());
		$offset = $pagesize * ($page - 1);

		static::sort($sortcol, $sortdir);
		static::take($pagesize);
		static::skip($offset);

		$result = static::loadAll();

		if ($result)
		{
			$rows = $result->fetchAll();
			$no = Paginator::getNumStart();

			foreach ($rows as $row)
				$row->{':no'} = $no++;

			return $rows;
		}
		else
			return false;
	}

	/**
	 * Encodes the recordset to JSON string.
	 *
	 * @return string  Returns the JSON string.
	 */
	public static function toJson() : string // ok
	{
		$data = static::loadAll();
		$json = json_encode($data);

		// Prevent json_decode() from converting empty value to array.
		$json = str_replace('{}', '""', $json);

		return $json;
	}

	/**
	 * Converts the recordset to CSV string.
	 *
	 * @param  bool   $header  Whether to include the header row.
	 * @return string          Returns the CSV string.
	 */
	public static function toCsv(bool $header = false) : string // ok
	{
		$data = static::loadAll();

		if ($header === true and isset($data[0]))
		{
			$header = [];

			foreach ($data[0] as $key => $value)
				$header[] = $key;

			array_unshift($data, $header);
		}

		$csv = CSV::fromRecordset($data);

		return $csv;
	}

	/**
	 * Converts the recordset to XML string.
	 *
	 * @return string  Returns the XML string.
	 */
	public static function toXML() : string // ok
	{
		$data = static::loadAll();
		$xml = XML::fromRecordset($data);

		return $xml;
	}

	// Transaction

	/**
	 * Runs a transaction.
	 *
	 * @param  Closure  $callback  The callback function.
	 * @param  bool     $testMode  Whether to run the transaction in test mode. Defaults to false.
	 * @return bool                Returns true on success, or false on failure.
	 */
	public static function transaction(Closure $callback, bool $testMode = false) : bool // ok
	{
		static::beginTransaction();

		$callback();

		if (static::transactionSuccess())
		{
			if (!$testMode)
				static::commit();

			return true;
		}
		else
		{
			static::rollback();

			return false;
		}
	}

	/**
	 * Initiates a transaction.
	 *
	 * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the DB object
	 * instance are not committed until you end the transaction by calling static::commit(). Calling static::rollBack()
	 * will roll back all changes to the database and return the connection to autocommit mode.
	 *
	 * @return bool  Returns true on success, or false on failure.
	 */
	public static function beginTransaction() : bool // ok
	{
		// Ensure the connection is established in case we
		// start a transaction manually before setting a query.
		static::_getInstance();

		$result = static::$_connection->beginTransaction();

		static::$_transactionMode = false;

		return $result;
	}

	/**
	 * Commits a transaction.
	 *
	 * Commits a transaction, returning the database connection to autocommit mode until the next call to
	 * static::beginTransaction() starts a new transaction.
	 *
	 * @return bool  Returns true on success, or false on failure.
	 */
	public static function commit() : bool // ok
	{
		static::_queryTransaction();

		$result = static::$_connection->commit();

		static::$_transactionMode = false;
		static::$_transactionSqls = [];

		return $result;
	}

	/**
	 * Rolls back a transaction.
	 *
	 * Rolls back the current transaction, as initiated by static::beginTransaction().
	 *
	 * @return bool  Returns true on success, or false on failure.
	 */
	public static function rollback() : bool // ok
	{
		$result = static::$_connection->rollback();

		static::$_transactionMode = false;
		static::$_transactionSqls = [];

		return $result;
	}

	public static function transactionSuccess()
	{
		return static::_queryTransaction();
	}

	/**
	 * Queries the SQL statements in the transaction.
	 *
	 * @return bool  Returns true on success, or false on failure.
	 */
	protected static function _queryTransaction() : bool // ok
	{
		$sqls = static::$_transactionSqls;

		static::$_transactionMode = false;
		static::$_transactionSqls = [];

		if (count($sqls))
		{
			try
			{
				foreach ($sqls as $sql)
				{
					if (Config::app('env') === 'development')
						static::$_executedQueries[] = $sql;

					static::$_connection->query($sql);
				}

				return true;
			}
			catch (PDOException $e)
			{
				return false;
			}
		}
		else
			return false;
	}

	// Build

	/**
	 * Builds the SQL query string for retrieving data.
	 *
	 * @return string  Returns the SQL query string.
	 */
	protected static function _buildQuerySelect() : string // ok
	{
		if (static::$_sqlRaw)
			return static::$_sqlRaw;

		$sql = 'SELECT ';

		if (count(static::$_sqlSelects))
			$sql .= implode(', ', static::$_sqlSelects);
		else
			$sql .= '*';

		$sql .= static::_buildFrom();
		$sql .= static::_buildWhere();
		$sql .= static::_buildGroup();
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	/**
	 * Builds the SQL query string for inserting data.
	 *
	 * @param  array|object $data  The data to be saved. If it's an array, it can be multidimensional array.
	 * @return string              Returns the SQL query string.
	 */
	protected static function _buildQueryInsert($data) : string // ok
	{
		$datas = static::_prepareDataBeforeSave($data);

		// Automatically set values for the 'ordering', 'created' and 'creator' fields.

		$autoOrdering = false;
		$ordering = 0;
		$userId = (int)@Auth::identity()->id;

		if (static::columnExists('ordering') and !array_key_exists('ordering', $datas[0]))
		{
			$autoOrdering = true;
			$ordering = static::getNewOrdering();
		}

		for ($i = 0, $n = count($datas); $i < $n; ++$i)
		{
			if ($autoOrdering)
			{
				$datas[$i] = Data::set($datas[$i], 'ordering', $ordering);
				++$ordering;
			}

			if (static::columnExists('created') and !array_key_exists('created', $datas[$i]))
				$datas[$i] = Data::set($datas[$i], 'created', date('Y-m-d H:i:s'));

			if (static::columnExists('creator') and !array_key_exists('creator', $datas[$i]))
				$datas[$i] = Data::set($datas[$i], 'creator', $userId);
		}

		//  Build the SQL query string.

		$table = static::$_sqlTable;
		$columns = [];
		$values = '';

		foreach (array_keys($datas[0]) as $column)
			$columns[] = static::wrapColumn($column);

		foreach ($datas as $data)
		{
			$data = array_map([static::$_instance, 'escape'], array_values($data));
			$values .= '(' . implode(', ', $data) . '), ';
		}

		$columns = implode(', ', $columns);
		$values = substr($values, 0, -2);

		$sql = 'INSERT INTO ' . $table . ' (' . $columns . ') VALUES ' . $values;

		return $sql;
	}

	/**
	 * Builds the SQL query string for updating data.
	 *
	 * @param  array|object $data  The data to be saved. If it's a multidimensional array, only the first item will be used.
	 * @return string              Returns the SQL query string.
	 */
	protected static function _buildQueryUpdate($data) : string // ok
	{
		$datas = static::_prepareDataBeforeSave($data);
		$data = $datas[0];

		// Data may be empty after calling static::_prepareDataBeforeSave()
		if (empty($data))
			return '';

		// Automatically set values for the 'updated' and 'updater' fields.

		$userId = (int)@Auth::identity()->id;

		if (static::columnExists('updated') and !array_key_exists('updated', $data))
			$data = Data::set($data, 'updated', date('Y-m-d H:i:s'));

		if (static::columnExists('updater') and !array_key_exists('updater', $data))
			$data = Data::set($data, 'updater', $userId);

		// Build the SQL query string.

		$columns = array_keys($data);
		$values = array_values($data);

		$sql = 'UPDATE ' . static::$_sqlTable . ' SET ';

		for ($i = 0, $n = count($columns); $i < $n; ++$i)
			$sql .= static::wrapColumn($columns[$i]) . ' = ' . static::escape($values[$i]) . ', ';

		$sql = substr($sql, 0, -2);

		$sql .= static::_buildWhere();
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	/**
	 * Builds the SQL query string for the 'INSERT' or 'UPDATE' query based on the WHERE clause. If the WHERE
	 * clause is empty, it will return the SQL query string for the INSERT. Otherwise, it will return the
	 * SQL query string for the UPDATE.
	 *
	 * @param  array|object $data  The data to be saved.
	 * @return string              Returns the SQL query string.
	 */
	protected static function _buildQuerySave($data) : string // ok
	{
		$where = static::_buildWhere();

		if ($where)
			$sql = static::_buildQueryUpdate($data);
		else
			$sql = static::_buildQueryInsert($data);

		return $sql;
	}

	/**
	 * Builds the SQL query string for deleting data.
	 *
	 * @param  string|null $where  Optionally, the WHERE clause. Defaults to null.
	 * @return string              Returns the SQL query string.
	 */
	protected static function _buildQueryDelete(?string $where = null) : string // ok
	{
		$sql = 'DELETE FROM ' . static::$_sqlTable . $where;
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	/**
	 * Builds the SQL query string for the FROM clause.
	 *
	 * @return string  Returns the SQL query string.
	 */
	protected static function _buildFrom() : string // ok
	{
		$sql = ' FROM ' . static::$_sqlTable;

		if (static::$_sqlJoins)
			$sql .= ' ' . implode(' ', static::$_sqlJoins);

		return $sql;
	}

	/**
	 * Builds the SQL query string for the WHERE clause.
	 *
	 * @return string
	 */
	protected static function _buildWhere() : string // ok
	{
		$where = '';

		if (static::$_sqlWheres)
		{
			$where = ' WHERE ';
			$_sqlWheres = static::$_sqlWheres;

			for ($i = 0, $n = count($_sqlWheres); $i < $n; ++$i)
			{
				if ($i > 0 and $_sqlWheres[$i][1] !== ')' and $_sqlWheres[$i - 1][1] !== '(')
					$where .= ' ' . $_sqlWheres[$i][0] . ' ';

				$where .= $_sqlWheres[$i][1];

				//$where .= $where .= $_sqlWheres[$i][0]. ' ' . $_sqlWheres[$i][1]; // todo
			}
		}

		return $where;
	}

	/**
	 * Builds the SQL query string for the GROUP BY clause.
	 *
	 * @return string  Returns the SQL query string.
	 */
	protected static function _buildGroup() : string // ok
	{
		if (static::$_sqlGroups)
			return ' GROUP BY ' . implode(', ', static::$_sqlGroups);

		return '';
	}

	/**
	 * Builds the SQL query string for the ORDER BY clause.
	 *
	 * @return string  Returns the SQL query string.
	 */
	protected static function _buildSort() : string // ok
	{
		if (static::$_sqlSorts)
			return ' ORDER BY ' . implode(', ', static::$_sqlSorts);

		return '';
	}

	/**
	 * Builds the SQL query string for the LIMIT clause.
	 *
	 * @return string  Returns the SQL query string.
	 */
	protected static function _buildLimit() : string // ok
	{
		$sql = '';

		if (static::$_sqlSkip and !static::$_sqlTake)
		{
			// Convert to string. If it's a number,
			// PHP will convert it to 1.844674407371E+19
			static::$_sqlTake = '18446744073709551615';
		}

		if (static::$_sqlTake)
			$sql = ' LIMIT ' . static::$_sqlTake;

		if (static::$_sqlSkip)
			$sql .= ' OFFSET ' . static::$_sqlSkip;

		return $sql;
	}

	// Other

	protected static function _removeDelimitIdentifier(string $str) : string // ok
	{
		$str = trim($str);
		$str = ltrim($str, static::$_delimitIdentifierLeft);
		$str = rtrim($str, static::$_delimitIdentifierRight);

		return $str;
	}

	/**
	 * Formats table name by adding prefix and change the first letter to uppercase.
	 *
	 *  For example,
	 *
	 * ```php
	 * $result = DB::formatTable('user');
	 * // The $result will be: vd_User
	 * ```
	 *
	 * @param  string $table  The table name.
	 * @return string         Returns the formatted table name.
	 */
	public static function formatTable(string $table) : string // ok
	{
		$table = trim($table);
		$table = ltrim($table, static::$_delimitIdentifierLeft);
		$table = rtrim($table, static::$_delimitIdentifierRight);

		if (substr($table, 0, strlen(Config::db('prefix'))) !== Config::db('prefix'))
			$table = Config::db('prefix') . ucfirst($table);

		return $table;
	}

	/**
	 * Formats table name and wraps it with identifier.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = DB::wrapTable('user');
	 * // The $result will be: `vd_User`
	 *
	 * $result = DB::wrapTable('user AS t');
	 * // The $result will be: `vd_User` AS `t`
	 * ```
	 *
	 * @param  string $table  The table name.
	 * @return string         Returns the formatted table name wrapped with identifier.
	 */
	public static function wrapTable(string $table) : string // ok
	{
		$pos = stripos($table, ' AS ');

		if ($pos)
		{
			$haystack = $table;

			$table = substr($haystack, 0, $pos);
			$alias = substr($haystack, $pos + 4);

			$table = trim($table);
			$alias = trim($alias);
		}
		else
		{
			$table = trim($table);
			$alias = '';
		}

		$table = static::formatTable($table);
		$table = static::$_delimitIdentifierLeft . $table . static::$_delimitIdentifierRight;

		if ($alias)
		{
			$alias = static::$_delimitIdentifierLeft . $alias . static::$_delimitIdentifierRight;
			$table .= ' AS ' . $alias;
		}

		return $table;
	}

	/**
	 * Wraps column name with identifier.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = DB::wrapColumn('*');
	 * // The $result will be: *
	 *
	 * $result = DB::wrapColumn('id');
	 * $result = DB::wrapColumn('`id`');
	 * // The $result will be: `id`
	 *
	 * $result = DB::wrapColumn('`id` AS userId');
	 * $result = DB::wrapColumn('`id` AS `userId`');
	 * // The $result will be: `id` AS `userId`
	 *
	 * $result = DB::wrapColumn('u.id');
	 * $result = DB::wrapColumn('`u`.`id`');
	 * // The $result will be: `u`.`id`
	 *
	 * $result = DB::wrapColumn('u.id AS userId');
	 * $result = DB::wrapColumn('`u`.`id` AS `userId`');
	 * // The $result will be: `u`.`id` AS `userId`
	 * ```
	 *
	 * @param  string $column  The column name.
	 * @return string          Returns the column name wrapped with identifier.
	 */
	public static function wrapColumn(string $column) : string // ok
	{
		if (trim($column) === '*')
			$column = '*';
		else
		{
			$pos = stripos($column, ' AS ');

			if ($pos)
			{
				$haystack = $column;

				$column = substr($haystack, 0, $pos);
				$alias = substr($haystack, $pos + 4);

				$alias = trim($alias);

				if (substr($alias, 0, 1) !== static::$_delimitIdentifierLeft)
					$alias = static::$_delimitIdentifierLeft . $alias;

				if (substr($alias, 0, -1) !== static::$_delimitIdentifierRight)
					$alias .= static::$_delimitIdentifierRight;
			}
			else
				$alias = '';

			$arr = explode('.', $column);
			$column = trim($arr[0]);

			if (substr($column, 0, 1) !== static::$_delimitIdentifierLeft)
				$column = static::$_delimitIdentifierLeft . $column;

			if (substr($column, 0, -1) !== static::$_delimitIdentifierRight)
				$column .= static::$_delimitIdentifierRight;

			if (isset($arr[1]))
			{
				$arr[1] = trim($arr[1]);

				if (substr($arr[1], 0, 1) !== static::$_delimitIdentifierLeft)
					$arr[1] = static::$_delimitIdentifierLeft . $arr[1];

				if (substr($arr[1], 0, -1) !== static::$_delimitIdentifierRight)
					$arr[1] .= static::$_delimitIdentifierRight;

				$column .= '.' . $arr[1];
			}

			if ($alias)
				$column .= ' AS ' . $alias;
		}

		return $column;
	}

	/**
	 * An alias of method static::wrapColumn().
	 *
	 * @param  string $column  The column name.
	 * @return string          Returns the column name wrapped with identifier.
	 */
	public static function wrap(string $column) : string // ok
	{
		return static::wrapColumn($column);
	}

	/**
	 * Escapes strings for use in SQL statements.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = DB::escape('abc');
	 * // The $result will be: 'abc'
	 *
	 * $result = DB::escape(0812345678);
	 * // The $result will be: '0812345678'
	 *
	 * $result = DB::escape('3.14');
	 * // The $result will be: '3.14'
	 *
	 * $result = DB::escape(true);
	 * // The $result will be: 1
	 *
	 * $result = DB::escape(false);
	 * // The $result will be: 0
	 *
	 * $result = DB::escape(null);
	 * // The $result will be: NULL
	 * ```
	 *
	 * @param  string|int|float|bool|null $value  The value to be escaped.
	 * @return string                             Returns the escaped string.
	 */
	public static function escape($value) : string // ok
	{
		if (is_object($value) or is_resource($value))
			throw InvalidArgumentException::typeError(1, ['string', 'int', 'float', 'bool', 'null'], $value);

		// Numbers sent via the URL (e.g., delete?id[]=1&id[]=2) will be
		// treated as strings. Phone numbers starting with '0' will disappear
		// if an escape string is not added. So, we need to escape both
		// strings and numbers to ensure they will work correctly.
		//
		// Using is_int() and is_float() is faster than using is_numeric().
		if (is_string($value) or is_int($value) or is_float($value))
			$value = static::quote($value);
		elseif (is_bool($value))
			$value = ($value === false ? 0 : 1);
		elseif (is_null($value))
			$value = 'NULL';

		return (string)$value;
	}

	/**
	 * Places quotes around the input string (if required) and escapes
	 * special characters within the input string, using a quoting style
	 * appropriate to the underlying driver.
	 *
	 * @param  string|int|float $string  The string to be quoted.
	 * @return string                    Returns a quoted string that is theoretically
	 *                                   safe to pass into an SQL statement.Returns
	 *                                   false if the driver does not support quoting
	 *                                   in this way.
	 */
	public static function quote($string) : string // ok
	{
		if (!is_string($string) and !is_int($string) and !is_float($string))
			throw InvalidArgumentException::typeError(1, ['string', 'int', 'float'], $string);

		return static::$_connection->quote($string);
	}

	/**
	 * Escapes LIKE String for use in an SQL statement.
	 *
	 * For example,
	 *
	 * ```php
	 * $result = DB::escapeLike('%abc%');
	 * // The $result will be: '\%abc\%'
	 * ```
	 *
	 * @param  string|int|float|bool|null $value  The value to be escaped.
	 * @return string                             Returns the escaped string.
	 */
	public static function escapeLike($value) : string // ok
	{
		if (is_object($value) or is_resource($value))
			throw InvalidArgumentException::typeError(1, ['string', 'int', 'float', 'bool', 'null'], $value);

		$removeQuote = false;

		if (is_string($value) or is_int($value) or is_float($value))
			$removeQuote = true;

		$value = static::escape($value);
		$value = addcslashes($value, '%_');

		if ($removeQuote)
			$value = substr($value, 1, -1);

		return (string)$value;
	}

	public static function getNewOrdering()
	{
		$table = static::$_sqlTable;
		$ordering = static::wrapColumn('ordering');

		// TODO do i need to lock table first?

		$sql = 'SELECT IFNULL(MAX(' . $ordering . '), 0) + 1 '
			. 'AS ' . $ordering . ' '
			. 'FROM ' . $table;

		if (Config::app('env') === 'development')
			static::$_executedQueries[] = $sql;

		$result = static::$_connection->query($sql);
		$row = $result->fetch();

		return $row->ordering;
	}

	/**
	 * Gets the last query.
	 *
	 * @return string  Returns the last query.
	 */
	public static function getLastQuery() : string // ok
	{
		return (string)end(static::$_executedQueries);
	}

	/**
	 * Gets all executed queries.
	 *
	 * @return array  Returns all executed queries.
	 */
	public static function getAllQueries() : array // ok
	{
		return static::$_executedQueries;
	}

	/**
	 * Gets the last insert ID.
	 *
	 * @return int  Returns the last insert ID.
	 */
	public static function getLastInsertId() : int // ok
	{
		return static::$_connection->lastInsertId();
	}

	/**
	 * Gets the number of affected rows.
	 *
	 * @return int  Returns the number of affected rows.
	 */
	public static function getAffectedRows() : int // ok
	{
		return (int)static::$_affectedRows;
	}

	/**
	 * Determines whether the given data exists in the database.
	 *
	 * For example,
	 *
	 * ```php
	 *
	 * // Checks if there exists a record with the username 'withnat'.
	 * DB::table('User')->exists(['username' => 'withnat']);
	 *
	 * // Checks if there exists a record with the username 'withnat', not with the id = 1
	 * DB::table('User')->exists(['id' => 1, 'username' => 'withnat']);
	 * ```
	 *
	 * @param  array|object $data  The data to be checked, the provided data can be an array or object.
	 * @return bool                Returns true if the data exists in the database. Otherwise, returns false.
	 */
	public static function exists($data) : bool // ok
	{
		if (!Arr::isAssociative($data) and !is_object($data) and !is_null($data))
			throw InvalidArgumentException::typeError(1, ['associative array', 'object', 'null'], $data);

		// Convert object to array.
		$data = (array)$data;

		$idcol = static::wrapColumn('id');
		$idval = $data['id'] ?? null;

		unset($data['id']);

		$columns = array_keys($data);
		$values = array_values($data);

		for ($i = 0, $n = count($columns); $i < $n; ++$i)
			static::where($columns[$i], $values[$i]);

		if ($idval)
		{
			$idval = static::escape($idval);
			$where = static::_buildWhere();

			if ($where)
				static::where($idcol, '!=', $idval);

			// Only id, no other fields.
			// For example, DB::table('User')->exists(['id' => 1]);
			else
				static::where($idcol, $idval);
		}

		$args = func_get_args();

		if (isset($args[1])) // Calling from Model::exists()
		{
			if (static::columnExists('status') and !array_key_exists('status', $data))
				static::where('status', '>', -1);
		}

		static::select($idcol);

		if (static::loadSing())
			return true;
		else
			return false;
	}

	/**
	 * Gets a listing of tables in the database.
	 *
	 * @return array  Returns an array of table names.
	 */
	public static function getTables() : array // ok
	{
		if (empty(static::$_tables))
		{
			$filename = '__vanda_tablelist.php';
			$file = static::$_dbCachePath . $filename;

			if (is_file(static::$_dbCachePath . $filename))
			{
				$content = file_get_contents(static::$_dbCachePath . $filename);
				$content = substr($content, 8); // Remove "<?php //" from the content.

				static::$_tables = @unserialize($content);
			}

			if (empty(static::$_tables))
			{
				$sql = 'SHOW TABLES';
				$result = static::query($sql);

				while ($table = $result->fetch())
				{
					// Get the first element of an object or array.
					$table = current($table);
					static::$_tables[] = $table;
				}

				$content = '<?php //' . serialize(static::$_tables);
				file_put_contents($file, $content);
			}
		}

		return static::$_tables;
	}

	/**
	 * Gets a listing of columns and their types for the given table.
	 *
	 * @param  string|null $table  Optionally, the table name. Defaults to null.
	 * @return array
	 */
	protected static function _getTableInfo(?string $table = null) : array // ok
	{
		if ($table)
			$table = static::formatTable($table);
		else
			$table = static::$_sqlTable;

		if (empty(static::$_tableInfo[$table]))
		{
			$filename = '__vanda_table_' . strtolower($table) . '.php';
			$file = static::$_dbCachePath . $filename;

			if (is_file($file))
			{
				$content = file_get_contents($file);
				$content = substr($content, 8); // Remove "<?php //" from the content.

				static::$_tableInfo[$table] = unserialize($content);
			}
			else
			{
				$sql = 'DESCRIBE ' . static::wrapTable($table);
				$result = static::query($sql);

				$rows = $result->fetchAll();
				$tableInfo = [];
				$i = 0;

				foreach ($rows as $row)
				{
					$column = $row->Field;
					$columnPosition = ++$i;
					$dataType = $row->Type;
					$default = $row->Default;
					$nullable = ($row->Null === 'YES');
					$length = null;
					$scale = null;
					$precision = null;
					$unsigned = (strpos($row->Type, 'unsigned') !== false);
					$primary = ($row->Key === 'PRI');
					$primaryPosition = ($primary ? $columnPosition : null);
					$autoIncrement = ($row->Extra === 'auto_increment');

					if (strpos($dataType, '('))
					{
						$arr = explode('(', $dataType);
						$dataType = $arr[0];
						$length = rtrim($arr[1]);

						if (strpos($length, ',')) // todo
						{
							$arr = explode(',', $length);
							$scale = $arr[1];
							$precision = $arr[0];
							$length = null;
						}
					}

					$tableInfo[$row->Field]['TABLE_NAME'] = $table;
					$tableInfo[$row->Field]['COLUMN_NAME'] = $column;
					$tableInfo[$row->Field]['COLUMN_POSITION'] = $columnPosition;
					$tableInfo[$row->Field]['DATA_TYPE'] = $dataType;
					$tableInfo[$row->Field]['DEFAULT'] = $default;
					$tableInfo[$row->Field]['NULLABLE'] = $nullable;
					$tableInfo[$row->Field]['LENGTH'] = $length;
					$tableInfo[$row->Field]['SCALE'] = $scale;
					$tableInfo[$row->Field]['PRECISION'] = $precision;
					$tableInfo[$row->Field]['UNSIGNED'] = $unsigned;
					$tableInfo[$row->Field]['PRIMARY'] = $primary;
					$tableInfo[$row->Field]['PRIMARY_POSITION'] = $primaryPosition;
					$tableInfo[$row->Field]['IDENTITY'] = '';
					$tableInfo[$row->Field]['AUTO_INCREMENT'] = $autoIncrement;
				}

				static::$_tableInfo[$table] = $tableInfo;

				$content = '<?php //' . serialize($tableInfo);
				file_put_contents($file, $content);
			}
		}

		return static::$_tableInfo[$table];
	}

	/**
	 * Gets a listing of columns in the given table.
	 *
	 * @return array  Returns an array of column names.
	 */
	public static function getColumns($table = null) : array // ok
	{
		$info = static::_getTableInfo($table);
		$columns = array_keys($info);

		return $columns;
	}

	/**
	 * Gets the column information.
	 *
	 * @param  string    $column  The column name.
	 * @return stdClass
	 */
	public static function getColumnInfo(string $column) : stdClass // ok
	{
		$info = static::_getTableInfo();
		$data = new stdClass();

		$data->name = $info[$column]['COLUMN_NAME'];
		$data->position = $info[$column]['COLUMN_POSITION'];
		$data->datatype = $info[$column]['DATA_TYPE'];
		$data->default = $info[$column]['DEFAULT'];
		$data->nullable = $info[$column]['NULLABLE'];
		$data->length = $info[$column]['LENGTH'];
		$data->scale = $info[$column]['SCALE'];
		$data->precision = $info[$column]['PRECISION'];
		$data->unsigned = $info[$column]['UNSIGNED'];

		return $data;
	}

	/**
	 * Determines whether the given table exists.
	 *
	 * @param  string $table  The table name.
	 * @return bool           Returns true if the table exists. Otherwise, returns false.
	 */
	public static function tableExists(string $table) : bool // ok
	{
		$table = static::formatTable($table);
		$tables = static::getTables();

		return in_array($table, $tables);
	}

	/**
	 * Determines whether the given column exists.
	 *
	 * @param  string $column  The column name.
	 * @return bool            Returns true if the column exists. Otherwise, returns false.
	 */
	public static function columnExists(string $column) : bool // ok
	{
		$columns = static::getColumns();

		return in_array($column, $columns);
	}

	/**
	 * Locks current table to control access during transactions.
	 *
	 * This method allows you to lock the current table to control access during transactions.
	 *
	 *  MySQL has a variety of locks that may be applied to tables. The following are the most popular lock types:
	 *
	 *  READ: Allows other users to read data from the table but stops them from writing until the lock is removed.
	 *  WRITE: Restrict all table read and write operations until the lock is freed.
	 *
	 * @param  bool $write        Whether to restrict write operations until the lock is released.
	 * @return PDOStatement|void  Returns the result of the query.
	 */
	public static function lock(bool $write = false) // ok
	{
		$sql = 'LOCK TABLES ' . static::$_sqlTable . ($write ? ' WRITE' : ' READ');

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
			return static::query($sql);
	}

	/**
	 * Locks the given table to control access during transactions.
	 *
	 * This method allows you to lock the given table to control access during transactions.
	 *
	 * MySQL has a variety of locks that may be applied to tables. The following are the most popular lock types:
	 *
	 * READ: Allows other users to read data from the table but stops them from writing until the lock is removed.
	 * WRITE: Restrict all table read and write operations until the lock is freed.
	 *
	 * @param  string             $tables  List of tables separated by comma.
	 * @param  bool               $write   Whether to restrict write operations until the lock is freed.
	 * @return PDOStatement|void
	 */
	public static function lockTables(string $tables, bool $write = false) // ok
	{
		/*
		 * There are 2 styles of 'LOCK TABLES' queries:
		 *
		 * 1. LOCK TABLES table1 WRITE, table2 WRITE;
		 *
		 *    This query locks 2 tables.
		 *
		 * 2. LOCK TABLES table1 WRITE;
		 *    LOCK TABLES table2 WRITE;
		 *
		 *    Only table2 is marked as locked.
		 *
		 * This is because the 'LOCK TABLES' command will first unlock all tables locked by the current session
		 * before performing the specified lock. Therefore, the call to lock table 2 results in unlocking table 1.
		 */

		$tables = static::_parseTable($tables);
		$sql = 'LOCK TABLES ';

		foreach ($tables as $table)
			$sql .= $table . ($write ? ' WRITE, ' : ' READ, ');

		$sql = rtrim($sql, ', ');

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
			return static::query($sql);
	}

	/**
	 * Releases any table locks held during the current transaction.
	 *
	 * This method allows you to release any table locks held during the current transaction.
	 *
	 * @return PDOStatement|void
	 */
	public static function unlockTables() // ok
	{
		$sql = 'UNLOCK TABLES';

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
			return static::query($sql);
	}

	/**
	 * Gets the SQL representation of the 'SELECT' query.
	 *
	 * An alias of method static::toSqlSelect().
	 *
	 * @return string  Returns the SQL query string.
	 */
	public static function toSql() : string // ok
	{
		return static::toSqlSelect();
	}

	/**
	 * Gets the SQL representation of the 'SELECT' query.
	 *
	 * @return string  Returns the SQL query string.
	 */
	public static function toSqlSelect() : string // ok
	{
		return static::_buildQuerySelect();
	}

	/**
	 * Gets the SQL representation of the 'INSERT' query.
	 *
	 * @param  array|object $data  The data to be inserted. If it's an array, it can be multidimensional array.
	 * @return string              Returns the SQL query string.
	 */
	public static function toSqlInsert($data) : string // ok
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::typeError(1, ['array', 'object'], $data);

		return static::_buildQueryInsert($data);
	}

	/**
	 * Gets the SQL representation of the 'UPDATE' query.
	 *
	 * @param  array|object $data  The data to be updated. If it's an array, it can only be a one-dimension array.
	 * @return string              Returns the SQL query string.
	 */
	public static function toSqlUpdate($data) : string // ok
	{
		if (!is_array($data) and !is_object($data))
			throw InvalidArgumentException::typeError(1, ['array', 'object'], $data);

		return static::_buildQueryUpdate($data);
	}

	/**
	 * Gets the SQL representation of the 'INSERT' or 'UPDATE' query based on the WHERE clause. If the WHERE
	 * clause is empty, it will return the SQL query string for the INSERT. Otherwise, it will return the
	 * SQL query string for the UPDATE.
	 *
	 * @param  array|object $data  The data to be inserted or updated.
	 *                             The data to be inserted can be multidimensional array.
	 *                             The data to be updated can only be a one-dimension array.
	 * @return string              Returns the SQL query string.
	 */
	public static function toSqlSave($data) : string // ok
	{
		return static::_buildQuerySave($data);
	}

	/**
	 * Gets the SQL representation of the 'DELETE' query.
	 *
	 * @return string  Returns the SQL query string.
	 */
	public static function toSqlDelete() : string
	{
		$where = static::_buildWhere();
		$sql = static::_buildQueryDelete($where);

		return $sql;
	}

	/**
	 * Parses the given columns to array.
	 *
	 * @param  string $columns  List of columns separated by comma.
	 * @return array            Return array of columns.
	 */
	protected static function _parseColumn(string $columns) : array // ok
	{
		$columns = explode(',', $columns);

		for ($i = 0, $n = count($columns); $i < $n; ++$i)
			$columns[$i] = static::wrapColumn($columns[$i]);

		return $columns;
	}

	/**
	 * Parses the given tables to array.
	 *
	 * @param  string $tables  List of tables separated by comma.
	 * @return array           Return array of columns.
	 */
	protected static function _parseTable(string $tables) : array // ok
	{
		$tables = explode(',', $tables);

		for ($i = 0, $n = count($tables); $i < $n; ++$i)
			$tables[$i] = static::wrapTable($tables[$i]);

		return $tables;
	}

	/**
	 * Gets the version of the database server.
	 *
	 * @return string  Returns the version of the database server.
	 */
	public static function version() : string // ok
	{
		static::_reset();

		return static::raw('SELECT VERSION()')->loadSingle();
	}

	private static function _displayError()
	{
		echo '<style>body { font-family: tahoma, arial; font-size: 12px; background: #222; color: #b5d5ff; }</style>';
		echo '<fieldset><legend>SQL Error</legend>' . static::$_connection->errorInfo()[2] . '</fieldset>';
		echo '<fieldset><legend>In Query</legend>' . static::getLastQuery() . '</fieldset>';

		if (Paginator::getSortCol())
		{
			$cookie = Uri::getContext() . 'sortcol';

			echo '<script type="text/javascript">
			function fixit()
			{
				document.cookie = \'' . $cookie . '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/\';
				location.reload();
			}
			</script>';

			echo '<p>Try to remove cookie to fix it or
				<span style="cursor:pointer; text-decoration:underline; font-weight:bold;"
					onclick="fixit();">
					click here.
				</span></p>';
		}

		exit;
	}

	/**
	 * Resets all static properties.
	 *
	 * @return void
	 */
	protected static function _reset() : void // ok
	{
		static::$_sqlRaw = null;
		static::$_sqlSelects = [];
		static::$_sqlTable = null;
		static::$_sqlJoins = [];
		static::$_sqlWheres = [];
		static::$_sqlGroups = [];
		static::$_sqlSorts = [];
		static::$_sqlTake = null;
		static::$_sqlSkip = null;
		static::$_transactionMode = false;
		static::$_transactionSqls = [];
	}
}
