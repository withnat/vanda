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
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System;

use ErrorException;
use System\Exception\InvalidArgumentException;

/**
 * Class Paginator
 *
 * The Paginator class is integrated with the query builder (DB class) and
 * ORM (Model class) and provides convenient, easy-to-use pagination of
 * database records with zero configuration.
 *
 * @package System
 */
class Paginator
{
	protected static $_totalrecord;
	protected static $_totalpage;
	protected static $_page;
	protected static $_pagesize;
	protected static $_numstart;
	protected static $_numend;
	protected static $_sortcol;
	protected static $_sortdir;

	/**
	 * Paginator constructor.
	 */
	public function __construct(){}

	/**
	 * Set up the paginator.
	 *
	 * @param  int         $totalrecord  Total number of records.
	 * @param  int|null    $page         Optionally, current page number. Defaults to null.
	 * @param  int|null    $pagesize     Optionally, number of records per page. Defaults to null.
	 * @param  string|null $sortcol      Optionally, column to sort by. Defaults to null.
	 * @param  string|null $sortdir      Optionally, sort direction. Defaults to null.
	 * @return void
	 * @throws ErrorException
	 */
	public static function setup(int $totalrecord, int $page = null, int $pagesize = null, string $sortcol = null, string $sortdir = null) : void
	{
		static::setTotalRecord($totalrecord);

		if ($page) static::setPage($page);
		if ($pagesize) static::setPageSize($pagesize);
		if ($sortcol) static::setSortCol($sortcol);
		if ($sortdir) static::setSortDir($sortdir);

		static::_calculate();
	}

	/**
	 * Sets the current page number.
	 *
	 * @param  int  $page  Page number.
	 * @return void
	 * @throws ErrorException
	 */
	public static function setPage(int $page) : void
	{
		static::$_page = $page;

		$context = Url::getContext();
		Cookie::set($context . 'page', $page);

		static::_calculate();
	}

	/**
	 * Gets the current page number.
	 *
	 * @return int             Returns the current page number.
	 * @throws ErrorException
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
			$context = Url::getContext();
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
	 * Sets the number of records per page.
	 *
	 * @param  int $pagesize Number of records per page.
	 * @return void
	 * @throws ErrorException
	 */
	public static function setPageSize(int $pagesize) : void
	{
		if (!$pagesize)
		{
			$pagesize = (int)Config::app('pagesize', 20);

			if (!$pagesize)
				$pagesize = 20;
		}

		static::$_pagesize = $pagesize;

		$context = Url::getContext();
		Cookie::set($context . 'pagesize', $pagesize);

		static::_calculate();
	}

	/**
	 * Gets the number of records per page.
	 *
	 * @return int             Returns the number of records per page.
	 * @throws ErrorException
	 */
	public static function getPageSize() : int
	{
		if (static::$_pagesize)
			$pagesize = static::$_pagesize;
		else
		{
			$context = Url::getContext();
			$pagesize = (int)Cookie::get($context . 'pagesize');

			if (!$pagesize)
			{
				$pagesize = (int)Config::app('pagesize', 20);

				if (!$pagesize)
					$pagesize = 20;

				static::setPageSize($pagesize);
			}
		}

		return $pagesize;
	}

	/**
	 * Sets the column to sort by.
	 *
	 * @param  string $sortcol  Column to sort by.
	 * @return void
	 */
	public static function setSortCol(string $sortcol) : void
	{
		static::$_sortcol = $sortcol;

		$context = Url::getContext();
		Cookie::set($context . 'sortcol', $sortcol);
	}

	/**
	 * Gets the column to sort by.
	 *
	 * @return string          Returns the column to sort by.
	 * @throws ErrorException
	 */
	public static function getSortCol() : string
	{
		if (static::$_sortcol)
			$sortcol = static::$_sortcol;
		else
		{
			$context = Url::getContext();
			$sortcol = Cookie::get($context . 'sortcol');
		}

		return $sortcol;
	}

	/**
	 * Sets the sort direction.
	 *
	 * @param  string $sortdir  Sort direction.
	 * @return void
	 */
	public static function setSortDir(string $sortdir) : void
	{
		static::$_sortdir = $sortdir;

		$context = Url::getContext();
		Cookie::set($context . 'sortdir', $sortdir);
	}

	/**
	 * Gets the sort direction.
	 *
	 * @return string          Returns the sort direction.
	 * @throws ErrorException
	 */
	public static function getSortDir() : string
	{
		if (static::$_sortdir)
			$sortdir = static::$_sortdir;
		else
		{
			$context = Url::getContext();
			$sortdir = Cookie::get($context . 'sortdir');

			if (!$sortdir)
			{
				$sortdir = 'asc';
				static::setSortDir($sortdir);
			}
		}

		return $sortdir;
	}

	/**
	 * Sets the total number of records.
	 *
	 * @param  int  $totalrecord  Total number of records.
	 * @return void
	 * @throws ErrorException
	 */
	public static function setTotalRecord(int $totalrecord) : void
	{
		static::$_totalrecord = $totalrecord;
		static::_calculate();
	}

	/**
	 * Gets the total number of records.
	 *
	 * @return int
	 */
	public static function getTotalRecord() : int
	{
		// The default value of $_totalrecord is null.
		// Ensure it does not return a null value.
		return (int)static::$_totalrecord;
	}

	/**
	 * Gets the starting item number of the current page.
	 *
	 * @return int  Returns the starting item number of the current page.
	 */
	public static function getNumStart() : int
	{
		return static::$_numstart;
	}

	/**
	 * Gets the ending item number of the current page.
	 *
	 * @return int  Returns the ending item number of the current page.
	 */
	public static function getNumEnd() : int
	{
		return static::$_numend;
	}

	/**
	 * Generates the HTML for the paginator sorting.
	 *
	 * @param  string         $title    Title of the column.
	 * @param  string         $sortcol  Column to sort by.
	 * @return string                   Returns the HTML for the paginator sorting.
	 * @throws ErrorException
	 */
	public static function sort(string $title, string $sortcol) : string
	{
		$html = '<span class="sort" '
			. 'data-toggle="tooltip" '
			. 'data-placement="top" '
			. 'data-original-title="' . t('Click to sort by this column') . '" '
			. 'onclick="__vanda.sortPage(\'' . $sortcol . '\')">'
			. $title
			. '</span>';

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
	 * Generates the HTML for the paginator pagesize dropdown.
	 *
	 * @param  string|array|null $options  Optionally, the options for the dropdown. Defaults to null.
	 * @return string                      Returns the HTML for the paginator pagesize dropdown.
	 * @throws ErrorException
	 */
	public static function options($options = null) : string
	{
		if (!is_string($options) and !is_array($options) and !is_null($options))
			throw InvalidArgumentException::create(1, ['string', 'array', 'null'], $options);

		if (is_string($options))
		{
			$options = explode(',', $options);
			$options = array_map('trim', $options);
		}
		elseif (is_null($options))
		{
			$pagesizeoption = Config::app('$pagesizeoption', '20,50,100,250,500');

			if ($pagesizeoption)
			{
				$options = explode(',', $pagesizeoption);
				$options = array_map('trim', $options);
			}
			else
				$options = [20, 50, 100, 250, 500];
		}

		$attribs = 'class="form-control form-control-sm select pagination-pagesize" onchange="__vanda.setPageSize(this[selectedIndex].value)"';
		$dropdown = Form::select('pagesize', $options, static::getPageSize(), null, $attribs);
		$html = t('Show') . $dropdown . t('records');

		return $html;
	}

	/**
	 * Generates the HTML for the paginator detail.
	 *
	 * @return string  Returns the HTML for the paginator detail.
	 */
	public static function detail() : string
	{
		$html = t('Showing') . ' ' . number_format(static::$_numstart) . ' '
			. t('to') . ' ' . number_format(static::$_numend) . ' '
			. t('of') . ' ' . number_format(static::$_totalrecord) . ' ' . t('records');

		return $html;
	}

	/**
	 * Generates the HTML for the paginator link.
	 *
	 * @param  string|null $pagelink  Optionally, the number of page links to display. Defaults to null.
	 * @return string                 Returns the HTML for the paginator link.
	 * @throws ErrorException
	 */
	public static function link(string $pagelink = null) : string
	{
		if (!$pagelink)
		{
			$pagelink = (int)Config::app('pagelink', 10);

			if (!$pagelink)
				$pagelink = 10;
		}

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
				$first = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(1)"><i class="fa fa-angle-double-left"></i></a></li>';
				$previous = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . ($page - 1) . ')"><i class="fa fa-angle-left"></i></a></li>';
			}

			if ($totalpage > $page)
			{
				$next = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . ($page + 1) . ')"><i class="fa fa-angle-right"></i></a></li>';
				$last = '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . $totalpage . ')"><i class="fa fa-angle-double-right"></i></a></a></li>';
			}

			// Directly dividing by 2 for both $backward and $forward
			// is not possible because $pagelink may be an odd number.
			// For example, if $pagelink is 9, the $backward value will
			// be 4, and the $forward value will be 5.
			$backward = (int)($pagelink / 2);
			$forward = $pagelink - $backward;

			$min = $page - $backward;
			$max = $page + $forward;

			if ($min < 1)
			{
				$max += abs($min);
				$min = 1;
			}

			if ($max > $totalpage)
				$max = $totalpage;

			for ($i = $min; $i <= $max; ++$i)
			{
				if ($i === $page)
					$html .= '<li class="page-item active"><a class="page-link" >' . $i . '</a></li>';
				else
					$html .= '<li class="page-item"><a class="page-link" onclick="__vanda.goToPage(' . $i . ')">' . $i . '</a></li>';
			}

			$html = '<ul class="pagination">' . $first . $previous . $html . $next . $last . '</ul>';
		}

		return $html;
	}

	/**
	 * Calculate and set the total number of pages, the starting and ending
	 *
	 * @return void
	 * @throws ErrorException
	 */
	protected static function _calculate() : void
	{
		$page = static::getPage();
		$pagesize = static::getPageSize();
		$totalrecord = static::$_totalrecord;

		// If $totalrecord is 0, the ceil() function below will set $page to 0.
		// If $page is 0, the resulting $offset below will be negative, causing a SQL error.
		if ($totalrecord)
		{
			// In case of reducing the page size, for example, when there are a total of 9 items
			// and the page size is selected from 20 to 5 items per page, and then navigating to
			// the last page (page 2) and select the page size back to 20 items per page, the
			// system will not display data. This is because the $page is still 2, which is greater
			// than the actual number of pages (9 items displayed with a page size of 20, max page
			// should be 1).
			if ($totalrecord / $pagesize < $page)
			{
				$page = (int)ceil($totalrecord / $pagesize);
				static::setPage($page);
			}
		}
		else
		{
			$page = 1;
			static::setPage($page);
		}

		$totalpage = (int)ceil($totalrecord / $pagesize);
		$numstart = (($page - 1) * $pagesize) + 1;

		if ($page === $totalpage)
			$numend = $totalrecord;
		elseif ($page < $totalpage)
			$numend = $page * $pagesize;
		else
			$numend = 1;

		static::$_totalpage = $totalpage;
		static::$_numstart = $numstart;
		static::$_numend = $numend;
	}
}
