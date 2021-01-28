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

namespace System\Mvc;

use System\Arr;
use System\DB;
use System\File;
use System\Auth;

/**
 * TASK is [load|loadAll|activate|deactivate|archive|trash|discontinue|delete|deleteAll]
 *
 * User::[TASK]ById(1);
 * User::[TASK]ByName[And|Or]Email('Nat Withe', 'nat@withnat.com');
 * User::[TASK]ById[Not]GreaterThan(1);
 * User::[TASK]ById[Not]LessThan(1);
 * User::[TASK]ByName[Not]Contain('Wit');
 * User::[TASK]ByName[Not]StartWith('Nat');
 * User::[TASK]ByName[Not]EndWith('Withe');
 * User::[TASK]ById[Not]Between([1,100]);
 * User::[TASK]ById[Not]In([1,100]);
 * User::[TASK]ByNameIs[Not]Null();
 *
 * User::[TASK]Where('name=?', 'Nat Withe');
 * User::[TASK]Where('name=:name', [':name' => 'Nat Withe']);
 *
 * For count|sum|min|max|avg|std|increase|decrease
 *      Product::countByCategoryId(1);                          // count (*)
 *      Product::countIdCategoryId(1);                          // count specific column name
 *      Product::minPriceByCategoryId(1);                       // must specify column name
 *      Product::increaseOrderingByCategoryId($id, $num = 1);   // must specify column name
 *      Product::decreaseOrderingByCategoryId($id, $num = 1);   // must specify column name
 *
 * $user = User::loadById(1);
 * $user->delete();
 *
 * ----------------------------------------
 *
 * -Empty & Truncate table
 *
 * User::deleteAll();   // no where
 * User::truncate();
 *
 * ----------------------------------------
 *
 * -Inserting new record
 *
 * $user = new User();
 * $user->name = 'Nat Withe';
 * $user->[save|bind]();
 *
 * User::[save|bind]($data);
 *
 * ----------------------------------------
 *
 * -Updating data
 *
 * User::where($where)->[save|bind]($data);
 *
 * $user = User::load($id);
 * $user->name = 'Nat Withe';
 * $user->[save|bind]();
 */
class Model
{
	protected static $_instance = null;
	protected static $_table;
	private static $_modelLocations = [];

	protected static $_tasks = ['load', 'increase', 'decrease', 'activate',
							'deactivate', 'archive', 'trash', 'discontinue',
							'delete', 'truncate', 'count', 'sum', 'min', 'max', 'avg', 'std'];
	protected static $_scopes = ['All', ''];
	//protected static $_bys = ['By', ''];
	protected static $_bys = [''];

	protected static $error;

	public function __construct()
	{
		Model::_setTable();

		$table = Model::_getTable();
		$columns = DB::getColumnListing($table);

		Model::$_instance[$table] = $this;

		foreach ($columns as $column)
			Model::$_instance[$table]->$column = null;

		return Model::$_instance[$table];
	}

	private static function _getInstance()
	{
		$table = Model::_getTable();

		if (!isset(Model::$_instance[$table]))
			new static();

		return Model::$_instance[$table];
	}

	public static function table($table)
	{
		Model::$_table = $table;

		return Model::_getInstance();
	}

	private static function _getTable()
	{
		if (empty(Model::$table))
			Model::_setTable();
		else
			Model::$_table = Model::$table;

		return Model::$_table;
	}

	private static function _setTable()
	{
		if (empty(Model::$_table))
			Model::$_table = get_called_class();
	}

	public static function __callStatic($method, $args)
	{
		return Model::_processCall($method, $args);
	}

	public function __call($method, $args)
	{
		return Model::_processCall($method, $args);
	}

	private static function _processCall($method, $args)
	{
		$tasks = Model::$_tasks;
		$scopes = Model::$_scopes;
		$bys = Model::$_bys;

		foreach ($tasks as $task)
		{
			foreach ($scopes as $scope)
			{
				foreach ($bys as $by)
				{
					$pattern = $task . $scope . $by;

					if (substr($method, 0, strlen($pattern)) === $pattern)
					{
						$column = substr($method, strlen($pattern));
						return Model::{'_' . $task}($column, $args, $scope);
					}
				}
			}
		}

		return false;
	}

	private static function _extractColumn($column)
	{
		$column = ltrim($column, 'By');
		$column = 'And' . $column;
		$column = str_replace('And', ',And', $column);
		$column = str_replace('Or', ',Or', $column);

		$columns = explode(',', $column);
		array_splice($columns, 0, 1);

		return $columns;
	}

	private static function _load($column, $args, $scope)
	{
		$table = Model::_getTable();

		if ($column === 'Where')
			Model::_buildWhere($column, $args);
		else
		{
			$columns = DB::getColumnListing($table);

			if (in_array('status', $columns))
			{
				$cs = Model::_extractColumn($column);

				$autoWhereStatus = true;
				$seeklist = ['StatusGreaterThan',
					'StatusLessThan',
					'StatusContain',
					'StatusStartWith',
					'StatusEndWith',
					'StatusBetween',
					'StatusIn',
					'StatusIsNull',
					'Status',
					'StatusNotGreaterThan',
					'StatusNotLessThan',
					'StatusNotContain',
					'StatusNotStartWith',
					'StatusNotEndWith',
					'StatusNotBetween',
					'StatusNotIn',
					'StatusIsNull',
					'StatusNot'];

				foreach ($cs as $c)
				{
					if (substr($c, 0, 3) === 'And')
						$c = substr($c, 3);
					elseif (substr($c, 0, 2) === 'Or')
						$c = substr($c, 2);

					if (in_array($c, $seeklist))
					{
						$autoWhereStatus = false;
						break;
					}
				}

				if ($autoWhereStatus)
				{
					if ($column)
					{
						DB::where(function () use ($column, $args)
						{
							Model::_buildWhere($column, $args);
						});
					}

					if (SIDE === 'backend')
						DB::where('status', '>', 0);
					else
						DB::where('status', 2);
				}
				elseif ($column)
					Model::_buildWhere($column, $args);
			}
			elseif ($column)
				Model::_buildWhere($column, $args);
		}

		$data = DB::table($table)->{'load' . $scope}();

		if ($scope === 'All')
			return $data;
		else
		{
			Model::_getInstance();
			$instance = Model::$_instance[$table];

			foreach ($data as $key => $value)
				$instance->$key = $value;

			return $instance;
		}
	}

	private static function _activate($column, $args)
	{
		DB::table(Model::_getTable());
		Model::_buildWhere($column, $args);

		return DB::activate();
	}

	private static function _deactivate($column, $args)
	{
		DB::table(Model::_getTable());
		Model::_buildWhere($column, $args);

		return DB::deactivate();
	}

	private static function _archive($column, $args)
	{
		DB::table(Model::_getTable());
		Model::_buildWhere($column, $args);

		return DB::archive();
	}

	private static function _trash($column, $args)
	{
		DB::table(Model::_getTable());
		Model::_buildWhere($column, $args);

		return DB::trash();
	}

	private static function _discontinue($column, $args)
	{
		DB::table(Model::_getTable());
		Model::_buildWhere($column, $args);

		return DB::discontinue();
	}

	private static function _increase($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		return DB::increase($column, Arr::last($args));
	}

	private static function _decrease($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		if ($arr[1] === 'Where')
		{
			if (strpos($args[0], ':') === false)
				$sign = ':';
			else
				$sign = '?';

			$markers = explode($sign, $args[0]);
			pr($markers);

			if (strpos($args[0], ':') === false)
			{
				$args = Arr::flatten($args);

				// Remove where string (e.g., id=?) from first element
				array_shift($args);
			}
			else
			{
				// We need associative array so don't need to flatten it.
				$args = @$args[1];
			}
		}
		else
		{
			$columns = Model::_extractColumn($arr[1]);

			// Product::decreaseOrderingById(1, 2, 3);
			// $num should be 2 not 3
			if (count($args) > count($columns))
				$num = $args[count($columns)];
			else
				$num = 1;
		}

		return DB::decrease($column, $num);
	}

	private static function _delete($column, $args)
	{
		DB::table(Model::_getTable());
		Model::_buildWhere($column, $args);

		return DB::delete(true);
	}

	private static function _truncate()
	{
		return DB::table(Model::_getTable())->truncate(true);
	}

	private static function _count($column, $args)
	{
		$arr = explode('By', $column);
		DB::table(Model::_getTable());

		if (count($arr) > 1)
		{
			$column = Model::_formatColumnName($arr[0]);
			Model::_buildWhere($arr[1], $args);
		}
		else
		{
			$column = '*';
			Model::_buildWhere($arr[0], $args);
		}

		return DB::count($column);
	}

	private static function _sum($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		return DB::sum($column);
	}

	private static function _min($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		return DB::min($column);
	}

	private static function _max($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		return DB::max($column);
	}

	private static function _avg($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		return DB::avg($column);
	}

	private static function _std($column, $args)
	{
		$arr = explode('By', $column);
		$column = Model::_formatColumnName($arr[0]);

		DB::table(Model::_getTable());
		Model::_buildWhere($arr[1], $args);

		return DB::std($column);
	}

	// Private method does not work on PHP 5.6 and below. Use protected instead.
	protected static function _buildWhere($column, $args)
	{
		// Product::loadWhere('{id}=?', 1);
		if ($column === 'Where')
		{
			$where = $args[0];

			if (strpos($args[0], ':') === false)
			{
				$args = Arr::flatten($args);

				// Remove where string (e.g., id=?) from first element
				array_shift($args);
			}
			else
			{
				// We need associative array so don't need to flatten it.
				$args = @$args[1];
			}

			DB::where($where, $args);
		}
		// Product::loadById(1);
		else
		{
			$columns = Model::_extractColumn($column);

			foreach ($columns as $index => $column)
			{
				$column = $columns[$index];
				$value = @$args[$index]; // not provide $args, leave it null

				if (substr($column, 0, 3) === 'And')
				{
					$method = 'where';
					$column = substr($column, 3);
				}
				elseif (substr($column, 0, 2) === 'Or')
				{
					$method = 'orWhere';
					$column = substr($column, 2);
				}

				if (substr($column, -14) === 'NotGreaterThan')
				{
					$column = rtrim($column, 'NotGreaterThan');
					$column = Model::_formatColumnName($column);
					DB::{$method}($column, '<=', $value);
				}
				elseif (substr($column, -11) === 'GreaterThan')
				{
					$column = rtrim($column, 'GreaterThan');
					$column = Model::_formatColumnName($column);
					DB::{$method}($column, '>', $value);
				}
				elseif (substr($column, -11) === 'NotLessThan')
				{
					$column = rtrim($column, 'NotLessThan');
					$column = Model::_formatColumnName($column);
					DB::{$method}($column, '>=', $value);
				}
				elseif (substr($column, -8) === 'LessThan')
				{
					$column = rtrim($column, 'LessThan');
					$column = Model::_formatColumnName($column);
					DB::{$method}($column, '<', $value);
				}
				elseif (substr($column, -10) === 'NotContain')
				{
					$column = rtrim($column, 'NotContain');
					$column = Model::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::whereNotContain($column, $v);
						});
					}
					else
						DB::{$method . 'NotContain'}($column, $value);
				}
				elseif (substr($column, -7) === 'Contain')
				{
					$column = rtrim($column, 'Contain');
					$column = Model::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
					{
						foreach ($value as $v)
							DB::orWhereContain($column, $v);
					});
					}
					else
						DB::{$method . 'Contain'}($column, $value);
				}
				elseif (substr($column, -12) === 'NotStartWith')
				{
					$column = rtrim($column, 'NotStartWith');
					$column = Model::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::whereNotStartWith($column, $v);
						});
					}
					else
						DB::{$method . 'NotStartWith'}($column, $value);
				}
				elseif (substr($column, -9) === 'StartWith')
				{
					$column = rtrim($column, 'StartWith');
					$column = Model::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
					{
						foreach ($value as $v)
							DB::orWhereStartWith($column, $v);
					});
					}
					else
						DB::{$method . 'StartWith'}($column, $value);
				}
				elseif (substr($column, -10) === 'NotEndWith')
				{
					$column = rtrim($column, 'NotEndWith');
					$column = Model::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::whereNotEndWith($column, $v);
						});
					}
					else
						DB::{$method . 'NotEndWith'}($column, $value);
				}
				elseif (substr($column, -7) === 'EndWith')
				{
					$column = rtrim($column, 'EndWith');
					$column = Model::_formatColumnName($column);

					if (is_array($value) or is_object($value))
					{
						DB::{$method}(function () use ($column, $value)
						{
							foreach ($value as $v)
								DB::orWhereEndWith($column, $v);
						});
					}
					else
						DB::{$method . 'EndWith'}($column, $value);
				}
				elseif (substr($column, -10) === 'NotBetween')
				{
					$column = rtrim($column, 'NotBetween');
					$column = Model::_formatColumnName($column);
					DB::{$method . 'NotBetween'}($column, Arr::first($value), Arr::last($value));
				}
				elseif (substr($column, -7) === 'Between')
				{
					$column = rtrim($column, 'Between');
					$column = Model::_formatColumnName($column);
					DB::{$method . 'Between'}($column, Arr::first($value), Arr::last($value));
				}
				elseif (substr($column, -5) === 'NotIn')
				{
					$column = rtrim($column, 'NotIn');
					$column = Model::_formatColumnName($column);
					DB::{$method . 'NotIn'}($column, $value);
				}
				elseif (substr($column, -2) === 'In')
				{
					$column = rtrim($column, 'In');
					$column = Model::_formatColumnName($column);
					DB::{$method . 'In'}($column, $value);
				}
				elseif (substr($column, -9) === 'IsNotNull')
				{
					$column = rtrim($column, 'IsNotNull');
					$column = Model::_formatColumnName($column);
					DB::{$method . 'NotNull'}($column);
				}
				elseif (substr($column, -6) === 'IsNull')
				{
					$column = rtrim($column, 'IsNull');
					$column = Model::_formatColumnName($column);
					DB::{$method . 'Null'}($column);
				}
				elseif (substr($column, -3) === 'Not')
				{
					$column = rtrim($column, 'Not');
					$column = Model::_formatColumnName($column);
					DB::{$method}($column, '!=', $value);
				}
				else
				{
					$column = Model::_formatColumnName($column);
					DB::{$method}($column, $value);
				}
			}
		}
		/*
		elseif ($args)
		{
			// Don't need to format column name.
			// Maybe column name is where statement
			// e.g., id=? or id=:id etc
			$column = $args[0];
			array_shift($args);
			DB::where($column, $args);
		}
		*/
	}

	public static function __loadWhere($where)
	{
		$args = func_get_args();

		if (strpos($args[0], ':') === false)
		{
			$args = Arr::flatten($args);
			// Remove where string (e.g., id=?) from first element
			array_shift($args);
		}
		else
		{
			// We need associative array so don't need to flatten it.
			$args = @$args[1];
		}

		return DB::table(Model::_getTable())->where($where, $args)->load();
	}

	public static function __loadAllWhere($where)
	{
		$args = func_get_args();

		if (strpos($args[0], ':') === false)
		{
			$args = Arr::flatten($args);
			// Remove where string (e.g., id=?) from first element
			array_shift($args);
		}
		else
		{
			// We need associative array so don't need to flatten it.
			$args = @$args[1];
		}

		return DB::table(Model::_getTable())->where($where, $args)->loadAll();
	}

	/*public static function deleteAll()
	{
		return DB::table(Model::_getTable())->deleteAll();
	}*/

	// abstract method
	public static function rules()
	{
		return [];
	}

	public static function errorInfo()
	{
		return Model::$error;
	}

	private static function _validate()
	{
		$columns = Model::rules();

		foreach ($columns as $column => $validate)
		{
			$label = '';
			$rules = [];

			if (is_array($validate))
			{
				foreach ($validate as $key => $value)
				{
					$key = strtolower(trim($key));

					if ($key === 'label')
					{
						$label = $value;
						continue;
					}

					if (is_array($value))
					{
						$rules[$key]['value'] = trim($value[0]);
						$rules[$key]['message'] = trim(@$value[1]);
					}
					else
					{
						$rules[$key]['value'] = trim($value);
						$rules[$key]['message'] = '';
					}
				}
			}
			else
			{
				$arr1s = explode('|', $validate);

				foreach ($arr1s as $arr1)
				{
					$arr2s = explode(':', $arr1);
					$arr2s = array_map('trim', $arr2s);
					$arr2s[0] = strtolower($arr2s[0]);

					if ($arr2s[0] === 'label')
					{
						$label = @$arr2s[1];
						continue;
					}

					if ($arr2s[0] === 'required')
					{
						$rules['required']['value'] = true;
						$rules['required']['message'] = @$arr2s[1];
					}
					elseif ($arr2s[0] === 'unique')
					{
						$rules['unique']['value'] = true;
						$rules['unique']['message'] = @$arr2s[1];
					}
					elseif (in_array($arr2s[0], ['alnum', 'num', 'number', 'int', 'integer', 'email', 'ip', 'ipv4', 'ipv6', 'url']))
					{
						$rules['type']['value'] = $arr2s[0];
						$rules['type']['message'] = @$arr2s[1];
					}
					else
					{
						$rules[$arr2s[0]]['value'] = @$arr2s[1];
						$rules[$arr2s[0]]['message'] = @$arr2s[2];
					}
				}
			}

			if (!$label)
				$label = $column;

			//

			$orderedRules = [];
			$orderedKeys = ['required', 'alnum', 'num', 'number', 'int', 'integer', 'email', 'ip', 'ipv4', 'ipv6', 'url',
						'min', 'max', 'between', 'minlength', 'maxlength', 'length', 'equalto', 'is', 'not',
						'contain', 'notcontain', 'startwith', 'notstartwith', 'endwith', 'notendwith', 'unique'];

			foreach ($orderedKeys as $key)
			{
				if (array_key_exists($key, $rules))
					$orderedRules[$key] = $rules[$key];
			}

			$rules = $orderedRules;

			//

			foreach ($rules as $rule => $value)
			{
				$data = trim(@Model::$_writeData[$column]);

				$error = '';
				$spec = $value['value'];
				$message = $value['message'];

				// Required
				if ($rule === 'required' and $spec and !mb_strlen($data))
					$error = $label . ($message ? $message : ' is required');

				elseif ($rule === 'requiredif')
				{
					if (strpos($data, '='))
					{
						$arr = explode('=', $data);
					}
					$error = '<li>' . $label . ($message ? $message : ' is required') . '</li>';
					continue;
				}

				// Data type
				elseif ($rule === 'alnum' and $data and !ctype_alnum($data))
					$error = $label . ($message ? $message : ' not alnum');

				elseif (in_array($rule, ['num', 'number']) and $data and !is_int($data) and !is_float($data))
					$error = $label . ($message ? $message : ' not number');

				elseif (in_array($rule, ['int', 'integer']) and $data and preg_match('/^\d+$/', $data))
					$error = $label . ($message ? $message : ' not int');

				elseif ($rule === 'email' and $data and !filter_var($data, FILTER_VALIDATE_EMAIL))
					$error = $label . ($message ? $message : ' not email');

				elseif ($rule === 'ip' and $data and !filter_var($data, FILTER_VALIDATE_IP))
					$error = $label . ($message ? $message : ' not ip');

				elseif ($rule === 'ipv4' and $data and !filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
					$error = $label . ($message ? $message : ' not ipv4');

				elseif ($rule === 'ipv6' and $data and !filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
					$error = $label . ($message ? $message : ' not ipv6');

				elseif ($rule === 'url' and $data and !filter_var($data, FILTER_VALIDATE_URL))
					$error = $label . ($message ? $message : ' not url');

				// Comparison
				elseif ($rule === 'min' and $data and $data < $spec)
					$error = $label . ($message ? $message : ' min is ' . $spec);

				elseif ($rule === 'max' and $data and $data > $spec)
					$error = $label . ($message ? $message : ' max is ' . $spec);

				elseif ($rule === 'between' and $data)
				{
					$arr = explode(',', $spec);
					$min = trim($arr[0]);
					$max = trim(@$arr[1]);

					if ($data < $min or $data > $max)
						$error = $label . ($message ? $message : ' must between ' . $min . ' and ' . $max);
				}

				elseif ($rule === 'minlength' and $data and mb_strlen($data) < $spec)
					$error = $label . ($message ? $message : ' minlen ' . $spec);

				elseif ($rule === 'maxlength' and $data and mb_strlen($data) > $spec)
					$error = $label . ($message ? $message : ' maxlen ' . $spec);

				elseif ($rule === 'length'and $data )
				{
					$arr = explode(',', $spec);
					$min = trim($arr[0]);
					$max = trim(@$arr[1]);

					if (mb_strlen($data) < $min or mb_strlen($data) > $max)
						$error = $label . ($message ? $message : ' length must between ' . $min . ' and ' . $max);
				}

				elseif ($rule === 'equalto' and $data != trim(@Model::$_writeData[$spec]))
					$error = $label . ($message ? $message : ' must same as ' . $spec);

				// other
				elseif ($rule === 'is' and $data != $spec)
					$error = $label . ($message ? $message : ' must be ' . $spec);

				elseif ($rule === 'not' and $data == $spec)
					$error = $label . ($message ? $message : ' cannot be ' . $spec);

				elseif ($rule === 'contain' and mb_stripos($spec, $data) === false)
					$error = $label . ($message ? $message : ' must contain ' . $spec);

				elseif ($rule === 'notcontain' and mb_stripos($spec, $data) !== false)
					$error = $label . ($message ? $message : ' must not contain ' . $spec);

				elseif ($rule === 'startwith' and mb_substr($data, 0, mb_strlen($spec)) != $spec)
					$error = $label . ($message ? $message : ' must startwith ' . $spec);

				elseif ($rule === 'notstartwith' and mb_substr($data, 0, mb_strlen($spec)) == $spec)
					$error = $label . ($message ? $message : ' must not startwith ' . $spec);

				elseif ($rule === 'endwith' and mb_substr($data, (0 - mb_strlen($spec))) != $spec)
					$error = $label . ($message ? $message : ' must endwith ' . $spec);

				elseif ($rule === 'notendwith' and mb_substr($data, (0 - mb_strlen($spec))) == $spec)
					$error = $label . ($message ? $message : ' must not endwith ' . $spec);

				elseif ($rule === 'unique' and $spec and $data)
				{
					$dataSet[$column] = $data;
					/*
					$arr = explode(',', $spec);

					foreach ($arr as $key)
					{
						if (trim($key))
							$dataSet[$key] = @Model::$_readData[$key];
					}
					*/

					if (DB::table(Model::$_table)->duplicate($dataSet, Model::$_readData['id']))
						$error = $label . ($message ? $message : ' is exists.');

					unset($dataSet); // Don't forget to reset variable for each loop
				}

				if ($error)
				{
					Model::$error .= '<li>' . $error . '</li>';
					break;
				}
			}
		}

		if (Model::$error)
		{
			Model::$error = '<ul>' . Model::$error . '</ul>';
			return false;
		}

		return true;
	}

	public static function exists($data = null)
	{
		return DB::table(Model::_getTable())->exists($data, true);
	}

	public static function bind($data)
	{
		if (Arr::isMultidimensional($data))
			$data = $data[0];

		foreach ($data as $key => $value)
			Model::$_instance[Model::_getTable()]->$key = $value;
	}

	public static function save()
	{

//		if (!Model::_validate())
//		{
//			echo Model::$error;
//			return false;
//		}

		$instance = Model::$_instance[Model::_getTable()];
		$columns = DB::getColumnListing(Model::$_table);
		$userId = (int)@Auth::identity()->id;

		if (@$instance->id)
		{
			// DB::where($instance->id); TODO โค้ดเดิมบรรทัดนี้เคยใช้ได้ ตอนนี้ต้องใส่ 'id' แบบบรรทัดล่างด้วย!
			DB::where('id', $instance->id);

			if (in_array('updated', $columns))
				$instance->updated = date('Y-m-d H:i:s');

			if (in_array('updater', $columns))
				$instance->updater = $userId;

			$instance = Arr::fromObject($instance);

			DB::table(Model::$_table)->update($instance);
		}
		else
		{
			if (in_array('ordering', $columns))
				$instance->ordering = DB::table(Model::$_table)->getNewOrdering();

			if (in_array('created', $columns))
				$instance->created = date('Y-m-d H:i:s');

			if (in_array('creator', $columns))
				$instance->creator = $userId;

			$instance = Arr::fromObject($instance);

			DB::table(Model::$_table)->insert($instance);
			$instance->id = DB::getLastInsertId();
		}

		return true;
	}

	private static function _formatColumnName($column)
	{
		return lcfirst($column);
	}

	public static function getModelLocation($class) // ok
	{
		$paths = [
			PATH_APP . DS . 'models' . DS,
			PATH_SYSTEM . DS . 'models' . DS
		];

		foreach ($paths as $path)
		{
			$path .= $class . '.php';

			if (is_file($path))
				return $path;
		}
	}
}
