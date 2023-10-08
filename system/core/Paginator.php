<?php
/**
 * Vanda
 *
 * A lightweight & flexible PHP CMS framework
 *
 * @package		Vanda
 * @author		Nat Withe <nat@withnat.com>
 * @copyright	Copyright (c) 2010 - 2020, Nat Withe. All rights reserved.
 * @link		http://vanda.io
 */

declare(strict_types=1);

namespace System;

use System\Exception\InvalidArgumentException;

/**
 * Class Paginator
 * @package System
 */
final class Paginator
{
	private static $_totalrecord;
	private static $_totalpage;
	private static $_page;
	private static $_pagesize;
	private static $_numstart;
	private static $_numend;
	private static $_sortcol;
	private static $_sortdir;

	/**
	 * Paginator constructor.
	 */
	public function __construct(){}

	/**
	 * @param  int         $totalrecord
	 * @param  int|null    $page
	 * @param  int|null    $pagesize
	 * @param  string|null $sortcol
	 * @param  string|null $sortdir
	 * @return void
	 */
	public static function init(int $totalrecord, int $page = null, int $pagesize = null, string $sortcol = null, string $sortdir = null) : void
	{
		static::setTotalRecord($totalrecord);

		if ($page) static::setPage($page);
		if ($pagesize) static::setPageSize($pagesize);
		if ($sortcol) static::setSortCol($sortcol);
		if ($sortdir) static::setSortDir($sortdir);

		static::_calculate();
	}

	/**
	 * @return void
	 */
	private static function _calculate() : void
	{
		$page = static::getPage();
		$pagesize = static::getPageSize();

		$totalpage = (int)ceil(static::$_totalrecord / $pagesize);
		$numstart = (($page - 1) * $pagesize) + 1;

		if ($page === $totalpage)
			$numend = static::$_totalrecord;
		elseif ($page < $totalpage)
			$numend = $page * $pagesize;
		else
			$numend = 1;

		static::$_totalpage = $totalpage;
		static::$_numstart = $numstart;
		static::$_numend = $numend;
	}

	/**
	 * @param  int $page
	 * @return void
	 */
	public static function setPage(int $page) : void
	{
		static::$_page = $page;

		$context = Uri::getContext();
		Cookie::set($context . 'page', (string)$page);

		static::_calculate();
	}

	/**
	 * @return int
	 */
	public static function getPage() : int
	{
		if (Request::get('page'))
		{
			$page = (int)Request::get('page');

			if (!$page)
				$page = 1;

			static::setPage($page);
		}
		elseif (static::$_page)
			$page = static::$_page;
		else
		{
			$context = Uri::getContext();
			$page = (int)Cookie::get($context . 'page');

			if (!$page)
			{
				$page = 1;
				static::setPage($page);
			}
		}

		return $page;
	}

	/**
	 * @param  int  $pagesize
	 * @return void
	 */
	public static function setPageSize(int $pagesize) : void
	{
		if (!$pagesize)
			$pagesize = (int)\Setting::get('pagesize', 20);

		static::$_pagesize = $pagesize;

		$context = Uri::getContext();
		Cookie::set($context . 'pagesize', (string)$pagesize);

		static::_calculate();
	}

	/**
	 * @return int
	 */
	public static function getPageSize() : int
	{
		if (static::$_pagesize)
			$pagesize = static::$_pagesize;
		else
		{
			$context = Uri::getContext();
			$pagesize = (int)Cookie::get($context . 'pagesize');

			if (!$pagesize)
			{
				$pagesize = (int)\Setting::get('pagesize', 20);
				static::setPageSize($pagesize);
			}
		}

		return $pagesize;
	}

	/**
	 * @param  string $sortcol
	 * @return void
	 */
	public static function setSortCol(string $sortcol) : void
	{
		static::$_sortcol = $sortcol;

		$context = Uri::getContext();
		Cookie::set($context . 'sortcol', $sortcol);
	}

	/**
	 * @return string
	 */
	public static function getSortCol() : string
	{
		if (static::$_sortcol)
			$sortcol = static::$_sortcol;
		else
		{
			$context = Uri::getContext();
			$sortcol = (string)Cookie::get($context . 'sortcol');
		}

		return $sortcol;
	}

	/**
	 * @param  string $sortdir
	 * @return void
	 */
	public static function setSortDir(string $sortdir) : void
	{
		static::$_sortdir = $sortdir;

		$context = Uri::getContext();
		Cookie::set($context . 'sortdir', $sortdir);
	}

	/**
	 * @return string
	 */
	public static function getSortDir() : string
	{
		if (static::$_sortdir)
			$sortdir = static::$_sortdir;
		else
		{
			$context = Uri::getContext();
			$sortdir = (string)Cookie::get($context . 'sortdir');
		}

		return $sortdir;
	}

	/**
	 * @param  int  $totalrecord
	 * @return void
	 */
	public static function setTotalRecord(int $totalrecord) : void
	{
		static::$_totalrecord = $totalrecord;
		static::_calculate();
	}

	/**
	 * @return int
	 */
	public static function getTotalRecord() : int
	{
		// Default value of $_totalrecord is null.
		// Ensure not return null value.
		return (int)static::$_totalrecord;
	}

	/**
	 * @param  string $title
	 * @param  string $sortcol
	 * @return string
	 */
	public static function sort(string $title, string $sortcol) : string
	{
		$html = '<span class="sort" '
			. 'data-toggle="tooltip" '
			. 'data-placement="top" '
			. 'data-original-title="' . t('Select to sort by this column') . '" '
			. 'onclick="__vanda.sortPage(\'' . $sortcol . '\');">'
			. $title . '</span>';

		if (strtolower($sortcol) === strtolower(static::getSortCol()))
		{
			if (strtolower(static::getSortDir()) === 'asc')
				$html .= '<i class="fa fa-caret-up"></i>';
			else
				$html .= '<i class="fa fa-caret-down"></i>';
		}
		else
			$html .= '<i class="fa fa-caret-left"></i>';

		return $html;
	}

	/**
	 * @param  string|array|null $options
	 * @return string
	 */
	public static function options($options = null) : string
	{
		if (!is_string($options) and !is_array($options) and !is_null($options))
			throw InvalidArgumentException::create(1, ['string','array','null'], $options);

		if (is_null($options))
			$options = [20, 50, 100, 250, 500];
		elseif (!is_array($options))
		{
			$options = explode(',', $options);
			$options = array_map('trim', $options);
		}

		$attribs = 'class="form-control form-control-sm select pagination-pagesize" onchange="__vanda.setPageSize(this[selectedIndex].value);"';
		$select = Form::select('pagesize', $options, static::getPageSize(), null, $attribs);
		$html = t('Show') . $select . t('records');

		return $html;
	}

	/**
	 * @return string
	 */
	public static function detail() : string
	{
		$html = t('Showing') . ' ' . number_format(static::$_numstart) . ' ';
		$html .= t('to') . ' ' . number_format(static::$_numend) . ' ';
		$html .= t('of') . ' ' . number_format(static::$_totalrecord) . ' ' . t('records');

		return $html;
	}

	/**
	 * @param  string|null $pagelink
	 * @return string
	 */
	public static function link(string $pagelink = null) : string
	{
		if (!$pagelink)
			$pagelink = \Setting::get('pagelink', 10);

		$html = '';
		$totalpage = static::$_totalpage;
		$page = static::getPage();

		if ($totalpage)
		{
			$first = '';
			$previous = '';
			$next = '';
			$last = '';

			if ($page > 1)
			{
				$first = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(1);"><i class="fa fa-angle-double-left"></i></a></li>';
				$previous = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . ($page - 1) . ');"><i class="fa fa-angle-left"></i></a></li>';
			}

			if ($totalpage > $page)
			{
				$next = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . ($page + 1) . ');"><i class="fa fa-angle-right"></i></a></li>';
				$last = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . $totalpage . ');"><i class="fa fa-angle-double-right"></i></a></a></li>';
			}

			if ($pagelink === 'all')
			{
				$min = 1;
				$max = $totalpage;
			}
			else
			{
				// If $pagelink is 9
				$backward = (int)($pagelink / 2); // This will be 4
				$forward = $pagelink - $backward; // This will be 5

				$min = $page - $backward;
				$max = $page + $forward;

				if ($min < 1)
				{
					$max += abs($min);
					$min = 1;
				}

				if ($max > $totalpage)
					$max = $totalpage;
			}

			for ($i = $min; $i <= $max; ++$i)
			{
				if ($i === $page)
					$html .= '<li class="page-item active"><a class="page-link" >' . $i . '</a></li>';
				else
					$html .= '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . $i . ');">' . $i . '</a></li>';
			}

			$html = '<ul class="pagination">' . $first . $previous . $html . $next . $last . '</ul>';
		}

		return $html;
	}
}
