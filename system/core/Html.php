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

namespace System;

use System\Exception\InvalidArgumentException;

/**
 * Class Html
 * @package System
 */
class Html
{
	public static $addedCss = [];
	public static $addedJs = [];
	private static $_printedOutCss = [];
	private static $_printedOutJs = [];

	/**
	 * Html constructor.
	 */
	private function __construct(){}

	/**
	 * @param  string|null       $url
	 * @param  string|null       $title
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function link(string $url = null, string $title = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::typeError(3, ['string','array','null'], $attribs);

		$routeUrl = Url::create($url);

		if (is_null($attribs))
			$attribs = '';
		elseif (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Str::isBlank($title))
			$title = $routeUrl;

		if (App::isSpa())
		{
			$hash = Url::hashSpa((string)$url);
			$attribs = static::setAttribute($attribs, 'href', $hash);
			$attribs = static::setAttribute($attribs, 'data-url', $routeUrl);
		}
		else
			$attribs = static::setAttribute($attribs, 'href', $routeUrl);

		$html = '<a ' . $attribs . '>' . $title . '</a>';

		return $html;
	}

	/**
	 * @param  string|null       $url
	 * @param  string|null       $title
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function linkUnlessCurrent(string $url = null, string $title = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(3, ['string','array','null'], $attribs);

		$currentUrl = Request::url();
		$url = Url::create($url);

		if (Str::isBlank($title))
			$title = $url;

		if ($url != $currentUrl)
		{
			if (is_array($attribs))
				$attribs = Arr::toString($attribs);

			return '<a href="' . $url . '" ' . $attribs . '>' . $title . '</a>';
		}
		else
			return $title;
	}

	/**
	 * @param  string            $email
	 * @param  string|null       $title
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function mailto(string $email, string $title = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(3, ['string','array','null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Str::isBlank($title))
			$title = $email;

		return '<a href="mailto:' . $email . '" ' . $attribs . '>' . $title . '</a>';
	}

	/**
	 * @param  string            $url
	 * @param  string|null       $alt
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function image(string $url, string $alt = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(3, ['string','array','null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if ($alt)
			$attribs .= ' alt="' . $alt . '" title="' . $alt . '"';

		$url = trim($url);

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			if (substr($url, 0, 1) != '/')
			{
				if (is_file($url))
					$path = $url;
				else
				{
					$backtrace = debug_backtrace();
					$path = File::getAssetPath($url, 'images', $backtrace[1]['file']);
				}
			}
			else
				$path = ltrim($url);

			if (is_file($path))
			{
				Image::load($path);
				$attribs .= ' width="' . Image::width() . '" height="' . Image::height() . '"';
			}

			$url = Request::basePath() . '/' . $path;
		}

		$html = '<img src="' . $url . '" ' . $attribs . ' />';

		return $html;
	}

	/**
	 * @param  string            $url
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function css(string $url, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(2, ['string','array','null'], $attribs);

		list($url, $query) = static::_getCssUrl($url);

		if (in_array($url, static::$_printedOutCss) and DEV_MODE)
		{
			static::_showIncludeFileWarning($url);
			return '';
		}

		static::$_printedOutCss[] = $url;

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (DEV_MODE)
			$query = '?v=' . Str::random(32);

		return '<link rel="stylesheet" type="text/css" href="' . $url . $query . '" ' . $attribs . ' />' . "\n";
	}

	/**
	 * @param  string            $url
	 * @param  string|array|null $attribs
	 * @return void
	 */
	public static function addCss(string $url, $attribs = null) : void
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(2, ['string','array','null'], $attribs);

		list($url, $query) = static::_getCssUrl($url);

		if (!in_array($url, array_column(static::$addedCss, 'url')))
		{
			if (is_array($attribs))
				$attribs = Arr::toString($attribs);

			if (DEV_MODE)
				$query = '?v=' . Str::random(32);

			static::$addedCss[] = ['url' => $url, 'query' => $query, 'attribs' => $attribs];
		}
	}

	/**
	 * @param  string $url
	 * @return string
	 */
	public static function js(string $url) : string
	{
		list($url, $query) = static::_getJsUrl($url);

		if (in_array($url, static::$_printedOutJs) and DEV_MODE)
		{
			static::_showIncludeFileWarning($url);
			return '';
		}

		static::$_printedOutJs[] = $url;

		if (DEV_MODE)
			$query = '?v=' . Str::random(32);

		return '<script type="text/javascript" src="' . $url . $query . '"></script>' . "\n";
	}

	/**
	 * @param  string $url
	 * @return void
	 */
	public static function addJs(string $url) : void
	{
		list($url, $query) = static::_getJsUrl($url);

		if (!in_array($url, array_column(static::$addedJs, 'url')))
		{
			if (DEV_MODE)
				$query = '?v=' . Str::random(32);

			static::$addedJs[] = ['url' => $url, 'query' => $query];
		}
	}

	/**
	 * @param  string $url
	 * @return array
	 */
	private static function _getCssUrl(string $url) : array
	{
		$url = trim($url);
		$query = '';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			$base = Request::basePath();

			$arr = explode('?', $url);
			$url = $arr[0];
			$query = $arr[1] ?? '';

			if (substr($url, 0, 1) != '/')
			{
				if (is_file($url))
					$path = $url;
				else
				{
					$backtrace = debug_backtrace();
					$path = File::getAssetPath($url, 'css', $backtrace[1]['file']);
				}

				$url = $base . DS . $path;
			}
			else
				$url = $base . $url;
		}

		if (DS === '\\')
			$url = str_replace('\\', '/', $url);

		return [$url, $query];
	}

	/**
	 * @param  string $url
	 * @return array
	 */
	private static function _getJsUrl(string $url) : array
	{
		$query = '';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			$base = Request::basePath();

			if (substr($url, 0, 1) != '/')
			{
				$arr = explode('?', $url);
				$url = $arr[0];
				$query = $arr[1] ?? '';

				if (is_file($url))
					$path = $url;
				else
				{
					$backtrace = debug_backtrace();
					$path = File::getAssetPath($url, 'js', $backtrace[1]['file']);
				}

				$url = $base . DS . $path;
			}
			else
				$url = $base . $url;
		}

		if (DS === '\\')
			$url = str_replace('\\', '/', $url);

		return [$url, $query];
	}

	/**
	 * @param  string $url
	 * @return void
	 */
	private static function _showIncludeFileWarning(string $url) : void
	{
		$backtrace = debug_backtrace();
		$msg = 'The \'' . $url . '\' file being included multiple times.<br />'
			. '&nbsp;&nbsp;file : '.$backtrace[0]['file'].'<br />'
			. '&nbsp;&nbsp;line : '.number_format($backtrace[0]['line']);

		Flash::warning($msg);
	}

	/**
	 * @param  string $url
	 * @return string
	 */
	public static function linkFile(string $url) : string
	{
		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
			$href = Request::baseUrl() . '/' . $url;
		else
			$href = $url;

		$filename = File::getName($url);

		$html = '<a href="' . $href . '" target="_blank">' . $filename . '</a>';

		return $html;
	}

	/**
	 * @param  int    $multiplier
	 * @return string
	 */
	public static function br(int $multiplier) : string
	{
		return str_repeat('<br />', $multiplier);
	}

	/**
	 * @param  int    $multiplier
	 * @return string
	 */
	public static function nbs(int $multiplier) : string
	{
		return str_repeat('&nbsp;', $multiplier);
	}

	/**
	 * @param  array|object      $items
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function ul($items, $attribs = null) : string
	{
		if (!is_array($items) and !is_object($items))
			throw InvalidArgumentException::create(1, ['array','object'], $items);

		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(2, ['string','array','null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		$html = '<ul' . $attribs . '>';

		foreach ($items as $item)
			$html .= '<li>' . $item . '</li>';

		$html .= '</ul>';

		return $html;
	}

	/**
	 * @param  array|object      $items
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function ol($items, $attribs = null) : string
	{
		if (!is_array($items) and !is_object($items))
			throw InvalidArgumentException::create(1, ['array','object'], $items);

		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(2, ['string','array','null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		$html = '<ol' . $attribs . '>';

		foreach ($items as $item)
			$html .= '<li>' . $item . '</li>';

		$html .= '</ol>';

		return $html;
	}

	/**
	 * @param  array             $items
	 * @param  string|array|null $attribs
	 * @return string
	 */
	public static function table(array $items, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(2, ['string','array','null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		$html = '<table ' . $attribs . '>';

		foreach ($items as $row)
		{
			$html .= '<tr>';

			if (!is_array($row) and !is_object($row))
				$row = [$row];

			foreach ($row as $column)
				$html .= '<td>' . $column . '</td>';

			$html .= '</tr>';
		}

		$html .= '</table>';

		return $html;
	}

	/**
	 * @param  string      $attribName
	 * @param  string|null $html
	 * @return string
	 */
	public static function getAttribute(string $attribName, string $html = null) : string
	{
		$string = preg_replace('/.*\s' . $attribName . '\s*=\s*"/', '', (string)$html);
		$value = preg_replace('/".*/', '', $string);

		return $value;
	}

	/**
	 * @param  string|array|null $attribs      NULL value from Form.php
	 * @param  string            $attribName
	 * @param  string            $attribValue
	 * @return string
	 */
	public static function setAttribute($attribs, string $attribName, string $attribValue) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::create(1, ['string','array','null'], $attribs);

		if (is_array($attribs))
		{
			if (!isset($attribs[$attribName]))
				$attribs[$attribName] = $attribValue;

			$attribs = Arr::toString($attribs);
		}
		else
		{
			if (is_null($attribs))
				$attribs = ' ';
			elseif ($attribs)
				$attribs .= ' ';

			if (strpos($attribs, $attribName . '=') === false)
				$attribs .= $attribName . '="' . $attribValue . '"';
			//else
				//$attribs = ' ' . $attribs; // Maybe overwrite by empty attribute e.g., 'class=""'.
		}

		return $attribs;
	}

	/**
	 * @param  string $html
	 * @return string
	 */
	public static function reduceMultipleAttributeSpaces(string $html) : string
	{
		$html = preg_replace('!"\s+!', '" ', $html);
		$html = preg_replace('!"\s+>!', '">', $html);
		$html = trim($html);

		return $html;
	}

	/**
	 * @return void
	 */
	public static function addAssetFileUpload() : void
	{
		static::addCss('templates/backend/vanda/bootstrap-fileinput-master/css/fileinput.min.css');
		static::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/plugins/canvas-to-blob.min.js');
		static::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/plugins/sortable.min.js');
		static::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/plugins/purify.min.js');
		static::addJs('templates/backend/vanda/bootstrap-fileinput-master/js/fileinput.min.js');
		static::addJs('templates/backend/vanda/bootstrap-fileinput-master/themes/fa/theme.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetAutocomplete() : void
	{
		static::addCss('plugins/chosen/bootstrap-chosen.css');
		static::addJs('plugins/chosen/chosen.jquery.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetCheckbox() : void
	{
		static::addCss('plugins/iCheck/custom.css');
		static::addJs('plugins/iCheck/icheck.min.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetClockpicker() : void
	{
		static::addCss('plugins/clockpicker/clockpicker.css');
		static::addJs('plugins/clockpicker/clockpicker.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetColorpicker() : void
	{
		static::addCss('plugins/colorpicker/bootstrap-colorpicker.min.css');
		static::addJs('plugins/colorpicker/bootstrap-colorpicker.min.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetDatepicker() : void
	{
		static::addCss('plugins/datapicker/datepicker3.css');
		// Date range use moment.js same as full calendar plugin.
		static::addJs('plugins/fullcalendar/moment.min.js');
		static::addJs('plugins/datepicker/bootstrap-datepicker.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetDaterangpicker() : void
	{
		static::addAssetDatepicker();
		static::addCss('plugins/daterangepicker/daterangepicker-bs3.css');
		static::addJs('plugins/daterangepicker/daterangepicker.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetDatatype() : void
	{
		static::addCss('plugins/jasny/jasny-bootstrap.min.css');
		static::addJs('plugins/jasny/jasny-bootstrap.min.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetEditor() : void
	{
		static::addCss('plugins/summernote/summernote-bs4.css');
		static::addJs('plugins/summernote/summernote-bs4.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetRangeSpin() : void
	{
		static::addCss('plugins/touchspin/jquery.bootstrap-touchspin.min.css');
		static::addJs('plugins/touchspin/jquery.bootstrap-touchspin.min.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetMarkdown() : void
	{
		static::addCss('plugins/bootstrap-markdown/bootstrap-markdown.min.css');
		static::addJs('plugins/bootstrap-markdown/bootstrap-markdown.js');
		static::addJs('plugins/bootstrap-markdown/markdown.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetSwitcher() : void
	{
		static::addCss('plugins/switchery/switchery.min.css');
		static::addJs('plugins/switchery/switchery.min.js');
	}

	/**
	 * @return void
	 */
	public static function addAssetTagsinput() : void
	{
		static::addCss('plugins/bootstrap-tagsinput/bootstrap-tagsinput.css');
		static::addJs('plugins/bootstrap-tagsinput/bootstrap-tagsinput.js');
	}
}
