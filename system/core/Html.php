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
 * A lightweight & flexible PHP web framework
 *
 * @package      Vanda
 * @author       Nat Withe <nat@withnat.com>
 * @copyright    Copyright (c) 2010 - 2023, Nat Withe. All rights reserved.
 * @link         https://vanda.io
 */

declare(strict_types=1);

namespace System;

use System\Exception\InvalidArgumentException;

/**
 * Class Html
 *
 * The HTML class acts as a utility, providing essential functions for
 * seamless manipulation and generation of HTML elements.
 *
 * @package System
 */
class Html
{
	public static $addedCss = [];
	public static $addedJs = [];
	protected static $_printedOutCss = [];
	protected static $_printedOutJs = [];

	/**
	 * Html constructor.
	 */
	private function __construct(){}

	/**
	 * Generates a '<a>' elment.
	 *
	 * @param  string|null       $url      The relative URL to use for the href attribute. Defaults to null.
	 * @param  string|null       $text     The text to be wrapped by '<a>' element. Defaults to null.
	 * @param  string|array|null $attribs  Attributes to be added to the '<a>' element. Defaults to null.
	 * @return string                      Returns the generated '<a>' element.
	 */
	public static function link(?string $url = null, ?string $text = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $attribs);

		$routeUrl = Url::create($url);

		if (is_null($attribs))
			$attribs = '';
		elseif (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Str::isBlank($text))
			$text = $routeUrl;

		if (App::isSpa())
		{
			$hash = Url::hashSpa((string)$url);
			$attribs = static::setAttribute($attribs, 'href', $hash);
			$attribs = static::setAttribute($attribs, 'data-url', $routeUrl);
		}
		else
			$attribs = static::setAttribute($attribs, 'href', $routeUrl);

		$html = '<a ' . $attribs . '>' . $text . '</a>';

		return $html;
	}

	/**
	 * Generates a '<a>' elment if the current URL is not the same as the given URL.
	 *
	 * @param  string|null       $url      The relative URL to use for the href attribute. Defaults to null.
	 * @param  string|null       $text     The text to be wrapped by '<a>' element. Defaults to null.
	 * @param  string|array|null $attribs  Attributes to be added to the '<a>' element. Defaults to null.
	 * @return string                      Returns the generated '<a>' element.
	 */
	public static function linkUnlessCurrent(?string $url = null, ?string $text = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $attribs);

		$currentUrl = Url::current();
		$url = Url::create($url);

		if (Str::isBlank($text))
			$text = $url;

		if ($url !== $currentUrl)
			return static::link($url, $text, $attribs);
		else
			return $text;
	}

	/**
	 * Generates a mailto link.
	 *
	 * @param  string            $email    The email address to be used.
	 * @param  string|null       $text     The text to be wrapped by '<a>' element. Defaults to null.
	 * @param  string|array|null $attribs  Attributes to be added to the '<a>' element. Defaults to null.
	 * @return string                      Returns the generated mailto link.
	 */
	public static function mailto(string $email, ?string $text = null, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $attribs);

		if (is_null($attribs))
			$attribs = '';
		elseif (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Str::isBlank($text))
			$text = $email;

		$attribs = static::setAttribute($attribs, 'href', 'mailto:' . $email);
		$html = '<a ' . $attribs . '>' . $text . '</a>';

		return $html;
	}

	/**
	 * Generates a '<image>' element.
	 *
	 * @param  string            $url      The image URL to use for the src attribute.
	 * @param  string|null       $alt      The alt attribute. Defaults to null.
	 * @param  string|array|null $attribs  Attributes to be added to the '<image>' element. Defaults to null.
	 * @return string                      Returns the generated '<image>' element.
	 */
	public static function image(string $url, ?string $alt = null, $attribs = null) : string
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $attribs);

		// If the $alt is still null, convert it to string.
		$alt = (string)$alt;

		if (is_null($attribs))
			$attribs = '';
		elseif (is_array($attribs))
			$attribs = Arr::toString($attribs);

		$attribs = static::setAttribute($attribs, 'alt', $alt);
		$attribs = static::setAttribute($attribs, 'title', $alt);

		$url = trim($url);

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			if (substr($url, 0, 1) !== '/')
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
				$path = ltrim($url, '/');

			if (Image::load($path))
			{
				$attribs = static::setAttribute($attribs, 'width', Image::width());
				$attribs = static::setAttribute($attribs, 'height', Image::height());
			}

			$url = Request::basePath() . '/' . $path;
		}

		$attribs = rtrim($attribs);

		$html = '<img src="' . $url . '" ' . $attribs . '>';

		return $html;
	}

	/**
	 * Generates a CSS <link> element.
	 *
	 * @param  string            $url      The CSS URL to use for the href attribute.
	 * @param  string|array|null $attribs  Attributes to be added to the '<link>' element. Defaults to null.
	 * @return string                      Returns the generated CSS link.
	 */
	public static function css(string $url, $attribs = null) : string
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		list($url, $query) = static::_extractCssUrl($url);

		if (Config::app('env') === 'development' and in_array($url, static::$_printedOutCss))
		{
			static::_showIncludeFileWarning($url);
			return '';
		}

		static::$_printedOutCss[] = $url;

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Config::app('env') === 'development')
		{
			if ($query)
			{
				if (stripos($query, 'v=') === false)
					$query .= '&v=' . time();
			}
			else
				$query = 'v=' . time();
		}

		if ($query) $url .= '?' . $query;
		if ($attribs) $attribs = ' ' . $attribs;

		return '<link rel="stylesheet" type="text/css" href="' . $url . '"' . $attribs . '>';
	}

	/**
	 * Generates a '<script>' element.
	 *
	 * @param  string $url                The JS URL to use for the src attribute.
	 * @param string|array|null $attribs  Attributes to be added to the '<script>' element. Defaults to null.
	 * @return string                     Returns the generated '<script>' element.
	 */
	public static function js(string $url, $attribs = null) : string
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		list($url, $query) = static::_extractJsUrl($url);

		if (Config::app('env') === 'development' and in_array($url, static::$_printedOutJs))
		{
			static::_showIncludeFileWarning($url);
			return '';
		}

		static::$_printedOutJs[] = $url;

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		if (Config::app('env') === 'development')
		{
			if ($query)
			{
				if (strpos($query, 'v=') === false)
					$query .= '&v=' . time();
			}
			else
				$query = 'v=' . time();
		}

		if ($query) $url .= '?' . $query;
		if ($attribs) $attribs = ' ' . $attribs;

		return '<script src="' . $url . '"' . $attribs . '></script>';
	}

	/**
	 * Registers the CSS file to be included and printed in the template at a later point.
	 *
	 * @param  string            $url      The CSS URL to use for the href attribute.
	 * @param  string|array|null $attribs  Attributes to be added to the '<link>' element. Defaults to null.
	 * @return void
	 */
	public static function addCss(string $url, $attribs = null) : void
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		list($url, $query) = static::_extractCssUrl($url);

		if (!in_array($url, array_column(static::$addedCss, 'url')))
		{
			if (is_array($attribs))
				$attribs = Arr::toString($attribs);

			if (Config::get('env') === 'development')
			{
				if ($query)
				{
					if (strpos($query, 'v=') === false)
						$query .= '&v=' . time();
				}
				else
					$query = 'v=' . time();
			}

			static::$addedCss[] = ['url' => $url, 'query' => $query, 'attribs' => $attribs];
		}
	}

	/**
	 * Registers the JS file to be included and printed in the template at a later point.
	 *
	 * @param  string           $url      The JS URL to use for the src attribute.
	 * @param string|array|null $attribs  Attributes to be added to the '<script>' element. Defaults to null.
	 * @return void
	 */
	public static function addJs(string $url, $attribs = null) : void
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		list($url, $query) = static::_extractJsUrl($url);

		if (!in_array($url, array_column(static::$addedJs, 'url')))
		{
			if (is_array($attribs))
				$attribs = Arr::toString($attribs);

			if (Config::get('env') === 'development')
			{
				if ($query)
				{
					if (strpos($query, 'v=') === false)
						$query .= '&v=' . time();
				}
				else
					$query = 'v=' . time();
			}

			static::$addedJs[] = ['url' => $url, 'query' => $query, 'attribs' => $attribs];
		}
	}

	/**
	 * Extracts the URL and query string from the given CSS URL.
	 *
	 * @param  string $url  The CSS URL to be extracted.
	 * @return array        Returns the CSS URL and query string.
	 */
	protected static function _extractCssUrl(string $url) : array
	{
		$url = trim($url);
		$query = '';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			$arr = explode('?', $url);
			$url = $arr[0];
			$query = $arr[1] ?? '';

			if (substr($url, 0, 1) !== '/')
			{
				if (is_file($url))
					$path = $url;
				else
				{
					$backtrace = debug_backtrace();
					$path = File::getAssetPath($url, 'css', $backtrace[1]['file']);
				}

				$url = Request::basePath() . DS . $path;
			}
			else
				$url = Request::basePath() . $url;
		}

		if (DS === '\\')
			$url = str_replace('\\', '/', $url);

		return [$url, $query];
	}

	/**
	 * Extracts the URL and query string from the given JS URL.
	 *
	 * @param  string $url  The JS URL to be extracted.
	 * @return array        Returns the JS URL and query string.
	 */
	protected static function _extractJsUrl(string $url) : array
	{
		$query = '';

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
		{
			if (substr($url, 0, 1) !== '/')
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

				$url = Request::basePath() . DS . $path;
			}
			else
				$url = Request::basePath() . $url;
		}

		if (DS === '\\')
			$url = str_replace('\\', '/', $url);

		return [$url, $query];
	}

	/**
	 * Display warning message if the file is included multiple times.
	 *
	 * @param  string $url  The URL of the file.
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected static function _showIncludeFileWarning(string $url) : void
	{
		$backtrace = debug_backtrace();

		$msg = 'The \'' . $url . '\' file being included multiple times.<br />'
			. '&nbsp;&nbsp;file : ' . $backtrace[0]['file'] . '<br />'
			. '&nbsp;&nbsp;line : ' . number_format($backtrace[0]['line']);

		Flash::warning($msg);
	}

	/**
	 * Extracts file name from the given URL and generates a '<a>' element linking to the file.
	 *
	 * @param  string $url  The URL of the file.
	 * @return string       Returns the generated '<a>' element.
	 */
	public static function linkFile(string $url, $attribs = null) : string
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		if (stripos($url, 'http://') === false and stripos($url, 'https://') === false)
			$href = Request::baseUrl() . '/' . $url;
		else
			$href = $url;

		$filename = File::getName($url);
		$attribs = static::setAttribute($attribs, 'target', '_blank');

		$html = '<a href="' . $href . '" ' . $attribs . '>' . $filename . '</a>';

		return $html;
	}

	/**
	 * Generates a '<br>' element.
	 *
	 * @param  int    $multiplier  The number of times the '<br>' element should be repeated.
	 * @return string              Returns the generated '<br>' element.
	 */
	public static function br(int $multiplier) : string
	{
		return str_repeat('<br>', $multiplier);
	}

	/**
	 * Generates a '&nbsp;' element.
	 *
	 * @param  int    $multiplier  The number of times the '&nbsp;' element should be repeated.
	 * @return string              Returns the generated '&nbsp;' element.
	 */
	public static function nbsp(int $multiplier) : string
	{
		return str_repeat('&nbsp;', $multiplier);
	}

	/**
	 * Generates a '<ul>' element.
	 *
	 * @param  array|object      $items    The items to be wrapped by '<li>' element.
	 * @param  string|array|null $attribs  The attributes to be added to the '<ul>' element. Defaults to null.
	 * @return string                      Returns the generated '<ul>' element.
	 */
	public static function ul($items, $attribs = null) : string
	{
		if (!is_array($items) and !is_object($items))
			throw InvalidArgumentException::typeError(1, ['array', 'object'], $items);

		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		$html = '<ul' . $attribs . '>';

		foreach ($items as $item)
			$html .= '<li>' . $item . '</li>';

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Generates a '<ol>' element.
	 *
	 * @param  array|object      $items    The items to be wrapped by '<li>' element.
	 * @param  string|array|null $attribs  The attributes to be added to the '<ol>' element. Defaults to null.
	 * @return string                      Returns the generated '<ol>' element.
	 */
	public static function ol($items, $attribs = null) : string
	{
		if (!is_array($items) and !is_object($items))
			throw InvalidArgumentException::typeError(1, ['array','object'], $items);

		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		if (is_array($attribs))
			$attribs = Arr::toString($attribs);

		$html = '<ol' . $attribs . '>';

		foreach ($items as $item)
			$html .= '<li>' . $item . '</li>';

		$html .= '</ol>';

		return $html;
	}

	/**
	 * Generates a '<table>' element.
	 *
	 * @param  array             $items    The items to be wrapped by '<td>' element.
	 * @param  string|array|null $attribs  The attributes to be added to the '<table>' element. Defaults to null.
	 * @return string                      Returns the generated '<table>' element.
	 */
	public static function table(array $items, $attribs = null) : string
	{
		if (!is_null($attribs) and !is_string($attribs) and !is_array($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array','null'], $attribs);

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

	public static function refresh($url, $delay)
	{
	}

	/**
	 * Extracts the value of the given attribute from the given HTML.
	 *
	 * @param  string      $attribName  The name of the attribute.
	 * @param  string|null $html        The HTML to be extracted.
	 * @return string                   Returns the value of the given attribute.
	 */
	public static function getAttribute(string $attribName, ?string $html = null) : string
	{
		$string = preg_replace('/.*\s' . $attribName . '\s*=\s*"/', '', (string)$html);
		$value = preg_replace('/".*/', '', $string);

		return $value;
	}

	/**
	 * Generates an attribute string from the given data.
	 *
	 * @param  string|array|null $attribs  The existing attributes. NULL value is from Form.php.
	 * @param  string            $name     The name of the attribute to be added.
	 * @param  string|int        $value    The value of the attribute to be added.
	 * @return string                      Returns the generated attribute string.
	 */
	public static function setAttribute($attribs, string $name, $value) : string
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(1, ['string', 'array', 'null'], $attribs);

		if (!is_string($value) and !is_int($value))
			throw InvalidArgumentException::typeError(1, ['string', 'int'], $value);

		if (is_array($attribs))
		{
			if (!isset($attribs[$name]))
				$attribs[$name] = $value;

			$attribs = Arr::toString($attribs);
		}
		else
		{
			if (is_null($attribs))
				$attribs = ' ';
			elseif ($attribs)
				$attribs = rtrim($attribs) . ' ';

			if (strpos($attribs, $name . '=') === false)
				$attribs .= $name . '="' . $value . '"';
			//else
				//$attribs = ' ' . $attribs; // Maybe overwrite by empty attribute e.g., 'class=""'.
		}

		return $attribs;
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	public static function addAssetUpload() : void
	{
		static::_addAssetFilesByType('upload');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	public static function addAssetAutocomplete() : void
	{
		static::_addAssetFilesByType('autocomplete');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	public static function addAssetCheckbox() : void
	{
		static::_addAssetFilesByType('checkbox');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	public static function addAssetRadio() : void
	{
		static::_addAssetFilesByType('radio');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 *
	 */
	public static function addAssetClockpicker() : void
	{
		static::_addAssetFilesByType('clockpicker');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetColorpicker() : void
	{
		static::_addAssetFilesByType('colorpicker');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetDatepicker() : void
	{
		static::_addAssetFilesByType('datepicker');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetDaterangpicker() : void
	{
		static::_addAssetFilesByType('daterangepicker');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetDatatype() : void
	{
		static::_addAssetFilesByType('datatypechecker');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetEditor() : void
	{
		static::_addAssetFilesByType('editor');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetRangeSpin() : void
	{
		static::_addAssetFilesByType('rangespin');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetMarkdown() : void
	{
		static::_addAssetFilesByType('markdown');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetSwitcher() : void
	{
		static::_addAssetFilesByType('switcher');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @return void
	 */
	public static function addAssetTagsinput() : void
	{
		static::_addAssetFilesByType('tagsinput');
	}

	/**
	 * Add asset files to be included in the template.
	 *
	 * @param string $type  The type of the asset files.
	 * @return void
	 */
	protected static function _addAssetFilesByType(string $type) : void
	{
		$assets = Config::core('assets.' . $type);

		foreach ($assets as $asset)
		{
			if (stripos($asset, '.css'))
				static::addCss($asset);
			elseif (stripos($asset, '.js'))
				static::addJs($asset);
		}
	}
}
