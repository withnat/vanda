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
	protected static $_instance;
	protected static $_connection;
	protected static $_tables;
	protected static $_info;
	protected static $_sqlRaw;
	protected static $_sqlSelects;
	protected static $_sqlTable;
	protected static $_sqlJoins;
	protected static $_sqlWheres;
	protected static $_sqlGroups;
	protected static $_sqlSorts;
	protected static $_sqlTake;
	protected static $_sqlSkip;
	protected static $_autoSearchKeyword;
	protected static $_autoSearchColumns;
	protected static $_transactionMode;
	protected static $_transactionSqls;
	protected static $_queries;
	protected static $_identifierLeft = '`';
	protected static $_identifierRight = '`';
	protected static $_affectedRows;
	protected static $_dbCachePath = PATH_STORAGE . DS . 'cache' . DS . 'db' . DS;
	protected static $_queryCachePath = PATH_STORAGE . DS . 'cache' . DS . 'queries' . DS;

	/**
	 * AbstractPlatform constructor.
	 */
	public function __construct()
	{
		if (is_null(static::$_connection))
		{
			static::_connect();

			if (DEV_MODE)
			{
				Folder::delete(static::$_dbCachePath);
				Folder::delete(static::$_queryCachePath);
			}

			Folder::create(static::$_dbCachePath);
			Folder::create(static::$_queryCachePath);

			static::$_instance = $this;
		}
	}

	/**
	 * @return void
	 */
	abstract protected static function _connect() : void;

	// Select

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return AbstractPlatform
	 */
	public static function select(string $columns = '*') : AbstractPlatform
	{
		$columns = static::_parseColumn($columns);

		if (static::$_sqlSelects)
			static::$_sqlSelects = array_merge(static::$_sqlSelects, $columns);
		else
			static::$_sqlSelects = $columns;

		return static::$_instance;
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function avg(string $columns)
	{
		return static::_queryAggregate('AVG()', $columns);
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function count(string $columns = '*')
	{
		return static::_queryAggregate('COUNT()', $columns);
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function countDistinct(string $columns)
	{
		return static::_queryAggregate('COUNT(DISTINCT())', $columns);
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function min(string $columns)
	{
		return static::_queryAggregate('MIN()', $columns);
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function max(string $columns)
	{
		return static::_queryAggregate('MAX()', $columns);
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function std(string $columns)
	{
		return static::_queryAggregate('STD()', $columns);
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return mixed
	 */
	public static function sum(string $columns)
	{
		return static::_queryAggregate('SUM()', $columns);
	}

	/**
	 * @param  string $column
	 * @return mixed
	 */
	public static function distinct(string $column)
	{
		$column = static::wrapColumn($column);
		$alias = '';

		if (stripos($column, ' AS '))
		{
			$arr = explode(' AS ', $column);
			$column = $arr[0];
			$alias = ' AS ' . $arr[1];
		}

		static::$_sqlSelects[] = 'DISTINCT(' . $column . ')' . $alias;

		return static::loadAll();
	}

	/**
	 * @param  string $function
	 * @param  string $columns   List of columns separated by comma.
	 * @return mixed
	 */
	private static function _queryAggregate(string $function, string $columns)
	{
		$columns = static::_parseColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSelects[] = str_replace('()', '(' . $column . ')', $function);

		if (count($columns) > 1)
			return static::load();
		else
			return static::loadSingle();
	}

	// todo
	public static function autoSearchKeyword($keyword)
	{
		static::$_autoSearchKeyword = $keyword;
	}

	// todo
	public static function autoSearchColumn($columns = '*')
	{
		if (!is_array($columns))
			$columns = explode(',', $columns);

		foreach ($columns as $key => $value)
			$columns[$key] = static::wrapColumn($value);

		if (isset(static::$_autoSearchColumns))
			static::$_autoSearchColumns = array_merge(static::$_autoSearchColumns, $columns);
		else
			static::$_autoSearchColumns = $columns;
	}

	// From

	/**
	 * @param  string $table
	 * @return AbstractPlatform
	 */
	public static function table(string $table) : AbstractPlatform
	{
		static::from($table);

		return static::$_instance;
	}

	/**
	 * @param  string $table
	 * @return AbstractPlatform
	 */
	public static function from(string $table) : AbstractPlatform
	{
		static::$_sqlTable = static::wrapTable($table);

		return static::$_instance;
	}

	/**
	 * @param  string $type
	 * @param  string $table
	 * @param  string $condition
	 * @return void
	 */
	private static function _setJoin(string $type, string $table, string $condition) : void
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

	/**
	 * @param  string $table
	 * @param  string $condition
	 * @return AbstractPlatform
	 */
	public static function innerJoin(string $table, string $condition) : AbstractPlatform
	{
		static::_setJoin('INNER JOIN', $table, $condition);

		return static::$_instance;
	}

	/**
	 * @param  string $table
	 * @param  string $condition
	 * @return AbstractPlatform
	 */
	public static function leftJoin(string $table, string $condition) : AbstractPlatform
	{
		static::_setJoin('LEFT JOIN', $table, $condition);

		return static::$_instance;
	}

	/**
	 * @param  string $table
	 * @param  string $condition
	 * @return AbstractPlatform
	 */
	public static function rightJoin(string $table, string $condition) : AbstractPlatform
	{
		static::_setJoin('RIGHT JOIN', $table, $condition);

		return static::$_instance;
	}

	// Save

	public static function insert($data)
	{
		$sql = static::_buildQueryInsert($data);

		if (is_array($data) and count($data) > 1)
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
	 * @param  array    $data
	 * @return int|null
	 */
	public static function update(array $data) : ?int
	{
		$sql = static::_buildQueryUpdate($data);

		// If no data or all elements in data are removed
		// by static::_buildQueryUpdate, $sql will be empty.
		if ($sql)
		{
			static::raw($sql)->execute();

			return static::getAffectedRows();
		}
	}

	private static function _prepareDataBeforeSave($data)
	{
		$data = (array)$data;
		$datas = Arr::toMultidimensional($data);

		$n = count($datas);

		for ($i = 0; $i < $n; ++$i)
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

				// Data from submitted form always be string.
				elseif (is_string($value) and !mb_strlen($value)) // บรรทัดบนบอกว่า always be string แล้ววทำไมต้องใช้ is_string เช็คอีก?
				{
					$default = static::column($key)->default;

					//if (mb_strlen($default)) TODO ก่อนหน้านี้ใช้บรรทัดนี้ ตอนนี้ error กรณีบันทึก บทต. โดยไม่ระบุ maintenanceDate คือ mb_strlen() expects parameter 1 to be string, null given
					if (mb_strlen((string)$default))
					{
						if ($default === 'CURRENT_TIMESTAMP')
						{
							// Unset variable and database server
							// will set default value automatically.
							unset($datas[$i][$key]);
						}
						else
							$datas[$i][$key] = $default;
					}
					elseif (static::column($key)->nullable)
						$datas[$i][$key] = null;
				}

				// Unset variable and database server
				// will set default value automatically.
				elseif (is_null($value) and !static::column($key)->nullable)
					unset($datas[$i][$key]);
			}
		}

		return $datas;
	}

	public static function save($data)
	{
		$where = static::_buildWhere();
		$data = (array)$data;

		if ($where)
		{
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
				static::lockTable(@static::$_sqlTable);
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

	public static function increase($columns, $num = 1)
	{
		$columns = static::_parseColumn($columns);
		$columns = static::wrapColumn($columns);
		$where = static::_buildWhere();
		$num = (float)$num;

		$sql = 'UPDATE ' . @static::$_sqlTable . ' SET ';

		foreach ($columns as $column)
			$sql .= $column . ' = IFNULL(' . $column . ', 0) + ' . $num . ', ';

		$sql = substr($sql, 0, -2) . $where;
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

	public static function decrease($columns, $num = 1)
	{
		$columns = static::_parseColumn($columns);
		$columns = static::wrapColumn($columns);
		$where = static::_buildWhere();
		$num = (float)$num;

		$sql = 'UPDATE ' . static::$_sqlTable . ' SET ';

		foreach ($columns as $column)
			$sql .= $column . ' = IFNULL(' . $column . ', 0) - ' . $num . ', ';

		$sql = substr($sql, 0, -2) . $where;

		static::raw($sql)->execute();

		return static::getAffectedRows();
	}

	/**
	 * @return int  Affected rows.
	 */
	public static function publish() : int
	{
		return static::update(['status' => 2]);
	}

	/**
	 * @return int  Affected rows.
	 */
	public static function activate() : int
	{
		return static::update(['status' => 1]);
	}

	/**
	 * @return int  Affected rows.
	 */
	public static function deactivate() : int
	{
		return static::update(['status' => 0]);
	}

	/**
	 * @return int  Affected rows.
	 */
	public static function archive() : int
	{
		return static::update(['status' => -1]);
	}

	/**
	 * @return int  Affected rows.
	 */
	public static function trash() : int
	{
		return static::update(['status' => -2]);
	}

	/**
	 * @return int  Affected rows.
	 */
	public static function discontinue() : int
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

		if ($result and DEV_MODE)
			static::$_queries[] = $sql;
		else
			static::deleteAll();
	}

	// todo
	private static function _deleteUploadedFiles($where = null)
	{
		// Have to use raw query because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.

		$sql = 'SELECT * FROM ' . static::$_sqlTable . $where;
		$result = static::$_connection->query($sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

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

		// Have to use raw query because in case we call
		// method truncate() without DROP privilege. System will calls
		// method deleteAll() automatically. And static::$_sqlTable
		// will be removed by this method that called in method
		// truncate() already.

		$sql = 'SELECT * FROM ' . static::$_sqlTable . $where;
		$result = static::$_connection->query($sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

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
	 * @return AbstractPlatform
	 */
	public static function groupStart() : AbstractPlatform
	{
		static::$_sqlWheres[] = ['AND', '('];

		return static::$_instance;
	}

	/**
	 * @return AbstractPlatform
	 */
	public static function orGroupStart() : AbstractPlatform
	{
		static::$_sqlWheres[] = ['OR', '('];

		return static::$_instance;
	}

	/**
	 * @return AbstractPlatform
	 */
	public static function groupEnd() : AbstractPlatform
	{
		static::$_sqlWheres[] = ['', ')'];

		return static::$_instance;
	}

	public static function where($where) : AbstractPlatform
	{
		$args = func_get_args();

		if ($args[0] instanceof \Closure)
		{
			static::groupStart();
			$args[0]();
			static::groupEnd();
		}
		else
		{
			$where = static::_parseWhere($args);

			static::$_sqlWheres[] = ['AND', $where];
		}

		return static::$_instance;
	}

	public static function orWhere($where) : AbstractPlatform
	{
		$args = func_get_args();

		if (is_callable($args[0]))
		{
			static::orGroupStart();
			$args[0]();
			static::groupEnd();
		}
		else
		{
			$where = static::_parseWhere($args);

			if ($where)
				static::$_sqlWheres[] = ['OR', $where];
		}

		return static::$_instance;
	}

	private static function _parseWhere(array $args) : string
	{
		$countArgs = count($args);

		if ($countArgs === 1)
		{
			if (is_int($args[0]))
				$where = static::wrapColumn('id') . ' = ' . $args[0];
			else
				$where = $args[0];
		}
		else
		{
			$where = $args[0];
			$where = str_replace('{', static::$_identifierLeft, $where); // todo เอาไว้ทำอะไร
			$where = str_replace('}', static::$_identifierRight, $where);

			if (strpos($args[0], '?'))
			{
				$args = Arr::flatten($args);

				for ($i = 1, $n = count($args); $i < $n; ++$i)
					$where = Str::replace($where, '?', static::escape($args[$i]), 1);
			}
			elseif (strpos($args[0], ':'))
			{
				// We need associative array so don't need to flatten it.
				foreach ($args[1] as $key => $value)
					$where = str_replace($key, static::escape($value), $where);
			}
			else
			{
				$args = Arr::flatten($args);
				$column = static::wrapColumn($args[0]);
				$operator = '';
				$value = '';

				if ($countArgs === 2)
				{
					$operator = '=';
					$value = $args[1];
				}
				elseif ($countArgs > 2)
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
	 * @param  string           $column
	 * @param  string|int|float $start
	 * @param  string|int|float $end
	 * @return AbstractPlatform
	 */
	public static function whereBetween(string $column, $start, $end) : AbstractPlatform
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::create(2, ['string','int','float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::create(3, ['string','int','float'], $end);

		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[] = ['AND', $column . ' BETWEEN ' . $start . ' AND ' . $end];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $start
	 * @param  string|int|float $end
	 * @return AbstractPlatform
	 */
	public static function orWhereBetween(string $column, $start, $end) : AbstractPlatform
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::create(2, ['string','int','float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::create(3, ['string','int','float'], $end);

		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[] = ['OR', $column . ' BETWEEN ' . $start . ' AND ' . $end];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $start
	 * @param  string|int|float $end
	 * @return AbstractPlatform
	 */
	public static function whereNotBetween(string $column, $start, $end) : AbstractPlatform
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::create(2, ['string','int','float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::create(3, ['string','int','float'], $end);

		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[] = ['AND', $column . ' NOT BETWEEN ' . $start . ' AND ' . $end];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $start
	 * @param  string|int|float $end
	 * @return AbstractPlatform
	 */
	public static function orWhereNotBetween(string $column, $start, $end) : AbstractPlatform
	{
		if (!is_string($start) and !is_int($start) and !is_float($start))
			throw InvalidArgumentException::create(2, ['string','int','float'], $start);

		if (!is_string($end) and !is_int($end) and !is_float($end))
			throw InvalidArgumentException::create(3, ['string','int','float'], $end);

		$column = static::wrapColumn($column);
		$start = static::escape($start);
		$end = static::escape($end);

		static::$_sqlWheres[] = ['OR', $column . ' NOT BETWEEN ' . $start . ' AND ' . $end];

		return static::$_instance;
	}

	// Where like

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function whereContain(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' LIKE \'%' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function orWhereContain(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' LIKE \'%' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function whereStartWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' LIKE \'' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function orWhereStartWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' LIKE \'' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function whereEndWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' LIKE \'%' . $value . '\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function orWhereEndWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' LIKE \'%' . $value . '\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function whereNotContain(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' NOT LIKE \'%' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function orWhereNotContain(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' NOT LIKE \'%' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function whereNotStartWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' NOT LIKE \'' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function orWhereNotStartWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' NOT LIKE \'' . $value . '%\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function whereNotEndWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['AND', $column . ' NOT LIKE \'%' . $value . '\''];

		return static::$_instance;
	}

	/**
	 * @param  string           $column
	 * @param  string|int|float $value
	 * @return AbstractPlatform
	 */
	public static function orWhereNotEndWith(string $column, $value) : AbstractPlatform
	{
		if (!is_string($value) and !is_int($value) and !is_float($value))
			throw InvalidArgumentException::create(2, ['string','int','float'], $value);

		$column = static::wrapColumn($column);
		$value = static::escapeLike($value);

		static::$_sqlWheres[] = ['OR', $column . ' NOT LIKE \'%' . $value . '\''];

		return static::$_instance;
	}

	// Where in

	public static function whereIn($column, $values)
	{
		static::_setWhereIn('IN', $column, $values, 'AND');

		return static::$_instance;
	}

	public static function orWhereIn($column, $values)
	{
		static::_setWhereIn('IN', $column, $values, 'OR');

		return static::$_instance;
	}

	public static function whereNotIn($column, $values)
	{
		static::_setWhereIn('NOT IN', $column, $values, 'AND');

		return static::$_instance;
	}

	public static function orWhereNotIn($column, $values)
	{
		static::_setWhereIn('NOT IN', $column, $values, 'OR');

		return static::$_instance;
	}

	private static function _setWhereIn($operator, $column, $values, $condition)
	{
		$column = static::wrapColumn($column);
		$values = (array)$values;
		$values = Arr::flatten($values); // $args parameter sent from model class as multi-dimension array

		$values = array_map([static::_getInstance(), 'escape'], $values);
		$where = $column . ' ' . $operator . ' (' . implode(', ', $values) . ')';

		static::$_sqlWheres[] = [$condition, $where];

	}

	// Where null

	/**
	 * @param  string $column
	 * @return AbstractPlatform
	 */
	public static function whereNull(string $column) : AbstractPlatform
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['AND', $column . ' IS NULL'];

		return static::$_instance;
	}

	/**
	 * @param  string $column
	 * @return AbstractPlatform
	 */
	public static function orWhereNull($column) : AbstractPlatform
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['OR', $column . ' IS NULL'];

		return static::$_instance;
	}

	/**
	 * @param  string $column
	 * @return AbstractPlatform
	 */
	public static function whereNotNull($column) : AbstractPlatform
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['AND', $column . ' IS NOT NULL'];

		return static::$_instance;
	}

	/**
	 * @param  string $column
	 * @return AbstractPlatform
	 */
	public static function orWhereNotNull($column) : AbstractPlatform
	{
		$column = static::wrapColumn($column);

		static::$_sqlWheres[] = ['OR', $column . ' IS NOT NULL'];

		return static::$_instance;
	}

	// Group

	public static function group($columns)
	{
		static::$_sqlGroups[] = static::wrapColumn($columns);

		return static::$_instance;
	}

	// Order by

	/**
	 * @param  string $columns    List of columns separated by comma.
	 * @param  string $direction
	 * @return AbstractPlatform
	 */
	public static function sort(string $columns, string $direction = 'ASC') : AbstractPlatform
	{
		$columns = static::_parseColumn($columns);

		foreach ($columns as $column)
			static::$_sqlSorts[] = $column . ' ' . strtoupper($direction);

		return static::$_instance;
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return AbstractPlatform
	 */
	public static function sortAsc(string $columns) : AbstractPlatform
	{
		static::sort($columns, 'ASC');

		return static::$_instance;
	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return AbstractPlatform
	 */
	public static function sortDesc(string $columns) : AbstractPlatform
	{
		static::sort($columns, 'DESC');

		return static::$_instance;
	}

	// Limit

	/**
	 * @param  int  $num
	 * @return AbstractPlatform
	 */
	public static function take(int $num) : AbstractPlatform
	{
		static::$_sqlTake = $num;

		return static::$_instance;
	}

	/**
	 * @param  int  $num
	 * @return AbstractPlatform
	 */
	public static function skip(int $num) : AbstractPlatform
	{
		static::$_sqlSkip = $num;

		return static::$_instance;
	}

	// Query

	/**
	 * @param  string $sql
	 * @return AbstractPlatform
	 */
	public static function raw(string $sql) : AbstractPlatform
	{
		static::$_sqlRaw = $sql;

		return static::$_instance;
	}

	/**
	 * @return int
	 */
	public static function execute() : int
	{
		$sql = static::_buildQuerySelect();

		static::_query($sql);

		return static::getAffectedRows();
	}

	private static function _query($sql)
	{
		$pos = mb_stripos($sql, ' WHERE ');

		if (!$pos)
			$pos = mb_stripos($sql, ' VALUES ');

		if (!$pos)
			$pos = mb_stripos($sql, ' SET ');

		// Prevent to replace #_ in where clause
		if ($pos)
		{
			$block1 = mb_substr($sql, 0, $pos);
			$block2 = mb_substr($sql, $pos);

			$block1 = str_replace('#_', Config::db('prefix'), $block1);

			$sql = $block1 . $block2;
		}
		else
			$sql = str_replace('#_', Config::db('prefix'), $sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			$result = static::$_connection->query($sql);

			if (is_object($result))
				static::$_affectedRows = $result->rowCount();
			elseif (static::$_connection->errorInfo()[2] and DEV_MODE)
				static::_displayError();

			static::reset();
			return $result;
		}
	}

	// Load data

	public static function loadSingle()
	{
		$sql = static::_buildQuerySelect();
		$data = static::load($sql);
		$data = (array)$data;
		$data = current($data);

		return $data;
	}

	public static function load()
	{
		$args = func_get_args();

		if (isset($args[0]))
			$sql = $args[0];
		else
			$sql = static::_buildQuerySelect();

		$sql = trim($sql);

		if (strtoupper(substr($sql, 0, 6)) != 'SELECT')
			return false;

		if (!strripos($sql, ' LIMIT '))
			$sql .= ' LIMIT 1 ';

		$result = static::_query($sql);

		if ($result)
		{
			if ($result->rowCount())
				return $result->fetch();
			else
			{
				$sql = trim($sql);

				if (strtoupper(substr($sql, 0, 6)) != 'SELECT') // todo โค้ดซ้ำกับข้างบนทำไม
					return false;

				$select = substr($sql, 7, stripos($sql, ' FROM ') - 7);

				if (trim($select) === '*')
				{
					$pattern = '/' . Config::db('prefix') . '(.*)\s+/U';
					preg_match_all($pattern, $sql, $tables);

					$columns = [];

					foreach ($tables[0] as $table)
					{
						$table = trim($table);
						$table = ltrim($table, static::$_identifierLeft);
						$table = rtrim($table, static::$_identifierRight);

						$columns = array_merge($columns, array_values(static::getColumnListing($table)));
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

						$column = ltrim($column, static::$_identifierLeft);
						$column = rtrim($column, static::$_identifierLeft);
						$column = trim($column);

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
		{
			// todo
		}
	}

	public static function loadAll()
	{
		$args = func_get_args();
		$paginate = false;
		$no = 0;

		if (isset($args[0]) and is_object($args[0]))
		{
			$paginate = true;
			$no = $args[0]->pagenumstart;
		}

		$sql = static::_buildQuerySelect();
		$result = static::_query($sql);

		if ($result)
		{
			$rows = $result->fetchAll();

			foreach ($rows as $row)
			{
				if ($paginate)
					$row->{':no'} = $no++;
			}

			return $rows;
		}
		else
			static::_displayError($result); // todo
	}

	// todo
	public static function paginate()
	{
		$sql = static::_buildQuerySelect();

		$sqlCount = 'SELECT COUNT(*) ' . substr($sql, stripos($sql, ' FROM '));
		$totalrecord = static::raw($sqlCount)->loadSingle();

		Paginator::init($totalrecord);

		$limitPos = strripos($sql, ' LIMIT ');
		$sortcol = Paginator::getSortCol();

		if ($sortcol and strripos($sql, ' ORDER BY ') === false)
		{
			$sortdir = Paginator::getSortDir();

			// Clean up values from cookie.
			// (Don't remove dot! In case order by alias table nam ie t.name)
			$sortcol = preg_replace('/[^.a-z0-9]+/i', '', $sortcol);

			if (!in_array(strtolower($sortdir), ['asc', 'desc']))
				$sortdir = 'ASC';

			$sortcol = static::wrapColumn($sortcol);
			$orderBy = ' ORDER BY ' . $sortcol . ' ' . $sortdir;

			if ($limitPos)
			{
				$limit = substr($sql, $limitPos);
				$sql = substr($sql, 0, $limitPos);

				$sql .= $orderBy . $limit;
			}
			else
				$sql .= $orderBy;
		}

		if (!$limitPos)
		{
			// Ensure integer
			$page = (int)Paginator::getPage();
			$pagesize = (int)Paginator::getPageSize();

			// In case of $totalrecord is 0, the ceil() function below will set $page to 0.
			// And if $page is 0, the below $offset will be negative that will make sql error.
			if ($totalrecord)
			{
				// กรณีปรับลด pagesize ให้น้อยลง เช่น มีข้อมูลทั้งหมดจำนวน 9 รายการ แล้วปรับ pagesize
				// จาก 20 เหลือ 5 รายการต่อหน้า แล้วเลื่อนหน้าแสดงผลไปที่หน้าสุดท้าย (หน้า 2) แล้วเลือก
				// pagesize กลับไป 20 รายการต่อหน้า ระบบจะไม่แสดงข้อมูล เพราะระบบไปอยู่ หน้า 2 ซึ่ง
				// มากกว่าที่คำนวณได้จริง (ข้อมูล 9 รายการ แสดง 20 รายการต่อหน้า จะมี max page = 1)
				if ($totalrecord / $pagesize < $page)
					$page = (int)ceil($totalrecord / $pagesize);
			}
			else
				$page = 1;

			Paginator::setPage($page);
			$offset = $pagesize * ($page - 1);

			$sql .= ' LIMIT ' . $pagesize . ' OFFSET ' . $offset;
		}

		$result = static::_query($sql);

		if ($result)
		{
			$rows = $result->fetchAll();
			$no = 0;

			foreach ($rows as $row)
				$row->{':no'} = $no++;

			return $rows;
		}
		else
			static::_displayError($result); // todo
	}

	public static function toJSON()
	{
		$data = static::loadAll();
		$json = json_encode($data);

		// Prevent json_encode from converting empty value to array.
		$json = str_replace('{}', '""', $json);

		return $json;
	}

	/**
	 * @param  bool   $header
	 * @return string
	 */
	public static function toCSV(bool $header = false) : string
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
	 * @return string
	 */
	public static function toXML() : string
	{
		$data = static::loadAll();
		$xml = XML::fromRecordset($data);

		return $xml;
	}

	public static function getCreatorUpdaterInfo($data)
	{
		if (empty($data))
			return $data; // Ensure to return the same input data type.

		if (is_array($data) and isset($data[0]))
			$row = $data[0];
		else
			$row = $data;

		if (!is_object($row))
			return $data;

		if (!array_key_exists('creator', $row) and
			!array_key_exists('updater', $row))
			return $data;

		$userIds = [];

		if (is_array($data))
		{
			foreach ($data as $row)
				$userIds = static::_addUserIdToArray($row, $userIds);
		}
		else
			$userIds = static::_addUserIdToArray($data, $userIds);

		if (empty($userIds))
			return $data;

		$sql = 'SELECT ' . static::wrapColumn('id, name, username, email') . ' '
			. 'FROM ' . static::wrapTable('User') . ' '
			. 'WHERE ' . static::wrapColumn('id') . ' '
						. 'IN (' . implode(', ', $userIds) . ') ';
		$result = static::$_connection->query($sql);

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$userInfo = [];

		while ($row = $result->fetch_assoc())
			$userInfo[$row['id']] = $row;

		if (is_array($data))
		{
			$returnData = [];

			foreach ($data as $row)
				$returnData[] = static::_getCreatorUpdaterInfoEachRow($row, $userInfo);
		}
		else
			$returnData = static::_getCreatorUpdaterInfoEachRow($data, $userInfo);

		return $returnData;
	}

	private static function _addUserIdToArray($row, $userIds)
	{
		foreach ($row as $column => $value)
		{
			if ($column === 'creator' or $column === 'updater')
			{
				if ($value and !Arr::has($userIds, $value))
					$userIds[] = $value;
			}
		}

		return $userIds;
	}

	private static function _getCreatorUpdaterInfoEachRow($row, $userInfo)
	{
		$returnRow = new stdClass();

		foreach ($row as $column => $value)
		{
			$returnRow->{$column} = $value;

			if ($column === 'creator' or $column === 'updater')
			{
				$data = new stdClass();

				if ($value and isset($userInfo[$value]))
				{
					$data->name = $userInfo[$value]['name'];
					$data->username = $userInfo[$value]['username'];
					$data->email = $userInfo[$value]['email'];
				}
				else
				{
					$data->name = '';
					$data->username = '';
					$data->email = '';
				}

				$returnRow->{':'.$column} = $data;
			}
		}

		return $returnRow;
	}

	// Transaction

	public static function transaction($callback, $testMode = false)
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

	public static function beginTransaction()
	{
		// Ensure the connection is established in case we
		// start a transaction manually before set a query.
		//static::_getInstance();
		// Call to undefined method System\DB\Platforms\Mysql::_getInstance()
		// เลยคอมเมนต์บรรทัดบนไปก่อน

		static::$_connection->beginTransaction();
		static::$_transactionMode = true;
	}

	public static function commit()
	{
		static::_queryTransaction();

		static::$_connection->commit();
		static::$_transactionMode = false;
		static::$_transactionSqls = null;
	}

	public static function rollback()
	{
		static::$_connection->rollback();
		static::$_transactionMode = false;
		static::$_transactionSqls = null;
	}

	public static function transactionSuccess()
	{
		return static::_queryTransaction();
	}

	private static function _queryTransaction()
	{
		$sqls = static::$_transactionSqls;

		static::$_transactionMode = false;
		static::$_transactionSqls = null;

		if (is_array($sqls))
		{
			try
			{
				foreach ($sqls as $sql)
				{
					if (DEV_MODE)
						static::$_queries[] = $sql;

					static::$_connection->query($sql);
				}

				return true;
			}
			catch (\PDOException $e)
			{
				return false;
			}
		}
		else
			return false;
	}

	// Build

	private static function _buildQuerySelect()
	{
		if (static::$_sqlRaw)
			return static::$_sqlRaw;

		$sql = 'SELECT ';

		if (static::$_sqlSelects)
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

	private static function _buildQueryInsert($data)
	{
		$data = (array)$data;
		$data = Arr::removeKey($data, 'id');
		$datas = static::_prepareDataBeforeSave($data);
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

	private static function _buildQueryUpdate($data)
	{
		$datas = static::_prepareDataBeforeSave($data);
		$data = $datas[0];

		// Data maybe empty after calling static::_prepareDataBeforeSave()
		if (empty($data))
			return '';

		$columns = array_keys($data);
		$values = array_values($data);

		$sql = 'UPDATE ' . static::$_sqlTable . ' SET ';
		$n = count($columns);

		for ($i = 0; $i < $n; ++$i)
			$sql .= static::wrapColumn($columns[$i]) . ' = ' . static::escape($values[$i]) . ', ';

		$sql = substr($sql, 0, -2);

		$sql .= static::_buildWhere();
		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	private static function _buildQuerySave($data)
	{
		$where = static::_buildWhere();
		$userId = (int)@Auth::identity()->id;

		if ($where)
		{
			if (Arr::isMultidimensional($data))
				$data = $data[0];

			if (static::columnExists('updated') and !array_key_exists('updated', $data))
				$data = Data::set($data, 'updated', date('Y-m-d H:i:s'));

			if (static::columnExists('updater') and !array_key_exists('updater', $data))
				$data = Data::set($data, 'updater', $userId);

			$sql = static::_buildQueryUpdate($data);
		}
		else
		{
			$datas = Arr::toMultidimensional($data);
			$autoOrdering = false;
			$ordering = 0;

			if (static::columnExists('ordering') and !array_key_exists('ordering', $datas[0]))
			{
				$autoOrdering = true;
				$ordering = static::getNewOrdering();
			}

			$n = count($datas);

			for ($i = 0; $i < $n; ++$i)
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

			$sql = static::_buildQueryInsert($datas);
		}

		return $sql;
	}

	private static function _buildQueryDelete($where = null)
	{
		$table = static::$_sqlTable;

		$sql = 'DELETE FROM ' . $table . $where;

		$sql .= static::_buildSort();
		$sql .= static::_buildLimit();

		return $sql;
	}

	private static function _buildFrom()
	{
		$table = static::$_sqlTable;

		$sql = ' FROM ' . $table;

		if (static::$_sqlJoins)
			$sql .= ' ' . implode(' ', static::$_sqlJoins);

		return $sql;
	}

	private static function _buildWhere()
	{
		if (static::$_autoSearchKeyword)
		{
			if (static::$_autoSearchColumns)
				$searchColumns = static::$_autoSearchColumns;
			elseif (static::$_sqlSelects)
				$searchColumns = static::$_sqlSelects;
			else
				$searchColumns = ['*'];

			if (Arr::has($searchColumns, '*') and static::$_sqlTable)
				$searchColumns = static::getColumnListing();

			foreach ($searchColumns as $searchColumn)
				static::orWhereContain($searchColumn, static::$_autoSearchKeyword);
		}

		$where = '';

		if (static::$_sqlWheres)
		{
			$where = ' WHERE ';
			$_sqlWheres = static::$_sqlWheres; // todo improve code
			$countWhere = count($_sqlWheres);

			for ($i=0; $i < $countWhere; ++$i)
			{
				if ($i > 0 and $_sqlWheres[$i][1] != ')' and $_sqlWheres[$i - 1][1] != '(')
					$where .= ' ' . $_sqlWheres[$i][0].' ';

				$where .= $_sqlWheres[$i][1];

				//$where .= $where .= $_sqlWheres[$i][0]. ' ' . $_sqlWheres[$i][1];
			}
		}

		return $where;
	}

	private static function _buildGroup()
	{
		if (static::$_sqlGroups)
			return ' GROUP BY ' . implode(', ', static::$_sqlGroups);
	}

	private static function _buildSort()
	{
		if (static::$_sqlSorts)
			return ' ORDER BY ' . implode(', ', static::$_sqlSorts);
	}

	private static function _buildLimit()
	{
		$sql = '';

		if (static::$_sqlSkip and !static::$_sqlTake)
			static::$_sqlTake = '18446744073709551615'; // Set to string. If number, PHP will convert to 1.844674407371E+19

		if (static::$_sqlTake)
			$sql = ' LIMIT ' . static::$_sqlTake;

		if (static::$_sqlSkip)
			$sql .= ' OFFSET ' . static::$_sqlSkip;

		return $sql;
	}

	// Other

	/**
	 * @param  string $table
	 * @return string
	 */
	public static function formatTableName(string $table) : string
	{
		if (substr($table, 0, 1) != static::$_identifierLeft and substr($table, 0, strlen(Config::db('prefix'))) != Config::db('prefix'))
			$table = Config::db('prefix') . ucfirst($table);

		return $table;
	}

	/**
	 * @param  string $table
	 * @return string
	 */
	public static function wrapTable(string $table) : string
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

		$table = static::formatTableName($table);
		$table = static::$_identifierLeft . $table . static::$_identifierRight;

		if ($alias)
		{
			$alias = static::$_identifierLeft . $alias . static::$_identifierRight;
			$table .= ' AS ' . $alias;
		}

		return $table;
	}

	/**
	 * @param  string $column
	 * @return string
	 */
	public static function wrapColumn(string $column) : string
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

				$column = trim($column);
				$alias = trim($alias);
			}
			else
			{
				$column = trim($column);
				$alias = '';
			}

			$arr = explode('.', $column);
			$column = static::$_identifierLeft . $arr[0] . static::$_identifierRight;

			if (isset($arr[1]))
				$column .= '.' . static::$_identifierLeft . $arr[1] . static::$_identifierRight;

			if ($alias)
				$column .= ' AS ' . static::$_identifierLeft . $alias . static::$_identifierRight;
		}

		return $column;
	}

//	public static function wrapColumnWhere($column)
//	{
//		$arr = explode('.', $column);
//		$column = static::$_identifierLeft . $arr[0] . static::$_identifierRight;
//
//		if (isset($arr[1]))
//			$column .= '.' . static::$_identifierLeft . $arr[1] . static::$_identifierRight;
//
//		return $column;
//	}

	/**
	 * @param  string $column
	 * @return string
	 */
	public static function wrap(string $column) : string
	{
		return static::wrapColumn($column);
	}

	/**
	 * @param  mixed $value
	 * @return mixed
	 */
	public static function escape($value)
	{
		if (is_object($value) or is_resource($value))
			throw InvalidArgumentException::create(1, ['string,int,float,bool,null'], $value);

		// Have to use both of is_string() and is_numeric()
		// function to check data type because number sent
		// via url ie delete?id[]=1&id[]=2 will be string.
		// Update : use is_int() and is_float() is faster
		// than is_numeric().
		if (is_string($value) and (!is_int($value) and !is_float($value)))
			$value = static::$_connection->quote($value);
		elseif (is_bool($value))
			$value = (($value === false) ? 0 : 1);
		elseif (is_null($value))
			$value = 'NULL';

		return $value;
	}

	public static function escapeLike($value)
	{
		$trimQuote = false;

		if (is_string($value) and (!is_int($value) and !is_float($value))) // todo
			$trimQuote = true;

		$value = static::escape($value);
		$value = addcslashes($value, '%_');

		if ($trimQuote)
			$value = substr($value, 1, -1);

		return $value;
	}

	public static function getNewOrdering()
	{
		$table = static::$_sqlTable;
		$ordering = static::wrapColumn('ordering');

		// TODO do i need to lock table first?

		$sql = 'SELECT IFNULL(MAX(' . $ordering . '), 0) + 1 '
			. 'AS ' . $ordering . ' '
			. 'FROM ' . $table;

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$result = static::$_connection->query($sql);
		$row = $result->fetch();

		return $row->ordering;
	}

	/**
	 * @return string
	 */
	public static function getLastQuery() : string
	{
		return end(static::$_queries);
	}

	/**
	 * @return array
	 */
	public static function getAllQueries() : array
	{
		return static::$_queries;
	}

	/**
	 * @return int
	 */
	public static function getLastInsertId() : int
	{
		return static::$_connection->lastInsertId();
	}

	/**
	 * @return int
	 */
	public static function getAffectedRows() : int
	{
		return (int)static::$_affectedRows;
	}

	public static function exists($data = null)
	{
		$data = (array)$data;

		if (is_int(@$data[0]))
		{
			$data['id'] = $data[0];
			unset($data[0]);
		}

		$columns = array_keys($data);
		$values = array_values($data);
		$where = [];

		$n = count($columns);

		for ($i = 0; $i < $n; ++$i)
		{
			if ($columns[$i] === 'id')
				continue;

			$where[] = static::wrapColumn($columns[$i]) . ' = ' . static::escape($values[$i]);
		}

		$where = implode(' AND ', $where);

		$idcol = static::wrapColumn('id');

		if (@$data['id'])
		{
			$value = static::escape($data['id']);

			if ($where)
				$where .= ' AND ' . $idcol . ' != ' . $value;
			else
				$where = $idcol . ' = ' . $value;
		}

		$args = func_get_args();

		if (isset($args[1])) // Called from Model::exists()
		{
			if (static::columnExists('status') and !array_key_exists('status', $data))
			{
				if ($where)
					$where .= ' AND ';

				$where .= static::wrapColumn('status') . ' > -1';
			}
		}

		$sql = 'SELECT ' . $idcol . ' FROM ' . static::$_sqlTable;

		if ($where)
			$sql .= ' WHERE ' . $where;

		$sql .= ' LIMIT 1 ';

		if (DEV_MODE)
			static::$_queries[] = $sql;

		$result = static::$_connection->query($sql);
		$row = $result->fetch();

		if (empty($row->id))
			return false;
		else
			return true;
	}

	public static function getTables()
	{
		if (!is_array(static::$_tables))
		{
			$filename = '_tables_.php';

			if (is_file(static::$_dbCachePath . $filename))
			{
				$content = file_get_contents(static::$_dbCachePath . $filename);
				$content = substr($content, 8);

				static::$_tables = @unserialize($content);
			}

			if (empty(static::$_tables))
			{
				$sql = 'SHOW TABLES';
				$result = static::$_connection->query($sql);

				if (DEV_MODE)
					static::$_queries[] = $sql;

				while ($table = $result->fetch()) // todo
				{
					// Get first element of object or array
					$table = current($table);
					static::$_tables[] = $table;
				}

				$fp = fopen(static::$_dbCachePath . $filename, 'w');
				fwrite($fp, '<?php //'.serialize(static::$_tables));
				fclose($fp);
			}
		}

		return static::$_tables;
	}

	private static function _getColumnInfo($table = null)
	{
		if ($table)
			$table = static::formatTableName($table);
		else
		{
			$table = static::$_sqlTable;
		}

		if (!isset(static::$_info[$table]))
		{
			$filename = $table;
			$filename = ltrim($filename, static::$_identifierLeft);
			$filename = rtrim($filename, static::$_identifierRight);
			$file = static::$_dbCachePath . $filename . '.php';

			if (is_file($file))
			{
				$content = file_get_contents($file);
				$content = substr($content, 8);

				static::$_info[$table] = unserialize($content);
			}
			else
			{
				$sql = 'DESCRIBE ' . $table;
				$result = static::$_connection->query($sql);

				if (DEV_MODE)
					static::$_queries[] = $sql;

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
						$length = Str::trimRight($arr[1]);

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

				static::$_info[$table] = $tableInfo;

				$fp = fopen($file, 'w');
				fwrite($fp, '<?php //'.serialize($tableInfo));
				fclose($fp);
			}
		}

		return static::$_info[$table];
	}

	public static function getColumnListing($table = null)
	{
		$info = static::_getColumnInfo($table);
		$columns = array_keys($info);

		return $columns;
	}

	public static function column($column)
	{
		$info = static::_getColumnInfo();
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
	 * @param  string $table
	 * @return bool
	 */
	public static function tableExists(string $table) : bool
	{
		$table = static::formatTableName($table);
		$tables = static::getTables();

		return Arr::has($tables, $table);
	}

	/**
	 * @param  string $column
	 * @return bool
	 */
	public static function columnExists(string $column) : bool
	{
		$columns = static::getColumnListing();

		return Arr::has($columns, $column);
	}

	public static function lockTable($table)
	{
		// TODO why don't wrap $table name ???!!!
		$sql = 'LOCK TABLES ' . $table . ' WRITE, ' . static::wrapTable('Session') . ' WRITE';

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			static::$_connection->query($sql);

			if (DEV_MODE)
				static::$_queries[] = $sql;
		}
	}

	public static function unlockTables()
	{
		$sql = 'UNLOCK TABLES';

		if (static::$_transactionMode)
			static::$_transactionSqls[] = $sql;
		else
		{
			static::$_connection->query($sql);

			if (DEV_MODE)
				static::$_queries[] = $sql;
		}
	}

	public static function getPreparedSelect()
	{
		$sql = static::_buildQuerySelect();

		return $sql;
	}

	public static function getPreparedInsert($data)
	{
		$sql = static::_buildQueryInsert($data);

		return $sql;
	}

	public static function getPreparedUpdate($data)
	{
		$sql = static::_buildQueryUpdate($data);

		return $sql;
	}

	public static function getPreparedSave($data)
	{
		$sql = static::_buildQuerySave($data);

		return $sql;
	}

	public static function getPreparedDelete()
	{
		$where = static::_buildWhere();
		$sql = static::_buildQueryDelete($where);

		return $sql;
	}

//	private static function _columnsToString($columns) // ok
//	{
//		if (is_array($columns))
//			$columns = implode(', ', $columns);
//
//		return $columns;
//	}

	/**
	 * @param  string $columns  List of columns separated by comma.
	 * @return array
	 */
	private static function _parseColumn(string $columns) : array
	{
		$columns = explode(',', $columns);
		$n = count($columns);

		for ($i = 0; $i < $n; ++$i)
			$columns[$i] = static::wrapColumn($columns[$i]);

		return $columns;
	}

	public static function version()
	{
		static::reset();

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

	public static function reset()
	{
		static::$_sqlRaw = null;
		static::$_sqlSelects = null;
		static::$_sqlTable = null;
		static::$_sqlJoins = null;
		static::$_sqlWheres = null;
		static::$_sqlGroups = null;
		static::$_sqlSorts = null;
		static::$_sqlTake = null;
		static::$_sqlSkip = null;
		static::$_autoSearchKeyword = null;
		static::$_autoSearchColumns = null;
		static::$_transactionMode = null;
		static::$_transactionSqls = null;
	}
}
