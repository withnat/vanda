<?php
use System\Mvc\Model;
use System\Mvc\View;
use System\Arr;
use System\Auth;
use System\Cookie;
use System\DateTime;
use System\DB;
use System\Html;
use System\Request;
use System\Url;

class User extends Model
{
	public static $table = 'User';
	private static $_permissions = null;

	public static function rules() : array
	{
		$rules = [
			'userGroupId' => 'required',
			'vendorId' => 'requiredIf:userGroupId=3:This field is required.',
			'branchId' => 'requiredIf:userGroupId=6,7:This field is required.',
			'name' => 'required',
			'email' => 'label:t(Email)|required|email|unique:user/ajax-check-available',
			'password' => 'minlength:6',
			'passwordConfirm' => 'label:Confirm Password|equalTo:password',
			'languageId' => 'required'
		];

		if (!Request::get('id'))
			$rules['password'] = 'required|minlength:4';

		return $rules;
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
		$userGroupId = @$data['userGroupId'];
		$vendorId = @$data['vendorId'];
		$branchId = @$data['branchId'];

		DB::select('u.id, u.name, u.email, g.name AS usergroup, u.status, u.visited')
			->from('User AS u')
			->innerJoin('UserGroup AS g', 'u.userGroupId = g.id')
			->where('u.status', '>', -1);

		if ($search)
		{
			DB::where(function () use ($search)
			{
				DB::whereContain('u.name', $search)
					->orWhereContain('u.email', $search);
			});
		}

		if ($userGroupId)
			DB::where('u.userGroupId', $userGroupId);

		if ($vendorId)
			DB::where('u.vendorId', $vendorId);

		if ($branchId)
			DB::where('u.branchId', $branchId);

		$detect = new Mobile_Detect();

		if ($detect->isMobile())
			DB::sortDesc('u.created');

		$rows = DB::paginate();

		foreach ($rows as $row)
		{
			$row->name = DataHelper::stringLimit($row->name, 30);

			if (DateTime::valid($row->visited))
				$row->visited = date('Y-m-d H:i', strtotime($row->visited));
			else
				$row->visited = t('Never');

			if (User::hasWritePermission('user'))
				$row->link = Html::link('user/form?id=' . $row->id, $row->name);
			else
				$row->link = Html::link('user/detail?id=' . $row->id, $row->name);
		}

		$view = new View();
		$view->setLayout(null);
		$view->rows = $rows;

		return $view->render('datatable');
	}

	public static function hasPermission($section) : bool
	{
		if (is_null(static::$_permissions))
			static::$_permissions = UserGroup::loadById(Auth::identity()->userGroupId)->permission;

		if ((strpos(static::$_permissions, ',' . $section . ':r,') === false and
			strpos(static::$_permissions, ',' . $section . ':w,') === false and
			strpos(static::$_permissions, ',' . $section . ':d,') === false) or
			strpos(static::$_permissions, ',' . $section . ':n,') !== false)
			return false;

		return true;
	}

	public static function hasWritePermission($section) : bool
	{
		if (is_null(static::$_permissions))
			static::$_permissions = UserGroup::loadById(Auth::identity()->userGroupId)->permission;

		if (strpos(static::$_permissions, ',' . $section . ':w,') !== false or
			strpos(static::$_permissions, ',' . $section . ':d,') !== false)
			return true;

		return false;
	}

	public static function hasDeletePermission($section) : bool
	{
		if (is_null(static::$_permissions))
			static::$_permissions = UserGroup::loadById(Auth::identity()->userGroupId)->permission;

		if (strpos(static::$_permissions, ',' . $section . ':d,') !== false)
			return true;

		return false;
	}
}
