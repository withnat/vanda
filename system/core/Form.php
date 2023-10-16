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

use System\Exception\InvalidArgumentException;

/**
 * Class Form
 *
 * The Form class manages form functionality for the framework, allowing
 * easy creation, validation, and processing of HTML forms.
 *
 * @package System
 */
class Form extends Mvc\View
{
	protected static $_formId;
	protected static $_rules;
	protected static $_messages;

	/**
	 * Form constructor.
	 */
	private function __construct(){}

	/**
	 * Creates a form open tag with the necessary attributes.
	 *
	 * @param  string|null       $action   The URL or package action to submit the form to. Defaults to null.
	 *                                     If null, the form will submit to the current package's saveAction().
	 * @param  string|array|null $attribs  Additional attributes for the form tag. Defaults to null.
	 * @return string                      Returns the form open tag.
	 */
	public static function open(?string $action = null, $attribs = null) : string
	{
		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(2, ['string', 'array', 'null'], $attribs);

		$action = trim((string)$action);

		if (stripos($action, 'http://') === false and stripos($action, 'https://') === false)
		{
			if (!$action)
				$action = 'save';

			$action = Url::createFromAction($action);
		}

		$id = Html::getAttribute($attribs, 'id');
		$name = Html::getAttribute($attribs, 'name');

		if (!$id and !$name)
		{
			$id = 'frm-default';
			$name = 'frm-default';
		}
		elseif ($id)
			$name = $id;
		elseif ($name)
			$id = $name;

		static::$_formId = $id;

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'action', $action);
		$attribs = Html::setAttribute($attribs, 'method', 'post');
		$attribs = Html::setAttribute($attribs, 'class', 'form-horizontal form');
		$attribs = Html::setAttribute($attribs, 'enctype', 'multipart/form-data');

		$html = '<form ' . $attribs . '>';

		return $html;
	}

	/**
	 * Creates a form close tag.
	 *
	 * @return string  Returns the form close tag.
	 */
	public static function close() : string
	{
		$html = "</form>\n";

		if (static::$_rules)
		{
			$html .= "<script>\n";
			$html .= "\t$(document).ready(function(){\n";
			$html .= "\t\t$(\"#" . static::$_formId . "\").validate({\n";
			$html .= "\t\t\tignore: [],\n";
			$html .= "\t\t\tonkeyup: false,\n";
			$html .= "\t\t\tsubmitHandler: function(form){\n";
			$html .= "\t\t\t\t$(\"#" . static::$_formId . "\").LoadingOverlay(\"show\",{\n";
			$html .= "\t\t\t\t\tbackgroundClass: \"overlay\",\n";
			$html .= "\t\t\t\t\timage: \"\",\n";
			$html .= "\t\t\t\t\tfontawesome: \"fa fa-spinner fa-spin\",\n";
			$html .= "\t\t\t\t\tzIndex: \"9999\"\n";
			$html .= "\t\t\t\t});\n";
			$html .= "\t\t\t\tform.submit();\n";
			$html .= "\t\t\t},\n";
			$html .= "\t\t\trules:{\n";

			$i = 1;
			$in = count(static::$_rules);

			foreach (static::$_rules as $column => $rules)
			{
				$html .= "\t\t\t\t\"$column\":{\n";

				$j = 1;
				$jn = count($rules);

				foreach ($rules as $key => $value)
				{
					if (is_array($value))
					{
						$html .= "\t\t\t\t\t$key:{\n";

						$k = 1;
						$kn = count($value);

						foreach ($value as $subkey => $subvalue)
						{
							$html .= "\t\t\t\t\t\t$subkey: $subvalue";

							if ($k < $kn)
								$html .= ",";

							$html .= "\n";
							++$k;
						}

						$html .= "\t\t\t\t\t}";
					}
					else
						$html .= "\t\t\t\t\t$key: $value";

					if ($j < $jn)
						$html .= ",";

					$html .= "\n";
					++$j;
				}

				$html .= "\t\t\t\t}";

				if ($i < $in)
					$html .= ",";

				$html .= "\n";
				++$i;
			}

			$html .= "\t\t\t}";

			if (static::$_messages)
			{
				$html .= ",\n";
				$html .= "\t\t\tmessages:{\n";

				$i = 1;
				$in = count(static::$_messages);

				foreach (static::$_messages as $column => $messages)
				{
					$html .= "\t\t\t\t\"$column\":{\n";

					$j = 1;
					$jn = count($messages);

					foreach ($messages as $key => $value)
					{
						$html .= "\t\t\t\t\t$key: $value";

						if ($j < $jn)
							$html .= ",";

						$html .= "\n";
						++$j;
					}

					$html .= "\t\t\t\t}";

					if ($i < $in)
						$html .= ",";

					$html .= "\n";
					++$i;
				}

				$html .= "\t\t\t}\n";
			}
			else
				$html .= "\n";

			$html .= "\t\t});\n";
			$html .= "\t});\n";
			$html .= "</script>\n";
		}

		static::$_rules = null;
		static::$_messages = null;

		return $html;
	}

	/**
	 * Generates the CSRF token name.
	 *
	 * @return string  Returns the CSRF token name.
	 */
	public static function token() : string
	{
		return static::hidden(Session::getToken(), 1);
	}

	/**
	 * Generates a text input field.
	 *
	 * @param  string $name                     The name of the input field.
	 * @param  string|int|float|null  $value    The value of the input field. Defaults to null.
	 * @param  string|array|null      $attribs  Additional attributes for the input field. Defaults to null.
	 * @return string                           Returns the text input field.
	 */
	public static function text(string $name, $value = null, $attribs = null) : string
	{
		if (!is_string($value) and !is_int($value) and !is_float($value) and !is_null($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'null'], $value);

		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $attribs);

		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$value = static::_getValue($name, $value);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'value', $value);
		$attribs = Html::setAttribute($attribs, 'type', 'text');
		$attribs = Html::setAttribute($attribs, 'class', 'form-control text');

		$html = '<input ' . $attribs . '/>';

		return $html;
	}

	/**
	 * Generates a color picker input field.
	 *
	 * @param  string                 $name     The name of the input field.
	 * @param  string|int|float|null  $value    The value of the input field. Defaults to null.
	 * @param  string|array|null      $attribs  Additional attributes for the input field. Defaults to null.
	 * @return string                           Returns the color picker input field.
	 */
	public static function color(string $name, $value = null, $attribs = null) : string
	{
		if (!is_string($value) and !is_int($value) and !is_float($value) and !is_null($value))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'null'], $value);

		if (!is_string($attribs) and !is_array($attribs) and !is_null($attribs))
			throw InvalidArgumentException::typeError(3, ['string', 'array', 'null'], $attribs);

		$attribs = Html::setAttribute($attribs, 'type', 'color');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	/**
	 * Generates an element ID from the given name.
	 *
	 * @param  string $name  The name of the element.
	 * @return string        Returns the element ID.
	 */
	protected static function _getId(string $name) : string
	{
		$id = $name;

		if (strpos($id, '.'))
		{
			$arr = explode('.', $id);
			$id = end($arr);
		}

		if (strpos($id, '['))
		{
			$id = str_replace('][', '_', $id);
			$id = str_replace('[', '_', $id);
			$id = rtrim($id, ']');
			$id = rtrim($id, '_');
		}

		$id = trim((string)$id);

		return $id;
	}

	/**
	 * Generates an element name from the given name.
	 *
	 * @param  string $name  The name of the element.
	 * @return string        Returns the element name.
	 */
	protected static function _getName(string $name) : string
	{
		if (strpos($name, '.'))
		{
			$arr = explode('.', $name);
			$name = end($arr);
		}

		$name = trim((string)$name);

		return $name;
	}

	/**
	 * Gets the value of the element.

	 * @param  string                $name     The name of the element.
	 * @param  string|int|float|null $default  The default value of the element. Defaults to null.
	 *                                         If null, the value will be retrieved from the POST data or
	 *                                         View data via the $formVals property.
	 * @return string                          Returns the value of the element.
	 */
	protected static function _getValue(string $name, $default = null) : string
	{
		if (!is_string($default) and !is_int($default) and !is_float($default) and !is_null($default))
			throw InvalidArgumentException::typeError(2, ['string', 'int', 'float', 'null'], $default);

		if (!is_null($default))
			return htmlspecialchars((string)$default);

		$key = '';

		if (strpos($name, '['))
		{
			$arr = explode('[', $name);
			$name = $arr[0];
			$key = rtrim($arr[1], ']');
		}

		$value = '';

		if (Request::isPost())
		{
			$data = Request::post();
			$value = $data->{$name} ?? '';

			// The checkbox will send an array of data, which will
			// be converted to an object by the Request class.
			if (is_object($value))
			{
				$value = (array)$value;
				$value = ',' . implode(',', $value) . ',';
			}
		}

		if (!$value and parent::$_formVals)
		{
			if (is_object(parent::$_formVals))
				$value = @parent::$_formVals->$name;
			elseif (is_array(parent::$_formVals))
				$value = @parent::$_formVals[$name];
		}

		if ($name == 'params' and $key)
		{
			parse_str($value, $params);
			$value = $params[$key] ?? '';
		}

		$value = (string)$value;

		return $value;
	}
}
