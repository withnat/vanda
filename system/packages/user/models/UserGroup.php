<?php
use System\Mvc\Model;
use System\Mvc\View;
use System\Arr;
use System\Cookie;
use System\DB;
use System\Html;
use System\Request;
use System\Uri;

class UserGroup extends Model
{
	public static $table = 'UserGroup';

	public static function rules() : array
	{
		return [
			'name' => 'label:t(Name)|required|unique:user/group/ajax-check-available'
		];
	}

	public static function dataTable() : string
	{
		if (Request::isPost())
		{
			$data = Request::post();
			$data = Arr::fromObject($data);
		}
		else
		{
			$context = Uri::getContext();
			$data = Cookie::get($context . 'datatablesearchform');
			$data = json_decode($data, true);
		}

		$search = preg_replace('/\s+/', ' ', @$data['search']);

		DB::table('UserGroup')
			->where('status', '>', -1);

		if ($search)
			DB::whereContain('name', $search);

		$detect = new Mobile_Detect();

		if ($detect->isMobile())
			DB::sortDesc('created');

		$rows = DB::paginate();
		$fixedIds = static::getFixedIds();

		foreach ($rows as $row)
		{
			if (User::hasWritePermission('user') and !in_array($row->id, $fixedIds))
				$row->link = Html::link('user/group/form?id=' . $row->id, $row->name);
			else
				$row->link = Html::link('user/group/detail?id=' . $row->id, $row->name);
		}

		$view = new View();
		$view->setLayout(null);
		$view->rows = $rows;

		return $view->render('datatable');
	}

	public static function hasFixedId($requestDeleteId) : bool
	{
		$fixedIds = static::getFixedIds();

		foreach ($fixedIds as $id)
		{
			if (in_array($id, $requestDeleteId))
				return true;
		}

		return false;
	}

	public static function getFixedIds() : array
	{
		$ids = [];
		$userGroups = UserGroup::loadAllByFixPermission(1);

		foreach ($userGroups as $userGroup)
			$ids[] = $userGroup->id;

		return $ids;
	}

	public static function getFixedName() : string
	{
		$userGroups = UserGroup::loadAllByFixPermission(1);
		$n = count($userGroups);
		$name = '';

		for ($i=0; $i < $n; ++$i)
		{
			if ($i < $n - 1)
				$name .= $userGroups[$i]->name . ', ';
			else
				$name .= ' and ' . $userGroups[$i]->name;
		}

		return $name;
	}
}
