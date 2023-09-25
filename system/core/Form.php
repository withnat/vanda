<?php
namespace System;

final class Form extends Mvc\View
{
	private static $_formId;
	private static $_validate;
	private static $_hasResetButton = false;
	private static $_errorMsgId = array();
	private static $_elementIdList = array();
	private static $_rules;
	private static $_messages;

	public static function open($action = null, $attribs = null)
	{
		if (trim($action) == '')
		{
			$package = PACKAGE;
			$subpackage = SUBPACKAGE ? '/' . SUBPACKAGE : '';
			$action = $package . $subpackage . '/save';
		}

		$action = Uri::route($action);

		$id = Html::getAttribute($attribs, 'id');
		$name = Html::getAttribute($attribs, 'name');

		setValueIfEmpty($id, $name, 'frm-default', 'frm-default');
		static::$_formId = $id;

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'action', $action);
		$attribs = Html::setAttribute($attribs, 'method', 'post');
		$attribs = Html::setAttribute($attribs, 'class', 'form-horizontal form');
		$attribs = Html::setAttribute($attribs, 'enctype', 'multipart/form-data');

		$html = '<form ' . $attribs . '>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function close()
	{
		$html = "</form>\n";

		if (static::$_rules)
		{
			$html .= "<script type=\"text/javascript\">\n";
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

	public static function token()
	{
		return static::hidden(Session::getToken(), 1);
	}

	private static function _setRule($name)
	{
		if (strpos($name, '.'))
		{
			$arr = explode('.', $name);
			$model = trim(current($arr));
			$name = trim(end($arr));
			$columns = $model::rules();

			if (isset($columns[$name]))
			{
				$validate = $columns[$name];

				$rules = static::$_rules;
				$messages = static::$_messages;

				if (is_array($validate))
				{
					foreach ($validate as $key => $value)
					{
						$key = strtolower(trim($key));

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
					$label = '';
					$arr1s = explode('|', $validate);

					foreach ($arr1s as $arr1)
					{
						$arr2s = explode(':', $arr1);
						$arr2s = array_map('trim', $arr2s);
						$key = strtolower($arr2s[0]);

						if ($key == 'label')
						{
							if (@$arr2s[1])
							{
								if (substr($arr2s[1], 0, 2) == 't(' and substr($arr2s[1], -1) == ')')
									$label = t(trim(substr($arr2s[1], 2, -1)));
								else
									$label = addslashes($arr2s[1]);
							}
						}
						elseif ($key == 'required')
						{
							$rules[$name]['required'] = 'true';

							if (@$arr2s[1])
								$messages[$name]['required'] = '"' . addslashes($arr2s[1]) . '"';
						}
						elseif ($key == 'requiredif' and @$arr2s[1])
						{
							$explode = '';
							$operator = '';

							if (strpos($arr2s[1], '='))
							{
								$explode = '=';
								$operator = '==';
							}
							elseif (strpos($arr2s[1], '<'))
							{
								$explode = '<';
								$operator = '<';
							}
							elseif (strpos($arr2s[1], '>'))
							{
								$explode = '>';
								$operator = '>';
							}
							elseif (strpos($arr2s[1], '!='))
							{
								$explode = '!=';
								$operator = '!=';
							}
							elseif (strpos($arr2s[1], '!'))
							{
								$explode = '!';
								$operator = '!=';
							}

							if ($explode)
							{
								$arr = explode($explode, $arr2s[1]);

								if (strpos($arr[1], ','))
								{
									$values = explode(',', $arr[1]);
									$rules[$name]['required'] = 'function() { return (';

									foreach ($values as $value)
										$rules[$name]['required'] .= '$.trim($("#' . $arr[0] . '").val()) ' . $operator . ' "' . $value . '" ||';

									$rules[$name]['required'] = substr($rules[$name]['required'], 0, -3) . '); }';
										
								}
								else
									$rules[$name]['required'] = 'function() { '
																	. 'if ($("#' . $arr[0] . '0").is(":radio")) { '
																			. 'return $(\'input[name="result"]:checked\').val() ' . $operator . ' "' . $arr[1] . '"; '
																	. ' } else { '
																			. 'return $.trim($("#' . $arr[0] . '").val()) ' . $operator . ' "' . $arr[1] . '"; '
																	. ' } '
																. ' }';
							}

							if (@$arr2s[2])
								$messages[$name]['required'] = '"' . addslashes($arr2s[2]) . '"';
						}
//						elseif ($key == 'requiredifempty' and @$arr2s[1])
//						{
//							if (@$arr2s[2])
//								$messages[$name]['required'] = '"' . addslashes($arr2s[2]) . '"';
//						}
//						elseif ($key == 'requiredifnotempty' and @$arr2s[1])
//						{
//							if (@$arr2s[2])
//								$messages[$name]['required'] = '"' . addslashes($arr2s[2]) . '"';
//						}
						elseif ($key == 'unique' and strpos(@$arr2s[1], '/'))
						{
							$rules[$name]['remote']['url'] = '"' . Uri::route($arr2s[1]) . '"';
							$rules[$name]['remote']['type'] = '"post"';
							$rules[$name]['remote']['data'] = '{ id: function() { return $("#id").val() } }';
							$rules[$name]['remote']['dataFilter'] = 'function(response) { if (/<\/html>/i.test($.trim(response))) window.location.href = __vandaServerVars.homeUrl; else return response; }';

							if (@$arr2s[2])
								$messages[$name]['remote'] = '"' . addslashes($arr2s[2]) . '"';
							else
							{
								//$messages[$name]['remote'] = 'function() { return $.validator.format("{0} is already in use.", $("#' . $name . '").val()); }';
								if (Auth::identity()->languageId == 1)
									$msg = ' is already in use.';
								else
									$msg = 'นี้มีอยู่แล้วในระบบ';

								$messages[$name]['remote'] = 'function() { return "' . $label . $msg . '"; }';
							}
						}
						elseif ($key == 'range' and @$arr2s[1])
						{
							$rules[$name]['range'] = '[' . @$arr2s[1] . ']';

							if (@$arr2s[2])
								$messages[$name]['range'] = '"' . addslashes($arr2s[2]) . '"';
						}
						elseif ($key == 'rangelength' and @$arr2s[1])
						{
							$rules[$name]['rangelength'] = '[' . @$arr2s[1] . ']';

							if (@$arr2s[2])
								$messages[$name]['rangelength'] = '"' . addslashes($arr2s[2]) . '"';
						}
						elseif (in_array($key, ['email', 'number', 'digits']))
						{
							$rules[$name][$key] = 'true';

							if (@$arr2s[1])
								$messages[$name][$key] = '"' . addslashes($arr2s[1]) . '"';
						}
						elseif ($key == 'equalto' and @$arr2s[1])
						{
							$rules[$name]['equalTo'] = '"#' . $arr2s[1] . '"';

							if (@$arr2s[2])
								$messages[$name]['equalTo'] = '"' . addslashes($arr2s[2]) . '"';
						}
						elseif ($key == 'extension' and @$arr2s[1])
						{
							$rules[$name]['extension'] = '"' . str_replace(',', '|', @$arr2s[1]) . '"';

							if (@$arr2s[2])
								$messages[$name]['extension'] = '"' . addslashes($arr2s[2]) . '"';
						}
						elseif (@$arr2s[1])
						{
							// Using $key instead of $arr2s[0] to ensure it is lower case letter.
							$rules[$name][$key] = '"' . $arr2s[1] . '"';

							if (@$arr2s[2])
								$messages[$name][$arr2s[0]] = '"' . addslashes($arr2s[2]) . '"';
						}
					}
				}

				static::$_rules = $rules;
				static::$_messages = $messages;
			}
		}
	}

	public static function text($name, $value = null, $attribs = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$value = static::_getValue($name, $value);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'value', $value);
		$attribs = Html::setAttribute($attribs, 'type', 'text');
		$attribs = Html::setAttribute($attribs, 'class', 'form-control text');

		$html = '<input ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function color($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'color');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function date($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'date');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function datetime($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'datetime');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function datetimelocal($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'datetime-local');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function email($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'email');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function month($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'month');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function number($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'number'); // Android
		$attribs = Html::setAttribute($attribs, 'pattern', '\d*'); // iOS
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function range($name, $min = 0, $max = 100, $step = 1, $value = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'number');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function tel($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'tel');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function time($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'time');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function url($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'url');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function week($name, $value = null, $attribs = null)
	{
		$attribs = Html::setAttribute($attribs, 'type', 'week');
		$html = static::text($name, $value, $attribs);

		return $html;
	}

	public static function hidden($name, $value = null, $attribs = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$value = static::_getValue($name, $value);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'value', $value);
		$attribs = Html::setAttribute($attribs, 'type', 'hidden');
		$attribs = Html::setAttribute($attribs, 'class', 'form-control hidden');

		$html = '<input ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	// Default value is '' instead NULL because
	// we don't need system get value from POST or DB
	public static function password($name, $value = '', $attribs = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$value = static::_getValue($name, $value);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'value', $value);
		$attribs = Html::setAttribute($attribs, 'type', 'password');
		$attribs = Html::setAttribute($attribs, 'class', 'form-control password');

		$html = '<input ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function textarea($name, $value = null, $attribs = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$value = static::_getValue($name, $value);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'class', 'form-control textarea');

		$html = '<textarea ' . $attribs . '>' . $value . '</textarea>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function editor($name, $value = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$value = static::_getValue($name, $value);

		$attribs = '';
		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'class', 'summernote');

		$html = '<textarea ' . $attribs . '>' . $value . '</textarea>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function file($name, $attribs = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'type', 'file');
		$attribs = Html::setAttribute($attribs, 'class', 'file');

		$html = '<input ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function avatar($name)
	{
		Html::addAssetsForFileUpload();
	}

	public static function image($name, $width = null, $height = null)
	{
		$value = static::_getValue($name);
		$html = static::file($name);

		if ($value)
		{
			if (File::isImage($value))
			{

			}
			else
			{
				$ext = File::getExtension($value);

				if (Arr::has(['doc', 'docx', 'rtf', 'odt'], $ext, true))
					$icon = 'ico_writer.png';
				elseif (Arr::has(['xls', 'xlsx', 'ods'], $ext, true))
					$icon = 'ico_calc.png';
				elseif ($ext == 'pdf')
					$icon = 'ico_pdf.png';
				elseif (Arr::has(['zip', 'rar'], $ext, true))
					$icon = 'ico_zip.png';

				elseif (Arr::has(['jpg', 'jpeg'], $ext, true))
					$icon = 'ico_jpeg.png';
				elseif ($ext == 'gif')
					$icon = 'ico_gif.png';
				elseif ($ext == 'png')
					$icon = 'ico_png.png';

				elseif ($ext == 'mp3')
					$icon = 'ico_sound.png';
				elseif (Arr::has(['mp4', 'mov', 'flv'], $ext, true))
					$icon = 'ico_video.png';
				else
					$icon = 'ico_file.png';

				$icon = 'icons/filetypes/22/' . $icon;
			}
		}

		return $html;
	}

	/**
	 * For options in array, can be
	 *  1. array('a', 'b', 'c');
	 *  2. array(array('a'), array('b'), array('c'));
	 *  3. array(array('a', 'A'), array('b', 'B'), array('c', 'C'));
	 *  4. array(array('a'=>'A'), array('b'=>'B'), array('c'=>'C'));
	 *
	 * For options in text can be
	 *  1. jan:January|feb:February|mar:March
	 *  2. January|February|March
	 *  3. jan:January
	 *  4. Single text
	 *
	 * Selected value can be number or string
	 */
	public static function select($name, $options = null, $selected = null, $title = '', $attribs = null, $columnValue = null, $columnText = null, $where = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$selected = static::_getValue($name, $selected);

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'class', 'form-control select');

		if (is_null($options) and strlen($id) > 2 and substr($id, -2) == 'Id')
			$options = static::_getOptionFromDB($id, $columnValue, $columnText, $where, $selected);
		elseif (is_string($options))
			$options = static::_getOptionFromString($options);

		$arr = static::_extractOptions($options);
		$aryValue = $arr[0];
		$aryText = $arr[1];

		$html = '<select ' . $attribs . '>' . "\n";

		// Some dropdown does not need to show title ie Paginator::options()
		if (is_null($title) == false)
			$html .= '<option value="">' . $title . '</option>' . "\n";

		$n = count($aryValue);

		for ($i = 0; $i < $n; ++$i)
		{
			$attribs = '';
			$attribs = Html::setAttribute($attribs, 'value', $aryValue[$i]);
			$attribs .= (string)$aryValue[$i] == (string)$selected ? ' selected="selected"' : '';

			$html .= '<option '. $attribs . '>' . $aryText[$i] . '</option>' . "\n";
		}

		$html .= '</select>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	/**
	 * For options in array, can be
	 *  1. array('a', 'b', 'c');
	 *  2. array(array('a'), array('b'), array('c'));
	 *  3. array(array('a', 'A'), array('b', 'B'), array('c', 'C'));
	 *  4. array(array('a'=>'A'), array('b'=>'B'), array('c'=>'C'));
	 *
	 * For options in text can be
	 *  1. jan:January|feb:February|mar:March
	 *  2. January|February|March
	 *  3. jan:January
	 *  4. Single text
	 *
	 * Checked value can be array alphanumeric in format ,1,2,3,
	 */
	public static function checkbox($name, $options = null, $checked = null, $attribs = null, $columnValue = null, $columnText = null, $where = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);

		if (strpos($name, '[]') === false)
			$name .= '[]';

		$checked = static::_getValue($name, $checked);

		if (is_array($checked))
			$checked = ',' . implode(',', $checked) . ',';

		if (is_null($options) and strlen($id) > 3 and substr($id, -3) == 'Ids')
			$options = static::_getOptionFromDB($id, $columnValue, $columnText, $where);
		elseif (is_string($options))
			$options = static::_getOptionFromString($options);

		$arr = static::_extractOptions($options);
		$aryValue = $arr[0];
		$aryText = $arr[1];

		$html = '<div class="col-sm-10">' . "\n";
		$n = count($aryValue);

		for ($i = 0; $i < $n; ++$i)
		{
			$attribs = '';
			$attribs = Html::setAttribute($attribs, 'id', $id . $i);
			$attribs = Html::setAttribute($attribs, 'name', $name);
			$attribs = Html::setAttribute($attribs, 'value', $aryValue[$i]);
			$attribs = Html::setAttribute($attribs, 'type', 'checkbox');
			$attribs .= (strpos($checked, ',' . $aryValue[$i] . ',') !== false ? ' checked="checked" ' : '');

			$html .= '<div class="i-checks"><label>' . "\n";
			$html .= '<input ' . $attribs . '> ' . $aryText[$i]  . "\n";
			$html .= '</label></div>' . "\n";
		}

		$html .= '</div>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	/**
	 * For array, can be
	 * array(a, b);
	 * array(a, b, c);
	 * array(array('a'), array('b'));
	 * array(array('a', 'A'), array('b', 'B'));
	 * array(array('a'=>'A'), array('b'=>'B'));
	 */

	/**
	 * For options in array, can be
	 *  1. array('a', 'b', 'c');
	 *  2. array(array('a'), array('b'), array('c'));
	 *  3. array(array('a', 'A'), array('b', 'B'), array('c', 'C'));
	 *  4. array(array('a'=>'A'), array('b'=>'B'), array('c'=>'C'));
	 *
	 * For options in text can be
	 *  1. jan:January|feb:February|mar:March
	 *  2. January|February|March
	 *  3. jan:January
	 *  4. Single text
	 *
	 * Checked value can be number or string
	 */
	public static function radio($name, $options = null, $checked = null, $attribs = null, $columnValue = null, $columnText = null, $where = null)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$checked = static::_getValue($name, $checked);

		if (is_null($options) and strlen($id) > 2 and substr($id, -2) == 'Id')
			$options = static::_getOptionFromDB($id, $columnValue, $columnText, $where);
		elseif (is_string($options))
			$options = static::_getOptionFromString($options);

		$arr = static::_extractOptions($options);
		$aryValue = $arr[0];
		$aryText = $arr[1];

		$html = '<div class="col-sm-10">' . "\n";
		$n = count($aryValue);

		for ($i = 0; $i < $n; ++$i)
		{
			$subattribs = $attribs;
			$subattribs = Html::setAttribute($subattribs , 'id', $id . $i);
			$subattribs = Html::setAttribute($subattribs , 'name', $name);
			$subattribs = Html::setAttribute($subattribs , 'value', $aryValue[$i]);
			$subattribs = Html::setAttribute($subattribs , 'type', 'radio');
			$subattribs .= ((string)$aryValue[$i] == (string)$checked ? ' checked="checked"' : '');

			$html .= '<div class="i-checks"><label>' . "\n";
			$html .= '<input ' . $subattribs  . '> ' . $aryText[$i] . "\n";
			$html .= '</label></div>' . "\n";
		}

		$html .= '</div>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function boolean($name, $checked = null, $default = true)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$checked = static::_getValue($name, $checked);

		// $checked maybe an empty space
		if (mb_strlen($checked) == 0)
			$checked = $default;

		$attribs = ' id="' . $id . '" ';
		$attribs .= ' name="' . $name . '" ';
		$attribs .= ' value="1" ';
		$attribs .= ' class="js-switch" ';
		$attribs .= ((int)$checked == 1) ? ' checked="checked" ' : '';

		$html = '<input type="checkbox" ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function enable($name, $checked = null, $default = true)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$checked = static::_getValue($name, $checked);

		// $checked value maybe an empty space
		if (mb_strlen($checked) == 0)
			$checked = $default;

		$attribs = ' id="' . $id . '" ';
		$attribs .= ' name="' . $name . '" ';
		$attribs .= ' value="1" ';
		$attribs .= ' class="js-switch" ';
		$attribs .= ((int)$checked > 0) ? ' checked="checked" ' : '';

		$html = '<input type="checkbox" ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function active_bak($name, $checked = null, $default = true)
	{
		static::_setRule($name);

		$id = static::_getId($name);
		$name = static::_getName($name);
		$checked = static::_getValue($name, $checked);

		// $checked value maybe an empty space
		if (mb_strlen($checked) == 0)
			$checked = $default;

		$attribs = ' id="' . $id . '" ';
		$attribs .= ' name="' . $name . '" ';
		$attribs .= ' value="1" ';
		$attribs .= ' class="js-switch" ';
		$attribs .= ((int)$checked > 0) ? ' checked="checked" ' : '';

		$html = '<input type="checkbox" ' . $attribs . '/>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	public static function datepicker($name, $value = null, $attribs = null)
	{
		if ($attribs == '')
			$attribs = 'class="form-control text datepicker"';

		$html = '<div class="input-group date">';
		$html .= '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
		$html .= static::text($name, $value, $attribs);
		$html .= '</div>';

		return $html;
	}

	public static function calendar($name, $value=null, $attribs=null, $dateFormat='dd/mm/y')
	{
		if (is_array($attribs))
		{
			if (isset($attribs['style']) === false)
				$attribs['style'] = 'width:80px;';
			else
			{
				if (stripos($attribs['style'], 'width') === false)
				{
					$attribs['style'] = trim($attribs['style']);
					if (Str::getRight($attribs['style']) !== ';')
						$attribs['style'] .= ';';
					$attribs['style'] .= ' width:80px;';
				}
			}
			$attribs = Arr::toString($attribs);
		 }
		 else
		 {
		 	if (stripos($attribs, 'style') === false)
				$attribs .= ' style="width:80px;" ';
			else
			{
				if (stripos($attribs, 'width') === false)
				{
					$existing_value = self::_getAttribute($attribs, 'style');
					$new_value = trim($existing_value);
					if (Str::getRight($new_value) !== ';')
						$new_value .= ';';
					$new_value .= ' width:80px;';
					$attribs = Str::replace($attribs, $existing_value, $new_value);
				}
			}
		}

		echo Html::script('/system/3rdparty/jquery-ui/js/jquery-ui.js');
		echo Html::css('/system/3rdparty/jquery-ui/css/smoothness/jquery-ui.css');

		echo '<script type="text/javascript">
			jQuery(function() {
				jQuery( "#'.$name.'" ).datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat: "'.$dateFormat.'"
				});
			});
		</script>';

		$id = static::_getId($name);

		if ($value === null)
			$value = static::_getValue($name);
		else
			$value = Str::safe($value);

		if ($value == '0000-00-00' or $value == '0000-00-00 00:00:00')
			$value = '';

		/*
		if ($value)
			$value = date('d/m/y', strtotime($value));
		*/

		if (is_array($attribs))

		{
			if (isset($attribs['class']) == false) $attribs['class'] = 'inputbox';
			$attribs = Arr::toString($attribs);
		 }
		 else
			if (stripos($attribs, 'class=') === false) $attribs .= ' class="inputbox" ';

		$attribs = self::_genOnblur2Validate($attribs);

		$html = '<input type="text" id="'.$id.'" name="'.$ename.'" value="'.$value.'" '.$attribs.' />'."\n";
		return self::_removeMultipleSpacesBetweenHTMLAttributes($html);
	}

	public static function buttonSubmit($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Submit');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-check-square-o"></i> ' . $label;

		return static::_createButton('submit', $label, $attribs);
	}

	public static function buttonReset($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Reset');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-check-square-o"></i> ' . $label;

		return static::_createButton('reset', $label, $attribs);
	}

	public static function buttonSave($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Save');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-check-square-o"></i> ' . $label;

		return static::_createButton('submit', $label, $attribs);
	}

	public static function buttonConfirm($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Confirm');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-check-square-o"></i> ' . $label;

		return static::_createButton('submit', $label, $attribs);
	}

	public static function buttonDelete($label = null, $attribs = null)
	{
		if (Request::get('id') == '')
			return '';

		if (trim($label) == '')
			$label = t('Delete');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-trash"></i> ' . $label;

		if (is_null($attribs))
			$attribs = 'id="btn-delete" name="btn-delete"';

		$uri = PACKAGE . (SUBPACKAGE ? '/'. SUBPACKAGE : '') . '/delete';
		$url = Uri::route($uri) . '?id=' . Request::get('id');

		$onclick = 'bootbox.confirm(\'' . t('Are you sure?') . '\', function(result){
						if (result){
							window.location.href = \'' . $url . '\';
						}
					});';
		$attribs = Html::setAttribute($attribs, 'onclick', $onclick);

		return static::_createButton('button', $label, $attribs);
	}

	public static function buttonCancel($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Cancel');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-times"></i> ' . $label;

		$uri = PACKAGE . (SUBPACKAGE ? '/'. SUBPACKAGE : '');

		if (spa())
			$url = Uri::hashSPA($uri);
		else
			$url = Uri::route($uri);

		$attribs = Html::setAttribute($attribs, 'id', 'btn-cancel');
		$attribs = Html::setAttribute($attribs, 'name', 'btn-cancel');
		$attribs = Html::setAttribute($attribs, 'onclick', 'window.location.href=\'' . $url . '\';');

		return static::_createButton('button', $label, $attribs);
	}

	public static function buttonClose($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Close');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-times"></i> ' . $label;

		$uri = PACKAGE . (SUBPACKAGE ? '/'. SUBPACKAGE : '');

		if (spa())
			$url = Uri::hashSPA($uri);
		else
			$url = Uri::route($uri);

		$attribs = Html::setAttribute($attribs, 'id', 'btn-close');
		$attribs = Html::setAttribute($attribs, 'name', 'btn-close');
		$attribs = Html::setAttribute($attribs, 'onclick', 'window.location.href=\'' . $url . '\';');

		return static::_createButton('button', $label, $attribs);
	}

	public static function buttonBack($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Back');

		if (strpos($label, '<i') === false)
			$label = '<i class="fa fa-arrow-left"></i> ' . $label;

		$attribs = Html::setAttribute($attribs, 'id', 'btn-back');
		$attribs = Html::setAttribute($attribs, 'name', 'btn-back');
		$attribs = Html::setAttribute($attribs, 'onclick', 'window.history.back();');

		return static::_createButton('button', $label, $attribs);
	}

	public static function button($label = null, $attribs = null)
	{
		if (trim($label) == '')
			$label = t('Button');

		return static::_createButton('button', $label, $attribs);
	}

	private static function _createButton($type, $label, $attribs)
	{
		if (trim($label) == '')
			$label = t(ucfirst($type));

		$id = Html::getAttribute($attribs, 'id');
		$name = Html::getAttribute($attribs, 'name');

		setValueIfEmpty($id, $name, 'btn-' . $type, 'btn-' . $type);

		if ($type == 'submit')
			$class = 'btn btn-primary submit';
		else
			$class = 'btn btn-white ' . $type;

		$attribs = Html::setAttribute($attribs, 'id', $id);
		$attribs = Html::setAttribute($attribs, 'name', $name);
		$attribs = Html::setAttribute($attribs, 'type', $type);
		$attribs = Html::setAttribute($attribs, 'class', $class);

		$html = '<button ' . $attribs . '>' . $label . '</button>' . "\n";
		$html = Html::removeMultipleSpacesBetweenHTMLAttributes($html);

		return $html;
	}

	private static function _getId($name)
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

		$id = trim($id);

		return $id;
	}

	private static function _getName($name)
	{
		if (strpos($name, '.'))
		{
			$arr = explode('.', $name);
			$name = end($arr);
		}

		$name = trim($name);

		return $name;
	}

	private static function _getValue($name, $default)
	{
		if (is_null($default) == false)
			return htmlspecialchars($default);

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
			$value = isset($data->{$name}) ? $data->{$name} : '';

			// Checkbox will send array data
			// and converted to object by Request class
			if (is_object($value))
			{
				$value = (array)$value;
				$value = ',' . implode(',', $value) . ',';
			}
		}

		if ($value == '' and parent::$formVals)
		{
			if (is_object(parent::$formVals))
				$value = @parent::$formVals->$name;
			elseif (is_array(parent::$formVals))
				$value = @parent::$formVals[$name];
		}

		if ($name == 'params' and $key)
		{
			parse_str($value, $params);
			$value = isset($params[$key]) ? $params[$key] : '';
		}

		return $value;
	}

	private static function _getOptionFromDB($id, $columnValue = null, $columnText = null, $where = null, $selected = null)
	{
		$output = [];

		if (substr($id, -2) == 'Id')
			$table = substr($id, 0, strlen($id) - 2);
		elseif (substr($id, -3) == 'Ids')
			$table = substr($id, 0, strlen($id) - 3);
		else
			return $output;

		if (DB::tableExists($table) == false)
			return $output;

		$columns = DB::getColumnListing($table);

		if ($columnValue == '' or $columnText == '')
		{
			if ($columnValue == '')
			{
				if (Arr::has($columns, 'id', true))
					$columnValue = 'id';
				else
					$columnValue = $columns[0];
			}

			if ($columnText == '')
			{
				if (Arr::has($columns, 'name', true))
					$columnText = 'name';
				elseif (Arr::has($columns, 'title', true))
					$columnText = 'title';
				elseif (Arr::has($columns, 'subject', true))
					$columnText = 'subject';
				else
					$columnText = $columns[1];
			}
		}

		if (!$where)
			$where = '1=1'; // TODO default where

		if (Arr::has($columns, 'status', true) and stripos($where, 'status') === false)
		{
			if ($where)
				$where .= ' AND ';

			if (SIDE == 'backend')
				$where .= DB::wrapColumn('status') . ' > 0';
			else
				$where .= DB::wrapColumn('status') . ' = 2';
		}

		if ($selected)
			$where .= ' OR ' . DB::wrapColumn($columnValue) . ' = \'' . $selected . '\'';

		if (stripos($where, ' ORDER BY ') === false)
		{
			$where .= ' ORDER BY ';

			if (Arr::has($columns, 'ordering', true))
				$where .= DB::wrapColumn('ordering') . ', ';

			$where .= DB::wrapColumn($columnText);
		}

		$recursive = Arr::has($columns, 'parentid', true) ? true : false;
		$indent = -1;

		static::_getOptionFromDBProceed($table, $columnValue, $columnText, $where, 0, $output, $indent, $recursive);

		return $output;
	}

	private static function _getOptionFromDBProceed($table, $columnValue, $columnText, $where, $index, &$output, &$indent, $recursive)
	{
		if ($recursive)
			$where = DB::wrapColumn('parentid') . ' = '. DB::escape($index) . ' AND ' . $where;

		$columns = $columnValue . ' AS value, ' . $columnText . ' AS text';
		$rows = DB::select($columns)->from($table)->where($where)->loadAll();

		++$indent;

		foreach ($rows as $row)
		{
			if ($indent)
				$row->text = str_repeat('&nbsp;', $indent * 5) . '|- ' . $row->text;

			$output[] = $row;

			if ($recursive)
				static::_getOptionFromDBProceed($table, $columnValue, $columnText, $where, $row->value, $output, $indent, $recursive);
		}

		--$indent;

		return $output;
	}

	private static function _getOptionFromString($string)
	{
		$options = [];

		/**
		 * jan:January|feb:February|mar:March
		 *  <option value="jan">January</option>
		 *  <option value="feb">February</option>
		 *  <option value="mar">March</option>
		 */
		if (strpos($string, '|') and strpos($string, ':'))
		{
			foreach (explode('|', $string) as $item)
				$options[] = array_pad(explode(':', $item), 2, '');
		}

		/**
		 * January|February|March
		 *  <option value="January">January</option>
		 *  <option value="February">February</option>
		 *  <option value="March">March</option>
		 */
		elseif (strpos($string, '|'))
			$options = explode('|', $string);
		/** 
		 * jan:January
		 *  <option value="jan">January</option>
		 */
		elseif (strpos($string, ':'))
			$options[] = explode(':', $string);
		/**
		 * Single text
		 *  <option value="Single text">Single text</option>
		 */
		else
			$options[] = $string;

		return $options;
	}

	private static function _extractOptions($options)
	{
		$aryValue = [];
		$aryText = [];

		if (is_array($options))
		{
			foreach ($options as $option)
			{
				if (is_object($option))
					$option = static::_objOption2Array($option);

				if (is_array($option))
				{
					if (Arr::isAssoc($option))
					{
						$keys = array_keys($option);
						$key = current($keys);

						$aryValue[] = $key;
						$aryText[] = $option[$key];
					}
					else
					{
						$aryValue[] = $option[0];
						$aryText[] = isset($option[1]) ? $option[1] : $option[0];
					}
				}
				else
				{
					$aryValue[] = $option;
					$aryText[] = $option;
				}
			}
		}

		return [$aryValue, $aryText];
	}

	private static function _objOption2Array($option)
	{
		if (isset($option->value))
		{
			$value = $option->value;
			$text = isset($option->text) ? $option->text : $value;
		}
		elseif (isset($option->id))
		{
			$value = $option->id;
			if (isset($option->name))
				$text = $option->name;
			elseif (isset($option->title))
				$text = $option->title;
			elseif (isset($option->subject))
				$text = $option->subject;
			else
				$text = $value;
		}
		else
		{
			$option = (array)$option;
			$keys = array_keys($option);

			$value = @$option[$keys[0]];
			$text = @$option[$keys[1]] ? $option[$keys[1]] : $value;
		}

		return [$value, $text];
	}
}
